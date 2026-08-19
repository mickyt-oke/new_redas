<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Submissions Report</title>
    <style>
        body { background:#fff; color:#111827; font-family:'DejaVu Sans', sans-serif; font-size:11px; margin:0; }
        .report-header { border-bottom:3px solid #006838; padding-bottom:12px; margin-bottom:16px; }
        .report-header-table { width:100%; border-collapse:collapse; }
        .report-header-table td { vertical-align:middle; border:none; padding:0; }
        .report-header img.logo { width:64px; height:64px; }
        .report-header h1 { font-size:16px; margin:0 0 3px; color:#006838; }
        .report-header p { margin:1px 0; font-size:10px; color:#4b5563; }
        table.report-table { width:100%; border-collapse:collapse; font-size:10px; margin-top:10px; }
        table.report-table th, table.report-table td { border:1px solid #d1d5db; padding:5px 8px; text-align:left; vertical-align:top; }
        table.report-table th { background:#f0fdf4; font-weight:bold; color:#14532d; }
        .status-badge { display:inline-block; padding:2px 8px; border-radius:10px; font-size:9px; font-weight:bold; text-transform:uppercase; }
        .status-approved { background:#ecfdf5; color:#166534; border:1px solid #86efac; }
        .status-pending { background:#fef9c3; color:#854d0e; border:1px solid #fde047; }
        .status-returned { background:#fef2f2; color:#991b1b; border:1px solid #fca5a5; }
        .report-footer { margin-top:24px; padding-top:8px; border-top:1px solid #e5e7eb; font-size:9px; color:#9ca3af; text-align:center; }
    </style>
</head>
<body>
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
                <p>Submissions Report — {{ $dashboardTitle ?? 'Review Dashboard' }}</p>
                <p>
                    <strong>Period:</strong> {{ ($filters['date_from'] ?? null) ?: '…' }} to {{ ($filters['date_to'] ?? null) ?: '…' }}
                    &nbsp;&nbsp;<strong>Status:</strong> {{ ucfirst($filters['status'] ?? 'All') }}
                    &nbsp;&nbsp;<strong>Records:</strong> {{ $submissions->count() }}
                </p>
            </td>
        </tr>
    </table>
</div>

<table class="report-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Officer</th>
            <th>Scope</th>
            <th>Report Period</th>
            <th>Status</th>
            <th>Submitted</th>
        </tr>
    </thead>
    <tbody>
        @forelse($submissions as $submission)
            @php
                $s = strtolower((string) $submission->status);
                $statusClass = match (true) {
                    $s === 'approved' => 'status-approved',
                    in_array($s, ['rejected', 'queried', 'returned'], true) => 'status-returned',
                    default => 'status-pending',
                };
            @endphp
            <tr>
                <td>#{{ $submission->id }}</td>
                <td>{{ optional($submission->user)->name ?? 'N/A' }}</td>
                <td style="text-transform:uppercase;">{{ $submission->scope_code ?? '—' }}</td>
                <td>{{ $submission->return_data['report_period'] ?? '—' }}</td>
                <td><span class="status-badge {{ $statusClass }}">{{ ucfirst($submission->status) }}</span></td>
                <td>{{ optional($submission->created_at)->format('d M Y, H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center;color:#9ca3af;">No submissions match the selected filters.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="report-footer">
    Generated from NIS-REDAS on {{ now()->format('d M Y, H:i') }}
</div>
</body>
</html>
