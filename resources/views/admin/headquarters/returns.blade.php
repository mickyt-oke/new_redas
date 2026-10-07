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

    @include('partials.admin-submissions-table', [
        'submissions' => $applications,
        'title' => 'Filtered Returns',
        'emptyMessage' => 'No returns match the selected filters.',
        'previewRoute' => 'admin.hq.returns.show',
        'documentRoute' => 'admin.hq.returns.document',
        'downloadRoute' => 'admin.hq.returns.download',
    ])

    <div style="padding:14px 20px;">
        {{ $applications->links() }}
    </div>
</main>

@endsection
