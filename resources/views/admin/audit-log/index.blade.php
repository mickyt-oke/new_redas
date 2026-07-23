@include('partials.header3')

<main class="redas-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Audit Trail</h1>
            <p class="page-subtitle">
                Security and administrative event logs.
            </p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.settings.index') }}" class="btn-nis btn-ghost">
                <i class="fas fa-cog"></i> System Settings
            </a>
        </div>
    </div>

    <!-- Filter Panel -->
    <div class="redas-card" style="margin-bottom:24px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-filter"></i></div>
                Filters
            </div>
            <a href="{{ route('admin.audit-log.index') }}" class="btn-nis btn-ghost btn-sm">Clear</a>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.audit-log.index') }}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;align-items:end;">
                <div>
                    <label style="font-size:.75rem;color:var(--gray-500);display:block;margin-bottom:4px;">Date From</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control" style="width:100%;padding:8px 12px;border-radius:var(--radius-md);border:1px solid var(--gray-200);">
                </div>
                <div>
                    <label style="font-size:.75rem;color:var(--gray-500);display:block;margin-bottom:4px;">Date To</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control" style="width:100%;padding:8px 12px;border-radius:var(--radius-md);border:1px solid var(--gray-200);">
                </div>
                <div>
                    <label style="font-size:.75rem;color:var(--gray-500);display:block;margin-bottom:4px;">User</label>
                    <select name="user_id" class="form-select" style="width:100%;padding:8px 12px;border-radius:var(--radius-md);border:1px solid var(--gray-200);">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ ($filters['user_id'] ?? '') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:.75rem;color:var(--gray-500);display:block;margin-bottom:4px;">Action</label>
                    <select name="action" class="form-select" style="width:100%;padding:8px 12px;border-radius:var(--radius-md);border:1px solid var(--gray-200);">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ ($filters['action'] ?? '') === $action ? 'selected' : '' }}>
                                {{ $action }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:.75rem;color:var(--gray-500);display:block;margin-bottom:4px;">Status</label>
                    <select name="status" class="form-select" style="width:100%;padding:8px 12px;border-radius:var(--radius-md);border:1px solid var(--gray-200);">
                        <option value="">All Statuses</option>
                        <option value="success" {{ ($filters['status'] ?? '') === 'success' ? 'selected' : '' }}>Success</option>
                        <option value="failure" {{ ($filters['status'] ?? '') === 'failure' ? 'selected' : '' }}>Failure</option>
                        <option value="blocked" {{ ($filters['status'] ?? '') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:.75rem;color:var(--gray-500);display:block;margin-bottom:4px;">Entity Type</label>
                    <select name="entity_type" class="form-select" style="width:100%;padding:8px 12px;border-radius:var(--radius-md);border:1px solid var(--gray-200);">
                        <option value="">All Entities</option>
                        @foreach($entityTypes as $type)
                            <option value="{{ $type }}" {{ ($filters['entity_type'] ?? '') === $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:.75rem;color:var(--gray-500);display:block;margin-bottom:4px;">Search</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="IP, location, action..." style="width:100%;padding:8px 12px;border-radius:var(--radius-md);border:1px solid var(--gray-200);">
                </div>
                <div>
                    <button type="submit" class="btn-nis btn-primary-nis" style="width:100%;">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Table -->
    <div class="redas-card">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#ede9fe;color:#7c3aed;"><i class="fas fa-list-alt"></i></div>
                Events
            </div>
            <span style="font-size:.72rem;color:var(--gray-400);">{{ $logs->total() }} records found</span>
        </div>
        <div class="card-body no-pad" style="overflow-x:auto;">
            <table class="redas-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Status</th>
                        <th>Entity</th>
                        <th>IP Address</th>
                        <th>Location</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="font-size:.78rem;color:var(--gray-600);white-space:nowrap;">
                                {{ $log->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td>
                                @if($log->user)
                                    <div style="font-weight:600;font-size:.82rem;">{{ $log->user->name }}</div>
                                    <div style="font-size:.7rem;color:var(--gray-400);">{{ $log->user->email }}</div>
                                @else
                                    <span style="font-size:.78rem;color:var(--gray-400);">—</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-family:monospace;font-size:.75rem;background:var(--gray-100);padding:3px 6px;border-radius:4px;">{{ $log->action }}</span>
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($log->status) {
                                        'success' => 'badge-approved',
                                        'failure' => 'badge-inactive',
                                        'blocked' => 'badge-danger',
                                        default => 'badge-pending',
                                    };
                                @endphp
                                <span class="status-badge {{ $badgeClass }}">{{ ucfirst($log->status) }}</span>
                            </td>
                            <td style="font-size:.75rem;color:var(--gray-600);">
                                {{ $log->entity_type ? $log->entity_type . ':' . $log->entity_id : '—' }}
                            </td>
                            <td style="font-family:monospace;font-size:.75rem;color:var(--gray-600);">
                                {{ $log->ip_address ?? '—' }}
                            </td>
                            <td style="font-size:.75rem;color:var(--gray-600);">
                                {{ $log->location ?? '—' }}
                            </td>
                            <td>
                                @if($log->details)
                                    <details style="font-size:.72rem;color:var(--gray-600);">
                                        <summary style="cursor:pointer;">View</summary>
                                        <pre style="margin:6px 0 0;background:var(--gray-50);padding:8px;border-radius:var(--radius-sm);max-width:260px;overflow:auto;">{{ json_encode($log->details, JSON_PRETTY_PRINT) }}</pre>
                                    </details>
                                @else
                                    <span style="font-size:.72rem;color:var(--gray-400);">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:24px;color:var(--gray-400);">
                                No audit events found matching the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:16px;">
            {{ $logs->links() }}
        </div>
    </div>
</main>

@include('partials.footer3')
