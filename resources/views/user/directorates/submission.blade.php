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
        .report-header-flex { display:flex; align-items:center; gap:16px; }
        .report-header img.logo { width:72px; height:72px; flex-shrink:0; }
        .report-header h1 { font-size:1.3rem; margin:0 0 4px; color:#006838; }
        .report-header p { margin:2px 0; font-size:.85rem; color:#4b5563; }
        .report-meta { display:flex; flex-wrap:wrap; gap:16px 32px; margin-top:10px; font-size:.85rem; }
        .report-meta strong { color:#111827; }
        .status-badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
        .status-approved { background:#ecfdf5; color:#166534; border:1px solid #86efac; }
        .status-pending { background:#fef9c3; color:#854d0e; border:1px solid #fde047; }
        .status-returned { background:#fef2f2; color:#991b1b; border:1px solid #fca5a5; }

        /* Each form tab is rendered as its own separated section. */
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
        .doc-card { display:flex; align-items:center; gap:10px; padding:10px 12px; border:1px solid #e5e7eb; border-radius:10px; cursor:pointer; background:#fff; transition:box-shadow .15s; }
        .doc-card:hover { box-shadow:0 2px 8px rgba(0,0,0,.12); border-color:#86efac; }
        .doc-card .doc-icon { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:.95rem; flex-shrink:0; }
        .doc-card .doc-name { font-size:.8rem; font-weight:600; color:#1f2937; word-break:break-all; }
        .doc-card .doc-hint { font-size:.7rem; color:#6b7280; }
        #docViewerModal { display:none; position:fixed; inset:0; z-index:1000; background:rgba(15,23,42,.65); align-items:center; justify-content:center; padding:24px; }
        #docViewerModal.open { display:flex; }
        #docViewerBox { background:#fff; border-radius:14px; width:100%; max-width:960px; max-height:90vh; display:flex; flex-direction:column; overflow:hidden; }
        #docViewerBody { flex:1; overflow:auto; display:flex; align-items:center; justify-content:center; background:#f8fafc; min-height:300px; }
        #docViewerBody img { max-width:100%; max-height:75vh; object-fit:contain; }
        #docViewerBody iframe { width:100%; height:75vh; border:none; }
        .doc-viewer-btn { display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:8px; font-size:.8rem; font-weight:600; cursor:pointer; text-decoration:none; border:1px solid #d1d5db; background:#fff; color:#374151; }

        .report-actions { display:flex; gap:10px; justify-content:flex-end; max-width:900px; margin:16px auto; }
        .report-actions a, .report-actions button {
            display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:8px;
            font-size:.85rem; font-weight:600; cursor:pointer; text-decoration:none; border:1px solid #d1d5db;
            background:#fff; color:#374151;
        }
        .report-actions button.print-btn { background:#006838; border-color:#006838; color:#fff; }

        @page { size:A4; margin:14mm 12mm; }
        @media print {
            body { background:#fff; }
            .report-page { margin:0; max-width:none; box-shadow:none; border-radius:0; padding:0; }
            .report-actions { display:none !important; }
            .report-section { border:none; border-radius:0; padding:0; margin:0 0 16px; }
            .section-title, .sub-section-title { break-after:avoid; }
            table.report-table thead { display:table-header-group; }
            table.report-table tr { break-inside:avoid; }
        }
    </style>
</head>

<body>
@php
    $data = is_array($application->return_data) ? $application->return_data : [];

    // Report metadata lives in the page header; uploaded file paths are
    // rendered separately at the end of the report.
    $skipKeys = ['directorate_slug', 'data_consent', 'report_period', 'reporting_officer', 'supporting_documents', 'attachments'];

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

    // Key/value table for a flat associative set of fields.
    $kvTable = function (array $pairs) use ($humanize, $formatValue) {
        $out = '<table class="report-table kv-table"><tbody>';
        foreach ($pairs as $key => $value) {
            $out .= '<tr><td class="kv-key">' . e($humanize($key)) . '</td><td>'
                . e($formatValue($value)) . '</td></tr>';
        }
        return $out . '</tbody></table>';
    };

    // Matrix table for rows of the same shape (e.g. rank => [male, female]):
    // first column is the row label, remaining columns come from the inner keys.
    $matrixTable = function (array $rows) use ($humanize, $formatValue) {
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
            return '<span>—</span>';
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
    $renderData = function ($value) use (&$renderData, $isAssoc, $isFlat, $formatValue, $kvTable, $matrixTable, $rowsTable, $humanize) {
        if (! is_array($value)) {
            return '<span>' . e($formatValue($value)) . '</span>';
        }
        if ($value === []) {
            return '<span>—</span>';
        }

        if (! $isAssoc($value)) {
            $allArrays = count(array_filter($value, 'is_array')) === count($value);
            if (! $allArrays) {
                return '<span>' . e(implode(', ', array_map($formatValue, $value))) . '</span>';
            }
            return $rowsTable($value);
        }

        $scalars = [];
        $matrixRows = [];
        $nested = [];
        foreach ($value as $key => $item) {
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

        return $out === '' ? '<span>—</span>' : $out;
    };

    // Group the submitted data into sections that mirror the form's tabs.
    // A top-level key matching the directorate slug (e.g. "hrm") is a wrapper,
    // so its children become the sections; any remaining top-level array is a
    // section of its own; leftover flat fields are grouped as "Return Details".
    $sections = [];
    $flatFields = [];

    foreach ($data as $key => $value) {
        if (in_array($key, $skipKeys, true)) {
            continue;
        }
        if ($key === $slug && is_array($value)) {
            foreach ($value as $subKey => $subValue) {
                if (is_array($subValue)) {
                    $sections[] = ['label' => $humanize($subKey), 'html' => $renderData($subValue)];
                } else {
                    $flatFields[$subKey] = $subValue;
                }
            }
        } elseif (is_array($value)) {
            $sections[] = ['label' => $humanize($key), 'html' => $renderData($value)];
        } else {
            $flatFields[$key] = $value;
        }
    }

    if ($flatFields !== []) {
        array_unshift($sections, ['label' => 'Return Details', 'html' => $kvTable($flatFields)]);
    }

    $documentGroups = [];
    foreach (['supporting_documents' => ['supporting', 'Supporting Documents'], 'attachments' => ['attachments', 'Attachments']] as $key => [$collection, $label]) {
        $paths = array_values(array_filter((array) ($data[$key] ?? []), 'is_string'));
        if ($paths !== []) {
            $documentGroups[] = [$collection, $label, $paths];
        }
    }

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
    <a href="{{ route('user.submissions.pdf', $application) }}">
        <i class="fas fa-file-pdf"></i> Download PDF
    </a>
    <button type="button" class="print-btn" onclick="window.print()">
        <i class="fas fa-print"></i> Print
    </button>
</div>

<div class="report-page">
    <div class="report-header">
        <div class="report-header-flex">
            <img class="logo" src="{{ asset('nis-logo.png') }}" alt="NIS Logo">
            <div>
                <h1>Nigeria Immigration Service</h1>
                <p>{{ $directorate['name'] ?? 'Directorate' }} — Monthly Return</p>
                <div class="report-meta">
                    <span><strong>Report Period:</strong> {{ $data['report_period'] ?? '—' }}</span>
                    <span><strong>Reporting Officer:</strong> {{ $data['reporting_officer'] ?? '—' }}</span>
                    <span><strong>Submitted:</strong> {{ $application->created_at?->format('d M Y, H:i') ?? '—' }}</span>
                    <span><strong>Status:</strong> <span class="status-badge {{ $statusClass }}">{{ ucfirst($application->status ?? 'pending') }}</span></span>
                </div>
            </div>
        </div>
    </div>

    @foreach($sections as $index => $section)
    <section class="report-section">
        <h2 class="section-title"><span class="section-num">{{ $index + 1 }}</span> {{ $section['label'] }}</h2>
        {!! $section['html'] !!}
    </section>
    @endforeach

    @if($documentGroups !== [])
    <section class="report-section">
        <h2 class="section-title"><span class="section-num">{{ count($sections) + 1 }}</span> Supporting Documents</h2>
        @foreach($documentGroups as [$collection, $label, $paths])
            <div class="sub-section-title">{{ $label }}</div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:10px;margin-bottom:12px;">
                @foreach($paths as $i => $path)
                    @php
                        $name = basename((string) $path);
                        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'], true);
                        [$iconBg, $iconColor, $icon] = match (true) {
                            $isImage => ['#ecfdf5', '#15803d', 'fa-image'],
                            $ext === 'pdf' => ['#fef2f2', '#b91c1c', 'fa-file-pdf'],
                            in_array($ext, ['xls', 'xlsx', 'csv'], true) => ['#eff6ff', '#1d4ed8', 'fa-file-excel'],
                            in_array($ext, ['doc', 'docx'], true) => ['#eff6ff', '#1d4ed8', 'fa-file-word'],
                            default => ['#f8fafc', '#475569', 'fa-file'],
                        };
                        $docUrl = route('user.directorates.submissions.document', [$application, $collection, $i]);
                    @endphp
                    <div class="doc-card"
                         data-doc-url="{{ $docUrl }}"
                         data-doc-name="{{ $name }}"
                         data-doc-ext="{{ $ext }}">
                        <div class="doc-icon" style="background:{{ $iconBg }};color:{{ $iconColor }};"><i class="fas {{ $icon }}"></i></div>
                        <div>
                            <div class="doc-name">{{ $name }}</div>
                            <div class="doc-hint">Click to preview</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </section>
    @endif
</div>

{{-- Document / image preview modal --}}
<div id="docViewerModal" role="dialog" aria-modal="true">
    <div id="docViewerBox">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;padding:12px 16px;border-bottom:1px solid #e5e7eb;">
            <div id="docViewerTitle" style="font-size:.88rem;font-weight:700;color:#1f2937;word-break:break-all;"></div>
            <div style="display:flex;gap:8px;flex-shrink:0;">
                <a id="docViewerDownload" href="#" class="doc-viewer-btn"><i class="fas fa-download"></i> Download</a>
                <button type="button" id="docViewerClose" class="doc-viewer-btn"><i class="fas fa-times"></i> Close</button>
            </div>
        </div>
        <div id="docViewerBody"></div>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('docViewerModal');
    if (!modal) return;
    var body = document.getElementById('docViewerBody');
    var title = document.getElementById('docViewerTitle');
    var downloadLink = document.getElementById('docViewerDownload');
    var imageExts = ['png', 'jpg', 'jpeg', 'gif', 'webp'];

    function closeViewer() {
        modal.classList.remove('open');
        body.innerHTML = '';
    }

    document.querySelectorAll('.doc-card').forEach(function (card) {
        card.addEventListener('click', function () {
            var url = card.dataset.docUrl;
            var name = card.dataset.docName;
            var ext = card.dataset.docExt;

            title.textContent = name;
            downloadLink.href = url + '?download=1';
            body.innerHTML = '';

            if (imageExts.indexOf(ext) !== -1) {
                var img = document.createElement('img');
                img.src = url;
                img.alt = name;
                body.appendChild(img);
            } else if (ext === 'pdf') {
                var frame = document.createElement('iframe');
                frame.src = url;
                body.appendChild(frame);
            } else {
                body.innerHTML = '<div style="padding:40px;text-align:center;color:#4b5563;font-size:.88rem;">' +
                    '<i class="fas fa-file" style="font-size:2rem;color:#9ca3af;display:block;margin-bottom:10px;"></i>' +
                    'This file type cannot be previewed in the browser.<br>Use the Download button to open it.' +
                    '</div>';
            }

            modal.classList.add('open');
        });
    });

    document.getElementById('docViewerClose').addEventListener('click', closeViewer);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeViewer();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeViewer();
    });
})();
</script>

@if($isPrint)
<script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>
@endif
</body>
</html>
