@extends('admin.headquarters.layout')

@section('title', 'Analytics')

@section('content')

<main class="redas-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Analytics</h1>
            <p class="page-subtitle">Consolidated return statistics across all directorates, state commands and CGIS units.</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('admin.hq.reports') }}" class="btn-nis btn-outline-nis">
                <i class="fas fa-file-excel"></i> Generate Report
            </a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:20px;margin-bottom:20px;">
        <div class="redas-card">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#e0f2fe;color:#0369a1;">
                        <i class="fas fa-chart-column"></i>
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
                    <div class="card-head-icon" style="background:#dcfce7;color:#15803d;">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    Status Distribution
                </div>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="260"></canvas>
            </div>
        </div>
    </div>

    <div class="redas-card" style="margin-bottom:20px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#ede9fe;color:#6d28d9;">
                    <i class="fas fa-chart-line"></i>
                </div>
                Submission Trend (Last 12 Months)
            </div>
        </div>
        <div class="card-body">
            <canvas id="trendChart" height="90"></canvas>
        </div>
    </div>

    <div class="redas-card">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#fef3c7;color:#b45309;">
                    <i class="fas fa-stopwatch"></i>
                </div>
                Average Stage Turnaround
            </div>
        </div>
        <div class="card-body no-pad">
            <div style="overflow:auto;">
                <table class="redas-table" style="min-width:420px;">
                    <thead>
                        <tr>
                            <th>Stage Reached</th>
                            <th style="text-align:right;">Avg. Time from Previous Stage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($turnaround as $row)
                            <tr>
                                <td style="font-weight:600;">{{ $row['stage'] }}</td>
                                <td style="text-align:right;">
                                    @if($row['avg_hours'] >= 24)
                                        {{ round($row['avg_hours'] / 24, 1) }} days
                                    @else
                                        {{ $row['avg_hours'] }} hrs
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" style="text-align:center;color:var(--gray-400);padding:24px;">Not enough workflow activity to compute turnaround times yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
    const directorateData = @json($directorateChart);
    const statusData = @json($statusChart);
    const trendData = @json($trendChart);

    if (window.Chart) {
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

        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: [{
                    label: 'Submissions',
                    data: trendData.data,
                    borderColor: '#1a5632',
                    backgroundColor: 'rgba(26, 86, 50, 0.08)',
                    fill: true,
                    tension: 0.35,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    }
</script>
@endpush

@endsection
