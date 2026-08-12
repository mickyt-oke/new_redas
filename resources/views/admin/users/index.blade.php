@include('partials.header3')

<main class="redas-content" style="max-width:1100px;margin:32px auto;padding:0 16px;">
    <div class="page-header">
        <div>
            <h1 class="page-title">User Management</h1>
            <p class="page-subtitle">Manage all user accounts, enable/disable access, and override geolocation state assignments.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-nis btn-primary-nis">
            <i class="fas fa-user-plus"></i> Add User
        </a>
    </div>

    @if(session('status'))
        <div class="alert alert-success" style="margin-bottom:16px;">{{ session('status') }}</div>
    @endif

    <div class="redas-card">
        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table class="redas-table" style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>State Override</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $user->user_category)) }}</td>
                            <td>{{ ucfirst($user->primary_location_type) }} {{ $user->primary_location_code ? "({$user->primary_location_code})" : '' }}</td>
                            <td>{{ $user->geo_state ?? 'None' }}</td>
                            <td>
                                <span class="status-badge {{ $user->is_enabled ? 'badge-approved' : 'badge-inactive' }}">
                                    {{ $user->is_enabled ? 'Enabled' : 'Disabled' }}
                                </span>
                            </td>
                            <td style="white-space:nowrap;">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn-nis btn-ghost btn-sm" style="margin-right:4px;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.users.toggle_status', $user) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-nis btn-sm" style="background:{{ $user->is_enabled ? '#fde8e8' : '#e8f8ef' }}; color:{{ $user->is_enabled ? '#b91c1c' : '#166534' }}; border:1px solid {{ $user->is_enabled ? '#fca5a5' : '#a7f3d0' }};">
                                        <i class="fas fa-{{ $user->is_enabled ? 'ban' : 'check' }}"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top:18px;">
        {{ $users->links() }}
    </div>
</main>

@include('partials.footer')
