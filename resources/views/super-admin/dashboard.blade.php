@extends('super-admin.layout')

@section('title', 'Executive Dashboard')

@section('content')

<main class="redas-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Executive Dashboard</h1>
            <p class="page-subtitle">National picture of all returns — every formation, status and period. This portal is <strong>view-only</strong>.</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('superadmin.returns') }}" class="btn-nis btn-outline-nis">
                <i class="fas fa-folder-open"></i> All Returns
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
            <div class="stat-change neutral">All time, all formations</div>
        </div>
        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-label">Approved</span>
                <span class="stat-icon"><i class="fas fa-circle-check"></i></span>
            </div>
            <div class="stat-value">{{ number_format($stats['approved']) }}</div>
            <div class="stat-change up">Cleared the full workflow</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-label">In Flight</span>
                <span class="stat-icon"><i class="fas fa-clock"></i></span>
            </div>
            <div class="stat-value">{{ number_format($stats['pending']) }}</div>
            <div class="stat-change neutral">Moving through review stages</div>
        </div>
        <div class="stat-card danger">
            <div class="stat-header">
                <span class="stat-label">Returned</span>
                <span class="stat-icon"><i class="fas fa-rotate-left"></i></span>
            </div>
            <div class="stat-value">{{ number_format($stats['returned']) }}</div>
            <div class="stat-change down">Sent back for correction</div>
        </div>
        <div class="stat-card gold">
            <div class="stat-header">
                <span class="stat-label">Avg. Turnaround</span>
                <span class="stat-icon"><i class="fas fa-stopwatch"></i></span>
            </div>
            <div class="stat-value">{{ $stats['avg_turnaround_days'] !== null ? $stats['avg_turnaround_days'] . 'd' : '—' }}</div>
            <div class="stat-change neutral">Submission to final approval</div>
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

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:20px;margin-bottom:20px;">
        <div class="redas-card">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#dcfce7;color:#15803d;">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    Status Distribution
                </div>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="240"></canvas>
            </div>
        </div>

        <div class="redas-card">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#e0f2fe;color:#0369a1;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    Category Split
                </div>
            </div>
            <div class="card-body">
                <canvas id="categoryChart" height="240"></canvas>
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
                    Returns per Directorate
                </div>
            </div>
            <div class="card-body">
                <canvas id="directorateChart" height="260"></canvas>
            </div>
        </div>

        <div class="redas-card">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#fee2e2;color:#b91c1c;">
                        <i class="fas fa-map"></i>
                    </div>
                    Returns per Zone
                </div>
            </div>
            <div class="card-body">
                <canvas id="zoneChart" height="260"></canvas>
            </div>
        </div>

        <div class="redas-card">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#e0e7ff;color:#4338ca;">
                        <i class="fas fa-location-dot"></i>
                    </div>
                    Top 10 State Commands
                </div>
            </div>
            <div class="card-body">
                <canvas id="stateChart" height="260"></canvas>
            </div>
        </div>
    </div>

    <div class="redas-card">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#dcfce7;color:#15803d;">
                    <i class="fas fa-circle-check"></i>
                </div>
                Latest Approved Returns
            </div>
            <a href="{{ route('superadmin.returns', ['status' => 'approved']) }}" style="font-size:.78rem;color:var(--nis-600);font-weight:600;text-decoration:none;">View all <i class="fas fa-arrow-right" style="font-size:.65rem;"></i></a>
        </div>
        <div class="card-body no-pad">
            <div style="overflow:auto;">
                <table class="redas-table" style="min-width:680px;">
                    <thead>
                        <tr>
                            <th>Return</th>
                            <th>Officer</th>
                            <th>Category</th>
                            <th>Period</th>
                            <th>Approved</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestApproved as $return)
                            <tr>
                                <td style="font-weight:600;">RET-{{ str_pad($return->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $return->user->name ?? '—' }}</td>
                                <td style="text-transform:capitalize;">{{ $return->category }}</td>
                                <td>{{ $return->return_data['report_period'] ?? '—' }}</td>
                                <td>{{ optional($return->updated_at)->format('d M Y') }}</td>
                                <td style="text-align:right;">
                                    <a href="{{ route('superadmin.returns.show', ['applicationHash' => \App\Services\HashidService::encode($return->id)]) }}" class="btn-nis btn-ghost" style="padding:6px 12px;font-size:.75rem;">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" style="text-align:center;color:var(--gray-400);padding:24px;">No approved returns yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
    const statusData = @json($statusChart);
    const categoryData = @json($categoryChart);
    const directorateData = @json($directorateChart);
    const zoneData = @json($zoneChart);
    const stateData = @json($stateChart);
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

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: statusData.labels,
                datasets: [{
                    data: statusData.data,
                    backgroundColor: ['#eab308', '#16a34a', '#dc2626'],
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
            },
        });

        new Chart(document.getElementById('categoryChart'), {
            type: 'pie',
            data: {
                labels: categoryData.labels,
                datasets: [{
                    data: categoryData.data,
                    backgroundColor: ['#0369a1', '#1a5632', '#6d28d9'],
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
            },
        });

        new Chart(document.getElementById('directorateChart'), {
            type: 'bar',
            data: {
                labels: directorateData.labels,
                datasets: [{
                    label: 'Returns',
                    data: directorateData.data,
                    backgroundColor: '#1a5632',
                    borderRadius: 6,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });

        new Chart(document.getElementById('zoneChart'), {
            type: 'bar',
            data: {
                labels: zoneData.labels,
                datasets: [{
                    label: 'Returns',
                    data: zoneData.data,
                    backgroundColor: '#b45309',
                    borderRadius: 6,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });

        new Chart(document.getElementById('stateChart'), {
            type: 'bar',
            data: {
                labels: stateData.labels,
                datasets: [{
                    label: 'Returns',
                    data: stateData.data,
                    backgroundColor: '#4338ca',
                    borderRadius: 6,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    }
</script>
@endpush

@endsection
