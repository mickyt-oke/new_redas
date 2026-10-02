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
    $skipKeys = ['command_name', 'period', 'return_type', 'report_period', 'reporting_officer', 'data_consent', 'supporting_documents', 'attachments'];

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
@endphp

@php
$sections = \App\Services\PreviewRenderer::buildSections($data, [
    'skipKeys' => $skipKeys,
    'directorateNames' => $directorateNames,
]);
@endphp

<div class="preview-note">
    <i class="fas fa-eye" style="margin-right:6px;"></i>
    This is a read-only preview. Attachments are included only after submission from the form page. Close this tab to return to the form.
</div>

<div class="report-page" data-preview-paginator>
    @include('partials.preview-pagination')

    <div class="preview-paginator">
        <div class="preview-paginator-top">
            <div class="preview-paginator-title preview-counter">Section 1 of {{ count($sections) }}</div>
            <div class="preview-paginator-controls">
                <button type="button" class="preview-prev-btn" disabled><i class="fas fa-arrow-left"></i> Previous</button>
                <button type="button" class="preview-next-btn">Next <i class="fas fa-arrow-right"></i></button>
                <button type="button" class="preview-all-btn">Show all</button>
            </div>
        </div>
        <div class="preview-section-tabs"></div>
    </div>

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
    <section class="report-section preview-section {{ $loop->first ? 'active' : '' }}" data-label="{{ $section['label'] }}">
        <h2 class="section-title"><span class="section-num">{{ $index + 1 }}</span> {{ $section['label'] }}</h2>
        {!! $section['html'] ?: '<p class="preview-section-empty">No data entered for this section.</p>' !!}
    </section>
    @empty
    <section class="report-section preview-section active" data-label="No Data">
        <p style="color:#6b7280;font-size:.88rem;margin:0 0 8px;">No data entered yet. Return to the form to fill in the sections.</p>
    </section>
    @endforelse
</div>

</body>
</html>
