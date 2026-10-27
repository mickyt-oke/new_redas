<?php

namespace App\Services;

use App\Models\Application;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Search, aggregate, score and export return_data records for the executive
 * data-explorer. All operations are read-only.
 */
class ReturnDataExplorerService
{
    private const CSV_LIMIT = 1000;

    private const ALLOWED_CATEGORIES = [
        SubmissionWorkflow::CATEGORY_STATE,
        SubmissionWorkflow::CATEGORY_DIRECTORATE,
        SubmissionWorkflow::CATEGORY_CGIS,
        SubmissionWorkflow::CATEGORY_ZONAL,
    ];

    private const ALLOWED_STATUSES = [
        SubmissionWorkflow::STATUS_PENDING,
        SubmissionWorkflow::STATUS_APPROVED,
        SubmissionWorkflow::STATUS_RETURNED,
        SubmissionWorkflow::STATUS_REJECTED,
    ];

    private const ALLOWED_OPERATORS = ['=', '!=', '>', '<', '>=', '<=', 'contains'];

    private const ALLOWED_RECORD_SORTS = [
        'id', 'category', 'scope_code', 'zonal_code', 'status', 'created_at',
        'report_period', 'reporting_officer', 'command_name',
    ];

    private const ALLOWED_AGGREGATION_SORTS = [
        'label', 'total', 'approved', 'pending', 'returned', 'rejected', 'sum',
    ];

    /**
     * Search applications and return paginated + raw collections plus the
     * normalised filters that were applied.
     *
     * @return array{paginator: LengthAwarePaginator, records: Collection, filters: array}
     */
    public function search(array $filters, int $perPage = 20, ?User $viewer = null): array
    {
        $filters = $this->normaliseFilters($filters);

        $query = $viewer === null
            ? Application::query()
            : SubmissionWorkflow::scopeQueryForViewer(Application::query(), $viewer);

        $query->select(['id', 'user_id', 'category', 'scope_code', 'zonal_code', 'status', 'return_data', 'created_at', 'updated_at'])
            ->with('user:id,name,service_number,assigned_cgis_unit_code');

        $this->applyColumnFilters($query, $filters);
        $this->applyTextSearch($query, $filters['search'] ?? '');
        $this->applyPeriodRange($query, $filters['period_from'] ?? '', $filters['period_to'] ?? '');

        // Paginate the SQL result set.
        $paginator = $query->latest('created_at')->paginate($perPage)->withQueryString();

        // Data-field filters run in PHP on the current page to avoid complex
        // JSON path queries while still being accurate for the result set.
        $records = $this->applyDataFieldFilter(
            collect($paginator->items()),
            $filters['field_path'] ?? '',
            $filters['field_operator'] ?? '',
            $filters['field_value'] ?? ''
        );

        // Apply user-selected sorting on the filtered record set.
        $records = $this->applySorting($records, $filters['sort_by'], $filters['sort_direction']);

        return [
            'paginator' => $paginator,
            'records' => $records->values(),
            'filters' => $filters,
        ];
    }

    /**
     * Fetch a raw collection for aggregation/export, capped to avoid memory
     * exhaustion on very large JSON datasets.
     */
    public function fetchForExport(array $filters, int $limit = self::CSV_LIMIT, ?User $viewer = null): Collection
    {
        $filters = $this->normaliseFilters($filters);

        $query = $viewer === null
            ? Application::query()
            : SubmissionWorkflow::scopeQueryForViewer(Application::query(), $viewer);

        $query->select(['id', 'user_id', 'category', 'scope_code', 'zonal_code', 'status', 'return_data', 'created_at', 'updated_at'])
            ->with('user:id,name,service_number,assigned_cgis_unit_code');

        $this->applyColumnFilters($query, $filters);
        $this->applyTextSearch($query, $filters['search'] ?? '');
        $this->applyPeriodRange($query, $filters['period_from'] ?? '', $filters['period_to'] ?? '');

        $items = $query->latest('created_at')->limit($limit)->get();

        return $this->applyDataFieldFilter(
            $items,
            $filters['field_path'] ?? '',
            $filters['field_operator'] ?? '',
            $filters['field_value'] ?? ''
        )->values();
    }

