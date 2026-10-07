@extends('desk-admin.layout')

@section('content')

<main class="redas-content">
    @php
        $user = auth()->user();
        $accessRole = $accessRole ?? 'state';
        $nextStage = $nextStage ?? ($accessRole === 'directorate' ? 'directorate-admin' : 'desk_admin');
        $stageName = $stageName ?? 'Review Queue';
        $dashboardTitle = $dashboardTitle ?? 'Review Dashboard';
        $approveRoute = $approveRoute ?? 'desk.admin.submissions.approve';
        $rejectRoute = $rejectRoute ?? 'desk.admin.submissions.reject';
        $pendingSubmissions = $pendingSubmissions ?? collect();
        $approvedSubmissions = $approvedSubmissions ?? collect();
        $rejectedSubmissions = $rejectedSubmissions ?? collect();
        $approvedCount = $approvedCount ?? 0;
        $rejectedCount = $rejectedCount ?? 0;
    @endphp

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $dashboardTitle }}</h1>
            <p class="page-subtitle">Welcome, <strong>{{ $user->name ?? 'Officer' }}</strong> — {{ $stageName }}</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('desk.admin.reports') }}" class="btn-nis btn-outline-nis">
                <i class="fas fa-file-export"></i> Generate Reports
            </a>
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

    <ul class="nav nav-tabs nav-tabs-nis" id="deskAdminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-pane" type="button" role="tab" aria-controls="pending-pane" aria-selected="true">
                <i class="fas fa-clock" style="margin-right:6px;"></i>Pending
                <span class="badge bg-warning text-dark" style="margin-left:6px;font-size:.65rem;">{{ $pendingSubmissions->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved-pane" type="button" role="tab" aria-controls="approved-pane" aria-selected="false">
                <i class="fas fa-circle-check" style="margin-right:6px;"></i>Approved
                <span class="badge bg-success" style="margin-left:6px;font-size:.65rem;">{{ $approvedCount }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="returned-tab" data-bs-toggle="tab" data-bs-target="#returned-pane" type="button" role="tab" aria-controls="returned-pane" aria-selected="false">
                <i class="fas fa-circle-xmark" style="margin-right:6px;"></i>Returned
                <span class="badge bg-danger" style="margin-left:6px;font-size:.65rem;">{{ $rejectedCount }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content tab-content-nis" id="deskAdminTabContent">
        <div class="tab-pane fade show active" id="pending-pane" role="tabpanel" aria-labelledby="pending-tab">
            @include('partials.admin-submissions-table', [
                'submissions' => $pendingSubmissions,
                'title' => 'Submissions Awaiting Action',
                'status' => 'pending',
                'emptyMessage' => 'No pending submissions in your provisioned scope.',
                'showApproveReject' => true,
                'approveRoute' => $approveRoute,
                'rejectRoute' => $rejectRoute,
            ])
        </div>
        <div class="tab-pane fade" id="approved-pane" role="tabpanel" aria-labelledby="approved-tab">
            @include('partials.admin-submissions-table', [
                'submissions' => $approvedSubmissions,
                'title' => 'Approved Submissions',
                'status' => 'approved',
                'emptyMessage' => 'No approved submissions in your provisioned scope yet.',
            ])
        </div>
        <div class="tab-pane fade" id="returned-pane" role="tabpanel" aria-labelledby="returned-tab">
            @include('partials.admin-submissions-table', [
                'submissions' => $rejectedSubmissions,
                'title' => 'Returned Submissions',
                'status' => 'returned',
                'emptyMessage' => 'No returned submissions in your provisioned scope yet.',
            ])
        </div>
    </div>
</main>

@endsection
