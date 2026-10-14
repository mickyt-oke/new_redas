@extends('super-admin.layout')

@section('title', 'Return Data Explorer')

@php
$currentRoute = request()->routeIs('cgis.data-explorer') ? 'cgis.data-explorer' : 'superadmin.data-explorer';

$sortColumns = ['id', 'category', 'scope_code', 'report_period', 'reporting_officer', 'command_name', 'status', 'created_at'];
$sortUrls = [];
$sortIcons = [];
foreach ($sortColumns as $col) {
    $direction = ($filters['sort_by'] ?? '') === $col && ($filters['sort_direction'] ?? 'desc') === 'desc' ? 'asc' : 'desc';
    $sortUrls[$col] = route($currentRoute, array_merge(request()->except(['sort_by', 'sort_direction']), ['sort_by' => $col, 'sort_direction' => $direction]));
    if (($filters['sort_by'] ?? '') !== $col) {
        $sortIcons[$col] = '<i class="fas fa-sort" style="color:var(--gray-300);"></i>';
    } else {
        $sortIcons[$col] = ($filters['sort_direction'] ?? 'desc') === 'desc'
            ? '<i class="fas fa-sort-down" style="color:var(--nis-600);"></i>'
            : '<i class="fas fa-sort-up" style="color:var(--nis-600);"></i>';
    }
}
$csvUrl = route($currentRoute, array_merge(request()->all(), ['export' => 'csv']));
$excelUrl = route($currentRoute, array_merge(request()->all(), ['export' => 'excel']));
@endphp

