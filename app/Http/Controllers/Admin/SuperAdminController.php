<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ResolvesHashedModels;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use App\Services\ExecutiveDashboardAggregator;
use App\Services\ReturnDataExplorerService;
use App\Services\SubmissionWorkflow;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Executive (super-admin) portal — strictly read-only visualization of all
 * returns across every formation, status and period.
 */
class SuperAdminController extends Controller
{
    use ResolvesHashedModels;
    private const DIRECTORATES = [
        'hrm' => 'HRM',
        'prs' => 'PRS',
        'finance' => 'Finance',
        'investigation' => 'Investigation',
        'passport' => 'Passport',
        'visa' => 'Visa',
        'migration' => 'Migration',
        'border' => 'Border',
        'ict' => 'ICT',
        'works-logistics' => 'Works & Logistics',
    ];

    private const STAGE_LABELS = [
        SubmissionWorkflow::STAGE_SUBMITTED => 'Submitted',
        SubmissionWorkflow::STAGE_DESK_REVIEW => 'Desk Admin Review',
        SubmissionWorkflow::STAGE_ZONAL_REVIEW => 'Zonal Review',
        SubmissionWorkflow::STAGE_DIRECTORATE_REVIEW => 'Directorate Admin Review',
        SubmissionWorkflow::STAGE_CGIS_DESK_REVIEW => 'CGIS Desk Review',
        SubmissionWorkflow::STAGE_HQ_REVIEW => 'HQ Admin Review',
        SubmissionWorkflow::STAGE_ADMIN_REVIEW => 'Final Approval',
        SubmissionWorkflow::STAGE_APPROVED => 'Approved',
    ];

    public function dashboard(): View
    {
        abort_unless(auth()->user()?->hasCategory('super_admin', 'admin', 'cgis_unit_user', 'cgis_desk_admin'), 403);

        $statusCounts = Application::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $stats = [
            'total' => (int) $statusCounts->sum(),
            'approved' => (int) ($statusCounts['approved'] ?? 0),
            'pending' => (int) ($statusCounts['pending'] ?? 0),
            'returned' => (int) ($statusCounts['returned'] ?? 0),
            'avg_turnaround_days' => $this->averageTurnaroundDays(),
        ];

        $statusChart = [
            'labels' => ['Pending', 'Approved', 'Returned'],
            'data' => [
                (int) ($statusCounts['pending'] ?? 0),
                (int) ($statusCounts['approved'] ?? 0),
                (int) ($statusCounts['returned'] ?? 0),
            ],
        ];

        $categoryCounts = Application::query()
            ->selectRaw('category, count(*) as aggregate')
            ->groupBy('category')
            ->pluck('aggregate', 'category');

        $categoryChart = [
            'labels' => ['State Commands', 'Directorates', 'CGIS Units'],
            'data' => [
                (int) ($categoryCounts[SubmissionWorkflow::CATEGORY_STATE] ?? 0),
                (int) ($categoryCounts[SubmissionWorkflow::CATEGORY_DIRECTORATE] ?? 0),
                (int) ($categoryCounts[SubmissionWorkflow::CATEGORY_CGIS] ?? 0),
            ],
        ];

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

        $zoneCounts = Application::query()
            ->selectRaw('zonal_code, count(*) as aggregate')
            ->whereNotNull('zonal_code')
            ->where('zonal_code', '!=', '')
            ->groupBy('zonal_code')
            ->orderBy('zonal_code')
            ->pluck('aggregate', 'zonal_code');

        $zoneChart = [
            'labels' => $zoneCounts->keys()->all(),
            'data' => $zoneCounts->map(fn ($count) => (int) $count)->values()->all(),
        ];

        $stateCounts = Application::query()
            ->selectRaw('scope_code, count(*) as aggregate')
            ->where('category', SubmissionWorkflow::CATEGORY_STATE)
            ->whereNotNull('scope_code')
            ->where('scope_code', '!=', '')
            ->groupBy('scope_code')
            ->orderByDesc('aggregate')
            ->limit(10)
            ->pluck('aggregate', 'scope_code');

        $stateChart = [
            'labels' => $stateCounts->keys()->all(),
            'data' => $stateCounts->map(fn ($count) => (int) $count)->values()->all(),
        ];

        $trendLabels = [];
        $submissions = [];
        $approvals = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $trendLabels[] = $month->format('M Y');
            $submissions[] = Application::query()
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $approvals[] = Application::query()
                ->where('status', 'approved')
                ->whereYear('updated_at', $month->year)
                ->whereMonth('updated_at', $month->month)
                ->count();
        }

        $latestApproved = Application::query()
            ->select(['id', 'user_id', 'category', 'return_data', 'updated_at'])
            ->with('user:id,name,service_number,assigned_cgis_unit_code')
            ->where('status', 'approved')
            ->latest('updated_at')
            ->limit(25)
            ->get();

        $executive = app(ExecutiveDashboardAggregator::class)->aggregate();

