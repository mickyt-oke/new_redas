@extends('super-admin.layout')

@section('title', 'Executive Dashboard')

@push('styles')
<style>
.executive-page-header {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}
.executive-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}
.executive-tab {
    padding: 10px 22px;
    border-radius: 999px;
    border: 1px solid var(--gray-300);
    background: #fff;
    color: var(--gray-700);
    font-weight: 600;
    font-size: .82rem;
    cursor: pointer;
    transition: all .2s ease;
}
.executive-tab:hover { border-color: var(--nis-500); color: var(--nis-600); }
.executive-tab.active { background: var(--nis-600); color: #fff; border-color: var(--nis-600); }
.executive-tab-content { display: none; }
.executive-tab-content.active { display: block; animation: fadeIn .35s ease; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
.infographic-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 18px;
}
.info-card {
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg, 14px);
    box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,.05));
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.info-card-header {
    padding: 14px 16px 10px;
    border-bottom: 1px solid var(--gray-100);
}
.info-card-title {
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--nis-700);
    margin: 0;
}
.info-card-body {
    padding: 18px 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    min-height: 220px;
}
.kpi-value {
    font-size: 3.2rem;
    font-weight: 800;
    color: var(--nis-600);
    line-height: 1;
}
.kpi-label {
    font-size: .85rem;
    color: var(--gray-500);
    font-weight: 600;
    text-transform: uppercase;
    margin-top: 8px;
}
.kpi-split {
    display: flex;
    gap: 24px;
    margin-top: 16px;
}
.kpi-split-item {
    text-align: center;
}
.kpi-split-value {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--gray-800);
}
.kpi-split-label {
    font-size: .72rem;
    color: var(--gray-500);
    text-transform: uppercase;
}
.chart-wrap {
    width: 100%;
    height: 200px;
    position: relative;
}
.info-card-footer {
    padding: 10px 16px;
    border-top: 1px solid var(--gray-100);
    text-align: center;
}
.btn-breakdown {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 999px;
    background: var(--nis-600);
    color: #fff;
    font-size: .75rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: background .2s;
}
.btn-breakdown:hover { background: var(--nis-700); }
.drill-down {
    display: none;
    width: 100%;
    margin-top: 16px;
    max-height: 220px;
    overflow: auto;
}
.drill-down.open { display: block; }
.drill-down table {
    width: 100%;
    border-collapse: collapse;
    font-size: .78rem;
}
.drill-down th, .drill-down td {
    padding: 6px 8px;
    border: 1px solid var(--gray-200);
    text-align: left;
}
.drill-down th { background: var(--gray-50); font-weight: 700; }
.drill-down td.num { text-align: right; font-variant-numeric: tabular-nums; }
.tab-section-title {
    text-align: center;
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: 999px;
    padding: 10px 28px;
    font-weight: 700;
    color: var(--nis-700);
    display: inline-block;
    margin-bottom: 18px;
}
.tab-section-header { text-align: center; }
.executive-green-frame {
    background: linear-gradient(135deg, #0d5c32 0%, #1a5632 100%);
    border-radius: var(--radius-lg, 14px);
    padding: 22px;
    margin-bottom: 20px;
}
</style>
@endpush

@section('content')

<main class="redas-content">
    <div class="executive-page-header">
        <div>
            <h1 class="page-title">Executive Dashboard (CGIS)</h1>
            <p class="page-subtitle">Nationwide aggregated datasets across all formations, directorates and units.</p>
        </div>
        <a href="{{ route('superadmin.returns') }}" class="btn-nis btn-outline-nis">
            <i class="fas fa-folder-open"></i> All Returns
        </a>
    </div>

    <div class="executive-tabs" id="executiveTabs">
        <button class="executive-tab active" data-tab="headquarter">Headquarter</button>
        <button class="executive-tab" data-tab="zonal">Zonal</button>
        <button class="executive-tab" data-tab="commands">Commands</button>
        <button class="executive-tab" data-tab="borders">Borders</button>
        <button class="executive-tab" data-tab="facilities">Facilities</button>
    </div>

    {{-- HEADQUARTER TAB --}}
    <div class="executive-tab-content active" id="tab-headquarter">
        <div class="executive-green-frame">
            <div class="tab-section-header">
                <div class="tab-section-title">Directorates and Units</div>
            </div>
            <div class="infographic-grid">
                {{-- Total Personnel Strength --}}
                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Total Personnel Strength</h3></div>
                    <div class="info-card-body">
                        <div class="kpi-value" data-count="{{ $executive['personnel']['total'] }}">0</div>
                        <div class="kpi-label">Gross Total</div>
                        <div class="kpi-split">
                            <div class="kpi-split-item">
                                <div class="kpi-split-value">{{ number_format($executive['personnel']['male']) }}</div>
                                <div class="kpi-split-label">Male</div>
                            </div>
                            <div class="kpi-split-item">
                                <div class="kpi-split-value">{{ number_format($executive['personnel']['female']) }}</div>
                                <div class="kpi-split-label">Female</div>
                            </div>
                        </div>
                    </div>
                    <div class="info-card-footer">
                        <button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button>
                    </div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Cadre</th><th class="num">Male</th><th class="num">Female</th><th class="num">Total</th></tr></thead>
                            <tbody>
                                @foreach($executive['personnel']['cadre'] as $row)
                                    <tr><td>{{ $row['label'] }}</td><td class="num">{{ number_format($row['male']) }}</td><td class="num">{{ number_format($row['female']) }}</td><td class="num">{{ number_format($row['total']) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Gender Distribution --}}
                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Gender Distribution</h3></div>
                    <div class="info-card-body">
                        <div class="chart-wrap"><canvas id="genderChart"></canvas></div>
                    </div>
                    <div class="info-card-footer">
                        <button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button>
                    </div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Gender</th><th class="num">Count</th><th class="num">%</th></tr></thead>
                            <tbody>
                                @php $totalGender = max(1, $executive['personnel']['male'] + $executive['personnel']['female']); @endphp
                                <tr><td>Male</td><td class="num">{{ number_format($executive['personnel']['male']) }}</td><td class="num">{{ round(($executive['personnel']['male'] / $totalGender) * 100, 1) }}%</td></tr>
                                <tr><td>Female</td><td class="num">{{ number_format($executive['personnel']['female']) }}</td><td class="num">{{ round(($executive['personnel']['female'] / $totalGender) * 100, 1) }}%</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Rank Distribution --}}
                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Rank Distribution</h3></div>
                    <div class="info-card-body">
                        <div class="chart-wrap"><canvas id="rankChart"></canvas></div>
                    </div>
                    <div class="info-card-footer">
                        <button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button>
                    </div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Rank</th><th class="num">Total</th></tr></thead>
                            <tbody>
                                @foreach($executive['personnel']['rank'] as $row)
                                    @if($row['total'] > 0)
                                        <tr><td>{{ $row['label'] }}</td><td class="num">{{ number_format($row['total']) }}</td></tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Facilities Issued --}}
                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Facilities Issued</h3></div>
                    <div class="info-card-body">
                        <div class="chart-wrap"><canvas id="facilitiesChart"></canvas></div>
                    </div>
                    <div class="info-card-footer">
                        <button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button>
                    </div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Facility</th><th class="num">Count</th></tr></thead>
                            <tbody>
                                <tr><td>Passport Books</td><td class="num">{{ number_format($executive['facilities']['passport_books']) }}</td></tr>
                                <tr><td>e-Visas</td><td class="num">{{ number_format($executive['facilities']['evisa']) }}</td></tr>
                                <tr><td>e-CERPAC</td><td class="num">{{ number_format($executive['facilities']['e_cerpac']) }}</td></tr>
                                <tr><td>e-TWP</td><td class="num">{{ number_format($executive['facilities']['e_twp']) }}</td></tr>
                                <tr><td>ENBIC</td><td class="num">{{ number_format($executive['facilities']['enbic']) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Performance Metrics --}}
                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Performance Metrics</h3></div>
                    <div class="info-card-body">
                        <div class="chart-wrap"><canvas id="performanceChart"></canvas></div>
                    </div>
                    <div class="info-card-footer">
                        <button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button>
                    </div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Indicator</th><th class="num">Value</th></tr></thead>
                            <tbody>
                                <tr><td>Total Returns</td><td class="num">{{ number_format($stats['total']) }}</td></tr>
                                <tr><td>Approved</td><td class="num">{{ number_format($stats['approved']) }}</td></tr>
                                <tr><td>Pending / In Flight</td><td class="num">{{ number_format($stats['pending']) }}</td></tr>
                                <tr><td>Returned</td><td class="num">{{ number_format($stats['returned']) }}</td></tr>
                                <tr><td>Avg Turnaround</td><td class="num">{{ $stats['avg_turnaround_days'] !== null ? $stats['avg_turnaround_days'] . 'd' : '—' }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Monthly Report Compliance --}}
                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Monthly Report Compliance</h3></div>
                    <div class="info-card-body">
                        <div class="chart-wrap"><canvas id="complianceChart"></canvas></div>
                    </div>
                    <div class="info-card-footer">
                        <button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button>
                    </div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Formation</th><th class="num">Total</th><th class="num">Approved</th><th class="num">Pending</th><th class="num">Returned</th></tr></thead>
                            <tbody>
                                @foreach(collect($executive['compliance'])->sortByDesc('total')->take(15) as $row)
                                    <tr><td>{{ $row['formation'] }}</td><td class="num">{{ number_format($row['total']) }}</td><td class="num">{{ number_format($row['approved']) }}</td><td class="num">{{ number_format($row['pending']) }}</td><td class="num">{{ number_format($row['returned']) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ZONAL TAB --}}
    <div class="executive-tab-content" id="tab-zonal">
        <div class="executive-green-frame">
            <div class="tab-section-header">
                <div class="tab-section-title">Zonal HQs (Zone A – H)</div>
            </div>
            <div class="infographic-grid">
                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Total Personnel Strength</h3></div>
                    <div class="info-card-body">
                        <div class="kpi-value" data-count="{{ $executive['personnel']['total'] }}">0</div>
                        <div class="kpi-label">Aggregated</div>
                    </div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Metric</th><th class="num">Count</th></tr></thead>
                            <tbody>
                                <tr><td>By Cadre</td><td class="num">{{ number_format(collect($executive['personnel']['cadre'])->sum('total')) }}</td></tr>
                                <tr><td>By Rank</td><td class="num">{{ number_format(collect($executive['personnel']['rank'])->sum('total')) }}</td></tr>
                                <tr><td>By Gender</td><td class="num">{{ number_format($executive['personnel']['male'] + $executive['personnel']['female']) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Personnel Distribution</h3></div>
                    <div class="info-card-body" style="align-items:flex-start; justify-content:flex-start;">
                        <div style="font-size:.82rem;color:var(--gray-600);line-height:1.8;">
                            <div><i class="fas fa-check-circle" style="color:var(--nis-500);margin-right:6px;"></i>By Cadre</div>
                            <div><i class="fas fa-check-circle" style="color:var(--nis-500);margin-right:6px;"></i>By Rank</div>
                            <div><i class="fas fa-check-circle" style="color:var(--nis-500);margin-right:6px;"></i>By Gender</div>
                            <div><i class="fas fa-check-circle" style="color:var(--nis-500);margin-right:6px;"></i>By Geopolitical Zones</div>
                        </div>
                    </div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Zone</th><th class="num">Total</th><th class="num">Approved</th></tr></thead>
                            <tbody>
                                @foreach($zoneChart['labels'] as $i => $label)
                                    <tr><td>{{ $label }}</td><td class="num">{{ number_format($zoneChart['data'][$i] ?? 0) }}</td><td class="num">—</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Zonal Statistics</h3></div>
                    <div class="info-card-body"><div class="chart-wrap"><canvas id="zonalStatsChart"></canvas></div></div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Zone</th><th class="num">Returns</th></tr></thead>
                            <tbody>
                                @foreach($zoneChart['labels'] as $i => $label)
                                    <tr><td>{{ $label }}</td><td class="num">{{ number_format($zoneChart['data'][$i] ?? 0) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Zonal Statistics</h3></div>
                    <div class="info-card-body"><div class="chart-wrap"><canvas id="zonalTrendChart"></canvas></div></div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Month</th><th class="num">Submitted</th><th class="num">Approved</th></tr></thead>
                            <tbody>
                                @foreach($trendChart['labels'] as $i => $label)
                                    <tr><td>{{ $label }}</td><td class="num">{{ number_format($trendChart['submissions'][$i] ?? 0) }}</td><td class="num">{{ number_format($trendChart['approvals'][$i] ?? 0) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Facilities Issued</h3></div>
                    <div class="info-card-body">
                        <div class="kpi-split" style="flex-wrap:wrap;justify-content:center;gap:14px;">
                            <div class="kpi-split-item"><div class="kpi-split-value">{{ number_format($executive['facilities']['passport_books']) }}</div><div class="kpi-split-label">Passport</div></div>
                            <div class="kpi-split-item"><div class="kpi-split-value">{{ number_format($executive['facilities']['evisa']) }}</div><div class="kpi-split-label">eVisas</div></div>
                            <div class="kpi-split-item"><div class="kpi-split-value">{{ number_format($executive['facilities']['e_cerpac']) }}</div><div class="kpi-split-label">e-CERPAC</div></div>
                            <div class="kpi-split-item"><div class="kpi-split-value">{{ number_format($executive['facilities']['e_twp']) }}</div><div class="kpi-split-label">e-TWP</div></div>
                            <div class="kpi-split-item"><div class="kpi-split-value">{{ number_format($executive['facilities']['enbic']) }}</div><div class="kpi-split-label">ENBIC</div></div>
                        </div>
                    </div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Facility</th><th class="num">Count</th></tr></thead>
                            <tbody>
                                @foreach($executive['facilities']['passport_detail'] as $key => $value)
                                    <tr><td>{{ ucwords(str_replace('_', ' ', $key)) }}</td><td class="num">{{ number_format($value) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Performance Metrics</h3></div>
                    <div class="info-card-body"><div class="chart-wrap"><canvas id="zonalPerformanceChart"></canvas></div></div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Status</th><th class="num">Count</th></tr></thead>
                            <tbody>
                                <tr><td>Approved</td><td class="num">{{ number_format($stats['approved']) }}</td></tr>
                                <tr><td>Pending</td><td class="num">{{ number_format($stats['pending']) }}</td></tr>
                                <tr><td>Returned</td><td class="num">{{ number_format($stats['returned']) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Monthly Report Compliance</h3></div>
                    <div class="info-card-body"><div class="chart-wrap"><canvas id="zonalComplianceChart"></canvas></div></div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Formation</th><th class="num">Approved</th><th class="num">Pending</th></tr></thead>
                            <tbody>
                                @foreach(collect($executive['compliance'])->sortByDesc('approved')->take(15) as $row)
                                    <tr><td>{{ $row['formation'] }}</td><td class="num">{{ number_format($row['approved']) }}</td><td class="num">{{ number_format($row['pending']) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- COMMANDS TAB --}}
    <div class="executive-tab-content" id="tab-commands">
        <div class="executive-green-frame">
            <div class="tab-section-header">
                <div class="tab-section-title">Commands</div>
            </div>
            <div class="infographic-grid">
                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Total Personnel Strength</h3></div>
                    <div class="info-card-body">
                        <div class="kpi-value" data-count="{{ $executive['personnel']['total'] }}">0</div>
                        <div class="kpi-label">Aggregated</div>
                    </div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Source</th><th class="num">Count</th></tr></thead>
                            <tbody>
                                <tr><td>HRM Cadre</td><td class="num">{{ number_format(collect($executive['personnel']['cadre'])->sum('total')) }}</td></tr>
                                <tr><td>HRM Rank</td><td class="num">{{ number_format(collect($executive['personnel']['rank'])->sum('total')) }}</td></tr>
                                <tr><td>Passport Staff</td><td class="num">{{ number_format(collect($executive['personnel']['passport_rank'])->sum('total')) }}</td></tr>
                                <tr><td>Border Staff</td><td class="num">{{ number_format(collect($executive['personnel']['border_staff'])->sum('total')) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Gender Distribution</h3></div>
                    <div class="info-card-body"><div class="chart-wrap"><canvas id="commandsGenderChart"></canvas></div></div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Gender</th><th class="num">Count</th></tr></thead>
                            <tbody>
                                <tr><td>Male</td><td class="num">{{ number_format($executive['personnel']['male']) }}</td></tr>
                                <tr><td>Female</td><td class="num">{{ number_format($executive['personnel']['female']) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Rank Distribution</h3></div>
                    <div class="info-card-body"><div class="chart-wrap"><canvas id="commandsRankChart"></canvas></div></div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Rank</th><th class="num">Total</th></tr></thead>
                            <tbody>
                                @foreach($executive['personnel']['passport_rank'] as $row)
                                    @if($row['total'] > 0)
                                        <tr><td>{{ $row['label'] }}</td><td class="num">{{ number_format($row['total']) }}</td></tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Facilities Issued</h3></div>
                    <div class="info-card-body"><div class="chart-wrap"><canvas id="commandsFacilitiesChart"></canvas></div></div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Facility</th><th class="num">Count</th></tr></thead>
                            <tbody>
                                <tr><td>Passport</td><td class="num">{{ number_format($executive['facilities']['passport_books']) }}</td></tr>
                                <tr><td>e-Visas</td><td class="num">{{ number_format($executive['facilities']['evisa']) }}</td></tr>
                                <tr><td>e-CERPAC</td><td class="num">{{ number_format($executive['facilities']['e_cerpac']) }}</td></tr>
                                <tr><td>e-TWP</td><td class="num">{{ number_format($executive['facilities']['e_twp']) }}</td></tr>
                                <tr><td>ENBIC</td><td class="num">{{ number_format($executive['facilities']['enbic']) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Performance Metrics</h3></div>
                    <div class="info-card-body"><div class="chart-wrap"><canvas id="commandsPerformanceChart"></canvas></div></div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Indicator</th><th class="num">Value</th></tr></thead>
                            <tbody>
                                <tr><td>Approval Rate</td><td class="num">{{ $stats['total'] ? round(($stats['approved'] / $stats['total']) * 100, 1) : 0 }}%</td></tr>
                                <tr><td>Return Rate</td><td class="num">{{ $stats['total'] ? round(($stats['returned'] / $stats['total']) * 100, 1) : 0 }}%</td></tr>
                                <tr><td>Pending Rate</td><td class="num">{{ $stats['total'] ? round(($stats['pending'] / $stats['total']) * 100, 1) : 0 }}%</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Monthly Report Compliance</h3></div>
                    <div class="info-card-body"><div class="chart-wrap"><canvas id="commandsComplianceChart"></canvas></div></div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Command</th><th class="num">Total</th><th class="num">Approved</th></tr></thead>
                            <tbody>
                                @foreach($stateChart['labels'] as $i => $label)
                                    <tr><td>{{ $label }}</td><td class="num">{{ number_format($stateChart['data'][$i] ?? 0) }}</td><td class="num">—</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BORDERS TAB --}}
    <div class="executive-tab-content" id="tab-borders">
        <div class="executive-green-frame">
            <div class="tab-section-header">
                <div class="tab-section-title">Borders</div>
            </div>
            <div class="infographic-grid">
                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Land Border Movement</h3></div>
                    <div class="info-card-body">
                        <div class="kpi-value" data-count="{{ $executive['borders']['land']['arrivals'] + $executive['borders']['land']['departures'] }}">0</div>
                        <div class="kpi-label">Total Movements</div>
                        <div class="kpi-split">
                            <div class="kpi-split-item"><div class="kpi-split-value">{{ number_format($executive['borders']['land']['arrivals']) }}</div><div class="kpi-split-label">Arrivals</div></div>
                            <div class="kpi-split-item"><div class="kpi-split-value">{{ number_format($executive['borders']['land']['departures']) }}</div><div class="kpi-split-label">Departures</div></div>
                        </div>
                    </div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>State</th><th class="num">Arrivals</th><th class="num">Departures</th></tr></thead>
                            <tbody>
                                @foreach(collect($executive['borders']['land_by_state'])->sortByDesc('arrivals')->take(15) as $label => $row)
                                    <tr><td>{{ $label }}</td><td class="num">{{ number_format($row['arrivals']) }}</td><td class="num">{{ number_format($row['departures']) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Airport Movement</h3></div>
                    <div class="info-card-body">
                        <div class="kpi-value" data-count="{{ $executive['borders']['air']['arrivals'] + $executive['borders']['air']['departures'] }}">0</div>
                        <div class="kpi-label">Total Movements</div>
                        <div class="kpi-split">
                            <div class="kpi-split-item"><div class="kpi-split-value">{{ number_format($executive['borders']['air']['arrivals']) }}</div><div class="kpi-split-label">Arrivals</div></div>
                            <div class="kpi-split-item"><div class="kpi-split-value">{{ number_format($executive['borders']['air']['departures']) }}</div><div class="kpi-split-label">Departures</div></div>
                        </div>
                    </div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Airport</th><th class="num">Arrivals</th><th class="num">Departures</th></tr></thead>
                            <tbody>
                                @foreach(collect($executive['borders']['air_by_airport'])->sortByDesc('arrivals')->take(15) as $label => $row)
                                    <tr><td>{{ $label }}</td><td class="num">{{ number_format($row['arrivals']) }}</td><td class="num">{{ number_format($row['departures']) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Seaport & Marine</h3></div>
                    <div class="info-card-body">
                        <div class="kpi-value" data-count="{{ $executive['borders']['sea']['arrivals'] + $executive['borders']['sea']['departures'] }}">0</div>
                        <div class="kpi-label">Total Movements</div>
                        <div class="kpi-split" style="flex-wrap:wrap;gap:12px;">
                            <div class="kpi-split-item"><div class="kpi-split-value">{{ number_format($executive['borders']['sea']['passenger_arrivals']) }}</div><div class="kpi-split-label">Pass. Arr</div></div>
                            <div class="kpi-split-item"><div class="kpi-split-value">{{ number_format($executive['borders']['sea']['passenger_departures']) }}</div><div class="kpi-split-label">Pass. Dep</div></div>
                            <div class="kpi-split-item"><div class="kpi-split-value">{{ number_format($executive['borders']['sea']['crew_arrivals']) }}</div><div class="kpi-split-label">Crew Arr</div></div>
                            <div class="kpi-split-item"><div class="kpi-split-value">{{ number_format($executive['borders']['sea']['crew_departures']) }}</div><div class="kpi-split-label">Crew Dep</div></div>
                        </div>
                    </div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Metric</th><th class="num">Count</th></tr></thead>
                            <tbody>
                                <tr><td>Passenger Arrivals</td><td class="num">{{ number_format($executive['borders']['sea']['passenger_arrivals']) }}</td></tr>
                                <tr><td>Passenger Departures</td><td class="num">{{ number_format($executive['borders']['sea']['passenger_departures']) }}</td></tr>
                                <tr><td>Crew Arrivals</td><td class="num">{{ number_format($executive['borders']['sea']['crew_arrivals']) }}</td></tr>
                                <tr><td>Crew Departures</td><td class="num">{{ number_format($executive['borders']['sea']['crew_departures']) }}</td></tr>
                                <tr><td>Boats Arrived</td><td class="num">{{ number_format($executive['borders']['sea']['boats_arrived']) }}</td></tr>
                                <tr><td>Boats Departed</td><td class="num">{{ number_format($executive['borders']['sea']['boats_departed']) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Border Movement by Type</h3></div>
                    <div class="info-card-body"><div class="chart-wrap"><canvas id="borderTypeChart"></canvas></div></div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Type</th><th class="num">Movements</th></tr></thead>
                            <tbody>
                                <tr><td>Land</td><td class="num">{{ number_format($executive['borders']['land']['arrivals'] + $executive['borders']['land']['departures']) }}</td></tr>
                                <tr><td>Air</td><td class="num">{{ number_format($executive['borders']['air']['arrivals'] + $executive['borders']['air']['departures']) }}</td></tr>
                                <tr><td>Sea</td><td class="num">{{ number_format($executive['borders']['sea']['arrivals'] + $executive['borders']['sea']['departures']) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Gender at Borders</h3></div>
                    <div class="info-card-body"><div class="chart-wrap"><canvas id="borderGenderChart"></canvas></div></div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Gender</th><th class="num">Land</th></tr></thead>
                            <tbody>
                                <tr><td>Male</td><td class="num">{{ number_format($executive['borders']['land']['male']) }}</td></tr>
                                <tr><td>Female</td><td class="num">{{ number_format($executive['borders']['land']['female']) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Monthly Report Compliance</h3></div>
                    <div class="info-card-body"><div class="chart-wrap"><canvas id="borderComplianceChart"></canvas></div></div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Formation</th><th class="num">Total</th><th class="num">Approved</th></tr></thead>
                            <tbody>
                                @foreach(collect($executive['compliance'])->sortByDesc('total')->take(15) as $row)
                                    <tr><td>{{ $row['formation'] }}</td><td class="num">{{ number_format($row['total']) }}</td><td class="num">{{ number_format($row['approved']) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FACILITIES TAB --}}
    <div class="executive-tab-content" id="tab-facilities">
        <div class="executive-green-frame">
            <div class="tab-section-header">
                <div class="tab-section-title">Facilities</div>
            </div>
            <div class="infographic-grid">
                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Passport Books Issued</h3></div>
                    <div class="info-card-body">
                        <div class="kpi-value" data-count="{{ $executive['facilities']['passport_books'] }}">0</div>
                        <div class="kpi-label">Total Books</div>
                    </div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Type</th><th class="num">Count</th></tr></thead>
                            <tbody>
                                @foreach($executive['facilities']['passport_detail'] as $key => $value)
                                    @if($value > 0)
                                        <tr><td>{{ ucwords(str_replace('_', ' ', $key)) }}</td><td class="num">{{ number_format($value) }}</td></tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">e-Visas & e-TWP</h3></div>
                    <div class="info-card-body">
                        <div class="kpi-split" style="flex-wrap:wrap;gap:16px;">
                            <div class="kpi-split-item"><div class="kpi-split-value">{{ number_format($executive['facilities']['evisa']) }}</div><div class="kpi-split-label">e-Visas</div></div>
                            <div class="kpi-split-item"><div class="kpi-split-value">{{ number_format($executive['facilities']['e_twp']) }}</div><div class="kpi-split-label">e-TWP</div></div>
                        </div>
                    </div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Facility</th><th class="num">Count</th></tr></thead>
                            <tbody>
                                <tr><td>e-Visa Applications</td><td class="num">{{ number_format($executive['facilities']['evisa']) }}</td></tr>
                                <tr><td>e-TWP Applications</td><td class="num">{{ number_format($executive['facilities']['e_twp']) }}</td></tr>
                                <tr><td>e-CERPAC Cards Issued</td><td class="num">{{ number_format($executive['facilities']['e_cerpac']) }}</td></tr>
                                <tr><td>ENBIC</td><td class="num">{{ number_format($executive['facilities']['enbic']) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">CERPAC & ENBIC</h3></div>
                    <div class="info-card-body"><div class="chart-wrap"><canvas id="cerpacEnbicChart"></canvas></div></div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Facility</th><th class="num">Count</th></tr></thead>
                            <tbody>
                                <tr><td>e-CERPAC</td><td class="num">{{ number_format($executive['facilities']['e_cerpac']) }}</td></tr>
                                <tr><td>ENBIC</td><td class="num">{{ number_format($executive['facilities']['enbic']) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Facilities by Directorate</h3></div>
                    <div class="info-card-body"><div class="chart-wrap"><canvas id="facilitiesDirectorateChart"></canvas></div></div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Directorate</th><th class="num">Returns</th></tr></thead>
                            <tbody>
                                @foreach($directorateChart['labels'] as $i => $label)
                                    <tr><td>{{ $label }}</td><td class="num">{{ number_format($directorateChart['data'][$i] ?? 0) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Performance Metrics</h3></div>
                    <div class="info-card-body"><div class="chart-wrap"><canvas id="facilitiesPerformanceChart"></canvas></div></div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Facility</th><th class="num">Share %</th></tr></thead>
                            <tbody>
                                @foreach($executive['performance']['facilities_ratio'] as $key => $value)
                                    <tr><td>{{ ucwords(str_replace('_', ' ', $key)) }}</td><td class="num">{{ $value }}%</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3 class="info-card-title">Monthly Report Compliance</h3></div>
                    <div class="info-card-body"><div class="chart-wrap"><canvas id="facilitiesComplianceChart"></canvas></div></div>
                    <div class="info-card-footer"><button type="button" class="btn-breakdown" onclick="toggleDrillDown(this)"><i class="fas fa-list"></i> View Breakdown</button></div>
                    <div class="drill-down">
                        <table>
                            <thead><tr><th>Month</th><th class="num">Submitted</th><th class="num">Approved</th></tr></thead>
                            <tbody>
                                @foreach($trendChart['labels'] as $i => $label)
                                    <tr><td>{{ $label }}</td><td class="num">{{ number_format($trendChart['submissions'][$i] ?? 0) }}</td><td class="num">{{ number_format($trendChart['approvals'][$i] ?? 0) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection

@push('scripts')
<script>
    const executiveData = @json($executive);
    const statsData = @json($stats);
    const trendData = @json($trendChart);
    const zoneData = @json($zoneChart);
    const stateData = @json($stateChart);
    const directorateData = @json($directorateChart);
    const statusData = @json($statusChart);
    const categoryData = @json($categoryChart);

    const NIS_GREEN = '#006633';
    const NIS_LIGHT = '#2d9e61';
    const NIS_GOLD = '#c5922a';
    const NIS_RED = '#dc2626';
    const NIS_BLUE = '#1d4ed8';
    const NIS_PURPLE = '#7c3aed';

    function chartDefaults() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { font: { family: 'Inter, sans-serif', size: 12 } } },
                tooltip: { cornerRadius: 8, padding: 10 },
            },
        };
    }

    function initExecutiveCharts() {
        // Gender distribution
        new Chart(document.getElementById('genderChart'), {
            type: 'doughnut',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    data: [executiveData.personnel.male, executiveData.personnel.female],
                    backgroundColor: [NIS_BLUE, NIS_RED],
                    borderWidth: 0,
                    hoverOffset: 6,
                }],
            },
            options: { ...chartDefaults(), cutout: '65%', plugins: { ...chartDefaults().plugins, legend: { position: 'bottom' } } },
        });

        // Rank distribution
        const rankLabels = [];
        const rankValues = [];
        Object.values(executiveData.personnel.rank).forEach(r => { if (r.total > 0) { rankLabels.push(r.label); rankValues.push(r.total); } });
        new Chart(document.getElementById('rankChart'), {
            type: 'bar',
            data: { labels: rankLabels, datasets: [{ label: 'Personnel', data: rankValues, backgroundColor: NIS_GREEN, borderRadius: 5 }] },
            options: { ...chartDefaults(), plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { ticks: { font: { size: 10 } } } } },
        });

        // Facilities issued
        new Chart(document.getElementById('facilitiesChart'), {
            type: 'bar',
            data: {
                labels: ['Passport', 'e-Visas', 'e-CERPAC', 'e-TWP', 'ENBIC'],
                datasets: [{
                    label: 'Issued',
                    data: [executiveData.facilities.passport_books, executiveData.facilities.evisa, executiveData.facilities.e_cerpac, executiveData.facilities.e_twp, executiveData.facilities.enbic],
                    backgroundColor: [NIS_GREEN, NIS_BLUE, NIS_GOLD, NIS_PURPLE, NIS_RED],
                    borderRadius: 5,
                }],
            },
            options: { ...chartDefaults(), plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } },
        });

        // Performance metrics
        new Chart(document.getElementById('performanceChart'), {
            type: 'pie',
            data: {
                labels: ['Approved', 'Pending', 'Returned'],
                datasets: [{
                    data: [statsData.approved, statsData.pending, statsData.returned],
                    backgroundColor: [NIS_GREEN, NIS_GOLD, NIS_RED],
                    borderWidth: 0,
                }],
            },
            options: { ...chartDefaults(), plugins: { ...chartDefaults().plugins, legend: { position: 'bottom' } } },
        });

        // Compliance stacked horizontal bar (top formations)
        const complianceRows = Object.values(executiveData.compliance).sort((a, b) => b.total - a.total).slice(0, 10);
        new Chart(document.getElementById('complianceChart'), {
            type: 'bar',
            data: {
                labels: complianceRows.map(r => r.formation),
                datasets: [
                    { label: 'Approved', data: complianceRows.map(r => r.approved), backgroundColor: NIS_GREEN },
                    { label: 'Pending', data: complianceRows.map(r => r.pending), backgroundColor: NIS_GOLD },
                    { label: 'Returned', data: complianceRows.map(r => r.returned), backgroundColor: NIS_RED },
                ],
            },
            options: { ...chartDefaults(), indexAxis: 'y', scales: { x: { stacked: true, beginAtZero: true }, y: { stacked: true } } },
        });

        // Zonal charts
        new Chart(document.getElementById('zonalStatsChart'), {
            type: 'doughnut',
            data: { labels: zoneData.labels, datasets: [{ data: zoneData.data, backgroundColor: [NIS_GREEN, NIS_BLUE, NIS_GOLD, NIS_RED, NIS_PURPLE, '#0891b2', '#ea580c', '#7c3aed'], borderWidth: 0 }] },
            options: { ...chartDefaults(), plugins: { ...chartDefaults().plugins, legend: { position: 'bottom' } } },
        });

        new Chart(document.getElementById('zonalTrendChart'), {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: [
                    { label: 'Submitted', data: trendData.submissions, borderColor: NIS_GREEN, backgroundColor: 'rgba(0,102,51,0.08)', fill: true, tension: 0.4 },
                    { label: 'Approved', data: trendData.approvals, borderColor: NIS_GOLD, backgroundColor: 'rgba(197,146,42,0.08)', fill: true, tension: 0.4 },
                ],
            },
            options: { ...chartDefaults(), scales: { y: { beginAtZero: true } } },
        });

        new Chart(document.getElementById('zonalPerformanceChart'), {
            type: 'doughnut',
            data: { labels: ['Approved', 'Pending', 'Returned'], datasets: [{ data: [statsData.approved, statsData.pending, statsData.returned], backgroundColor: [NIS_GREEN, NIS_GOLD, NIS_RED], borderWidth: 0 }] },
            options: { ...chartDefaults(), plugins: { ...chartDefaults().plugins, legend: { position: 'bottom' } } },
        });

        new Chart(document.getElementById('zonalComplianceChart'), {
            type: 'bar',
            data: { labels: complianceRows.map(r => r.formation), datasets: [{ label: 'Approved', data: complianceRows.map(r => r.approved), backgroundColor: NIS_GREEN, borderRadius: 5 }] },
            options: { ...chartDefaults(), indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } },
        });

        // Commands charts
        new Chart(document.getElementById('commandsGenderChart'), {
            type: 'doughnut',
            data: { labels: ['Male', 'Female'], datasets: [{ data: [executiveData.personnel.male, executiveData.personnel.female], backgroundColor: [NIS_BLUE, NIS_RED], borderWidth: 0 }] },
            options: { ...chartDefaults(), cutout: '65%', plugins: { ...chartDefaults().plugins, legend: { position: 'bottom' } } },
        });

        const passportRankLabels = [];
        const passportRankValues = [];
        Object.values(executiveData.personnel.passport_rank).forEach(r => { if (r.total > 0) { passportRankLabels.push(r.label); passportRankValues.push(r.total); } });
        new Chart(document.getElementById('commandsRankChart'), {
            type: 'bar',
            data: { labels: passportRankLabels, datasets: [{ label: 'Personnel', data: passportRankValues, backgroundColor: NIS_GREEN, borderRadius: 5 }] },
            options: { ...chartDefaults(), plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { ticks: { font: { size: 9 } } } } },
        });

        new Chart(document.getElementById('commandsFacilitiesChart'), {
            type: 'bar',
            data: {
                labels: ['Passport', 'e-Visas', 'e-CERPAC', 'e-TWP', 'ENBIC'],
                datasets: [{
                    data: [executiveData.facilities.passport_books, executiveData.facilities.evisa, executiveData.facilities.e_cerpac, executiveData.facilities.e_twp, executiveData.facilities.enbic],
                    backgroundColor: [NIS_GREEN, NIS_BLUE, NIS_GOLD, NIS_PURPLE, NIS_RED],
                    borderRadius: 5,
                }],
            },
            options: { ...chartDefaults(), plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } },
        });

        new Chart(document.getElementById('commandsPerformanceChart'), {
            type: 'pie',
            data: { labels: ['Approved', 'Pending', 'Returned'], datasets: [{ data: [statsData.approved, statsData.pending, statsData.returned], backgroundColor: [NIS_GREEN, NIS_GOLD, NIS_RED], borderWidth: 0 }] },
            options: { ...chartDefaults(), plugins: { ...chartDefaults().plugins, legend: { position: 'bottom' } } },
        });

        new Chart(document.getElementById('commandsComplianceChart'), {
            type: 'bar',
            data: { labels: stateData.labels, datasets: [{ label: 'Returns', data: stateData.data, backgroundColor: NIS_GREEN, borderRadius: 5 }] },
            options: { ...chartDefaults(), indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } },
        });

        // Borders charts
        new Chart(document.getElementById('borderTypeChart'), {
            type: 'doughnut',
            data: {
                labels: ['Land', 'Air', 'Sea'],
                datasets: [{
                    data: [
                        executiveData.borders.land.arrivals + executiveData.borders.land.departures,
                        executiveData.borders.air.arrivals + executiveData.borders.air.departures,
                        executiveData.borders.sea.arrivals + executiveData.borders.sea.departures,
                    ],
                    backgroundColor: [NIS_GREEN, NIS_BLUE, NIS_GOLD],
                    borderWidth: 0,
                }],
            },
            options: { ...chartDefaults(), plugins: { ...chartDefaults().plugins, legend: { position: 'bottom' } } },
        });

        new Chart(document.getElementById('borderGenderChart'), {
            type: 'doughnut',
            data: { labels: ['Male', 'Female'], datasets: [{ data: [executiveData.borders.land.male, executiveData.borders.land.female], backgroundColor: [NIS_BLUE, NIS_RED], borderWidth: 0 }] },
            options: { ...chartDefaults(), cutout: '65%', plugins: { ...chartDefaults().plugins, legend: { position: 'bottom' } } },
        });

        new Chart(document.getElementById('borderComplianceChart'), {
            type: 'bar',
            data: { labels: complianceRows.map(r => r.formation), datasets: [{ label: 'Total Returns', data: complianceRows.map(r => r.total), backgroundColor: NIS_GREEN, borderRadius: 5 }] },
            options: { ...chartDefaults(), indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } },
        });

        // Facilities charts
        new Chart(document.getElementById('cerpacEnbicChart'), {
            type: 'bar',
            data: { labels: ['e-CERPAC', 'ENBIC'], datasets: [{ data: [executiveData.facilities.e_cerpac, executiveData.facilities.enbic], backgroundColor: [NIS_GOLD, NIS_RED], borderRadius: 5 }] },
            options: { ...chartDefaults(), plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } },
        });

        new Chart(document.getElementById('facilitiesDirectorateChart'), {
            type: 'bar',
            data: { labels: directorateData.labels, datasets: [{ label: 'Returns', data: directorateData.data, backgroundColor: NIS_GREEN, borderRadius: 5 }] },
            options: { ...chartDefaults(), indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } },
        });

        new Chart(document.getElementById('facilitiesPerformanceChart'), {
            type: 'pie',
            data: {
                labels: ['Passport', 'e-Visas', 'e-CERPAC', 'e-TWP', 'ENBIC'],
                datasets: [{
                    data: Object.values(executiveData.performance.facilities_ratio),
                    backgroundColor: [NIS_GREEN, NIS_BLUE, NIS_GOLD, NIS_PURPLE, NIS_RED],
                    borderWidth: 0,
                }],
            },
            options: { ...chartDefaults(), plugins: { ...chartDefaults().plugins, legend: { position: 'bottom' } } },
        });

        new Chart(document.getElementById('facilitiesComplianceChart'), {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: [
                    { label: 'Submitted', data: trendData.submissions, borderColor: NIS_GREEN, backgroundColor: 'rgba(0,102,51,0.08)', fill: true, tension: 0.4 },
                    { label: 'Approved', data: trendData.approvals, borderColor: NIS_GOLD, backgroundColor: 'rgba(197,146,42,0.08)', fill: true, tension: 0.4 },
                ],
            },
            options: { ...chartDefaults(), scales: { y: { beginAtZero: true } } },
        });
    }

    function toggleDrillDown(btn) {
        const card = btn.closest('.info-card');
        const drill = card.querySelector('.drill-down');
        drill.classList.toggle('open');
        btn.innerHTML = drill.classList.contains('open')
            ? '<i class="fas fa-chevron-up"></i> Hide Breakdown'
            : '<i class="fas fa-list"></i> View Breakdown';
    }

    document.addEventListener('DOMContentLoaded', () => {
        const tabs = document.querySelectorAll('.executive-tab');
        const contents = document.querySelectorAll('.executive-tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
            });
        });

        if (window.Chart) {
            initExecutiveCharts();
        }
    });
</script>
@endpush
