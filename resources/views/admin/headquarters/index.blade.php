@extends('admin.headquarters.layout')

@section('title', 'HQ Admin Dashboard')

@section('content')

<main class="redas-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">HQ Admin Dashboard</h1>
            <p class="page-subtitle">National overview of returns from all directorates, state commands and CGIS units. All returns on this portal are <strong>view-only</strong>.</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('admin.hq.reports') }}" class="btn-nis btn-outline-nis">
                <i class="fas fa-file-excel"></i> Generate Report
            </a>
            <a href="{{ route('admin.hq.returns') }}" class="btn-nis btn-ghost">
                <i class="fas fa-folder-open"></i> All Returns
            </a>
        </div>
    </div>

    @if(session('status'))
        <div style="margin-bottom:16px;padding:12px 14px;border-radius:10px;border:1px solid #bbf7d0;background:#f0fdf4;color:#166534;">
            {{ session('status') }}
        </div>
    @endif

    <div class="stats-grid" style="margin-bottom:20px;">
        <div class="stat-card info">
            <div class="stat-header">
                <span class="stat-label">Total Returns</span>
                <span class="stat-icon"><i class="fas fa-file-lines"></i></span>
            </div>
            <div class="stat-value">{{ number_format($stats['total']) }}</div>
            <div class="stat-change neutral">All formations, all stages</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-label">Awaiting HQ Review</span>
                <span class="stat-icon"><i class="fas fa-clock"></i></span>
            </div>
            <div class="stat-value">{{ number_format($stats['awaiting_hq']) }}</div>
            <div class="stat-change neutral">Approved by directorate admins</div>
        </div>
        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-label">Approved</span>
                <span class="stat-icon"><i class="fas fa-circle-check"></i></span>
            </div>
            <div class="stat-value">{{ number_format($stats['approved']) }}</div>
            <div class="stat-change up">Finalized &amp; archived</div>
        </div>
        <div class="stat-card danger">
            <div class="stat-header">
                <span class="stat-label">Returned</span>
                <span class="stat-icon"><i class="fas fa-rotate-left"></i></span>
            </div>
            <div class="stat-value">{{ number_format($stats['returned']) }}</div>
            <div class="stat-change down">Sent back for correction</div>
        </div>
    </div>

    <ul class="nav nav-tabs nav-tabs-nis" id="hqTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview-pane" type="button" role="tab" aria-controls="overview-pane" aria-selected="true">
                <i class="fas fa-th-large" style="margin-right:6px;"></i>Overview
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="recent-tab" data-bs-toggle="tab" data-bs-target="#recent-pane" type="button" role="tab" aria-controls="recent-pane" aria-selected="false">
                <i class="fas fa-list" style="margin-right:6px;"></i>Recent Returns
                <span class="badge bg-secondary" style="margin-left:6px;font-size:.65rem;">{{ $recentReturns->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content tab-content-nis" id="hqTabContent">
        <div class="tab-pane fade show active" id="overview-pane" role="tabpanel" aria-labelledby="overview-tab">
            <div class="redas-card" style="margin-bottom:20px;">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#e0f2fe;color:#0369a1;">
                            <i class="fas fa-building"></i>
                        </div>
                        Directorate Returns Overview
                    </div>
                    <a href="{{ route('admin.hq.returns', ['category' => 'directorate']) }}" style="font-size:.78rem;color:var(--nis-600);font-weight:600;text-decoration:none;">View all <i class="fas fa-arrow-right" style="font-size:.65rem;"></i></a>
                </div>
                <div class="card-body no-pad">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered table-nis align-middle" style="min-width:680px;">
                            <thead>
                                <tr>
                                    <th>Directorate</th>
                                    <th style="text-align:center;">Total</th>
                                    <th style="text-align:center;">Pending</th>
                                    <th style="text-align:center;">Approved</th>
                                    <th style="text-align:center;">Returned</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($directorates as $directorate)
                                    <tr>
                                        <td style="font-weight:600;">{{ $directorate['name'] }}</td>
                                        <td style="text-align:center;">{{ $directorate['total'] }}</td>
                                        <td style="text-align:center;"><span class="status-badge badge-pending">{{ $directorate['pending'] }}</span></td>
                                        <td style="text-align:center;"><span class="status-badge badge-approved">{{ $directorate['approved'] }}</span></td>
                                        <td style="text-align:center;"><span class="status-badge badge-rejected">{{ $directorate['returned'] }}</span></td>
                                        <td style="text-align:right;">
                                            <a href="{{ route('admin.hq.returns', ['formation' => $directorate['slug']]) }}" class="btn-nis btn-ghost" style="padding:6px 12px;font-size:.75rem;">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align:center;color:var(--gray-400);padding:24px;">No directorate returns recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="redas-card" style="margin-bottom:20px;">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#fef3c7;color:#b45309;">
                            <i class="fas fa-star"></i>
                        </div>
                        CGIS Units Returns
                    </div>
                </div>
                <div class="card-body">
                    @if($cgisUnits->isEmpty())
                        <p style="margin:0;color:var(--gray-400);font-size:.85rem;">No returns have been submitted by CGIS units yet.</p>
                    @else
                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
                            @foreach($cgisUnits as $unit)
                                <a href="{{ route('admin.hq.returns', ['category' => 'cgis']) }}" style="display:block;border:1px solid var(--gray-200);border-radius:10px;padding:12px;text-decoration:none;color:inherit;">
                                    <div style="font-size:.75rem;color:var(--gray-500);text-transform:uppercase;letter-spacing:.03em;">{{ $unit['unit'] }}</div>
                                    <div style="font-size:1.15rem;font-weight:700;color:var(--gray-900);">{{ $unit['total'] }} <span style="font-size:.72rem;font-weight:500;color:var(--gray-400);">returns</span></div>
                                    <div style="font-size:.74rem;color:#15803d;">{{ $unit['approved'] }} approved</div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="recent-pane" role="tabpanel" aria-labelledby="recent-tab">
            @include('partials.admin-submissions-table', [
                'submissions' => $recentReturns,
                'title' => 'Recent Returns (All Formations)',
                'emptyMessage' => 'No returns recorded yet.',
                'previewRoute' => 'admin.hq.returns.show',
                'documentRoute' => 'admin.hq.returns.document',
                'downloadRoute' => 'admin.hq.returns.download',
            ])
        </div>
    </div>
</main>

@endsection
