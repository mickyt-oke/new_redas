<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ResolvesHashedModels;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\ExcelReportService;
use App\Services\SubmissionWorkflow;
use App\Services\HqAnalyticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HqAdminController extends Controller
{
    use ResolvesHashedModels;
    private const DIRECTORATES = [
        'hrm' => 'Human Resources Management (HRM)',
        'prs' => 'Planning, Research and Statistics (PRS)',
        'finance' => 'Finance and Accounts (F/A)',
        'investigation' => 'Investigation and Compliance (I/C)',
        'passport' => 'Passport and Other Travel Documents (P/OTD)',
        'visa' => 'Visa and Residency (V/R)',
        'migration' => 'Migration Directorate',
        'border' => 'Border Management',
        'ict' => 'ICT/Cybersecurity Directorate',
        'works-logistics' => 'Works and Logistics',
    ];

    private const STAGE_LABELS = [
        SubmissionWorkflow::STAGE_SUBMITTED => 'Submitted',
        SubmissionWorkflow::STAGE_DESK_REVIEW => 'Desk Admin Review',
        SubmissionWorkflow::STAGE_ZONAL_REVIEW => 'Zonal Review',
        SubmissionWorkflow::STAGE_DIRECTORATE_REVIEW => 'Directorate Admin Review',
        SubmissionWorkflow::STAGE_HQ_REVIEW => 'HQ Admin Review',
        SubmissionWorkflow::STAGE_ADMIN_REVIEW => 'Final Approval',
        SubmissionWorkflow::STAGE_APPROVED => 'Approved',
    ];

    public function index(): View
    {
        abort_unless(auth()->user()?->hasCategory('hq_admin', 'admin'), 403);

        $stats = Application::query()
            ->selectRaw("count(*) as total", [])
            ->selectRaw("count(case when workflow_stage = ? and status not in (?, ?) then 1 end) as awaiting_hq", [
                SubmissionWorkflow::STAGE_HQ_REVIEW,
                SubmissionWorkflow::STATUS_APPROVED,
                SubmissionWorkflow::STATUS_REJECTED,
            ])
            ->selectRaw("count(case when status = ? then 1 end) as approved", [SubmissionWorkflow::STATUS_APPROVED])
            ->selectRaw("count(case when status = ? then 1 end) as returned", [SubmissionWorkflow::STATUS_RETURNED])
            ->first();


        $directorateRows = Application::query()
            ->selectRaw("scope_code, status, count(*) as aggregate", [])
            ->where('category', SubmissionWorkflow::CATEGORY_DIRECTORATE)
            ->groupBy('scope_code', 'status')
            ->get();

        $directorates = collect(self::DIRECTORATES)->map(function (string $name, string $slug) use ($directorateRows) {
            $rows = $directorateRows->where('scope_code', $slug);

            return [
                'slug' => $slug,
                'name' => $name,
                'total' => $rows->sum('aggregate'),
                'pending' => $rows->where('status', SubmissionWorkflow::STATUS_PENDING)->sum('aggregate'),
                'approved' => $rows->where('status', SubmissionWorkflow::STATUS_APPROVED)->sum('aggregate'),
                'returned' => $rows->where('status', SubmissionWorkflow::STATUS_RETURNED)->sum('aggregate'),
            ];
        })->values();

        $cgisUnits = Application::query()
            ->join('users', 'applications.user_id', '=', 'users.id', 'inner', false)
            ->select('users.assigned_cgis_unit_code as unit')
            ->selectRaw('count(*) as total')
            ->selectRaw("sum(case when applications.status = ? then 1 else 0 end) as approved", [SubmissionWorkflow::STATUS_APPROVED])
            ->whereNotNull('users.assigned_cgis_unit_code')
            ->where('users.assigned_cgis_unit_code', '!=', '')
            ->groupBy('users.assigned_cgis_unit_code')
            ->orderBy('users.assigned_cgis_unit_code')
            ->get();


        $recentReturns = Application::query()
            ->with('user:id,name,service_number,assigned_cgis_unit_code')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.headquarters.index', [
            'stats' => $stats,
            'directorates' => $directorates,
            'cgisUnits' => $cgisUnits,
            'recentReturns' => $recentReturns,
        ]);
    }

    public function returns(Request $request): View
    {
        abort_unless(auth()->user()?->hasCategory('hq_admin', 'admin'), 403);

        $filters = $request->validate([
            'formation' => ['nullable', 'string', 'max:60'],
            'category' => ['nullable', 'in:' . implode(',', [
                SubmissionWorkflow::CATEGORY_STATE,
                SubmissionWorkflow::CATEGORY_DIRECTORATE,
                SubmissionWorkflow::CATEGORY_CGIS,
            ])],
            'status' => ['nullable', 'in:' . implode(',', [
                SubmissionWorkflow::STATUS_PENDING,
                SubmissionWorkflow::STATUS_APPROVED,
                SubmissionWorkflow::STATUS_RETURNED,
            ])],
            'stage' => ['nullable', 'string', 'max:60'],
            'period' => ['nullable', 'date_format:Y-m'],
            'search' => ['nullable', 'string', 'max:120'],
        ]);

        $query = Application::query()
            ->with('user:id,name,service_number,assigned_cgis_unit_code');

        $this->filterApplications($query, $filters);

        $applications = $query->latest()->paginate(20)->withQueryString();

        return view('admin.headquarters.returns', [
            'applications' => $applications,
            'directorates' => self::DIRECTORATES,
            'stageLabels' => self::STAGE_LABELS,
            'filters' => $filters,
        ]);
    }

    public function show(string $applicationHash): View
    {
        $application = $this->resolveApplication($applicationHash);
        $application->load(['user:id,name,service_number,email,assigned_cgis_unit_code', 'reviewComments.user']);

        $actorIds = collect($application->workflow_path ?? [])->pluck('by')->filter()->unique();
        $actors = User::query()->whereIn('id', $actorIds, 'and', false)->pluck('name', 'id');

        $timeline = collect($application->workflow_path ?? [])->map(function (array $entry) use ($actors) {
            return [
                'stage' => self::STAGE_LABELS[$entry['stage'] ?? ''] ?? ucwords(str_replace('_', ' ', (string) ($entry['stage'] ?? 'unknown'))),
                'action' => $entry['action'] ?? 'submitted',
                'by' => $actors[$entry['by'] ?? null] ?? 'System',
                'at' => $entry['at'] ?? null,
            ];
        });

        return view('admin.headquarters.return-show', [
            'application' => $application,
            'timeline' => $timeline,
            'stageLabels' => self::STAGE_LABELS,
            'downloadRoute' => 'admin.hq.returns.download',
        ]);
    }

    public function archive(Request $request): View
    {
        abort_unless(auth()->user()?->hasCategory('hq_admin', 'admin'), 403);

        $filters = $request->validate([
            'formation' => ['nullable', 'string', 'max:60'],
            'period' => ['nullable', 'date_format:Y-m'],
            'search' => ['nullable', 'string', 'max:120'],
        ]);

        $query = Application::query()
            ->with('user:id,name,service_number,assigned_cgis_unit_code')
            ->where('status', SubmissionWorkflow::STATUS_APPROVED);

        $this->filterApplications($query, $filters);

        $archived = $query->latest('updated_at')->paginate(20)->withQueryString();

        return view('admin.headquarters.archive', [
            'archived' => $archived,
            'directorates' => self::DIRECTORATES,
            'filters' => $filters,
        ]);
    }

    public function analytics(HqAnalyticsService $analytics): View
    {
        abort_unless(auth()->user()?->hasCategory('hq_admin', 'admin'), 403);

        return view('admin.headquarters.analytics', [
            'directorateChart' => $analytics->getDirectorateChart(self::DIRECTORATES),
            'statusChart' => $analytics->getStatusChart(),
            'trendChart' => $analytics->getTrendChart(),
            'turnaround' => $analytics->getTurnaroundStats(self::STAGE_LABELS),
        ]);
    }

    public function reports(): View
    {
        abort_unless(auth()->user()?->hasCategory('hq_admin', 'admin'), 403);

        return view('admin.headquarters.reports', [
            'templates' => ExcelReportService::TEMPLATES,
        ]);
    }

    public function generateReport(Request $request, ExcelReportService $excel): BinaryFileResponse|RedirectResponse
    {
        abort_unless(auth()->user()?->hasCategory('hq_admin', 'admin'), 403);

        $validated = $request->validate([
            'report_type' => ['required', 'in:' . implode(',', array_keys(ExcelReportService::TEMPLATES))],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'part' => ['nullable', 'integer', 'min:0', 'max:4'],
        ]);

        $reportType = $validated['report_type'];
        $year = (int) $validated['year'];
        $part = isset($validated['part']) ? (int) $validated['part'] : null;

        try {
            if ($reportType !== 'annual'
                && ($part === null || ! isset(ExcelReportService::TEMPLATES[$reportType]['parts'][$part]))) {
                return back()
                    ->withInput()
                    ->with('error', 'Please choose a valid period part for the selected report template.');
            }

            [$title, $label, $from, $to] = ExcelReportService::periodFor($reportType, $year, $part);

            $applications = Application::query()
                ->with('user:id,name,service_number,assigned_cgis_unit_code')
                ->whereBetween('created_at', [$from, $to])
                ->orderBy('scope_code')
                ->orderBy('created_at')
                ->get();

            if ($applications->isEmpty()) {
                return back()
                    ->withInput()
                    ->with('error', "No returns were submitted in {$label} {$year}.");
            }

            $path = $excel->generate($reportType, $year, $part, $applications);

            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'hq_report_generated',
                'entity_type' => 'report',
                'entity_id' => null,
                'details' => [
                    'report_type' => $reportType,
                    'year' => $year,
                    'part' => $part,
                    'period' => "{$from->toDateString()} to {$to->toDateString()}",
                    'returns_included' => $applications->count(),
                ],
                'ip_address' => $request->ip(),
                'status' => 'success',
                'created_at' => now(),
            ]);

            $filename = str(sprintf('redas-%s-report-%d', $reportType, $year))
                ->when($part, fn ($name) => $name->append('-part-' . $part))
                ->append('.xlsx')
                ->toString();

            return response()->download($path, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend();
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'The report could not be generated. Please try again.');
        }
    }

    /**
     * Escape LIKE wildcard characters so user-supplied search strings cannot
     * alter query semantics.
     */
    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    protected function filterApplications(\Illuminate\Database\Eloquent\Builder $query, array $filters): void
    {
        if ($category = $filters['category'] ?? null) {
            if ($category === SubmissionWorkflow::CATEGORY_CGIS) {
                $query->whereHas('user', fn ($q) => $q->whereNotNull('assigned_cgis_unit_code')->where('assigned_cgis_unit_code', '!=', ''));
            } else {
                $query->where('category', $category);
            }
        }

        if ($formation = $filters['formation'] ?? null) {
            $query->where('scope_code', $formation);
        }

        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }

        if ($stage = $filters['stage'] ?? null) {
            $query->where('workflow_stage', $stage);
        }

        if ($period = $filters['period'] ?? null) {
            $query->where('return_data->report_period', $period);
        }

        if ($search = trim((string) ($filters['search'] ?? ''))) {
            $escaped = $this->escapeLike($search);
            $query->where(function ($q) use ($escaped) {
                $q->where('return_data->reporting_officer', 'like', "%{$escaped}%")
                    ->orWhere('return_data->command_name', 'like', "%{$escaped}%")
                    ->orWhereHas('user', fn ($inner) => $inner
                        ->where('name', 'like', "%{$escaped}%")
                        ->orWhere('service_number', 'like', "%{$escaped}%"));
            });
        }
    }
}
