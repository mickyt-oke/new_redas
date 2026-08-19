@include('partials.visa-header')

<main class="redas-content">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="fas fa-chart-line"></i>
                Visa &amp; Residence Analytics &amp; Reports
            </h1>
            <p class="page-subtitle">Analyze cumulative performance data and generate formatted reports for the Visa Directorate.</p>
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
        .redas-sidebar, .redas-topbar, .topbar, .btn-nis, button, a, .page-header, .grid-2 {
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
    <div class="stats-grid animate-fade-up" style="margin-bottom: 24px;">
        <div class="stat-card info">
            <div class="stat-header">
                <span class="stat-label">Total Submissions</span>
                <span class="stat-icon"><i class="fas fa-folder-open"></i></span>
            </div>
            <div class="stat-value">{{ $totalSubmissions }}</div>
            <div class="stat-change neutral">Annual Returns</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-label">Residence Permits (Total)</span>
                <span class="stat-icon"><i class="fas fa-id-card"></i></span>
            </div>
            <div class="stat-value">{{ number_format($cumulativeResidencePermits) }}</div>
            <div class="stat-change neutral">Across all periods</div>
        </div>

        <div class="stat-card gold">
            <div class="stat-header">
                <span class="stat-label">Visas Processed (Total)</span>
                <span class="stat-icon"><i class="fas fa-passport"></i></span>
            </div>
            <div class="stat-value">{{ number_format($cumulativeVisaApplications) }}</div>
            <div class="stat-change neutral">Across all periods</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-label">CERPAC Issued (Total)</span>
                <span class="stat-icon"><i class="fas fa-address-card"></i></span>
            </div>
            <div class="stat-value">{{ number_format($cumulativeCerpacIssued) }}</div>
            <div class="stat-change neutral">Across all periods</div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid-2 animate-fade-up">
        
        <!-- Left: Annual Trends Chart -->
        <div class="redas-card">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);">
                        <i class="fas fa-chart-area"></i>
                    </div>
                    Annual Performance Trend
                </div>
            </div>
            <div class="card-body" style="height: 320px; position: relative;">
                @if(empty($yearlyData))
                    <div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--gray-400);font-size:.84rem;">
                        No report data available to graph.
                    </div>
                @else
                    <canvas id="visaTrendChart"></canvas>
                @endif
            </div>
        </div>

        <!-- Right: Custom Report Builder -->
        <div class="redas-card">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:var(--purple-50);color:var(--purple-600);">
                        <i class="fas fa-file-export"></i>
                    </div>
                    Visa Directorate Report Builder
                </div>
            </div>
            <div class="card-body">
                <form id="reportGenForm" onsubmit="event.preventDefault(); REDAS.showToast('Generating report...', 'info'); setTimeout(() => REDAS.showToast('Report downloaded successfully.', 'success'), 1500);">
                    <div class="fg" style="margin-bottom: 14px;">
                        <label class="form-label-nis" style="font-weight: 600; font-size: 0.8rem; color: var(--gray-700); margin-bottom: 4px; display: block;">Report Target</label>
                        <select class="ni" id="repYear" required>
                            <option value="all">Cumulative Statistics (All Years)</option>
                            @foreach($yearlyData as $yd)
                                <option value="{{ $yd['year'] }}">Year {{ $yd['year'] }} Annual Return</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="fg" style="margin-bottom: 14px;">
                        <label class="form-label-nis" style="font-weight: 600; font-size: 0.8rem; color: var(--gray-700); margin-bottom: 4px; display: block;">Sections to Include</label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-top: 4px;">
                            @foreach([
                                'staff' => 'Staff Strength',
                                'emigrant' => 'e-Migrant Centre',
                                'quota' => 'Quota Admin',
                                'residence' => 'Residence Permits',
                                'ftz' => 'Free Trade Zone',
                                'cerpac' => 'CERPAC Issued',
                                'visa' => 'Visa Applications',
                                'trv' => 'TRV/PRV/e-TWP',
                                'ecowas' => 'ECOWAS Affairs',
                                'african' => 'African Affairs'
                            ] as $val => $label)
                                <label style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 500; cursor: pointer; color: var(--gray-700);">
                                    <input type="checkbox" name="rep_sections[]" value="{{ $val }}" checked style="accent-color: var(--nis-600);">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="fg" style="margin-bottom: 16px;">
                        <label class="form-label-nis" style="font-weight: 600; font-size: 0.8rem; color: var(--gray-700); margin-bottom: 4px; display: block;">Output Format</label>
                        <div style="display: flex; gap: 16px; margin-top: 4px;">
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 0.82rem; cursor: pointer;">
                                <input type="radio" name="rep_format" value="pdf" checked style="accent-color: var(--nis-600);">
                                <i class="fas fa-file-pdf" style="color: #dc2626;"></i> PDF
                            </label>
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 0.82rem; cursor: pointer;">
                                <input type="radio" name="rep_format" value="excel" style="accent-color: var(--nis-600);">
                                <i class="fas fa-file-excel" style="color: #16a34a;"></i> Excel
                            </label>
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 0.82rem; cursor: pointer;">
                                <input type="radio" name="rep_format" value="csv" style="accent-color: var(--nis-600);">
                                <i class="fas fa-file-csv" style="color: #2563eb;"></i> CSV
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn-nis btn-primary-nis full-width">
                        <i class="fas fa-download"></i> Generate and Download
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- Data Table of Year-on-Year Metrics -->
    <div class="redas-card mt-4 animate-fade-up">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);">
                    <i class="fas fa-table"></i>
                </div>
                Year-on-Year Comparative Data
            </div>
        </div>
        <div class="card-body no-pad">
            @if(empty($yearlyData))
                <div style="padding: 30px; text-align: center; color: var(--gray-400);">
                    No submission data records found.
                </div>
            @else
                <table class="redas-table">
                    <thead>
                        <tr>
                            <th>Reporting Period</th>
                            <th>Status</th>
                            <th>Residence Permits</th>
                            <th>Visa Applications</th>
                            <th>CERPAC Issued</th>
                            <th>e-Migrants</th>
                            <th>Report Date</th>
                            <th style="width: 80px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $statusConfig = [
                            'pending'  => 'badge-pending',
                            'approved' => 'badge-approved',
                            'draft'    => 'badge-draft',
                            'queried'  => 'badge-review',
                            'rejected' => 'badge-rejected'
                        ];
                        @endphp
                        @foreach($yearlyData as $yd)
                            <tr>
                                <td><strong>Year {{ $yd['year'] }}</strong></td>
                                <td>
                                    <span class="status-badge {{ $statusConfig[$yd['status']] ?? 'badge-draft' }}">
                                        {{ ucfirst($yd['status']) }}
                                    </span>
                                </td>
                                <td>{{ number_format($yd['residence']) }}</td>
                                <td>{{ number_format($yd['visas']) }}</td>
                                <td>{{ number_format($yd['cerpac']) }}</td>
                                <td>{{ number_format($yd['migrants']) }}</td>
                                <td style="font-size: 0.78rem; color: var(--gray-500);">{{ $yd['updated_at'] }}</td>
                                <td style="text-align: center;">
                                    <button class="btn-nis btn-ghost btn-sm" onclick="REDAS.showToast('Preparing PDF download...', 'info')" title="Download PDF Report">
                                        <i class="fas fa-file-pdf" style="color: #dc2626;"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</main>

@if(!empty($yearlyData))
@php
    // Prepare reversed lists for chronological chart order
    $chronologicalData = array_reverse($yearlyData);
    $chartLabels = array_map(fn($d) => "Year " . $d['year'], $chronologicalData);
    $chartResidence = array_map(fn($d) => $d['residence'], $chronologicalData);
    $chartVisas = array_map(fn($d) => $d['visas'], $chronologicalData);
    $chartCerpac = array_map(fn($d) => $d['cerpac'], $chronologicalData);
    $chartMigrants = array_map(fn($d) => $d['migrants'], $chronologicalData);
@endphp
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('visaTrendChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Residence Permits',
                        data: {!! json_encode($chartResidence) !!},
                        borderColor: '#0B6B3A',
                        backgroundColor: 'rgba(11, 107, 58, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Visa Applications',
                        data: {!! json_encode($chartVisas) !!},
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'CERPAC Issued',
                        data: {!! json_encode($chartCerpac) !!},
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'e-Migrants',
                        data: {!! json_encode($chartMigrants) !!},
                        borderColor: '#a855f7',
                        backgroundColor: 'rgba(168, 85, 247, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            font: {
                                size: 10
                            }
                        }
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
