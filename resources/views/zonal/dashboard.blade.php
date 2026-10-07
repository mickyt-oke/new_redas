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

    <ul class="nav nav-tabs nav-tabs-nis" id="zonalTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="zonal-pending-tab" data-bs-toggle="tab" data-bs-target="#zonal-pending-pane" type="button" role="tab" aria-controls="zonal-pending-pane" aria-selected="true">
                <i class="fas fa-clock" style="margin-right:6px;"></i>Pending
                <span class="badge bg-warning text-dark" style="margin-left:6px;font-size:.65rem;">{{ $pendingSubmissions->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="zonal-approved-tab" data-bs-toggle="tab" data-bs-target="#zonal-approved-pane" type="button" role="tab" aria-controls="zonal-approved-pane" aria-selected="false">
                <i class="fas fa-circle-check" style="margin-right:6px;"></i>Approved
                <span class="badge bg-success" style="margin-left:6px;font-size:.65rem;">{{ $approvedCount }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="zonal-returned-tab" data-bs-toggle="tab" data-bs-target="#zonal-returned-pane" type="button" role="tab" aria-controls="zonal-returned-pane" aria-selected="false">
                <i class="fas fa-circle-xmark" style="margin-right:6px;"></i>Returned
                <span class="badge bg-danger" style="margin-left:6px;font-size:.65rem;">{{ $rejectedCount }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content tab-content-nis" id="zonalTabContent">
        <div class="tab-pane fade show active" id="zonal-pending-pane" role="tabpanel" aria-labelledby="zonal-pending-tab">
            @include('partials.admin-submissions-table', [
                'submissions' => $pendingSubmissions,
                'title' => 'Submissions Awaiting Action',
                'status' => 'pending',
                'emptyMessage' => 'No pending submissions in your provisioned scope.',
                'showApproveReject' => true,
                'previewRoute' => 'zonal.submissions.show',
                'documentRoute' => 'zonal.submissions.document',
                'downloadRoute' => 'zonal.submissions.download',
                'approveRoute' => $approveRoute,
                'rejectRoute' => $rejectRoute,
            ])
        </div>
        <div class="tab-pane fade" id="zonal-approved-pane" role="tabpanel" aria-labelledby="zonal-approved-tab">
            @include('partials.admin-submissions-table', [
                'submissions' => $approvedSubmissions,
                'title' => 'Approved Submissions',
                'status' => 'approved',
                'emptyMessage' => 'No approved submissions in your provisioned scope yet.',
                'previewRoute' => 'zonal.submissions.show',
                'documentRoute' => 'zonal.submissions.document',
                'downloadRoute' => 'zonal.submissions.download',
            ])
        </div>
        <div class="tab-pane fade" id="zonal-returned-pane" role="tabpanel" aria-labelledby="zonal-returned-tab">
            @include('partials.admin-submissions-table', [
                'submissions' => $rejectedSubmissions,
                'title' => 'Returned Submissions',
                'status' => 'returned',
                'emptyMessage' => 'No returned submissions in your provisioned scope yet.',
                'previewRoute' => 'zonal.submissions.show',
                'documentRoute' => 'zonal.submissions.document',
                'downloadRoute' => 'zonal.submissions.download',
            ])
        </div>
    </div>
</main>

@endsection
