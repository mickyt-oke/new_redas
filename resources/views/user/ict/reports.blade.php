@include('partials.ict-header')

<main class="redas-content animate-fade-up">

    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="fas fa-chart-bar"></i>
                ICT &amp; Cybersecurity Analytics &amp; Reports
            </h1>
            <p class="page-subtitle">Cumulative metrics and trends across all submitted returns.</p>
        </div>
        <button onclick="window.print()" class="btn-nis btn-ghost" style="background:#e0f2fe;border:1px solid #bae6fd;color:#0369a1;">
            <i class="fas fa-print"></i> Print Summary
        </button>
    </div>

    <style>
    @media print {
        body {
            background: white !important;
            color: black !important;
        }
        .redas-sidebar, .redas-topbar, .topbar, .btn-nis, button, a, .page-header, #ictYearlyTrendChart, .redas-card:has(#ictYearlyTrendChart) {
            display: none !important;
        }
        .redas-content {
            padding: 0 !important;
            margin: 0 !important;
        }
        .redas-main {
            margin-left: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .redas-card {
            border: none !important;
            box-shadow: none !important;
        }
        .redas-table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        .redas-table th, .redas-table td {
            border: 1px solid #ddd !important;
            padding: 8px !important;
            font-size: 10pt !important;
        }
    }
    </style>

    <!-- Stats row -->
    <div class="stats-grid animate-fade-up" style="margin-bottom:20px;">
        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-label">Total Submissions</span>
                <span class="stat-icon"><i class="fas fa-copy"></i></span>
            </div>
            <div class="stat-value">{{ number_format($totalSubmissions) }}</div>
            <div class="stat-change neutral">Returns filed</div>
        </div>

        <div class="stat-card gold">
            <div class="stat-header">
                <span class="stat-label">Cumulative Projects</span>
                <span class="stat-icon"><i class="fas fa-bars-progress"></i></span>
            </div>
            <div class="stat-value">{{ number_format($cumulativeProjects) }}</div>
            <div class="stat-change neutral">All active &amp; completed</div>
        </div>

        <div class="stat-card danger">
            <div class="stat-header">
                <span class="stat-label">Cumulative Incidents</span>
                <span class="stat-icon"><i class="fas fa-triangle-exclamation"></i></span>
            </div>
            <div class="stat-value">{{ number_format($cumulativeIncidents) }}</div>
            <div class="stat-change neutral">Systems &amp; Network faults</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-label">Cumulative Maintenance</span>
                <span class="stat-icon"><i class="fas fa-screwdriver-wrench"></i></span>
            </div>
            <div class="stat-value">{{ number_format($cumulativeMaintenance) }}</div>
            <div class="stat-change neutral">Hardware devices serviced</div>
        </div>

        <div class="stat-card info">
            <div class="stat-header">
                <span class="stat-label">Cumulative ID Cards</span>
                <span class="stat-icon"><i class="fas fa-id-card"></i></span>
            </div>
            <div class="stat-value">{{ number_format($cumulativeIdCards) }}</div>
            <div class="stat-change neutral">Cards &amp; e-Doc processed</div>
        </div>
    </div>

    <!-- Chart -->
    <div class="redas-card animate-fade-up" style="margin-bottom:24px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);">
                    <i class="fas fa-chart-line"></i>
                </div>
                Yearly Trend Analysis (Operational Metrics)
            </div>
        </div>
        <div class="card-body" style="height: 320px; position: relative;">
            @if(count($yearlyData) === 0)
                <div style="height:100%; display:flex; align-items:center; justify-content:center; color:var(--gray-400);">
                    No historical data available. Submit reports to build analytics.
                </div>
            @else
                <canvas id="ictYearlyTrendChart"></canvas>
            @endif
        </div>
    </div>

    <!-- Data Table -->
    <div class="redas-card animate-fade-up">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:var(--purple-50);color:var(--purple-600);">
                    <i class="fas fa-table"></i>
                </div>
                Yearly Operational Data Breakdowns
            </div>
        </div>
        <div class="card-body no-pad">
            @if(count($yearlyData) === 0)
                <div style="padding: 24px; text-align: center; color: var(--gray-400);">No statistics recorded yet.</div>
            @else
                <table class="redas-table">
                    <thead>
                        <tr>
                            <th>Reporting Year</th>
                            <th>Projects &amp; Systems</th>
                            <th>System Incidents</th>
                            <th>Maintenance Orders</th>
                            <th>ID Cards &amp; e-Doc</th>
                            <th>Status</th>
                            <th>Submitted On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($yearlyData as $row)
                        <tr>
                            <td><strong>Year {{ $row['year'] }}</strong></td>
                            <td>{{ number_format($row['projects']) }} Projects</td>
                            <td>{{ number_format($row['incidents']) }} Incidents</td>
                            <td>{{ number_format($row['maintenance']) }} Assets</td>
                            <td>{{ number_format($row['id_cards']) }} Cards</td>
                            <td>
                                @php
                                $cls = $row['status'] === 'approved' ? 'badge-approved' : ($row['status'] === 'pending' ? 'badge-pending' : 'badge-draft');
                                @endphp
                                <span class="status-badge {{ $cls }}">{{ strtoupper($row['status']) }}</span>
                            </td>
                            <td>{{ $row['updated_at'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</main>

@if(count($yearlyData) > 0)
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('ictYearlyTrendChart');
    if (ctx) {
        @php
        // Sort reverse order to show chronological trend in chart (e.g. 2024, 2025, 2026)
        $chartData = array_reverse($yearlyData);
        $labels = array_column($chartData, 'year');
        $projects = array_column($chartData, 'projects');
        $incidents = array_column($chartData, 'incidents');
        $maintenance = array_column($chartData, 'maintenance');
        $idCards = array_column($chartData, 'id_cards');
        @endphp

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [
                    {
                        label: 'Projects',
                        data: {!! json_encode($projects) !!},
                        borderColor: '#f59e0b',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.3,
                        pointRadius: 4
                    },
                    {
                        label: 'Incidents',
                        data: {!! json_encode($incidents) !!},
                        borderColor: '#ef4444',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.3,
                        pointRadius: 4
                    },
                    {
                        label: 'Maintenance',
                        data: {!! json_encode($maintenance) !!},
                        borderColor: '#0B6B3A',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.3,
                        pointRadius: 4
                    },
                    {
                        label: 'ID Cards (x1000)',
                        data: {!! json_encode(array_map(function($v) { return $v / 1000.0; }, $idCards)) !!},
                        borderColor: '#3b82f6',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.3,
                        pointRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>
@endif

@include('partials.footer')