@push('styles')
<style>
.explorer-page-header {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}
.explorer-filter-frame {
    background: linear-gradient(135deg, #0d5c32 0%, #1a5632 100%);
    border-radius: var(--radius-lg, 14px);
    padding: 22px;
    margin-bottom: 20px;
}
.explorer-filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 14px;
}
.explorer-filter-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.explorer-filter-field label {
    font-size: .72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: rgba(255,255,255,.85);
}
.explorer-filter-field input,
.explorer-filter-field select {
    padding: 9px 12px;
    border-radius: var(--radius-md, 8px);
    border: 1px solid rgba(255,255,255,.25);
    background: rgba(255,255,255,.95);
    color: var(--gray-800);
    font-size: .82rem;
}
.explorer-filter-field input:focus,
.explorer-filter-field select:focus {
    outline: none;
    border-color: var(--nis-300);
    box-shadow: 0 0 0 3px rgba(255,255,255,.15);
}
.explorer-filter-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 18px;
    align-items: center;
}
.btn-nis {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 16px;
    border-radius: var(--radius-md, 8px);
    font-size: .82rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: background .2s;
}
.btn-nis-primary { background: var(--nis-600); color: #fff; }
.btn-nis-primary:hover { background: var(--nis-700); }
.btn-nis-outline {
    background: transparent;
    color: #fff;
    border: 1px solid rgba(255,255,255,.6);
}
.btn-nis-outline:hover { background: rgba(255,255,255,.1); }
.btn-nis-light {
    background: #fff;
    color: var(--nis-700);
    border: 1px solid var(--gray-200);
}
.btn-nis-light:hover { background: var(--gray-50); }
.explorer-summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 14px;
    margin-bottom: 20px;
}
.explorer-summary-card {
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg, 14px);
    padding: 16px;
    text-align: center;
    box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,.05));
}
.explorer-summary-value {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--nis-600);
    line-height: 1;
}
.explorer-summary-label {
    font-size: .72rem;
    color: var(--gray-500);
    font-weight: 600;
    text-transform: uppercase;
    margin-top: 6px;
}
.explorer-results-frame {
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg, 14px);
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,.05));
}
.explorer-section-title {
    font-size: .85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--nis-700);
    margin: 0 0 14px;
}
.explorer-section-header {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}
.explorer-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 14px;
    border-bottom: 1px solid var(--gray-200);
    padding-bottom: 10px;
}
.explorer-tab {
    padding: 6px 14px;
    border-radius: 999px;
    border: 1px solid var(--gray-300);
    background: #fff;
    color: var(--gray-700);
    font-weight: 600;
    font-size: .75rem;
    cursor: pointer;
    transition: all .2s ease;
}
.explorer-tab:hover { border-color: var(--nis-500); color: var(--nis-600); }
.explorer-tab.active { background: var(--nis-600); color: #fff; border-color: var(--nis-600); }
.explorer-table-wrap { overflow-x: auto; }
.explorer-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .82rem;
}
.explorer-table th, .explorer-table td {
    padding: 10px 12px;
    border-bottom: 1px solid var(--gray-200);
    text-align: left;
    vertical-align: top;
}
.explorer-table th {
    background: var(--gray-50);
    font-weight: 700;
    color: var(--gray-700);
    white-space: nowrap;
}
.explorer-table th a {
    color: inherit;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.explorer-table th a:hover { color: var(--nis-600); }
.explorer-table tr:hover td { background: var(--gray-50); }
.explorer-table .num { text-align: right; font-variant-numeric: tabular-nums; }
.explorer-table .status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
}
.status-approved { background: #dcfce7; color: #166534; }
.status-pending { background: #fef9c3; color: #854d0e; }
.status-returned { background: #fee2e2; color: #991b1b; }
.status-rejected { background: #f3f4f6; color: #374151; }
.explorer-empty {
    text-align: center;
    padding: 40px 20px;
    color: var(--gray-500);
}
.explorer-empty i { font-size: 2rem; color: var(--gray-300); margin-bottom: 10px; }
.explorer-pagination { margin-top: 16px; }
.field-chip {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 999px;
    background: var(--gray-100);
    color: var(--gray-600);
    font-size: .72rem;
    font-weight: 600;
    margin: 2px 4px 2px 0;
}

/* Data dictionary */
.dictionary-frame {
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg, 14px);
    padding: 18px;
    margin-bottom: 20px;
}
.dictionary-title {
    font-size: .85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--nis-700);
    margin: 0 0 12px;
}
.dictionary-category {
    margin-bottom: 14px;
}
.dictionary-category-title {
    font-size: .78rem;
    font-weight: 700;
    color: var(--gray-700);
    margin-bottom: 8px;
    text-transform: uppercase;
}
.dictionary-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.dictionary-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 10px;
    border-radius: 999px;
    background: var(--gray-100);
    border: 1px solid var(--gray-200);
    color: var(--gray-700);
    font-size: .75rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s;
}
.dictionary-chip:hover { background: var(--nis-50); border-color: var(--nis-300); color: var(--nis-700); }
.dictionary-chip.active { background: var(--nis-600); border-color: var(--nis-600); color: #fff; }
.dictionary-chip-score { font-size: .65rem; opacity: .8; }

/* Scorecard */
.scorecard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 14px;
    margin-bottom: 16px;
}
.scorecard-item {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 1px solid #bbf7d0;
    border-radius: var(--radius-md, 8px);
    padding: 14px;
    text-align: center;
}
.scorecard-value {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--nis-700);
    line-height: 1;
}
.scorecard-label {
    font-size: .7rem;
    color: var(--gray-600);
    font-weight: 600;
    text-transform: uppercase;
    margin-top: 6px;
}
.scorecard-rank-list {
    list-style: none;
    margin: 0;
    padding: 0;
}
.scorecard-rank-list li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    border-bottom: 1px solid var(--gray-100);
    font-size: .8rem;
}
.scorecard-rank-list li:last-child { border-bottom: none; }
.scorecard-rank-bar {
    flex: 1;
    height: 6px;
    background: var(--gray-100);
    border-radius: 999px;
    margin: 0 10px;
    overflow: hidden;
}
.scorecard-rbar-fill {
    height: 100%;
    background: var(--nis-500);
    border-radius: 999px;
}

/* Toolbar */
.explorer-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 14px;
}
.btn-toolbar {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: var(--radius-md, 8px);
    border: 1px solid var(--gray-300);
    background: #fff;
    color: var(--gray-700);
    font-size: .75rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all .2s;
}
.btn-toolbar:hover { background: var(--gray-50); border-color: var(--nis-400); color: var(--nis-700); }