        return view('super-admin.executive-dashboard', [
            'stats' => $stats,
            'statusChart' => $statusChart,
            'categoryChart' => $categoryChart,
            'directorateChart' => $directorateChart,
            'zoneChart' => $zoneChart,
            'stateChart' => $stateChart,
            'trendChart' => [
                'labels' => $trendLabels,
                'submissions' => $submissions,
                'approvals' => $approvals,
            ],
            'latestApproved' => $latestApproved,
            'executive' => $executive,
        ]);
    }

    /**
     * Shared executive view exposed to CGIS administrators.
     */
    public function executiveDashboard(): View
    {
        return $this->dashboard();
    }

    /**
     * Searchable data explorer for return_data records.
     */
    public function dataExplorer(Request $request, ReturnDataExplorerService $service): View|Response
    {
        abort_unless(auth()->user()?->hasCategory('super_admin', 'admin', 'cgis_unit_user', 'cgis_desk_admin'), 403);

        $filters = $request->validate([
            'category' => ['nullable', 'in:state,directorate,cgis,zonal'],
            'scope_code' => ['nullable', 'string', 'max:60'],
            'zonal_code' => ['nullable', 'string', 'max:10'],
            'status' => ['nullable', 'in:pending,approved,returned,rejected'],
            'period_from' => ['nullable', 'date_format:Y-m'],
            'period_to' => ['nullable', 'date_format:Y-m', 'after_or_equal:period_from'],
            'search' => ['nullable', 'string', 'max:120'],
            'field_path' => ['nullable', 'string', 'max:120'],
            'field_operator' => ['nullable', 'in:=,!=,>,<,>=,<=,contains'],
            'field_value' => ['nullable', 'string', 'max:120'],
            'group_by' => ['nullable', 'in:formation,zone,state,directorate,category,status'],
            'sum_path' => ['nullable', 'string', 'max:120'],
        ]);

        if ($request->input('export') === 'csv') {
            $records = $service->fetchForExport($filters);
            $filename = 'return-data-explorer-' . now()->format('Y-m-d-His') . '.csv';

            return response()->make($service->toCsv($records), 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        }

        $result = $service->search($filters);
        $summary = $service->summarise($result['records'], $result['filters']['group_by']);
        $aggregations = $service->aggregate(
            $result['records'],
            $result['filters']['group_by'],
            $result['filters']['sum_path'] ?: null
        );

        return view('super-admin.data-explorer', [
            'records' => $result['records'],
            'paginator' => $result['paginator'],
            'filters' => $result['filters'],
            'summary' => $summary,
            'aggregations' => $aggregations,
            'groupBy' => $result['filters']['group_by'],
            'stageLabels' => self::STAGE_LABELS,
        ]);
    }

    /**
     * CGIS route alias for the data explorer.
     */
    public function cgisDataExplorer(Request $request, ReturnDataExplorerService $service): View|Response
    {
        return $this->dataExplorer($request, $service);
    }

    public function returns(Request $request): View
    {
        abort_unless(auth()->user()?->hasCategory('super_admin'), 403);

        $filters = $request->validate([
            'category' => ['nullable', 'in:state,directorate,cgis'],
            'status' => ['nullable', 'in:pending,approved,returned'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'search' => ['nullable', 'string', 'max:120'],
        ]);

        $query = Application::query()->with('user:id,name,service_number,assigned_cgis_unit_code');

        if ($category = $filters['category'] ?? null) {
            $query->where('category', $category);
        }

        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }

        if ($from = $filters['date_from'] ?? null) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $filters['date_to'] ?? null) {
            $query->whereDate('created_at', '<=', $to);
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

        $applications = $query->latest()->paginate(20)->withQueryString();

        return view('super-admin.returns', [
            'applications' => $applications,
            'filters' => $filters,
            'stageLabels' => self::STAGE_LABELS,
        ]);
    }

    public function show(string $applicationHash): View
    {
        abort_unless(auth()->user()?->hasCategory('super_admin'), 403);

        $application = $this->resolveApplication($applicationHash);
        $application->load(['user:id,name,service_number,email,assigned_cgis_unit_code', 'reviewComments.user']);

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
            'layout' => 'super-admin.layout',
            'backRoute' => 'superadmin.returns',
            'documentRoute' => 'superadmin.returns.document',
            'downloadRoute' => 'superadmin.returns.download',
        ]);
    }

    /**
     * Escape LIKE wildcard characters so user-supplied search strings cannot
     * alter query semantics.
     */
    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    /**
     * Average end-to-end turnaround (submission to final approval) in days,
     * derived from workflow_path timestamps.
     */
    private function averageTurnaroundDays(): ?float
    {
        $durations = [];

        foreach (Application::query()->select(['id', 'workflow_path'])->where('status', 'approved')->whereNotNull('workflow_path')->cursor() as $application) {
            $entries = collect($application->workflow_path ?? []);
            $first = $entries->first()['at'] ?? null;
            $last = $entries->last()['at'] ?? null;

            if ($first && $last) {
                $days = (strtotime((string) $last) - strtotime((string) $first)) / 86400;
                if ($days >= 0) {
                    $durations[] = $days;
                }
            }
        }

        return $durations === [] ? null : round(array_sum($durations) / count($durations), 1);
    }
}
