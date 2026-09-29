@extends('admin.headquarters.layout')

@section('title', 'Consolidation')

@section('content')

<main class="redas-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Returns Consolidation</h1>
            <p class="page-subtitle">Merged national aggregates across every formation — the consolidated view surfaced to the executive dashboard.</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('admin.hq.reports') }}" class="btn-nis btn-outline-nis">
                <i class="fas fa-file-excel"></i> Generate Report
            </a>
        </div>
    </div>

    <div class="stats-grid" style="margin-bottom:20px;">
        <div class="stat-card info">
            <div class="stat-header">
                <span class="stat-label">Total Returns</span>
                <span class="stat-icon"><i class="fas fa-file-lines"></i></span>
            </div>
            <div class="stat-value">{{ number_format($stats['total']) }}</div>
            <div class="stat-change neutral">All categories, all stages</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-label">Pending</span>
                <span class="stat-icon"><i class="fas fa-clock"></i></span>
            </div>
            <div class="stat-value">{{ number_format($stats['pending']) }}</div>
            <div class="stat-change neutral">In the review workflow</div>
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
                <div class="card-head-icon" style="background:#ede9fe;color:#6d28d9;">
                    <i class="fas fa-chart-line"></i>
                </div>
                Submissions vs Approvals (Last 12 Months)
            </div>
        </div>
        <div class="card-body">
            <canvas id="trendChart" height="80"></canvas>
        </div>
    </div>

    <div class="redas-card" style="margin-bottom:20px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#e0f2fe;color:#0369a1;">
                    <i class="fas fa-layer-group"></i>
                </div>
                Aggregate by Category
            </div>
        </div>
        <div class="card-body no-pad">
            <div style="overflow:auto;">
                <table class="redas-table" style="min-width:560px;">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th style="text-align:center;">Total</th>
                            <th style="text-align:center;">Pending</th>
                            <th style="text-align:center;">Approved</th>
                            <th style="text-align:center;">Returned</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $row)
                            <tr>
                                <td style="font-weight:600;">{{ $row['label'] }}</td>
                                <td style="text-align:center;">{{ number_format($row['total']) }}</td>
                                <td style="text-align:center;">{{ number_format($row['pending']) }}</td>
                                <td style="text-align:center;">{{ number_format($row['approved']) }}</td>
                                <td style="text-align:center;">{{ number_format($row['returned']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:20px;margin-bottom:20px;">
        <div class="redas-card">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#fef3c7;color:#b45309;">
                        <i class="fas fa-building"></i>
                    </div>
                    Directorates &amp; CGIS Units
                </div>
            </div>
            <div class="card-body no-pad">
                <div style="overflow:auto;max-height:380px;">
                    <table class="redas-table" style="min-width:320px;">
                        <thead>
                            <tr>
                                <th>Formation</th>
                                <th style="text-align:center;">Total</th>
                                <th style="text-align:center;">Approved</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($units as $unit)
                                <tr>
                                    <td>
                                        {{ $unit['name'] }}
                                        <div style="font-size:.72rem;color:var(--gray-400);text-transform:capitalize;">{{ $unit['category'] }}</div>
                                    </td>
                                    <td style="text-align:center;">{{ number_format($unit['total']) }}</td>
                                    <td style="text-align:center;">{{ number_format($unit['approved']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="text-align:center;color:var(--gray-400);padding:24px;">No directorate or CGIS returns yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="redas-card">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#e0e7ff;color:#4338ca;">
                        <i class="fas fa-location-dot"></i>
                    </div>
                    State Commands
                </div>
            </div>
            <div class="card-body no-pad">
                <div style="overflow:auto;max-height:380px;">
                    <table class="redas-table" style="min-width:320px;">
                        <thead>
                            <tr>
                                <th>State</th>
                                <th style="text-align:center;">Total</th>
                                <th style="text-align:center;">Approved</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($states as $state)
                                <tr>
                                    <td style="font-weight:600;">{{ $state['code'] }}</td>
                                    <td style="text-align:center;">{{ number_format($state['total']) }}</td>
                                    <td style="text-align:center;">{{ number_format($state['approved']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="text-align:center;color:var(--gray-400);padding:24px;">No state returns yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="redas-card">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#fee2e2;color:#b91c1c;">
                        <i class="fas fa-map"></i>
                    </div>
                    Zonal Commands
                </div>
            </div>
            <div class="card-body no-pad">
                <div style="overflow:auto;max-height:380px;">
                    <table class="redas-table" style="min-width:320px;">
                        <thead>
                            <tr>
                                <th>Zone</th>
                                <th style="text-align:center;">Total</th>
                                <th style="text-align:center;">Approved</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($zones as $zone)
                                <tr>
                                    <td style="font-weight:600;">{{ $zone['code'] }}</td>
                                    <td style="text-align:center;">{{ number_format($zone['total']) }}</td>
                                    <td style="text-align:center;">{{ number_format($zone['approved']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="text-align:center;color:var(--gray-400);padding:24px;">No zonal routing recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="redas-card">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#dcfce7;color:#15803d;">
                    <i class="fas fa-circle-check"></i>
                </div>
                Recently Approved Returns
            </div>
            <a href="{{ route('admin.hq.archive') }}" style="font-size:.78rem;color:var(--nis-600);font-weight:600;text-decoration:none;">Open archive <i class="fas fa-arrow-right" style="font-size:.65rem;"></i></a>
        </div>
        <div class="card-body no-pad">
            <div style="overflow:auto;">
                <table class="redas-table" style="min-width:680px;">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Officer</th>
                            <th>Scope</th>
                            <th>Category</th>
                            <th>Period</th>
                            <th>Approved</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentApproved as $return)
                            <tr>
                                <td style="font-weight:600;">RET-{{ str_pad($return->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $return->user->name ?? '—' }}</td>
                                <td>{{ $return->scope_code ?? '—' }}</td>
                                <td style="text-transform:capitalize;">{{ $return->category }}</td>
                                <td>{{ $return->return_data['report_period'] ?? '—' }}</td>
                                <td>{{ optional($return->updated_at)->format('d M Y') }}</td>
                                <td style="text-align:right;">
                                    <a href="{{ route('admin.hq.returns.show', $return) }}" class="btn-nis btn-ghost" style="padding:6px 12px;font-size:.75rem;">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" style="text-align:center;color:var(--gray-400);padding:24px;">No approved returns yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
    const trendData = @json($trendChart);

    if (window.Chart) {
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: [
                    {
                        label: 'Submissions',
                        data: trendData.submissions,
                        borderColor: '#1a5632',
                        backgroundColor: 'rgba(26, 86, 50, 0.08)',
                        fill: true,
                        tension: 0.35,
                    },
                    {
                        label: 'Approvals',
                        data: trendData.approvals,
                        borderColor: '#b45309',
                        backgroundColor: 'rgba(180, 83, 9, 0.08)',
                        fill: true,
                        tension: 0.35,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    }
</script>
@endpush

@endsection
