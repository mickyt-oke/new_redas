<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\ExcelReportService;
use App\Services\SubmissionWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HqAdminController extends Controller
{
    private const DIRECTORATES = [
        'hrm' => 'Human Resources Management (HRM)',
        'prs' => 'Planning, Research and Statistics (PRS)',
        'finance' => 'Finance and Accounts',
        'investigation' => 'Investigation and Compliance',
        'passport' => 'Passport and Other Travel Documents',
        'visa' => 'Visa and Residency',
        'migration' => 'Migration Directorate',
        'border' => 'Border Management',
        'ict' => 'ICT Directorate',
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
        $base = Application::query();

        $stats = [
            'total' => (clone $base)->count(),
            'awaiting_hq' => (clone $base)->awaiting(SubmissionWorkflow::STAGE_HQ_REVIEW)->count(),
            'approved' => (clone $base)->where('status', 'approved')->count(),
            'returned' => (clone $base)->where('status', 'returned')->count(),
        ];

        $directorateRows = Application::query()
            ->selectRaw("scope_code, status, count(*) as aggregate")
            ->where('category', SubmissionWorkflow::CATEGORY_DIRECTORATE)
            ->groupBy('scope_code', 'status')
            ->get();

        $directorates = collect(self::DIRECTORATES)->map(function (string $name, string $slug) use ($directorateRows) {
            $rows = $directorateRows->where('scope_code', $slug);

            return [
                'slug' => $slug,
                'name' => $name,
                'total' => $rows->sum('aggregate'),
                'pending' => $rows->where('status', 'pending')->sum('aggregate'),
                'approved' => $rows->where('status', 'approved')->sum('aggregate'),
                'returned' => $rows->where('status', 'returned')->sum('aggregate'),
            ];
        })->values();

        $cgisUnits = Application::query()
            ->with('user:id,assigned_cgis_unit_code')
            ->whereHas('user', fn ($q) => $q->whereNotNull('assigned_cgis_unit_code')->where('assigned_cgis_unit_code', '!=', ''))
            ->get()
            ->groupBy(fn (Application $application) => $application->user->assigned_cgis_unit_code)
            ->map(fn (Collection $group, string $unit) => [
                'unit' => $unit,
                'total' => $group->count(),
                'approved' => $group->where('status', 'approved')->count(),
            ])
            ->sortBy('unit')
            ->values();

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
        $filters = $request->validate([
            'formation' => ['nullable', 'string', 'max:60'],
            'category' => ['nullable', 'in:state,directorate,cgis'],
            'status' => ['nullable', 'in:pending,approved,returned'],
            'stage' => ['nullable', 'string', 'max:60'],
            'period' => ['nullable', 'date_format:Y-m'],
            'search' => ['nullable', 'string', 'max:120'],
        ]);

        $query = Application::query()->with('user:id,name,service_number,assigned_cgis_unit_code');

        if ($category = $filters['category'] ?? null) {
            if ($category === 'cgis') {
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
            $query->where(function ($q) use ($search) {
                $q->where('return_data->reporting_officer', 'like', "%{$search}%")
                    ->orWhere('return_data->command_name', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($inner) => $inner
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('service_number', 'like', "%{$search}%"));
            });
        }

        $applications = $query->latest()->paginate(20)->withQueryString();

        return view('admin.headquarters.returns', [
            'applications' => $applications,
            'directorates' => self::DIRECTORATES,
            'stageLabels' => self::STAGE_LABELS,
            'filters' => $filters,
        ]);
    }

    public function show(Application $application): View
    {
        $application->load('user:id,name,service_number,email,assigned_cgis_unit_code');

        $actorIds = collect($application->workflow_path ?? [])->pluck('by')->filter()->unique();
        $actors = User::query()->whereIn('id', $actorIds)->pluck('name', 'id');

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
        ]);
    }

    public function archive(Request $request): View
    {
        $filters = $request->validate([
            'formation' => ['nullable', 'string', 'max:60'],
            'period' => ['nullable', 'date_format:Y-m'],
            'search' => ['nullable', 'string', 'max:120'],
        ]);

        $query = Application::query()
            ->with('user:id,name,service_number,assigned_cgis_unit_code')
            ->where('status', 'approved');

        if ($formation = $filters['formation'] ?? null) {
            $query->where('scope_code', $formation);
        }

        if ($period = $filters['period'] ?? null) {
            $query->where('return_data->report_period', $period);
        }

        if ($search = trim((string) ($filters['search'] ?? ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('return_data->reporting_officer', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($inner) => $inner
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('service_number', 'like', "%{$search}%"));
            });
        }

        $archived = $query->latest('updated_at')->paginate(20)->withQueryString();

        return view('admin.headquarters.archive', [
            'archived' => $archived,
            'directorates' => self::DIRECTORATES,
            'filters' => $filters,
        ]);
    }

    public function analytics(): View
    {
        $perDirectorate = Application::query()
            ->selectRaw('scope_code, count(*) as aggregate')
            ->where('category', SubmissionWorkflow::CATEGORY_DIRECTORATE)
            ->groupBy('scope_code')
            ->pluck('aggregate', 'scope_code');

        $directorateChart = [
            'labels' => array_values(self::DIRECTORATES),
            'data' => collect(array_keys(self::DIRECTORATES))
                ->map(fn (string $slug) => (int) ($perDirectorate[$slug] ?? 0))
                ->all(),
        ];

        $statusCounts = Application::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $statusChart = [
            'labels' => ['Pending', 'Approved', 'Returned'],
            'data' => [
                (int) ($statusCounts['pending'] ?? 0),
                (int) ($statusCounts['approved'] ?? 0),
                (int) ($statusCounts['returned'] ?? 0),
            ],
        ];

        $trendLabels = [];
        $trendData = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $trendLabels[] = $month->format('M Y');
            $trendData[] = Application::query()
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        $stageTurnaround = [];
        foreach (Application::query()->whereNotNull('workflow_path')->cursor() as $application) {
            $entries = collect($application->workflow_path ?? []);
            for ($i = 1; $i < $entries->count(); $i++) {
                $from = $entries[$i - 1]['at'] ?? null;
                $to = $entries[$i]['at'] ?? null;
                $stage = $entries[$i]['stage'] ?? null;
                if ($from && $to && $stage) {
                    $hours = (strtotime((string) $to) - strtotime((string) $from)) / 3600;
                    if ($hours >= 0) {
                        $stageTurnaround[$stage][] = $hours;
                    }
                }
            }
        }

        $turnaround = collect($stageTurnaround)->map(fn (array $hours, string $stage) => [
            'stage' => self::STAGE_LABELS[$stage] ?? ucwords(str_replace('_', ' ', $stage)),
            'avg_hours' => round(array_sum($hours) / count($hours), 1),
        ])->sortBy('stage')->values();

        return view('admin.headquarters.analytics', [
            'directorateChart' => $directorateChart,
            'statusChart' => $statusChart,
            'trendChart' => ['labels' => $trendLabels, 'data' => $trendData],
            'turnaround' => $turnaround,
        ]);
    }

    public function reports(): View
    {
        return view('admin.headquarters.reports', [
            'templates' => ExcelReportService::TEMPLATES,
        ]);
    }

    public function generateReport(Request $request, ExcelReportService $excel): BinaryFileResponse|RedirectResponse
    {
        $validated = $request->validate([
            'report_type' => ['required', 'in:quarterly,biannual,annual'],
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
}
