<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $directorateName ?? 'Directorate' }} Return — {{ $application->return_data['report_period'] ?? '' }}</title>
    <style>
        body { background:#fff; color:#111827; font-family:'DejaVu Sans', sans-serif; font-size:11px; margin:0; }
        .report-header { border-bottom:3px solid #006838; padding-bottom:12px; margin-bottom:16px; }
        .report-header-table { width:100%; border-collapse:collapse; }
        .report-header-table td { vertical-align:middle; border:none; padding:0; }
        .report-header img.logo { width:64px; height:64px; }
        .report-header h1 { font-size:16px; margin:0 0 3px; color:#006838; }
        .report-header p { margin:1px 0; font-size:10px; color:#4b5563; }
        .report-meta { margin-top:8px; font-size:10px; }
        .report-meta span { display:inline-block; margin-right:18px; }
        .status-badge { display:inline-block; padding:2px 8px; border-radius:10px; font-size:9px; font-weight:bold; text-transform:uppercase; }
        .status-approved { background:#ecfdf5; color:#166534; border:1px solid #86efac; }
        .status-pending { background:#fef9c3; color:#854d0e; border:1px solid #fde047; }
        .status-returned { background:#fef2f2; color:#991b1b; border:1px solid #fca5a5; }
        .section-title { font-size:12px; font-weight:bold; color:#006838; margin:16px 0 6px; border-bottom:1px solid #e5e7eb; padding-bottom:3px; }
        .sub-section-title { font-size:11px; font-weight:bold; color:#374151; margin:10px 0 4px; }
        table.report-table { width:100%; border-collapse:collapse; font-size:10px; margin-bottom:6px; }
        table.report-table th, table.report-table td { border:1px solid #d1d5db; padding:4px 8px; text-align:left; vertical-align:top; }
        table.report-table th { background:#f0fdf4; font-weight:bold; color:#14532d; }
        table.report-table td.kv-key { width:38%; font-weight:bold; background:#f9fafb; }
        .report-footer { margin-top:24px; padding-top:8px; border-top:1px solid #e5e7eb; font-size:9px; color:#9ca3af; text-align:center; }
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

    // Exclude the report header metadata and uploaded document arrays from the
    // generic body renderer so the PDF mirrors the structured on-screen view.
    $skipKeys = ['directorate_slug', 'data_consent', 'report_period', 'reporting_officer', 'supporting_documents', 'attachments'];

    // Same recursive renderer as the on-screen directorate return preview.
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

<div class="report-header">
    <table class="report-header-table">
        <tr>
            <td style="width:76px;">
                @if(!empty($logoDataUri))
                    <img class="logo" src="{{ $logoDataUri }}" alt="NIS Logo">
                @endif
            </td>
            <td>
                <h1>Nigeria Immigration Service</h1>
                <p>{{ $directorateName ?? ucwords(str_replace(['_', '-'], ' ', (string) ($data['directorate_slug'] ?? $application->scope_code ?? 'State'))) }} — Monthly Return</p>
                <div class="report-meta">
                    <span><strong>Report Period:</strong> {{ $data['report_period'] ?? '—' }}</span>
                    <span><strong>Reporting Officer:</strong> {{ $data['reporting_officer'] ?? optional($application->user)->name ?? '—' }}</span>
                    <span><strong>Submitted:</strong> {{ optional($application->created_at)->format('d M Y, H:i') ?? '—' }}</span>
                    <span><strong>Status:</strong> <span class="status-badge {{ $statusClass }}">{{ ucfirst($application->status ?? 'pending') }}</span></span>
                </div>
            </td>
        </tr>
    </table>
</div>

{!! $renderData($data) !!}

<div class="report-footer">
    Generated from NIS-REDAS on {{ now()->format('d M Y, H:i') }} — Submission #{{ $application->id }}
</div>
</body>
</html>
