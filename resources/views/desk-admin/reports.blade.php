@extends('desk-admin.layout')

@section('content')
<main class="redas-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Report Generation</h1>
            <p class="page-subtitle">Filter submissions by date and status, then view or export the report.</p>
        </div>
        <a href="{{ route('user.desk.home') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    @if($errors->any())
        <div style="margin-bottom:16px;padding:12px 14px;border-radius:10px;border:1px solid #fca5a5;background:#fef2f2;color:#991b1b;">
            @foreach($errors->all() as $error)
                <div><i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="redas-card" style="margin-bottom:20px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#e0f2fe;color:#0369a1;"><i class="fas fa-filter"></i></div>
                Report Filters
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('desk.admin.reports') }}" id="reportFilterForm">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;align-items:end;">
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Date From</label>
                        <input type="date" name="date_from" class="ni" value="{{ $filters['date_from'] ?? '' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Date To</label>
                        <input type="date" name="date_to" class="ni" value="{{ $filters['date_to'] ?? '' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Status</label>
                        <select name="status" class="ni ni-select">
                            <option value="">All statuses</option>
                            @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'returned' => 'Returned'] as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        <button type="submit" class="btn-nis btn-primary-nis">
                            <i class="fas fa-chart-line"></i> Generate Report
                        </button>
                        <button type="submit" name="format" value="pdf" class="btn-nis btn-outline-nis">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </button>
                        <button type="submit" name="format" value="csv" class="btn-nis btn-outline-nis">
                            <i class="fas fa-file-csv"></i> Download CSV
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->filled('date_from') || request()->filled('date_to') || request()->filled('status'))
        <div class="redas-card">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#ede9fe;color:#6d28d9;"><i class="fas fa-table-list"></i></div>
                    Report Results
                </div>
                <span style="font-size:.8rem;color:var(--gray-500);">{{ $submissions->count() }} record(s) found</span>
            </div>
            <div class="card-body no-pad">
                <div style="overflow:auto;">
                    <table style="width:100%;border-collapse:collapse;min-width:760px;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">ID</th>
                                <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Officer</th>
                                <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Scope</th>
                                <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Report Period</th>
                                <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Status</th>
                                <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Submitted</th>
                                <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submissions as $submission)
                                @php
                                    $s = strtolower((string) $submission->status);
                                    $badge = match (true) {
                                        $s === 'approved' => 'background:#ecfdf5;color:#166534;',
                                        in_array($s, ['rejected', 'queried', 'returned'], true) => 'background:#fef2f2;color:#991b1b;',
                                        default => 'background:#fef9c3;color:#854d0e;',
                                    };
                                @endphp
                                <tr>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;">#{{ $submission->id }}</td>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ optional($submission->user)->name ?? 'N/A' }}</td>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;text-transform:uppercase;">{{ $submission->scope_code ?? '—' }}</td>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $submission->return_data['report_period'] ?? '—' }}</td>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;">
                                        <span style="display:inline-flex;padding:3px 8px;border-radius:999px;font-size:.72rem;font-weight:700;{{ $badge }}">
                                            {{ ucfirst($submission->status) }}
                                        </span>
                                    </td>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ optional($submission->created_at)->format('d M Y, H:i') }}</td>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;">
                                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                            <a href="{{ route('desk.admin.submissions.show', $submission) }}" class="btn-nis btn-sm btn-ghost" style="padding:6px 12px;">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('desk.admin.submissions.download', $submission) }}" class="btn-nis btn-sm btn-outline-nis" style="padding:6px 12px;">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="padding:16px;text-align:center;color:#64748b;">No submissions match the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="redas-card">
            <div class="card-body" style="text-align:center;color:var(--gray-500);font-size:.88rem;padding:32px;">
                <i class="fas fa-calendar-day" style="font-size:1.6rem;color:var(--gray-400);display:block;margin-bottom:10px;"></i>
                Choose a date range (and optionally a status), then click <strong>Generate Report</strong>.
            </div>
        </div>
    @endif
</main>
@endsection
