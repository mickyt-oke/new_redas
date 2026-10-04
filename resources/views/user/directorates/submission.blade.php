<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $directorate['name'] ?? $unit['name'] ?? 'Directorate' }} Return — {{ $application->return_data['report_period'] ?? '' }}</title>
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
    $skipKeys = ['directorate_slug', 'cgis_unit_slug', 'data_consent', 'report_period', 'reporting_officer', 'command_name', 'period', 'return_type', 'supporting_documents', 'attachments'];

    // State returns carry multiple directorate keys and command/period metadata.
    $isStateReturn = isset($data['command_name']);
    $directorateSlugForGrouping = $isStateReturn ? null : ($slug ?? null);

    $directorateNames = [];
    foreach (\App\Http\Controllers\Web\DashboardController::directorates() as $s => $meta) {
        $directorateNames[$s] = $meta['name'];
    }

    $status = strtolower((string) $application->status);
    $statusClass = match (true) {
        $status === 'approved' => 'status-approved',
        in_array($status, ['rejected', 'queried', 'returned'], true) => 'status-returned',
        default => 'status-pending',
    };

    $viewerCategory = auth()->user()?->user_category;
    $dashboardRoute = match ($viewerCategory) {
        'state_user' => 'user.submissions',
        'cgis_unit_user' => 'user.cgis-units.dashboard',
        default => 'user.directorates.dashboard',
    };
    $documentRoute = match ($viewerCategory) {
        'state_user' => 'user.returns.document',
        'cgis_unit_user' => 'user.cgis-units.submissions.document',
        default => 'user.directorates.submissions.document',
    };
@endphp

@php
$sections = \App\Services\PreviewRenderer::buildSections($data, [
    'skipKeys' => $skipKeys,
    'directorateSlug' => $directorateSlugForGrouping,
    'directorateNames' => $directorateNames,
]);
@endphp

<div class="report-actions">
    <a href="{{ route($dashboardRoute) }}">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
    <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('user.submissions.pdf', ['applicationHash' => \App\Services\HashidService::encode($application->id)]) }}">
        <i class="fas fa-file-pdf"></i> Download PDF
    </a>
    <button type="button" class="print-btn" onclick="window.print()">
        <i class="fas fa-print"></i> Print
    </button>
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
                <p>{{ $directorate['name'] ?? $unit['name'] ?? 'Directorate' }} — Monthly Return</p>
                <div class="report-meta">
                    <span><strong>Report Period:</strong> {{ $data['report_period'] ?? $data['period'] ?? '—' }}</span>
                    <span><strong>Reporting Officer:</strong> {{ $data['reporting_officer'] ?? '—' }}</span>
                    <span><strong>Submitted:</strong> {{ $application->created_at?->format('d M Y, H:i') ?? '—' }}</span>
                    <span><strong>Status:</strong> <span class="status-badge {{ $statusClass }}">{{ ucfirst($application->status ?? 'pending') }}</span></span>
                </div>
            </div>
        </div>
    </div>

    @foreach($sections as $index => $section)
    <section class="report-section preview-section {{ $loop->first ? 'active' : '' }}" data-label="{{ $section['label'] }}">
        <h2 class="section-title"><span class="section-num">{{ $index + 1 }}</span> {{ $section['label'] }}</h2>
        {!! $section['html'] ?: '<p class="preview-section-empty">No data entered for this section.</p>' !!}
    </section>
    @endforeach

    @include('partials.document-viewer', ['docRoute' => $documentRoute])

    @php
        $commentHistory = $application->relationLoaded('reviewComments') ? $application->reviewComments : collect();
    @endphp
    @if($commentHistory->isNotEmpty())
    <section class="report-section preview-section" data-label="Review History">
        <h2 class="section-title"><span class="section-num"><i class="fas fa-clock-rotate-left" style="font-size:.6rem;"></i></span> Review History</h2>
        <div style="display:flex;flex-direction:column;gap:10px;padding-bottom:8px;">
            @foreach($commentHistory as $entry)
                @php
                    [$badgeBg, $badgeColor, $badgeIcon] = match ($entry->action) {
                        'approved' => ['#ecfdf5', '#15803d', 'fa-check'],
                        'rejected' => ['#fef2f2', '#b91c1c', 'fa-undo'],
                        'resubmitted' => ['#eff6ff', '#1d4ed8', 'fa-paper-plane'],
                        'submitted' => ['#eff6ff', '#1d4ed8', 'fa-inbox'],
                        default => ['#f8fafc', '#475569', 'fa-comment'],
                    };
                @endphp
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <span style="flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:999px;background:{{ $badgeBg }};color:{{ $badgeColor }};font-size:.7rem;"><i class="fas {{ $badgeIcon }}"></i></span>
                    <div style="flex:1;">
                        <div style="font-size:.8rem;color:#1f2937;">
                            <strong>{{ optional($entry->user)->name ?? 'System' }}</strong>
                            <span style="color:#6b7280;">· {{ ucfirst($entry->action) }} at {{ str_replace('_', ' ', $entry->stage) }} · {{ optional($entry->created_at)->format('d M Y, H:i') }}</span>
                        </div>
                        @if($entry->comment)
                            <div style="font-size:.84rem;color:#374151;margin-top:2px;">{{ $entry->comment }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif
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
