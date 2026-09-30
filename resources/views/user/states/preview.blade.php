<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>State Command Return — Preview</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background:#f3f4f6; color:#111827; font-family:'Inter',system-ui,sans-serif; margin:0; }
        .report-page { max-width:900px; margin:24px auto; background:#fff; padding:32px 36px; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,.08); }
        .report-header { border-bottom:3px solid #006838; padding-bottom:16px; margin-bottom:20px; }
        .report-header-flex { display:flex; align-items:center; gap:16px; }
        .report-header img.logo { width:72px; height:72px; flex-shrink:0; }
        .report-header h1 { font-size:1.3rem; margin:0 0 4px; color:#006838; }
        .report-header p { margin:2px 0; font-size:.85rem; color:#4b5563; }
        .report-meta { display:flex; flex-wrap:wrap; gap:16px 32px; margin-top:10px; font-size:.85rem; }
        .report-meta strong { color:#111827; }

        .report-section { border:1px solid #e5e7eb; border-radius:10px; padding:16px 18px 8px; margin:0 0 18px; background:#fff; }
        .section-title { display:flex; align-items:center; gap:8px; font-size:.95rem; font-weight:700; color:#006838; margin:0 0 10px; border-bottom:2px solid #006838; padding-bottom:6px; }
        .section-num { display:inline-flex; align-items:center; justify-content:center; min-width:22px; height:22px; padding:0 6px; border-radius:6px; background:#006838; color:#fff; font-size:.72rem; font-weight:700; }
        .sub-section-title { font-size:.85rem; font-weight:700; color:#374151; margin:12px 0 6px; }

        table.report-table { width:100%; border-collapse:collapse; font-size:.8rem; margin-bottom:12px; }
        table.report-table th, table.report-table td { border:1px solid #d1d5db; padding:5px 8px; text-align:left; vertical-align:top; }
        table.report-table th { background:#f0fdf4; font-weight:700; color:#14532d; }
        table.report-table td.kv-key { width:38%; font-weight:600; background:#f9fafb; }
        table.report-table.kv-table td:last-child { white-space:pre-wrap; }
        table.report-table td.row-head { font-weight:600; background:#f9fafb; }

        .preview-note { background:#fef9c3; border:1px solid #fde047; color:#854d0e; border-radius:10px; padding:12px 16px; font-size:.84rem; max-width:900px; margin:16px auto; }

        @page { size:A4; margin:14mm 12mm; }
        @media print {
            body { background:#fff; }
            .report-page { margin:0; max-width:none; box-shadow:none; border-radius:0; padding:0; }
            .preview-note { display:none !important; }
            .report-section { border:none; border-radius:0; padding:0; margin:0 0 16px; }
            .section-title, .sub-section-title { break-after:avoid; }
            table.report-table thead { display:table-header-group; }
            table.report-table tr { break-inside:avoid; }
        }
    </style>
</head>

<body>
@php
    $data = is_array($previewData ?? null) ? $previewData : [];

    // Report metadata lives in the page header.
    $skipKeys = ['command_name', 'period', 'return_type', 'reporting_officer', 'report_period'];

    $directorateNames = [];
    foreach (\App\Http\Controllers\Web\DashboardController::directorates() as $slug => $meta) {
        $directorateNames[$slug] = $meta['name'];
    }

    $typeLabels = [
        'monthly'   => 'Monthly Return',
        'quarterly' => 'Quarterly Return',
        'biannual'  => 'Bi-Annual Return',
        'annual'    => 'Annual Return',
        'special'   => 'Special Report',
    ];

    $periodRaw = $data['period'] ?? null;
    $periodLabel = $periodRaw ?: '—';
    if ($periodRaw) {
        try {
            $periodLabel = \Carbon\Carbon::createFromFormat('Y-m', $periodRaw)->format('F Y');
        } catch (\Throwable $e) {
            $periodLabel = $periodRaw;
        }
    }
    $typeLabel = $typeLabels[$data['return_type'] ?? ''] ?? ucfirst((string) ($data['return_type'] ?? '—'));

    $lowercaseWords = ['of', 'in', 'to', 'and', 'the', 'for', 'by', 'on', 'a', 'an', 'if', 'any'];
    $specialWords = [
        'nimcos' => 'NIMCOS', 'mou' => 'MoU', 'mous' => 'MoUs', 'midas' => 'MIDAS',
        'evisa' => 'e-Visa', 'dfu' => 'DFU', 'dofit' => 'DOFIT', 'ecowas' => 'ECOWAS', 'cerpac' => 'CERPAC',
        // Zone/state command codes used as array keys in the forms.
        'lasc' => 'LASC', 'seme' => 'SEME', 'idbc' => 'IDBC', 'lspmc' => 'LSPMC', 'lapc' => 'LAPC',
        'mmia' => 'MMIA', 'ogsc' => 'OGSC', 'kdsc' => 'KDSC', 'knsc' => 'KNSC', 'itsk' => 'ITSK',
        'makia' => 'MAKIA', 'ktsc' => 'KTSC', 'jsbc' => 'JSBC', 'sosc' => 'SOSC', 'icsc' => 'ICSC',
        'illela' => 'ILLELA', 'zmsc' => 'ZMSC', 'jgsc' => 'JGSC', 'basc' => 'BASC', 'ybsc' => 'YBSC',
        'bosc' => 'BOSC', 'adsc' => 'ADSC', 'gmsc' => 'GMSC', 'plsc' => 'PLSC', 'fctc' => 'FCTC',
        'naia' => 'NAIA', 'kwsc' => 'KWSC', 'ngsc' => 'NGSC', 'kbsc' => 'KBSC', 'absc' => 'ABSC',
        'aksc' => 'AKSC', 'crsc' => 'CRSC', 'mfum' => 'MFUM', 'ebsc' => 'EBSC', 'imsc' => 'IMSC',
        'nitsol' => 'NITSOL', 'rvsc' => 'RVSC', 'phia' => 'PHIA', 'nitsa' => 'NITSA', 'rvmc' => 'RVMC',
        'oysc' => 'OYSC', 'ossc' => 'OSSC', 'odsc' => 'ODSC', 'eksc' => 'EKSC', 'ansc' => 'ANSC',
        'bysc' => 'BYSC', 'dtsc' => 'DTSC', 'ensc' => 'ENSC', 'aiia' => 'AIIA', 'edsc' => 'EDSC',
        'bnsc' => 'BNSC', 'kgsc' => 'KGSC', 'trsc' => 'TRSC', 'nasc' => 'NASC',
    ];

    $humanize = function ($key) use ($lowercaseWords, $specialWords) {
        $words = preg_split('/[_\-\s]+/', (string) $key) ?: [];
        $out = [];
        foreach ($words as $w) {
            $l = strtolower($w);
            if (isset($specialWords[$l])) {
                $out[] = $specialWords[$l];
            } elseif (strlen($l) <= 3 && ctype_alpha($l) && ! in_array($l, $lowercaseWords, true)) {
                $out[] = strtoupper($l); // short rank/unit codes: DCG, ACG, APU, CIS...
            } else {
                $out[] = ucfirst($l);
            }
        }
        return implode(' ', $out);
    };

    // Recursively empty: null, '', [], or an array whose children are all empty.
    $isEmptyValue = function ($value) use (&$isEmptyValue) {
        if ($value === null || $value === '') {
            return true;
        }
        if (is_array($value)) {
            foreach ($value as $v) {
                if (! $isEmptyValue($v)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    };

    $isAssoc = function (array $arr) {
        return $arr !== [] && array_keys($arr) !== range(0, count($arr) - 1);
    };

    $isFlat = function (array $arr) {
        foreach ($arr as $v) {
            if (is_array($v)) {
                return false;
            }
        }
        return true;
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

    // Key/value table for a flat associative set of fields (empty values skipped).
    $kvTable = function (array $pairs) use ($humanize, $formatValue, $isEmptyValue) {
        $out = '<table class="report-table kv-table"><tbody>';
        foreach ($pairs as $key => $value) {
            if ($isEmptyValue($value)) {
                continue;
            }
            $out .= '<tr><td class="kv-key">' . e($humanize($key)) . '</td><td>'
                . e($formatValue($value)) . '</td></tr>';
        }
        return $out . '</tbody></table>';
    };

    // Matrix table for rows of the same shape (e.g. rank => [male, female]):
    // first column is the row label, remaining columns come from the inner keys.
    $matrixTable = function (array $rows) use ($humanize, $formatValue, $isEmptyValue) {
        $columns = [];
        foreach ($rows as $row) {
            foreach (array_keys($row) as $col) {
                if (! in_array($col, $columns, true)) {
                    $columns[] = $col;
                }
            }
        }

        $out = '<table class="report-table"><thead><tr><th style="width:32%;">Item</th>';
        foreach ($columns as $col) {
            $out .= '<th>' . e($humanize($col)) . '</th>';
        }
        $out .= '</tr></thead><tbody>';
        foreach ($rows as $label => $row) {
            if ($isEmptyValue($row)) {
                continue;
            }
            $out .= '<tr><td class="row-head">' . e($humanize($label)) . '</td>';
            foreach ($columns as $col) {
                $out .= '<td>' . e($formatValue($row[$col] ?? null)) . '</td>';
            }
            $out .= '</tr>';
        }
        return $out . '</tbody></table>';
    };

    // Numbered table for repeatable rows (e.g. training entries).
    $rowsTable = function (array $rows) use ($humanize, $formatValue, &$renderData) {
        // Drop rows the user left completely empty.
        $rows = array_values(array_filter($rows, function ($row) {
            if (! is_array($row)) {
                return $row !== null && $row !== '';
            }
            foreach ($row as $v) {
                if ($v !== null && $v !== '') {
                    return true;
                }
            }
            return false;
        }));

        if ($rows === []) {
            return '';
        }

        $columns = [];
        foreach ($rows as $row) {
            foreach (array_keys($row) as $col) {
                if (! in_array($col, $columns, true)) {
                    $columns[] = $col;
                }
            }
        }

        $out = '<table class="report-table"><thead><tr><th style="width:44px;">S/N</th>';
        foreach ($columns as $col) {
            $out .= '<th>' . e($humanize($col)) . '</th>';
        }
        $out .= '</tr></thead><tbody>';
        foreach ($rows as $i => $row) {
            $out .= '<tr><td>' . ($i + 1) . '</td>';
            foreach ($columns as $col) {
                $cell = is_array($row) ? ($row[$col] ?? null) : null;
                $out .= '<td>' . (is_array($cell) ? $renderData($cell) : e($formatValue($cell))) . '</td>';
            }
            $out .= '</tr>';
        }
        return $out . '</tbody></table>';
    };

    // Recursive block renderer used inside a section: assoc arrays split into a
    // key/value table (scalars) plus a matrix table (uniform nested rows); deeper
    // nesting becomes sub-sections; indexed arrays become numbered tables.
    // Empty values and empty arrays are skipped throughout.
    $renderData = function ($value) use (&$renderData, $isAssoc, $isFlat, $isEmptyValue, $formatValue, $kvTable, $matrixTable, $rowsTable, $humanize) {
        if (! is_array($value)) {
            return $isEmptyValue($value) ? '' : '<span>' . e($formatValue($value)) . '</span>';
        }
        if ($value === [] || $isEmptyValue($value)) {
            return '';
        }

        if (! $isAssoc($value)) {
            $allArrays = count(array_filter($value, 'is_array')) === count($value);
            if (! $allArrays) {
                $filled = array_values(array_filter($value, fn ($v) => ! $isEmptyValue($v)));
                return $filled === [] ? '' : '<span>' . e(implode(', ', array_map($formatValue, $filled))) . '</span>';
            }
            return $rowsTable($value);
        }

        $scalars = [];
        $matrixRows = [];
        $nested = [];
        foreach ($value as $key => $item) {
            if ($isEmptyValue($item)) {
                continue;
            }
            if (! is_array($item)) {
                $scalars[$key] = $item;
            } elseif ($isFlat($item)) {
                $matrixRows[$key] = $item;
            } else {
                $nested[$key] = $item;
            }
        }

        $out = '';
        if ($scalars !== []) {
            $out .= $kvTable($scalars);
        }
        if ($matrixRows !== []) {
            $out .= $matrixTable($matrixRows);
        }
        foreach ($nested as $key => $item) {
            // Single-letter keys under a section are zone identifiers (A–H).
            $label = (strlen((string) $key) === 1 && ctype_alpha((string) $key))
                ? 'Zone ' . strtoupper((string) $key)
                : $humanize($key);
            $out .= '<div class="sub-section-title">' . e($label) . '</div>';
            $out .= $renderData($item);
        }

        return $out;
    };

    // Group the submitted data into sections. A top-level key matching a
    // directorate slug (e.g. "hrm") is a wrapper for that directorate's fields;
    // any other top-level array is a section of its own; leftover flat fields
    // are grouped as "Return Details".
    $sections = [];
    $flatFields = [];

    foreach ($data as $key => $value) {
        if (in_array($key, $skipKeys, true) || $isEmptyValue($value)) {
            continue;
        }
        if (is_array($value)) {
            $sections[] = ['label' => $directorateNames[$key] ?? $humanize($key), 'html' => $renderData($value)];
        } else {
            $flatFields[$key] = $value;
        }
    }

    if ($flatFields !== []) {
        array_unshift($sections, ['label' => 'Return Details', 'html' => $kvTable($flatFields)]);
    }
@endphp

<div class="preview-note">
    <i class="fas fa-eye" style="margin-right:6px;"></i>
    Attachments are included only when you submit from the form page. This is a preview — close this tab to return to the form.
</div>

<div class="report-page">
    <div class="report-header">
        <div class="report-header-flex">
            <img class="logo" src="{{ asset('nis-logo.png') }}" alt="NIS Logo">
            <div>
                <h1>Nigeria Immigration Service</h1>
                <p>STATE COMMAND RETURN — PREVIEW</p>
                <div class="report-meta">
                    <span><strong>Command:</strong> {{ $commandName ?? '—' }}</span>
                    <span><strong>Report Period:</strong> {{ $periodLabel }}</span>
                    <span><strong>Return Type:</strong> {{ $typeLabel }}</span>
                    <span><strong>Reporting Officer:</strong> {{ $data['reporting_officer'] ?? $user?->name ?? '—' }}</span>
                    <span><strong>Generated:</strong> {{ now()->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    @forelse($sections as $index => $section)
    <section class="report-section">
        <h2 class="section-title"><span class="section-num">{{ $index + 1 }}</span> {{ $section['label'] }}</h2>
        {!! $section['html'] !!}
    </section>
    @empty
    <section class="report-section">
        <p style="color:#6b7280;font-size:.88rem;margin:0 0 8px;">No data entered yet. Return to the form to fill in the sections.</p>
    </section>
    @endforelse
</div>

</body>
</html>
