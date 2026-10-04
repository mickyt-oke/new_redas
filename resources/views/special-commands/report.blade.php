@php
    $data = is_array($application->return_data) ? $application->return_data : [];
    $skipKeys = ['data_consent', 'report_period', 'reporting_officer', 'command_name', 'period', 'return_type', 'special_command_code', 'attachments'];
    $command = $commandName ?? $data['command_name'] ?? '—';
    $period = $data['report_period'] ?? $data['period'] ?? '—';
    $officer = $data['reporting_officer'] ?? optional($application->user)->name ?? '—';
    $typeLabels = ['monthly' => 'Monthly Return', 'quarterly' => 'Quarterly Return', 'biannual' => 'Bi-Annual Return', 'annual' => 'Annual Return', 'special' => 'Special Report'];
    $type = $typeLabels[$data['return_type'] ?? ''] ?? ucfirst((string) ($data['return_type'] ?? '—'));
    $status = strtolower((string) $application->status);
    $statusClass = match (true) {
        $status === 'approved' => 'status-approved',
        in_array($status, ['rejected', 'queried', 'returned'], true) => 'status-returned',
        default => 'status-pending',
    };

    $sections = \App\Services\PreviewRenderer::buildSections($data, [
        'skipKeys' => $skipKeys,
        'sectionLabels' => [
            'personnel' => 'Personnel Strength',
            'operations' => 'Operations Summary',
            'logistics' => 'Logistics / Equipment',
            'finance' => 'Finance / Budget Summary',
            'challenges' => 'Challenges & Way Forward',
        ],
    ]);
@endphp

@if(!($isPdf ?? false))
@include('partials.header')
@endif

@if($isPdf ?? false)
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $command }} Return — {{ $period }}</title>
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
        table.report-table { width:100%; border-collapse:collapse; font-size:10px; margin-bottom:6px; }
        table.report-table th, table.report-table td { border:1px solid #d1d5db; padding:4px 8px; text-align:left; vertical-align:top; }
        table.report-table th { background:#f0fdf4; font-weight:bold; color:#14532d; }
        table.report-table td.kv-key { width:38%; font-weight:bold; background:#f9fafb; }
        .report-footer { margin-top:24px; padding-top:8px; border-top:1px solid #e5e7eb; font-size:9px; color:#9ca3af; text-align:center; }
    </style>
</head>
<body>
@endif

<div class="report-header">
    <table class="report-header-table">
        <tr>
            <td style="width:76px;">
                @if(($isPdf ?? false) && !empty($logoDataUri ?? null))
                    <img class="logo" src="{{ $logoDataUri }}" alt="NIS Logo">
                @endif
            </td>
            <td>
                <h1>Nigeria Immigration Service</h1>
                <p>{{ $command }} — {{ $type }}</p>
                <div class="report-meta">
                    <span><strong>Report Period:</strong> {{ $period }}</span>
                    <span><strong>Reporting Officer:</strong> {{ $officer }}</span>
                    <span><strong>Submitted:</strong> {{ optional($application->created_at)->format('d M Y, H:i') ?? '—' }}</span>
                    <span><strong>Status:</strong> <span class="status-badge {{ $statusClass }}">{{ ucfirst($application->status ?? 'pending') }}</span></span>
                </div>
            </td>
        </tr>
    </table>
</div>

@foreach($sections as $section)
    <div class="section-title">{{ $section['label'] }}</div>
    @if($section['html'])
        {!! $section['html'] !!}
    @else
        <p style="color:#64748b;">No data entered for this section.</p>
    @endif
@endforeach

<div class="report-footer">
    Generated from NIS-REDAS on {{ now()->format('d M Y, H:i') }} — Submission #{{ $application->id }}
</div>

@if($isPdf ?? false)
</body>
</html>
@else
<div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
    <a href="{{ route('special-commands.returns.index') }}" class="btn-nis btn-ghost">Back to List</a>
    <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('special-commands.returns.report.pdf', ['applicationHash' => \App\Services\HashidService::encode($application->id)]) }}" class="btn-nis btn-outline-nis"><i class="fas fa-file-pdf"></i> Download PDF</a>
</div>

@include('partials.footer')
@endif
