<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\SubmissionWorkflow;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ConsolidationController extends Controller
{
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

    /**
     * Consolidated, read-only aggregation of all returns across every
     * formation, with emphasis on approved returns.
     */
    public function index(): View
    {
        $statusRows = Application::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $stats = [
            'total' => (int) $statusRows->sum(),
            'pending' => (int) ($statusRows['pending'] ?? 0),
            'approved' => (int) ($statusRows['approved'] ?? 0),
            'returned' => (int) ($statusRows['returned'] ?? 0),
        ];

        $categoryRows = Application::query()
            ->selectRaw('category, status, count(*) as aggregate')
            ->groupBy('category', 'status')
            ->get();

        $categories = collect([
            SubmissionWorkflow::CATEGORY_STATE => 'State Commands',
            SubmissionWorkflow::CATEGORY_DIRECTORATE => 'Directorates',
            SubmissionWorkflow::CATEGORY_CGIS => 'CGIS Units',
        ])->map(function (string $label, string $category) use ($categoryRows) {
            $rows = $categoryRows->where('category', $category);

            return [
                'category' => $category,
                'label' => $label,
                'total' => $rows->sum('aggregate'),
                'pending' => $rows->where('status', 'pending')->sum('aggregate'),
                'approved' => $rows->where('status', 'approved')->sum('aggregate'),
                'returned' => $rows->where('status', 'returned')->sum('aggregate'),
            ];
        })->values();

        $unitRows = Application::query()
            ->selectRaw('category, scope_code, status, count(*) as aggregate')
            ->whereIn('category', [SubmissionWorkflow::CATEGORY_DIRECTORATE, SubmissionWorkflow::CATEGORY_CGIS])
            ->whereNotNull('scope_code')
            ->where('scope_code', '!=', '')
            ->groupBy('category', 'scope_code', 'status')
            ->get();

        $units = $unitRows
            ->groupBy(fn ($row) => $row->category . '|' . $row->scope_code)
            ->map(function (Collection $rows, string $key) {
                [$category, $scope] = explode('|', $key, 2);

                return [
                    'category' => $category,
                    'scope_code' => $scope,
                    'name' => self::DIRECTORATES[$scope] ?? $scope,
                    'total' => $rows->sum('aggregate'),
                    'approved' => $rows->where('status', 'approved')->sum('aggregate'),
                ];
            })
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $stateRows = Application::query()
            ->selectRaw('scope_code, status, count(*) as aggregate')
            ->where('category', SubmissionWorkflow::CATEGORY_STATE)
            ->whereNotNull('scope_code')
            ->where('scope_code', '!=', '')
            ->groupBy('scope_code', 'status')
            ->get();

        $states = $this->summarizeByCode($stateRows, 'scope_code')
            ->sortByDesc('total')
            ->values();

        $zoneRows = Application::query()
            ->selectRaw('zonal_code, status, count(*) as aggregate')
            ->whereNotNull('zonal_code')
            ->where('zonal_code', '!=', '')
            ->groupBy('zonal_code', 'status')
            ->get();

        $zones = $this->summarizeByCode($zoneRows, 'zonal_code')
            ->sortBy('code', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

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

        $recentApproved = Application::query()
            ->with('user:id,name,service_number')
            ->where('status', 'approved')
            ->latest('updated_at')
            ->limit(50)
            ->get();

        return view('admin.consolidation', [
            'stats' => $stats,
            'categories' => $categories,
            'units' => $units,
            'states' => $states,
            'zones' => $zones,
            'trendChart' => [
                'labels' => $trendLabels,
                'submissions' => $submissions,
                'approvals' => $approvals,
            ],
            'recentApproved' => $recentApproved,
        ]);
    }

    /**
     * Collapse per-code/per-status aggregate rows into one summary row per code.
     */
    private function summarizeByCode(Collection $rows, string $codeColumn): Collection
    {
        return $rows->groupBy($codeColumn)->map(fn (Collection $group, string $code) => [
            'code' => $code,
            'total' => $group->sum('aggregate'),
            'pending' => $group->where('status', 'pending')->sum('aggregate'),
            'approved' => $group->where('status', 'approved')->sum('aggregate'),
            'returned' => $group->where('status', 'returned')->sum('aggregate'),
        ]);
    }
}