    /**
     * Build summary counts, aggregations and metadata for the explorer view.
     *
     * @return array{total: int, approved: int, pending: int, returned: int, rejected: int, formations: int, latest: string|null, groups: Collection}
     */
    public function summarise(Collection $records, string $groupBy = 'formation'): array
    {
        $groupBy = in_array($groupBy, ['formation', 'zone', 'state', 'directorate', 'category', 'status'], true)
            ? $groupBy
            : 'formation';

        return [
            'total' => $records->count(),
            'approved' => $records->where('status', SubmissionWorkflow::STATUS_APPROVED)->count(),
            'pending' => $records->where('status', SubmissionWorkflow::STATUS_PENDING)->count(),
            'returned' => $records->where('status', SubmissionWorkflow::STATUS_RETURNED)->count(),
            'rejected' => $records->where('status', SubmissionWorkflow::STATUS_REJECTED)->count(),
            'formations' => $records->pluck('scope_code')->filter()->unique()->count(),
            'latest' => $records->max('created_at')?->toDateTimeString(),
            'groups' => $this->aggregate($records, $groupBy),
        ];
    }

    /**
     * Group records by the requested key and return counts plus a numeric sum
     * when a field path is supplied.
     */
    public function aggregate(Collection $records, string $groupBy, ?string $sumPath = null, string $sortBy = 'total', string $direction = 'desc'): Collection
    {
        $grouped = $records->groupBy(function (Application $application) use ($groupBy) {
            return match ($groupBy) {
                'zone' => strtoupper((string) $application->zonal_code) ?: 'No Zone',
                'state' => strtoupper((string) $application->scope_code) ?: 'No State',
                'directorate' => $this->directorateLabel($application->scope_code),
                'category' => ucfirst((string) $application->category),
                'status' => ucfirst((string) $application->status),
                default => $this->formationLabel($application),
            };
        });

        $aggregated = $grouped->map(function (Collection $items, string $key) use ($sumPath) {
            $row = [
                'label' => $key,
                'total' => $items->count(),
                'approved' => $items->where('status', SubmissionWorkflow::STATUS_APPROVED)->count(),
                'pending' => $items->where('status', SubmissionWorkflow::STATUS_PENDING)->count(),
                'returned' => $items->where('status', SubmissionWorkflow::STATUS_RETURNED)->count(),
                'rejected' => $items->where('status', SubmissionWorkflow::STATUS_REJECTED)->count(),
                'sum' => 0,
            ];

            if ($sumPath) {
                $row['sum'] = $items->sum(fn (Application $a) => $this->numericValueAt($a->return_data, $sumPath));
            }

            return $row;
        });

        return $this->sortAggregation($aggregated, $sortBy, $direction)->values();
    }

    /**
     * Build a national scorecard for a numeric path across the current filtered
     * record set. Supports wildcard paths such as border.land.*.*.arrival_male.
     *
     * @return array{path: string, label: string, total: float, average: float, count: int, max: array|null, min: array|null, top_formations: Collection}
     */
    public function scorecard(Collection $records, string $path, ?string $label = null): array
    {
        $path = trim($path);
        if ($path === '') {
            return $this->emptyScorecard($path, $label);
        }

        // Per-formation totals.
        $formationTotals = $records->mapToGroups(function (Application $application) use ($path) {
            $value = $this->sumAtPath($application->return_data ?? [], $path);

            return [$this->formationLabel($application) => $value];
        })->map(fn (Collection $values) => $values->sum())->filter(fn ($value) => $value != 0);

        $total = $formationTotals->sum();
        $count = $formationTotals->count();

        if ($count === 0) {
            return $this->emptyScorecard($path, $label);
        }

        $maxLabel = $formationTotals->sortDesc()->keys()->first();
        $minLabel = $formationTotals->sort()->keys()->first();

        return [
            'path' => $path,
            'label' => $label ?? $path,
            'total' => $total,
            'average' => round($total / $count, 2),
            'count' => $count,
            'max' => ['label' => $maxLabel, 'value' => $formationTotals[$maxLabel]],
            'min' => ['label' => $minLabel, 'value' => $formationTotals[$minLabel]],
            'top_formations' => $formationTotals->sortDesc()->take(10),
        ];
    }

