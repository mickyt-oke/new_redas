@extends('admin.headquarters.layout')

@section('title', 'All Returns')

@section('content')

<main class="redas-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">All Returns</h1>
            <p class="page-subtitle">Every return across directorates, state commands and CGIS units — <strong>view-only</strong>.</p>
        </div>
    </div>

    <div class="redas-card" style="margin-bottom:20px;">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.hq.returns') }}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;align-items:end;">
                <div>
                    <label style="display:block;font-size:.75rem;font-weight:600;color:var(--gray-500);margin-bottom:4px;">Category</label>
                    <select name="category" style="width:100%;padding:8px 10px;border:1px solid var(--gray-200);border-radius:8px;font-size:.85rem;">
                        <option value="">All categories</option>
                        <option value="state" @selected(($filters['category'] ?? '') === 'state')>State Commands</option>
                        <option value="directorate" @selected(($filters['category'] ?? '') === 'directorate')>Directorates</option>
                        <option value="cgis" @selected(($filters['category'] ?? '') === 'cgis')>CGIS Units</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:.75rem;font-weight:600;color:var(--gray-500);margin-bottom:4px;">Formation</label>
                    <select name="formation" style="width:100%;padding:8px 10px;border:1px solid var(--gray-200);border-radius:8px;font-size:.85rem;">
                        <option value="">All formations</option>
                        @foreach($directorates as $slug => $name)
                            <option value="{{ $slug }}" @selected(($filters['formation'] ?? '') === $slug)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:.75rem;font-weight:600;color:var(--gray-500);margin-bottom:4px;">Status</label>
                    <select name="status" style="width:100%;padding:8px 10px;border:1px solid var(--gray-200);border-radius:8px;font-size:.85rem;">
                        <option value="">All statuses</option>
                        <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Pending</option>
                        <option value="approved" @selected(($filters['status'] ?? '') === 'approved')>Approved</option>
                        <option value="returned" @selected(($filters['status'] ?? '') === 'returned')>Returned</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:.75rem;font-weight:600;color:var(--gray-500);margin-bottom:4px;">Workflow Stage</label>
                    <select name="stage" style="width:100%;padding:8px 10px;border:1px solid var(--gray-200);border-radius:8px;font-size:.85rem;">
                        <option value="">All stages</option>
                        @foreach($stageLabels as $stage => $label)
                            <option value="{{ $stage }}" @selected(($filters['stage'] ?? '') === $stage)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:.75rem;font-weight:600;color:var(--gray-500);margin-bottom:4px;">Report Period</label>
                    <input type="month" name="period" value="{{ $filters['period'] ?? '' }}" style="width:100%;padding:8px 10px;border:1px solid var(--gray-200);border-radius:8px;font-size:.85rem;">
                </div>
                <div>
                    <label style="display:block;font-size:.75rem;font-weight:600;color:var(--gray-500);margin-bottom:4px;">Search</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Officer, service no., command" style="width:100%;padding:8px 10px;border:1px solid var(--gray-200);border-radius:8px;font-size:.85rem;">
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn-nis" style="flex:1;"><i class="fas fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.hq.returns') }}" class="btn-nis btn-ghost"><i class="fas fa-xmark"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="redas-card">
        <div class="card-body no-pad">
            <div style="overflow:auto;">
                <table class="redas-table" style="min-width:860px;">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Formation</th>
                            <th>Category</th>
                            <th>Officer</th>
                            <th>Period</th>
                            <th>Stage</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $return)
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
                                <td style="text-transform:capitalize;">{{ $return->category }}</td>
                                <td>
                                    {{ $data['reporting_officer'] ?? $return->user->name ?? '—' }}
                                    @if($return->user?->service_number)
                                        <div style="font-size:.72rem;color:var(--gray-400);">{{ $return->user->service_number }}</div>
                                    @endif
                                </td>
                                <td>{{ $data['report_period'] ?? '—' }}</td>
                                <td style="text-transform:capitalize;">{{ str_replace('_', ' ', $return->workflow_stage) }}</td>
                                <td><span class="status-badge {{ $badge }}">{{ ucfirst($return->status) }}</span></td>
                                <td>{{ optional($return->created_at)->format('d M Y') }}</td>
                                <td style="text-align:right;">
                                    <a href="{{ route('admin.hq.returns.show', $return) }}" class="btn-nis btn-ghost" style="padding:6px 12px;font-size:.75rem;">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" style="text-align:center;color:var(--gray-400);padding:24px;">No returns match the selected filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="padding:14px 20px;border-top:1px solid var(--gray-100);">
                {{ $applications->links() }}
            </div>
        </div>
    </div>
</main>

@endsection
