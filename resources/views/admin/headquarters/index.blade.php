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
            <div style="overflow:auto;">
                <table class="redas-table" style="min-width:680px;">
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
                            <tr><td colspan="6" style="text-align:center;color:var(--gray-400);padding:24px;">No directorate returns recorded yet.</td></tr>
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

    <div class="redas-card">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#ede9fe;color:#6d28d9;">
                    <i class="fas fa-list"></i>
                </div>
                Recent Returns (All Formations)
            </div>
            <a href="{{ route('admin.hq.returns') }}" style="font-size:.78rem;color:var(--nis-600);font-weight:600;text-decoration:none;">View all <i class="fas fa-arrow-right" style="font-size:.65rem;"></i></a>
        </div>
        <div class="card-body no-pad">
            <div style="overflow:auto;">
                <table class="redas-table" style="min-width:760px;">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Formation</th>
                            <th>Officer</th>
                            <th>Period</th>
                            <th>Stage</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentReturns as $return)
                            @php
                                $data = $return->return_data ?? [];
                                $badge = match ($return->status) {
                                    'approved' => 'badge-approved',
                                    'returned', 'rejected' => 'badge-rejected',
                                    default => 'badge-pending',
                                };
                            @endphp
                            <tr>
                                <td style="font-weight:600;">RET-{{ str_pad($return->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $data['command_name'] ?? $return->scope_code ?? '—' }}</td>
                                <td>{{ $data['reporting_officer'] ?? $return->user->name ?? '—' }}</td>
                                <td>{{ $data['report_period'] ?? '—' }}</td>
                                <td style="text-transform:capitalize;">{{ str_replace('_', ' ', $return->workflow_stage) }}</td>
                                <td><span class="status-badge {{ $badge }}">{{ ucfirst($return->status) }}</span></td>
                                <td>{{ optional($return->created_at)->format('d M Y') }}</td>
                                <td style="text-align:right;">
                                    <a href="{{ route('admin.hq.returns.show', $return) }}" class="btn-nis btn-ghost" style="padding:6px 12px;font-size:.75rem;">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" style="text-align:center;color:var(--gray-400);padding:24px;">No returns recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

@endsection