    /**
     * Convert a collection of records to a CSV string, optionally including a
     * scorecard summary header.
     */
    public function toCsv(Collection $records, ?array $scorecard = null, ?string $sumPath = null): string
    {
        $headers = [
            'Return ID',
            'Category',
            'Formation',
            'Zone',
            'Status',
            'Period',
            'Reporting Officer',
            'Command Name',
            'Submitted At',
            'Updated At',
        ];

        if ($sumPath) {
            $headers[] = 'Metric Value (' . $sumPath . ')';
        }

        $output = fopen('php://temp', 'r+');

        if ($scorecard) {
            fputcsv($output, ['National Scorecard']);
            fputcsv($output, ['Metric', $scorecard['label'] ?? $scorecard['path'] ?? '']);
            fputcsv($output, ['Total', $scorecard['total']]);
            fputcsv($output, ['Average Per Formation', $scorecard['average']]);
            fputcsv($output, ['Formations Reporting', $scorecard['count']]);
            fputcsv($output, ['Top Formation', ($scorecard['max']['label'] ?? '') . ' (' . ($scorecard['max']['value'] ?? 0) . ')']);
            fputcsv($output, []);
        }

        fputcsv($output, $headers);

        foreach ($records as $application) {
            $data = $application->return_data ?? [];
            $row = [
                $application->id,
                $application->category,
                $application->scope_code,
                $application->zonal_code,
                $application->status,
                $data['report_period'] ?? '',
                $data['reporting_officer'] ?? '',
                $data['command_name'] ?? '',
                $application->created_at?->toDateTimeString() ?? '',
                $application->updated_at?->toDateTimeString() ?? '',
            ];

            if ($sumPath) {
                $row[] = $this->sumAtPath($data, $sumPath);
            }

            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv ?: '';
    }

    /**
     * Apply SQL-level filters for columns that exist on the applications table.
     */
    private function applyColumnFilters(Builder $query, array $filters): void
    {
        if ($category = $filters['category'] ?? null) {
            $query->where('category', $category);
        }

        if ($scopeCode = $filters['scope_code'] ?? null) {
            $query->where('scope_code', $scopeCode);
        }

        if ($zonalCode = $filters['zonal_code'] ?? null) {
            $query->where('zonal_code', $zonalCode);
        }

        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }
    }

    /**
     * Search reporting_officer, command_name and report_period inside return_data.
     */
    private function applyTextSearch(Builder $query, string $term): void
    {
        $term = trim($term);
        if ($term === '') {
            return;
        }

        $escaped = $this->escapeLike($term);

        $query->where(function (Builder $q) use ($escaped) {
            $q->where('return_data->reporting_officer', 'like', "%{$escaped}%")
                ->orWhere('return_data->command_name', 'like', "%{$escaped}%")
                ->orWhere('return_data->report_period', 'like', "%{$escaped}%")
                ->orWhereHas('user', fn (Builder $inner) => $inner
                    ->where('name', 'like', "%{$escaped}%")
                    ->orWhere('service_number', 'like', "%{$escaped}%"));
        });
    }

    /**
     * Filter by the report_period stored in return_data (Y-m).
     */
    private function applyPeriodRange(Builder $query, string $from, string $to): void
    {
        if ($from !== '') {
            $query->where('return_data->report_period', '>=', $from);
        }

        if ($to !== '') {
            $query->where('return_data->report_period', '<=', $to);
        }
    }

