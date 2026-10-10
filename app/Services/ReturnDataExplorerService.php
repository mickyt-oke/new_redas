<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Search, aggregate and export return_data records for the executive
 * data-explorer. All filters are read-only; the service never mutates data.
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

    /**
     * Search applications and return paginated + raw collections plus the
     * normalised filters that were applied.
     *
     * @return array{paginator: LengthAwarePaginator, records: Collection, filters: array}
     */
    public function search(array $filters, int $perPage = 20): array
    {
        $filters = $this->normaliseFilters($filters);

        $query = Application::query()
            ->select(['id', 'user_id', 'category', 'scope_code', 'zonal_code', 'status', 'return_data', 'created_at', 'updated_at'])
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
    public function fetchForExport(array $filters, int $limit = self::CSV_LIMIT): Collection
    {
        $filters = $this->normaliseFilters($filters);

        $query = Application::query()
            ->select(['id', 'user_id', 'category', 'scope_code', 'zonal_code', 'status', 'return_data', 'created_at', 'updated_at'])
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
    public function aggregate(Collection $records, string $groupBy, ?string $sumPath = null): Collection
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

        return $grouped->map(function (Collection $items, string $key) use ($sumPath) {
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
        })->sortByDesc('total')->values();
    }

    /**
     * Convert a collection of records to a CSV string.
     */
    public function toCsv(Collection $records): string
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

        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);

        foreach ($records as $application) {
            $data = $application->return_data ?? [];
            fputcsv($output, [
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
            ]);
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
     * Normalise and whitelist user-supplied filters.
     */
    private function normaliseFilters(array $filters): array
    {
        $category = strtolower(trim((string) ($filters['category'] ?? '')));
        $status = strtolower(trim((string) ($filters['status'] ?? '')));

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
