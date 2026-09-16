@extends('admin.headquarters.layout')

@section('title', 'Archived Documents')

@section('content')

<main class="redas-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Archived Documents</h1>
            <p class="page-subtitle">Fully approved returns and their supporting documents from all formations — <strong>view-only</strong>.</p>
        </div>
    </div>

    <div class="redas-card" style="margin-bottom:20px;">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.hq.archive') }}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;align-items:end;">
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
                    <label style="display:block;font-size:.75rem;font-weight:600;color:var(--gray-500);margin-bottom:4px;">Report Period</label>
                    <input type="month" name="period" value="{{ $filters['period'] ?? '' }}" style="width:100%;padding:8px 10px;border:1px solid var(--gray-200);border-radius:8px;font-size:.85rem;">
                </div>
                <div>
                    <label style="display:block;font-size:.75rem;font-weight:600;color:var(--gray-500);margin-bottom:4px;">Search</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Officer or service number" style="width:100%;padding:8px 10px;border:1px solid var(--gray-200);border-radius:8px;font-size:.85rem;">
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn-nis" style="flex:1;"><i class="fas fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.hq.archive') }}" class="btn-nis btn-ghost"><i class="fas fa-xmark"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="redas-card">
        <div class="card-body no-pad">
            <div style="overflow:auto;">
                <table class="redas-table" style="min-width:820px;">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Formation</th>
                            <th>Officer</th>
                            <th>Period</th>
                            <th style="text-align:center;">Documents</th>
                            <th>Approved</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($archived as $return)
                            @php
                                $data = $return->return_data ?? [];
                                $docCount = count((array) ($data['supporting_documents'] ?? [])) + count((array) ($data['attachments'] ?? []));
                            @endphp
                            <tr>
                                <td style="font-weight:600;">RET-{{ str_pad($return->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $data['command_name'] ?? $return->scope_code ?? '—' }}</td>
                                <td>
                                    {{ $data['reporting_officer'] ?? $return->user->name ?? '—' }}
                                    @if($return->user?->service_number)
                                        <div style="font-size:.72rem;color:var(--gray-400);">{{ $return->user->service_number }}</div>
                                    @endif
                                </td>
                                <td>{{ $data['report_period'] ?? '—' }}</td>
                                <td style="text-align:center;">
                                    <span class="status-badge badge-draft">{{ $docCount }} file{{ $docCount === 1 ? '' : 's' }}</span>
                                </td>
                                <td>{{ optional($return->updated_at)->format('d M Y') }}</td>
                                <td style="text-align:right;">
                                    <a href="{{ route('admin.hq.returns.show', $return) }}" class="btn-nis btn-ghost" style="padding:6px 12px;font-size:.75rem;">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" style="text-align:center;color:var(--gray-400);padding:24px;">No approved returns in the archive yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="padding:14px 20px;border-top:1px solid var(--gray-100);">
                {{ $archived->links() }}
            </div>
        </div>
    </div>
</main>

@endsection