    /**
     * Apply a numeric or string comparison against a dot-notated path inside
     * return_data. Runs in PHP so arbitrary nested paths are supported.
     */
    private function applyDataFieldFilter(Collection $records, string $path, string $operator, mixed $value): Collection
    {
        $path = trim($path);
        $operator = strtolower(trim($operator));

        if ($path === '' || ! in_array($operator, self::ALLOWED_OPERATORS, true)) {
            return $records;
        }

        return $records->filter(function (Application $application) use ($path, $operator, $value) {
            $actual = data_get($application->return_data, $path);

            if ($operator === 'contains') {
                return is_string($actual) && str_contains(strtolower($actual), strtolower((string) $value));
            }

            $actualNumeric = is_numeric($actual) ? (float) $actual : null;
            $valueNumeric = is_numeric($value) ? (float) $value : null;

            if ($actualNumeric === null || $valueNumeric === null) {
                return false;
            }

            return match ($operator) {
                '>' => $actualNumeric > $valueNumeric,
                '<' => $actualNumeric < $valueNumeric,
                '>=' => $actualNumeric >= $valueNumeric,
                '<=' => $actualNumeric <= $valueNumeric,
                '!=' => $actualNumeric != $valueNumeric,
                default => $actualNumeric == $valueNumeric,
            };
        });
    }

    /**
     * Sort a collection of records by a column or nested return_data path.
     */
    private function applySorting(Collection $records, string $sortBy, string $direction): Collection
    {
        if (! in_array($sortBy, self::ALLOWED_RECORD_SORTS, true)) {
            return $records;
        }

        $multiplier = strtolower($direction) === 'asc' ? 1 : -1;

        return $records->sort(function ($a, $b) use ($sortBy, $multiplier) {
            $valueA = $this->sortValue($a, $sortBy);
            $valueB = $this->sortValue($b, $sortBy);

            if (is_numeric($valueA) && is_numeric($valueB)) {
                return ($valueA <=> $valueB) * $multiplier;
            }

            return strnatcasecmp((string) $valueA, (string) $valueB) * $multiplier;
        })->values();
    }

    /**
     * Resolve a sortable value from an application model.
     */
    private function sortValue(Application $application, string $sortBy): mixed
    {
        return match ($sortBy) {
            'id' => $application->id,
            'category' => $application->category,
            'scope_code' => $application->scope_code,
            'zonal_code' => $application->zonal_code,
            'status' => $application->status,
            'created_at' => $application->created_at?->timestamp ?? 0,
            'report_period' => data_get($application->return_data, 'report_period', ''),
            'reporting_officer' => data_get($application->return_data, 'reporting_officer', ''),
            'command_name' => data_get($application->return_data, 'command_name', ''),
            default => '',
        };
    }

    /**
     * Sort an aggregation collection by column and direction.
     */
    private function sortAggregation(Collection $aggregated, string $sortBy, string $direction): Collection
    {
        if (! in_array($sortBy, self::ALLOWED_AGGREGATION_SORTS, true)) {
            $sortBy = 'total';
        }

        $descending = strtolower($direction) !== 'asc';

        if ($sortBy === 'label') {
            return $aggregated->sortBy('label', SORT_NATURAL, $descending);
        }

        return $aggregated->sortBy($sortBy, SORT_REGULAR, $descending);
    }

