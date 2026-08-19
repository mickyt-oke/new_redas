@if(auth()->user()->primary_location_code === 'VISA')
    @include('partials.visa-header')
@elseif(auth()->user()->primary_location_code === 'ICT')
    @include('partials.ict-header')
@else
    @include('partials.header3')
@endif
    <!-- ─── Page Content ─── -->
    <main class="redas-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">User Management</h1>
                <p class="page-subtitle">
                    Manage system users and access privileges &bull; {{ now()->format('l, d F Y') }}
                </p>
            </div>
            <div style="display:flex;gap:8px;">
                <button onclick="window.print()" class="btn-nis btn-ghost" style="background:#e0f2fe;border:1px solid #bae6fd;color:#0369a1;">
                    <i class="fas fa-print"></i> Print Users List
                </button>
                <a href="{{ route('admin.users.create') }}" class="btn-nis btn-primary-nis">
                    <i class="fas fa-user-plus"></i> Add New User
                </a>
            </div>
        </div>

        <style>
        .redas-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
            overflow: hidden;
        }
        .redas-card:hover {
            box-shadow: 0 8px 12px -3px rgba(0, 0, 0, 0.06), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
        }
        .table-nis {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
        }
        .table-nis th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        .table-nis td {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 0.88rem;
            vertical-align: middle;
        }
        .table-nis tr:hover td {
            background-color: #f8fafc;
        }
        .table-nis tr:last-child td {
            border-bottom: none;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.74rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }
        .badge-user-category {
            background-color: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .badge-role-admin {
            background-color: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .badge-role-officer {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .badge-location {
            font-family: Menlo, Monaco, Consolas, "Courier New", monospace;
            background-color: #f8fafc;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            padding: 2px 6px;
            border-radius: 6px;
            font-size: 0.8rem;
        }
        .btn-nis {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            font-weight: 600;
            font-size: 0.86rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-primary-nis {
            background-color: var(--nis-600, #0B6B3A);
            color: #ffffff;
            border: 1px solid var(--nis-700, #09552E);
        }
        .btn-primary-nis:hover {
            background-color: var(--nis-700, #09552E);
            transform: translateY(-1px);
        }
        .btn-ghost {
            background-color: transparent;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .btn-ghost:hover {
            background-color: #f8fafc;
            color: #1e293b;
            border-color: #94a3b8;
        }
        .user-name-cell {
            font-weight: 600;
            color: #0f172a;
        }
        .user-email-cell {
            color: #64748b;
            font-size: 0.84rem;
        }
        
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            .sidebar, .redas-sidebar, .redas-topbar, .topbar, .btn-nis, button, a, .page-header {
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
            .table-nis {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            .table-nis th, .table-nis td {
                border: 1px solid #ddd !important;
                padding: 8px !important;
                font-size: 10pt !important;
            }
        }
        </style>

        @if (session('status'))
            <div style="background:#dcfce7;border:1px solid #86efac;color:#15803d;padding:12px;border-radius:8px;margin-bottom:20px;font-size:0.9rem;">
                <i class="fas fa-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;padding:12px;border-radius:8px;margin-bottom:20px;font-size:0.9rem;">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="redas-card animate-fade-up">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-users"></i></div>
                    Registered Users
                </div>
            </div>
            
            <div style="overflow-x:auto;">
                <table class="table-nis">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Service Number</th>
                            <th>Role</th>
                            <th>Submissions &amp; Activity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr>
                                <td class="user-name-cell">{{ $u->name }}</td>
                                <td class="user-email-cell">{{ $u->email }}</td>
                                <td><code class="badge-location">{{ $u->service_number }}</code></td>
                                <td><span class="status-badge {{ $u->role === 'admin' || $u->role === 'super_admin' ? 'badge-role-admin' : 'badge-role-officer' }}">{{ $u->role === 'officer' ? 'Desk Officer' : ($u->role === 'admin' ? 'Admin' : $u->role) }}</span></td>
                                <td>
                                    @php
                                        $drafts = $u->applications()->where('status', 'draft')->count();
                                        $pendings = $u->applications()->where('status', 'pending')->count();
                                        $approved = $u->applications()->where('status', 'approved')->count();
                                        $submitted = $u->applications()->where('status', 'submitted')->count();
                                    @endphp
                                    <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                        @if($drafts > 0) <span class="status-badge badge-draft" title="Drafts">{{ $drafts }} Draft</span> @endif
                                        @if($pendings > 0) <span class="status-badge badge-pending" title="Pending approval">{{ $pendings }} Pending</span> @endif
                                        @if($approved > 0) <span class="status-badge badge-approved" style="background:#def7ec;color:#03543f;" title="Approved">{{ $approved }} Approved</span> @endif
                                        @if($submitted > 0) <span class="status-badge badge-approved" style="background:#e1effe;color:#1e40af;" title="Submitted to HQ">{{ $submitted }} Sent</span> @endif
                                        @if($drafts + $pendings + $approved + $submitted === 0)
                                            <span style="color:var(--gray-400);font-size:0.8rem;">No activity recorded</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if ($u->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this user?');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-nis btn-sm" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;padding:4px 8px;">
                                                <i class="fas fa-trash-alt"></i> Remove
                                            </button>
                                        </form>
                                    @else
                                        <span style="color:var(--gray-400);font-size:0.8rem;">(Logged In)</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </main>
@if(auth()->user()->primary_location_code === 'VISA')
    @include('partials.footer')
@elseif(auth()->user()->primary_location_code === 'ICT')
    @include('partials.footer')
@else
    @include('partials.footer3')
@endif
