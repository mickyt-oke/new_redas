<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $directorate['name'] ?? 'Directorate' }} Return — {{ $application->return_data['report_period'] ?? '' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background:#f3f4f6; color:#111827; font-family:'Inter',system-ui,sans-serif; margin:0; }
        .report-page { max-width:900px; margin:24px auto; background:#fff; padding:32px 36px; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,.08); }
        .report-header { border-bottom:3px solid #006838; padding-bottom:16px; margin-bottom:20px; }
        .report-header h1 { font-size:1.3rem; margin:0 0 4px; color:#006838; }
        .report-header p { margin:2px 0; font-size:.85rem; color:#4b5563; }
        .report-meta { display:flex; flex-wrap:wrap; gap:16px 32px; margin-top:10px; font-size:.85rem; }
        .report-meta strong { color:#111827; }
        .status-badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
        .status-approved { background:#ecfdf5; color:#166534; border:1px solid #86efac; }
        .status-pending { background:#fef9c3; color:#854d0e; border:1px solid #fde047; }
        .status-returned { background:#fef2f2; color:#991b1b; border:1px solid #fca5a5; }
        .section-title { font-size:1rem; font-weight:700; color:#006838; margin:22px 0 8px; border-bottom:1px solid #e5e7eb; padding-bottom:4px; }
        .sub-section-title { font-size:.88rem; font-weight:700; color:#374151; margin:14px 0 6px; }
        table.report-table { width:100%; border-collapse:collapse; font-size:.82rem; margin-bottom:8px; }
        table.report-table th, table.report-table td { border:1px solid #d1d5db; padding:6px 10px; text-align:left; vertical-align:top; }
        table.report-table th { background:#f0fdf4; font-weight:700; color:#14532d; }
        table.report-table td.kv-key { width:38%; font-weight:600; background:#f9fafb; }
        .report-actions { display:flex; gap:10px; justify-content:flex-end; max-width:900px; margin:16px auto; }
        .report-actions a, .report-actions button {
            display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:8px;
            font-size:.85rem; font-weight:600; cursor:pointer; text-decoration:none; border:1px solid #d1d5db;
            background:#fff; color:#374151;
        }
        .report-actions button.print-btn { background:#006838; border-color:#006838; color:#fff; }
        @media print {
            body { background:#fff; }
            .report-page { margin:0; max-width:none; box-shadow:none; border-radius:0; padding:0; }
            .report-actions { display:none !important; }
        }
    </style>
</head>
<body>
@php
    $data = is_array($application->return_data) ? $application->return_data : [];

    $humanize = function ($key) {
        return ucwords(str_replace(['_', '-'], ' ', (string) $key));
    };

    $isAssoc = function (array $arr) {
        return $arr !== [] && array_keys($arr) !== range(0, count($arr) - 1);
    };

    $formatValue = function ($value) {
        if ($value === null || $value === '') {
            return '—';
        }
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }
        return (string) $value;
    };

    $skipKeys = ['directorate_slug', 'data_consent'];

    // Recursive renderer: assoc arrays -> key/value table (nested arrays become
    // sub-sections); indexed arrays of arrays -> table with columns from keys;
    // indexed arrays of scalars -> comma-separated list.
    $renderData = function ($data, int $depth = 0) use (&$renderData, $humanize, $isAssoc, $formatValue, $skipKeys) {
        if (! is_array($data)) {
            return '<span>' . e($formatValue($data)) . '</span>';
        }

        if ($data === []) {
            return '<span>—</span>';
        }

        $out = '';

        if ($isAssoc($data)) {
            $scalars = [];
            $nested = [];
            foreach ($data as $key => $value) {
                if (in_array($key, $skipKeys, true)) {
                    continue;
                }
                if (is_array($value)) {
                    $nested[$key] = $value;
                } else {
                    $scalars[$key] = $value;
                }
            }

            if ($scalars !== []) {
                $out .= '<table class="report-table"><tbody>';
                foreach ($scalars as $key => $value) {
                    $out .= '<tr><td class="kv-key">' . e($humanize($key)) . '</td><td>'
                        . e($formatValue($value)) . '</td></tr>';
                }
                $out .= '</tbody></table>';
            }

            foreach ($nested as $key => $value) {
                $out .= '<div class="' . ($depth === 0 ? 'section-title' : 'sub-section-title') . '">'
                    . e($humanize($key)) . '</div>';
                $out .= $renderData($value, $depth + 1);
            }

            return $out === '' ? '<span>—</span>' : $out;
        }

        // Indexed array: table when elements are arrays, comma list otherwise.
        $allArrays = count(array_filter($data, 'is_array')) === count($data);

        if (! $allArrays) {
            return '<span>' . e(implode(', ', array_map($formatValue, $data))) . '</span>';
        }

        $columns = [];
        foreach ($data as $row) {
            foreach (array_keys($row) as $col) {
                if (! in_array($col, $columns, true)) {
                    $columns[] = $col;
                }
            }
        }

        $out .= '<table class="report-table"><thead><tr>';
        foreach ($columns as $col) {
            $out .= '<th>' . e($humanize($col)) . '</th>';
        }
        $out .= '</tr></thead><tbody>';
        foreach ($data as $row) {
            $out .= '<tr>';
            foreach ($columns as $col) {
                $cell = $row[$col] ?? null;
                $out .= '<td>' . (is_array($cell) ? $renderData($cell, $depth + 1) : e($formatValue($cell))) . '</td>';
            }
            $out .= '</tr>';
        }
        $out .= '</tbody></table>';

        return $out;
    };

    $status = strtolower((string) $application->status);
    $statusClass = match (true) {
        $status === 'approved' => 'status-approved',
        in_array($status, ['rejected', 'queried', 'returned'], true) => 'status-returned',
        default => 'status-pending',
    };
@endphp

<div class="report-actions">
    <a href="{{ route('user.directorates.dashboard') }}">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
    <button type="button" class="print-btn" onclick="window.print()">
        <i class="fas fa-print"></i> Print
    </button>
</div>

<div class="report-page">
    <div class="report-header">
        <h1>Nigeria Immigration Service</h1>
        <p>{{ $directorate['name'] ?? 'Directorate' }} — Monthly Return</p>
        <div class="report-meta">
            <span><strong>Report Period:</strong> {{ $data['report_period'] ?? '—' }}</span>
            <span><strong>Reporting Officer:</strong> {{ $data['reporting_officer'] ?? '—' }}</span>
            <span><strong>Submitted:</strong> {{ $application->created_at?->format('d M Y, H:i') ?? '—' }}</span>
            <span><strong>Status:</strong> <span class="status-badge {{ $statusClass }}">{{ ucfirst($application->status ?? 'pending') }}</span></span>
        </div>
    </div>

    {!! $renderData($data) !!}
</div>

@if($isPrint)
<script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>
@endif
</body>
</html>
