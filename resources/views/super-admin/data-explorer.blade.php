@extends('super-admin.layout')

@section('title', 'Return Data Explorer')

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
</style>
@endpush

@section('content')

<main class="redas-content">
    <div class="explorer-page-header">
        <div>
            <h1 class="page-title">Return Data Explorer</h1>
            <p class="page-subtitle">Query and aggregate return data across all formations, zones and states.</p>
        </div>
        <a href="{{ route('superadmin.dashboard') }}" class="btn-nis btn-nis-light">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a>
    </div>

    {{-- Filters --}}
    <div class="explorer-filter-frame">
        <form method="GET" action="{{ request()->routeIs('cgis.data-explorer') ? route('cgis.data-explorer') : route('superadmin.data-explorer') }}">
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

            <div class="explorer-filter-actions">
                <button type="submit" class="btn-nis btn-nis-primary"><i class="fas fa-search"></i> Search</button>
                <a href="{{ request()->routeIs('cgis.data-explorer') ? route('cgis.data-explorer') : route('superadmin.data-explorer') }}" class="btn-nis btn-nis-outline">Reset</a>
                <button type="submit" name="export" value="csv" class="btn-nis btn-nis-outline" style="margin-left:auto;">
                    <i class="fas fa-file-csv"></i> Export CSV
                </button>
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

    {{-- Aggregations --}}
    <div class="explorer-results-frame">
        <h3 class="explorer-section-title">Aggregated Reports — By {{ ucfirst($groupBy) }}</h3>
        @if($aggregations->isEmpty())
            <div class="explorer-empty">
                <div><i class="fas fa-chart-bar"></i></div>
                <p>No aggregation data for the current filters.</p>
            </div>
        @else
            <div class="explorer-table-wrap">
                <table class="explorer-table">
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
        <h3 class="explorer-section-title">Matching Records</h3>
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
                            <th>Return ID</th>
                            <th>Formation</th>
                            <th>Period</th>
                            <th>Reporting Officer</th>
                            <th>Command</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $application)
                            @php $data = $application->return_data ?? []; @endphp
                            <tr>
                                <td>#{{ $application->id }}</td>
                                <td>
                                    <span class="field-chip">{{ ucfirst($application->category) }}</span>
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
