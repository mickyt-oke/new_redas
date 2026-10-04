@extends('desk-admin.layout')

@section('content')

<main class="redas-content">
    @php
        $user = auth()->user();
        $accessRole = $accessRole ?? 'zonal';
        $nextStage = $nextStage ?? 'hq_review';
        $stageName = $stageName ?? 'Zonal Command Review';
        $dashboardTitle = $dashboardTitle ?? 'Zonal Command Dashboard';
        $approveRoute = $approveRoute ?? 'zonal.submissions.approve';
        $rejectRoute = $rejectRoute ?? 'zonal.submissions.reject';
        $pendingSubmissions = $pendingSubmissions ?? collect();
        $approvedSubmissions = $approvedSubmissions ?? collect();
        $rejectedSubmissions = $rejectedSubmissions ?? collect();
        $approvedCount = $approvedCount ?? 0;
        $rejectedCount = $rejectedCount ?? 0;
    @endphp

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $dashboardTitle }}</h1>
            <p class="page-subtitle">Welcome, <strong>{{ $user->name ?? 'Commander' }}</strong> — {{ $stageName }}</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('user.archive') }}" class="btn-nis btn-ghost">
                <i class="fas fa-archive"></i> Archived Documents
            </a>
        </div>
    </div>

    @if(session('status'))
        <div style="margin-bottom:16px;padding:12px 14px;border-radius:10px;border:1px solid #bbf7d0;background:#f0fdf4;color:#166534;">
            {{ session('status') }}
        </div>
    @endif

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:20px;">
        <div style="border:1px solid #e5e7eb;border-radius:10px;padding:12px;background:#fff;">
            <div style="font-size:.75rem;color:#64748b;">Pending Approvals</div>
            <div style="font-size:1.2rem;font-weight:700;color:#0f172a;">{{ $pendingSubmissions->count() }}</div>
        </div>
        <div style="border:1px solid #e5e7eb;border-radius:10px;padding:12px;background:#fff;">
            <div style="font-size:.75rem;color:#64748b;">Approved</div>
            <div style="font-size:1.2rem;font-weight:700;color:#166534;">{{ $approvedCount }}</div>
        </div>
        <div style="border:1px solid #e5e7eb;border-radius:10px;padding:12px;background:#fff;">
            <div style="font-size:.75rem;color:#64748b;">Returned</div>
            <div style="font-size:1.2rem;font-weight:700;color:#b91c1c;">{{ $rejectedCount }}</div>
        </div>
    </div>

    <div class="redas-card">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#ede9fe;color:#6d28d9;">
                    <i class="fas fa-list-check"></i>
                </div>
                Submissions Awaiting Action
            </div>
        </div>
        <div class="card-body no-pad">
            <div style="overflow:auto;">
                <table style="width:100%;border-collapse:collapse;min-width:760px;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">ID</th>
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Officer</th>
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Report Period</th>
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Status</th>
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Date</th>
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingSubmissions as $submission)
                            <tr>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">#{{ $submission->id }}</td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ optional($submission->user)->name ?? 'N/A' }}</td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $submission->return_data['report_period'] ?? '—' }}</td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">
                                    <span style="display:inline-flex;padding:3px 8px;border-radius:999px;background:#fef3c7;color:#92400e;font-size:.72rem;font-weight:700;">
                                        {{ ucfirst($submission->status) }}
                                    </span>
                                </td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ optional($submission->created_at)->format('d M Y, H:i') }}</td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">
                                    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:flex-end;">
                                        <a href="{{ route('zonal.submissions.show', ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" class="btn-nis btn-sm btn-ghost" style="padding:6px 12px;">
                                            <i class="fas fa-eye"></i> Preview
                                        </a>
                                        <form method="POST" action="{{ \Illuminate\Support\Facades\URL::signedRoute($approveRoute, ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" style="display:flex;gap:8px;flex-wrap:wrap;min-width:240px;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="note" maxlength="1000" placeholder="Approval note (optional)" style="padding:6px 8px;border:1px solid #cbd5e1;border-radius:8px;font-size:.75rem;flex:1;min-width:140px;">
                                            <button type="submit" class="btn-nis btn-sm btn-primary-nis">Approve</button>
                                        </form>

                                        <form method="POST" action="{{ \Illuminate\Support\Facades\URL::signedRoute($rejectRoute, ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" style="display:flex;gap:8px;flex-wrap:wrap;min-width:260px;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="comment" maxlength="1000" required placeholder="Rejection reason (required)" style="padding:6px 8px;border:1px solid #cbd5e1;border-radius:8px;font-size:.75rem;flex:1;min-width:140px;">
                                            <button type="submit" class="btn-nis btn-sm" style="background:#b91c1c;color:#fff;">Reject</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:16px;text-align:center;color:#64748b;">No pending submissions in your provisioned scope.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="redas-card" style="margin-top:20px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#ecfdf5;color:#15803d;">
                    <i class="fas fa-circle-check"></i>
                </div>
                Approved Submissions
            </div>
        </div>
        <div class="card-body no-pad">
            <div style="overflow:auto;">
                <table style="width:100%;border-collapse:collapse;min-width:720px;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">ID</th>
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Officer</th>
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Report Period</th>
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Status</th>
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Approved</th>
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($approvedSubmissions as $submission)
                            <tr>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">#{{ $submission->id }}</td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ optional($submission->user)->name ?? 'N/A' }}</td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $submission->return_data['report_period'] ?? '—' }}</td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">
                                    <span style="display:inline-flex;padding:3px 8px;border-radius:999px;background:#ecfdf5;color:#166534;font-size:.72rem;font-weight:700;">
                                        {{ ucfirst($submission->status) }}
                                    </span>
                                </td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ optional($submission->updated_at)->format('d M Y, H:i') }}</td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">
                                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                        <a href="{{ route('zonal.submissions.show', ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" class="btn-nis btn-sm btn-ghost" style="padding:6px 12px;">
                                            <i class="fas fa-eye"></i> Preview
                                        </a>
                                        <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('user.submissions.pdf', ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" class="btn-nis btn-sm btn-outline-nis" style="padding:6px 12px;">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:16px;text-align:center;color:#64748b;">No approved submissions in your provisioned scope yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="redas-card" style="margin-top:20px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#fee2e2;color:#991b1b;">
                    <i class="fas fa-circle-xmark"></i>
                </div>
                Returned Submissions
            </div>
        </div>
        <div class="card-body no-pad">
            <div style="overflow:auto;">
                <table style="width:100%;border-collapse:collapse;min-width:720px;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">ID</th>
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Officer</th>
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Report Period</th>
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Status</th>
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Returned</th>
                            <th style="text-align:left;padding:10px;font-size:.75rem;color:#475569;border-bottom:1px solid #e5e7eb;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rejectedSubmissions as $submission)
                            <tr>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">#{{ $submission->id }}</td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ optional($submission->user)->name ?? 'N/A' }}</td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $submission->return_data['report_period'] ?? '—' }}</td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">
                                    <span style="display:inline-flex;padding:3px 8px;border-radius:999px;background:#fee2e2;color:#991b1b;font-size:.72rem;font-weight:700;">
                                        {{ ucfirst($submission->status) }}
                                    </span>
                                </td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ optional($submission->updated_at)->format('d M Y, H:i') }}</td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">
                                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                        <a href="{{ route('zonal.submissions.show', ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" class="btn-nis btn-sm btn-ghost" style="padding:6px 12px;">
                                            <i class="fas fa-eye"></i> Preview
                                        </a>
                                        <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('user.submissions.pdf', ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" class="btn-nis btn-sm btn-outline-nis" style="padding:6px 12px;">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:16px;text-align:center;color:#64748b;">No returned submissions in your provisioned scope yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

@endsection