@media print {
    .redas-sidebar, .redas-topbar, .explorer-filter-frame, .explorer-page-header, .explorer-toolbar, .explorer-pagination, .dictionary-frame, .btn-breakdown, .btn-toolbar { display: none !important; }
    .redas-main { margin-left: 0 !important; padding: 0 !important; }
    .explorer-results-frame, .explorer-summary-grid, .scorecard-grid { break-inside: avoid; page-break-inside: avoid; }
    body { background: #fff; }
}
</style>
@endpush

@section('content')

<main class="redas-content" id="explorerReport">
    <div class="explorer-page-header">
        <div>
            <h1 class="page-title">Return Data Explorer</h1>
            <p class="page-subtitle">Query, sort and aggregate return data across all formations, zones and states.</p>
        </div>
        <a href="{{ route('superadmin.dashboard') }}" class="btn-nis btn-nis-light">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a>
    </div>

    {{-- Data Dictionary --}}
    <div class="dictionary-frame">
        <h3 class="dictionary-title"><i class="fas fa-book"></i> Data Dictionary — Predefined Search Criteria</h3>
        @foreach($dictionary as $category)
            <div class="dictionary-category">
                <div class="dictionary-category-title">{{ $category['label'] }}</div>
                <div class="dictionary-chips">
                    @foreach($category['entries'] as $entry)
                        <a href="{{ route($currentRoute, array_merge(request()->except(['dictionary_key', 'field_path', 'field_operator', 'field_value', 'sum_path', 'page']), ['dictionary_key' => $entry['key']])) }}"
                           class="dictionary-chip {{ ($dictionaryEntry['key'] ?? '') === $entry['key'] ? 'active' : '' }}"
                           title="{{ $entry['path'] }}">
                            {{ $entry['label'] }}
                            <span class="dictionary-chip-score"><i class="fas fa-chart-bar"></i></span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
        @if($dictionaryEntry)
            <div style="margin-top:10px;font-size:.78rem;color:var(--gray-600);">
                <strong>Selected:</strong> {{ $dictionaryEntry['label'] }} — <code>{{ $dictionaryEntry['aggregate_path'] ?? $dictionaryEntry['path'] }}</code>
            </div>
        @endif
    </div>

    {{-- Filters --}}
    <div class="explorer-filter-frame">
        <form method="GET" action="{{ route($currentRoute) }}">
            <div class="explorer-filter-grid">
                <div class="explorer-filter-field">
                    <label for="category">Formation Category</label>
                    <select id="category" name="category">
                        <option value="">All Categories</option>
                        <option value="state" {{ ($filters['category'] ?? '') === 'state' ? 'selected' : '' }}>State Commands</option>
                        <option value="directorate" {{ ($filters['category'] ?? '') === 'directorate' ? 'selected' : '' }}>Directorates</option>
                        <option value="cgis" {{ ($filters['category'] ?? '') === 'cgis' ? 'selected' : '' }}>CGIS Units</option>
                        <option value="zonal" {{ ($filters['category'] ?? '') === 'zonal' ? 'selected' : '' }}>Zonal</option>
                    </select>
                </div>

                <div class="explorer-filter-field">
                    <label for="scope_code">Formation / Scope</label>
                    <input id="scope_code" type="text" name="scope_code" value="{{ $filters['scope_code'] ?? '' }}" placeholder="e.g. LA, passport">
                </div>

                <div class="explorer-filter-field">
                    <label for="zonal_code">Zone</label>
                    <input id="zonal_code" type="text" name="zonal_code" value="{{ $filters['zonal_code'] ?? '' }}" placeholder="e.g. A, B">
                </div>

                <div class="explorer-filter-field">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="approved" {{ ($filters['status'] ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="returned" {{ ($filters['status'] ?? '') === 'returned' ? 'selected' : '' }}>Returned</option>
                        <option value="rejected" {{ ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="explorer-filter-field">
                    <label for="period_from">Period From</label>
                    <input id="period_from" type="month" name="period_from" value="{{ $filters['period_from'] ?? '' }}">
                </div>

                <div class="explorer-filter-field">
                    <label for="period_to">Period To</label>
                    <input id="period_to" type="month" name="period_to" value="{{ $filters['period_to'] ?? '' }}">
                </div>

                <div class="explorer-filter-field">
                    <label for="search">Free-text Search</label>
                    <input id="search" type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Officer, command, period">
                </div>

                <div class="explorer-filter-field">
                    <label for="group_by">Group By</label>
                    <select id="group_by" name="group_by">
                        <option value="formation" {{ ($filters['group_by'] ?? 'formation') === 'formation' ? 'selected' : '' }}>Formation</option>
                        <option value="zone" {{ ($filters['group_by'] ?? '') === 'zone' ? 'selected' : '' }}>Zone</option>
                        <option value="state" {{ ($filters['group_by'] ?? '') === 'state' ? 'selected' : '' }}>State</option>
                        <option value="directorate" {{ ($filters['group_by'] ?? '') === 'directorate' ? 'selected' : '' }}>Directorate</option>
                        <option value="category" {{ ($filters['group_by'] ?? '') === 'category' ? 'selected' : '' }}>Category</option>
                        <option value="status" {{ ($filters['group_by'] ?? '') === 'status' ? 'selected' : '' }}>Status</option>
                    </select>
                </div>
            </div>

            <div style="margin-top:14px;">
                <div style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:rgba(255,255,255,.85);margin-bottom:8px;">
                    <i class="fas fa-sliders-h"></i> Data-Field Filter
                </div>
                <div class="explorer-filter-grid" style="grid-template-columns: 2fr 1fr 1fr;">
                    <div class="explorer-filter-field">
                        <label for="field_path">Field Path</label>
                        <input id="field_path" type="text" name="field_path" value="{{ $filters['field_path'] ?? '' }}" placeholder="e.g. hrm.cadre.comptroller.male">
                    </div>
                    <div class="explorer-filter-field">
                        <label for="field_operator">Operator</label>
                        <select id="field_operator" name="field_operator">
                            <option value="">—</option>
                            @foreach(['=' => 'Equals', '!=' => 'Not equals', '>' => 'Greater than', '<' => 'Less than', '>=' => 'Greater or equal', '<=' => 'Less or equal', 'contains' => 'Contains text'] as $op => $label)
                                <option value="{{ $op }}" {{ ($filters['field_operator'] ?? '') === $op ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="explorer-filter-field">
                        <label for="field_value">Value</label>
                        <input id="field_value" type="text" name="field_value" value="{{ $filters['field_value'] ?? '' }}" placeholder="e.g. 0, male">
                    </div>
                </div>
            </div>

            <input type="hidden" name="sort_by" value="{{ $filters['sort_by'] ?? 'created_at' }}">
            <input type="hidden" name="sort_direction" value="{{ $filters['sort_direction'] ?? 'desc' }}">
            @if($filters['dictionary_key'] ?? false)
                <input type="hidden" name="dictionary_key" value="{{ $filters['dictionary_key'] }}">
            @endif

            <div class="explorer-filter-actions">
                <button type="submit" class="btn-nis btn-nis-primary"><i class="fas fa-search"></i> Search</button>
                <a href="{{ route($currentRoute) }}" class="btn-nis btn-nis-outline">Reset</a>
            </div>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="explorer-summary-grid">
        <div class="explorer-summary-card">
            <div class="explorer-summary-value">{{ number_format($summary['total']) }}</div>
            <div class="explorer-summary-label">Total Matches</div>
        </div>
        <div class="explorer-summary-card">
            <div class="explorer-summary-value">{{ number_format($summary['approved']) }}</div>
            <div class="explorer-summary-label">Approved</div>
        </div>
        <div class="explorer-summary-card">
            <div class="explorer-summary-value">{{ number_format($summary['pending']) }}</div>
            <div class="explorer-summary-label">Pending</div>
        </div>
        <div class="explorer-summary-card">
            <div class="explorer-summary-value">{{ number_format($summary['returned']) }}</div>
            <div class="explorer-summary-label">Returned</div>
        </div>
        <div class="explorer-summary-card">
            <div class="explorer-summary-value">{{ number_format($summary['formations']) }}</div>
            <div class="explorer-summary-label">Formations</div>
        </div>
        <div class="explorer-summary-card">
            <div class="explorer-summary-value">{{ $summary['latest'] ? \Carbon\Carbon::parse($summary['latest'])->format('M Y') : '—' }}</div>
            <div class="explorer-summary-label">Latest Return</div>
        </div>
    </div>

    {{-- National Scorecard --}}
    @if($scorecard && $scorecard['count'] > 0)
        <div class="explorer-results-frame">
            <div class="explorer-section-header">
                <h3 class="explorer-section-title" style="margin:0;"><i class="fas fa-trophy"></i> National Scorecard — {{ $scorecard['label'] }}</h3>
                <div class="explorer-toolbar">
                    <button type="button" class="btn-toolbar" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                    <button type="button" class="btn-toolbar" onclick="copyScorecardToClipboard()"><i class="fas fa-copy"></i> Copy</button>
                    <a href="{{ $csvUrl }}" class="btn-toolbar"><i class="fas fa-file-csv"></i> CSV</a>
                    <a href="{{ $excelUrl }}" class="btn-toolbar"><i class="fas fa-file-excel"></i> Excel</a>
                </div>
            </div>
            <div class="scorecard-grid">
                <div class="scorecard-item">
                    <div class="scorecard-value">{{ number_format($scorecard['total']) }}</div>
                    <div class="scorecard-label">National Total</div>
                </div>
                <div class="scorecard-item">
                    <div class="scorecard-value">{{ number_format($scorecard['average'], 2) }}</div>
                    <div class="scorecard-label">Average Per Formation</div>
                </div>
                <div class="scorecard-item">
                    <div class="scorecard-value">{{ number_format($scorecard['count']) }}</div>
                    <div class="scorecard-label">Formations Reporting</div>
                </div>
                <div class="scorecard-item">
                    <div class="scorecard-value">{{ $scorecard['max']['label'] ?? '—' }}</div>
                    <div class="scorecard-label">Top: {{ number_format($scorecard['max']['value'] ?? 0) }}</div>
                </div>
                <div class="scorecard-item">
                    <div class="scorecard-value">{{ $scorecard['min']['label'] ?? '—' }}</div>
                    <div class="scorecard-label">Lowest: {{ number_format($scorecard['min']['value'] ?? 0) }}</div>
                </div>
            </div>
            @if($scorecard['top_formations']->isNotEmpty())
                @php $maxValue = $scorecard['top_formations']->max(); @endphp
                <h4 class="explorer-section-title" style="font-size:.75rem;margin-top:6px;">Top Formations</h4>
                <ul class="scorecard-rank-list" id="scorecardRankList">
                    @foreach($scorecard['top_formations'] as $label => $value)
                        <li>
                            <span>{{ $label }}</span>
                            <div class="scorecard-rank-bar"><div class="scorecard-rbar-fill" style="width:{{ $maxValue > 0 ? round(($value / $maxValue) * 100, 1) : 0 }}%;"></div></div>
                            <span class="num" style="font-weight:700;">{{ number_format($value) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    {{-- Aggregations --}}
    <div class="explorer-results-frame">
        <div class="explorer-section-header">
            <h3 class="explorer-section-title" style="margin:0;">Aggregated Reports — By {{ ucfirst($groupBy) }}</h3>
            <div style="display:flex;align-items:center;gap:10px;">
                <label for="agg_sort_by" style="font-size:.72rem;font-weight:600;color:var(--gray-600);">Sort by</label>
                <form method="GET" action="{{ route($currentRoute) }}" id="aggSortForm">
                    @foreach(request()->except(['agg_sort_by', 'agg_sort_direction']) as $key => $value)
                        @if(is_array($value))
                            @foreach($value as $subValue)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $subValue }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <select id="agg_sort_by" name="agg_sort_by" onchange="document.getElementById('aggSortForm').submit()" style="padding:5px 8px;border-radius:6px;border:1px solid var(--gray-300);font-size:.75rem;">
                        @foreach(['label' => 'Name', 'total' => 'Total', 'approved' => 'Approved', 'pending' => 'Pending', 'returned' => 'Returned', 'rejected' => 'Rejected', 'sum' => 'Metric Sum'] as $key => $label)
                            <option value="{{ $key }}" {{ ($filters['agg_sort_by'] ?? 'total') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="agg_sort_direction" onchange="document.getElementById('aggSortForm').submit()" style="padding:5px 8px;border-radius:6px;border:1px solid var(--gray-300);font-size:.75rem;">
                        <option value="desc" {{ ($filters['agg_sort_direction'] ?? 'desc') === 'desc' ? 'selected' : '' }}>Desc</option>
                        <option value="asc" {{ ($filters['agg_sort_direction'] ?? '') === 'asc' ? 'selected' : '' }}>Asc</option>
                    </select>
                </form>
            </div>
        </div>
        @if($aggregations->isEmpty())
            <div class="explorer-empty">
                <div><i class="fas fa-chart-bar"></i></div>
                <p>No aggregation data for the current filters.</p>
            </div>
        @else
            <div class="explorer-table-wrap">
                <table class="explorer-table" id="aggregationTable">
                    <thead>
                        <tr>
                            <th>{{ ucfirst($groupBy) }}</th>
                            <th class="num">Total</th>
                            <th class="num">Approved</th>
                            <th class="num">Pending</th>
                            <th class="num">Returned</th>
                            <th class="num">Rejected</th>
                            @if($filters['sum_path'] ?? false)
                                <th class="num">Sum ({{ $filters['sum_path'] }})</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($aggregations as $row)
                            <tr>
                                <td>{{ $row['label'] }}</td>
                                <td class="num">{{ number_format($row['total']) }}</td>
                                <td class="num">{{ number_format($row['approved']) }}</td>
                                <td class="num">{{ number_format($row['pending']) }}</td>
                                <td class="num">{{ number_format($row['returned']) }}</td>
                                <td class="num">{{ number_format($row['rejected']) }}</td>
                                @if($filters['sum_path'] ?? false)
                                    <td class="num">{{ number_format($row['sum']) }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Matching Records --}}
    <div class="explorer-results-frame">
        <div class="explorer-section-header">
            <h3 class="explorer-section-title" style="margin:0;">Matching Records</h3>
            <div class="explorer-toolbar">
                <button type="button" class="btn-toolbar" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                <a href="{{ $csvUrl }}" class="btn-toolbar"><i class="fas fa-file-csv"></i> CSV</a>
                <a href="{{ $excelUrl }}" class="btn-toolbar"><i class="fas fa-file-excel"></i> Excel</a>
            </div>
        </div>
        @if($records->isEmpty())
            <div class="explorer-empty">
                <div><i class="fas fa-inbox"></i></div>
                <p>No records match the selected criteria.</p>
            </div>
        @else
            <div class="explorer-table-wrap">
                <table class="explorer-table">
                    <thead>
                        <tr>
                            <th><a href="{{ $sortUrls['id'] }}">Return ID {!! $sortIcons['id'] !!}</a></th>
                            <th><a href="{{ $sortUrls['category'] }}">Category {!! $sortIcons['category'] !!}</a></th>
                            <th><a href="{{ $sortUrls['scope_code'] }}">Formation {!! $sortIcons['scope_code'] !!}</a></th>
                            <th><a href="{{ $sortUrls['report_period'] }}">Period {!! $sortIcons['report_period'] !!}</a></th>
                            <th><a href="{{ $sortUrls['reporting_officer'] }}">Reporting Officer {!! $sortIcons['reporting_officer'] !!}</a></th>
                            <th><a href="{{ $sortUrls['command_name'] }}">Command {!! $sortIcons['command_name'] !!}</a></th>
                            <th><a href="{{ $sortUrls['status'] }}">Status {!! $sortIcons['status'] !!}</a></th>
                            <th><a href="{{ $sortUrls['created_at'] }}">Submitted {!! $sortIcons['created_at'] !!}</a></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $application)
                            @php $data = $application->return_data ?? []; @endphp
                            <tr>
                                <td>#{{ $application->id }}</td>
                                <td>{{ ucfirst($application->category) }}</td>
                                <td>
                                    {{ strtoupper($application->scope_code) }}
                                    @if($application->zonal_code)
                                        <span class="field-chip">Zone {{ strtoupper($application->zonal_code) }}</span>
                                    @endif
                                </td>
                                <td>{{ $data['report_period'] ?? '—' }}</td>
                                <td>{{ $data['reporting_officer'] ?? '—' }}</td>
                                <td>{{ $data['command_name'] ?? '—' }}</td>
                                <td>
                                    <span class="status status-{{ $application->status }}">
                                        {{ $stageLabels[$application->status] ?? ucfirst($application->status) }}
                                    </span>
                                </td>
                                <td>{{ $application->created_at?->format('M d, Y') ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('superadmin.returns.show', \App\Services\HashidService::encode($application->id)) }}" class="btn-nis btn-nis-light" style="padding:5px 10px;font-size:.72rem;">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="explorer-pagination">
                {{ $paginator->links() }}
            </div>
        @endif
    </div>
</main>

@endsection

@push('scripts')
<script>
    function copyScorecardToClipboard() {
        const list = document.getElementById('scorecardRankList');
        if (!list) return;

        const rows = [];
        list.querySelectorAll('li').forEach(li => {
            const label = li.querySelector('span:first-child')?.innerText || '';
            const value = li.querySelector('span:last-child')?.innerText || '';
            rows.push(label + '\t' + value);
        });

        const text = 'National Scorecard\n' + rows.join('\n');
        navigator.clipboard.writeText(text).then(() => {
            alert('Scorecard copied to clipboard');
        }).catch(() => {
            alert('Could not copy automatically. Please select and copy manually.');
        });
    }
</script>
@endpush