    /**
     * Normalise and whitelist user-supplied filters.
     */
    private function normaliseFilters(array $filters): array
    {
        $category = strtolower(trim((string) ($filters['category'] ?? '')));
        $status = strtolower(trim((string) ($filters['status'] ?? '')));
        $sortBy = strtolower(trim((string) ($filters['sort_by'] ?? '')));
        $sortDirection = strtolower(trim((string) ($filters['sort_direction'] ?? '')));

        return [
            'category' => in_array($category, self::ALLOWED_CATEGORIES, true) ? $category : '',
            'scope_code' => strtolower(trim((string) ($filters['scope_code'] ?? ''))),
            'zonal_code' => strtolower(trim((string) ($filters['zonal_code'] ?? ''))),
            'status' => in_array($status, self::ALLOWED_STATUSES, true) ? $status : '',
            'period_from' => $this->normalisePeriod($filters['period_from'] ?? ''),
            'period_to' => $this->normalisePeriod($filters['period_to'] ?? ''),
            'search' => trim((string) ($filters['search'] ?? '')),
            'field_path' => trim((string) ($filters['field_path'] ?? '')),
            'field_operator' => in_array(strtolower(trim((string) ($filters['field_operator'] ?? ''))), self::ALLOWED_OPERATORS, true)
                ? strtolower(trim((string) ($filters['field_operator'] ?? '')))
                : '',
            'field_value' => $filters['field_value'] ?? '',
            'group_by' => in_array($filters['group_by'] ?? '', ['formation', 'zone', 'state', 'directorate', 'category', 'status'], true)
                ? $filters['group_by']
                : 'formation',
            'sum_path' => trim((string) ($filters['sum_path'] ?? '')),
            'sort_by' => in_array($sortBy, self::ALLOWED_RECORD_SORTS, true) ? $sortBy : 'created_at',
            'sort_direction' => in_array($sortDirection, ['asc', 'desc'], true) ? $sortDirection : 'desc',
            'agg_sort_by' => in_array(strtolower(trim((string) ($filters['agg_sort_by'] ?? ''))), self::ALLOWED_AGGREGATION_SORTS, true)
                ? strtolower(trim((string) ($filters['agg_sort_by'] ?? '')))
                : 'total',
            'agg_sort_direction' => in_array(strtolower(trim((string) ($filters['agg_sort_direction'] ?? ''))), ['asc', 'desc'], true)
                ? strtolower(trim((string) ($filters['agg_sort_direction'] ?? '')))
                : 'desc',
            'dictionary_key' => trim((string) ($filters['dictionary_key'] ?? '')),
        ];
    }

    private function normalisePeriod(mixed $value): string
    {
        $value = trim((string) $value);

        return preg_match('/^\d{4}-\d{2}$/', $value) ? $value : '';
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    private function numericValueAt(array $data, string $path): float
    {
        $value = data_get($data, $path);

        return is_numeric($value) ? (float) $value : 0;
    }

    /**
     * Sum a numeric value at a path, supporting `*` wildcards for arrays.
     */
    private function sumAtPath(array $data, string $path): float
    {
        if (! str_contains($path, '*')) {
            $value = data_get($data, $path);

            return is_numeric($value) ? (float) $value : 0;
        }

        return $this->sumWildcardPath($data, explode('.', $path));
    }

    /**
     * Recursively sum numeric values matching a wildcard path.
     */
    private function sumWildcardPath(mixed $node, array $segments): float
    {
        if ($node === null) {
            return 0;
        }

        $segment = array_shift($segments);

        if ($segment === null) {
            return is_numeric($node) ? (float) $node : 0;
        }

        if ($segment === '*') {
            if (! is_array($node)) {
                return 0;
            }

            $total = 0;
            foreach ($node as $child) {
                $total += $this->sumWildcardPath($child, $segments);
            }

            return $total;
        }

        if (is_array($node) && array_key_exists($segment, $node)) {
            return $this->sumWildcardPath($node[$segment], $segments);
        }

        return 0;
    }

    private function emptyScorecard(string $path, ?string $label): array
    {
        return [
            'path' => $path,
            'label' => $label ?? $path,
            'total' => 0,
            'average' => 0,
            'count' => 0,
            'max' => null,
            'min' => null,
            'top_formations' => collect(),
        ];
    }

    private function formationLabel(Application $application): string
    {
        if ($application->category === SubmissionWorkflow::CATEGORY_DIRECTORATE) {
            return $this->directorateLabel($application->scope_code);
        }

        if ($application->category === SubmissionWorkflow::CATEGORY_CGIS) {
            return strtoupper((string) $application->scope_code) . ' Unit';
        }

        if ($application->zonal_code) {
            return 'Zone ' . strtoupper((string) $application->zonal_code);
        }

        return ucfirst((string) $application->scope_code);
    }

    private function directorateLabel(?string $scopeCode): string
    {
        $labels = [
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

        return $labels[$scopeCode] ?? ucfirst((string) $scopeCode);
    }
}
