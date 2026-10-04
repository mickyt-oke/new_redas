<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HqAnalyticsService
{
    public function getDirectorateChart(array $directorates): array
    {
        $perDirectorate = Application::query()
            ->selectRaw('scope_code, count(*) as aggregate')
            ->where('category', SubmissionWorkflow::CATEGORY_DIRECTORATE)
            ->groupBy('scope_code')
            ->pluck('aggregate', 'scope_code');

        return [
            'labels' => array_values($directorates),
            'data' => collect(array_keys($directorates))
                ->map(fn (string $slug) => (int) ($perDirectorate[$slug] ?? 0))
                ->all(),
        ];
    }

    public function getStatusChart(): array
    {
        $statusCounts = Application::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            'labels' => ['Pending', 'Approved', 'Returned'],
            'data' => [
                (int) ($statusCounts[SubmissionWorkflow::STATUS_PENDING] ?? 0),
                (int) ($statusCounts[SubmissionWorkflow::STATUS_APPROVED] ?? 0),
                (int) ($statusCounts[SubmissionWorkflow::STATUS_RETURNED] ?? 0),
            ],
        ];
    }

    public function getTrendChart(): array
    {
        $startDate = now()->subMonths(11)->startOfMonth();

        $monthExpr = match (DB::getDriverName()) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };

        $trendData = Application::query()
            ->selectRaw("{$monthExpr} as month, count(*) as aggregate")
            ->where('created_at', '>=', $startDate)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('aggregate', 'month');

        $labels = [];
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $key = $month->format('Y-m');
            $labels[] = $month->format('M Y');
            $data[] = (int) ($trendData[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function getTurnaroundStats(array $stageLabels): Collection
    {
        $stageTurnaround = [];
        
        // We still need to iterate over workflow_path because it's a JSON array
        // But we can use cursor to keep memory low
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

        return collect($stageTurnaround)->map(fn (array $hours, string $stage) => [
            'stage' => $stageLabels[$stage] ?? ucwords(str_replace('_', ' ', $stage)),
            'avg_hours' => round(array_sum($hours) / count($hours), 1),
        ])->sortBy('stage')->values();
    }
}
