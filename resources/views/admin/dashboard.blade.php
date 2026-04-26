@include ('partials.header3')
    <!-- ─── Page Content ─── -->
    <main class="redas-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">National Command Dashboard</h1>
                <p class="page-subtitle">
                    Nigeria Immigration Service — HQ Overview &bull; {{ now()->format('l, d F Y') }}
                </p>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ url('/admin/users/create') }}" class="btn-nis btn-ghost">
                    <i class="fas fa-user-plus"></i> Add User
                </a>
                <a href="{{ url('/admin/reports/generate') }}" class="btn-nis btn-outline-nis">
                    <i class="fas fa-file-export"></i> Generate Report
                </a>
                <a href="{{ url('/admin/analytics') }}" class="btn-nis btn-primary-nis">
                    <i class="fas fa-chart-area"></i> Full Analytics
                </a>
            </div>
        </div>

        <!-- ── National KPI Stats ── -->
        <div class="stats-grid animate-fade-up">
            <div class="stat-card green">
                <div class="stat-header">
                    <span class="stat-label">Total Returns (YTD)</span>
                    <span class="stat-icon"><i class="fas fa-file-alt"></i></span>
                </div>
                <div class="stat-value" data-count="847">0</div>
                <div class="stat-change up"><i class="fas fa-arrow-up"></i> +12% from last year</div>
            </div>
            <div class="stat-card gold">
                <div class="stat-header">
                    <span class="stat-label">National Compliance</span>
                    <span class="stat-icon"><i class="fas fa-percentage"></i></span>
                </div>
                <div class="stat-value" data-count="79">0</div>
                <div class="stat-change up"><i class="fas fa-arrow-up"></i> +6% from last month</div>
            </div>
            <div class="stat-card danger">
                <div class="stat-header">
                    <span class="stat-label">Overdue Formations</span>
                    <span class="stat-icon"><i class="fas fa-exclamation-triangle"></i></span>
                </div>
                <div class="stat-value" data-count="15">0</div>
                <div class="stat-change down"><i class="fas fa-arrow-up"></i> Requires follow-up</div>
            </div>
            <div class="stat-card info">
                <div class="stat-header">
                    <span class="stat-label">Active Users</span>
                    <span class="stat-icon"><i class="fas fa-users"></i></span>
                </div>
                <div class="stat-value" data-count="142">0</div>
                <div class="stat-change up"><i class="fas fa-arrow-up"></i> 8 new this month</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-header">
                    <span class="stat-label">Archive Documents</span>
                    <span class="stat-icon"><i class="fas fa-archive"></i></span>
                </div>
                <div class="stat-value" data-count="12450">0</div>
                <div class="stat-change up"><i class="fas fa-arrow-up"></i> 71% of target</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-header">
                    <span class="stat-label">Pending at HQ</span>
                    <span class="stat-icon"><i class="fas fa-hourglass-half"></i></span>
                </div>
                <div class="stat-value" data-count="34">0</div>
                <div class="stat-change neutral"><i class="fas fa-minus"></i> Awaiting PRS review</div>
            </div>
        </div>

        <!-- ── Charts Row ── -->
        <div class="grid-2" style="margin-bottom:24px;">

            <!-- Submission Trend -->
            <div class="redas-card delay-2">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);"><i class="fas fa-chart-line"></i></div>
                        National Submission Trend — {{ now()->year }}
                    </div>
                    <div style="display:flex;gap:6px;">
                        <button class="btn-nis btn-ghost btn-sm period-btn active">Monthly</button>
                        <button class="btn-nis btn-ghost btn-sm period-btn">Quarterly</button>
                    </div>
                </div>
                <div class="card-body" style="height:240px;">
                    <canvas id="submissionTrend"></canvas>
                </div>
            </div>

            <!-- Status Breakdown -->
            <div class="redas-card delay-3">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:var(--gold-100);color:var(--gold-500);"><i class="fas fa-chart-pie"></i></div>
                        Returns Status Breakdown
                    </div>
                    <span style="font-size:.72rem;color:var(--gray-400);">{{ now()->format('F Y') }}</span>
                </div>
                <div class="card-body" style="height:240px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- ── Middle Row ── -->
        <div class="grid-2" style="margin-bottom:24px;">

            <!-- Formation Compliance Heatmap -->
            <div class="redas-card delay-3">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-map"></i></div>
                        Zonal Compliance — May {{ now()->year }}
                    </div>
                    <a href="{{ url('/admin/formations') }}" class="btn-nis btn-ghost btn-sm">Full View</a>
                </div>
                <div class="card-body" style="height:280px;">
                    <canvas id="complianceChart"></canvas>
                </div>
            </div>

            <!-- Directorate Returns -->
            <div class="redas-card delay-4">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);"><i class="fas fa-sitemap"></i></div>
                        Directorate Returns This Month
                    </div>
                </div>
                <div class="card-body" style="height:280px;">
                    <canvas id="directorateChart"></canvas>
                </div>
            </div>
        </div>

        <!-- ── Bottom Row ── -->
        <div class="grid-2">

            <!-- User Management Quick View -->
            <div class="redas-card delay-4">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-users-cog"></i></div>
                        Recent User Activity
                    </div>
                    <div style="display:flex;gap:6px;">
                        <a href="{{ url('/admin/users/create') }}" class="btn-nis btn-primary-nis btn-sm">
                            <i class="fas fa-user-plus"></i> Add User
                        </a>
                        <a href="{{ url('/admin/users') }}" class="btn-nis btn-ghost btn-sm">All Users</a>
                    </div>
                </div>
                <div class="card-body no-pad">
                    <table class="redas-table">
                        <thead>
                            <tr>
                                <th>Officer</th>
                                <th>Role</th>
                                <th>Formation</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach([
                                ['DCI Amaka Okafor',   'officer',     'Abuja State',   'active',   '2h ago'],
                                ['ACI Ibrahim Yusuf',  'supervisor',  'Zone B',        'active',   '1d ago'],
                                ['DCI Chidi Eze',      'officer',     'Lagos State',   'active',   '3h ago'],
                                ['CI Ngozi Obi',       'supervisor',  'Rivers State',  'active',   '5h ago'],
                                ['DCI Bayo Adeyemi',   'officer',     'Ogun State',    'inactive', '7d ago'],
                                ['ACI Sule Musa',      'zonal',       'Zone C',        'active',   '1h ago'],
                            ] as [$name, $role, $formation, $status, $lastLogin])
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div style="width:30px;height:30px;border-radius:50%;background:var(--nis-100);color:var(--nis-700);display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;flex-shrink:0;">
                                            {{ strtoupper(substr($name, 4, 1)) }}{{ strtoupper(substr(explode(' ', $name)[1] ?? 'X', 0, 1)) }}
                                        </div>
                                        <span style="font-weight:600;font-size:.84rem;">{{ $name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge {{ $role === 'officer' ? 'badge-draft' : ($role === 'zonal' ? 'badge-review' : 'badge-pending') }}" style="text-transform:capitalize;">{{ $role }}</span>
                                </td>
                                <td style="font-size:.8rem;color:var(--gray-600);">{{ $formation }}</td>
                                <td>
                                    <span class="status-badge {{ $status === 'active' ? 'badge-approved' : 'badge-inactive' }}">{{ ucfirst($status) }}</span>
                                </td>
                                <td style="font-size:.76rem;color:var(--gray-400);">{{ $lastLogin }}</td>
                                <td>
                                    <div style="display:flex;gap:4px;">
                                        <button class="btn-nis btn-ghost btn-sm" title="Edit user" onclick="REDAS.showToast('Navigating to user editor…','info')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-nis btn-sm btn-sm" style="background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;" title="{{ $status === 'active' ? 'Deactivate' : 'Activate' }}" onclick="REDAS.showToast('User status updated.','success')">
                                            <i class="fas fa-{{ $status === 'active' ? 'ban' : 'check' }}"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- System Health + Audit Log Teaser -->
            <div style="display:flex;flex-direction:column;gap:20px;">

                <!-- System Health -->
                <div class="redas-card delay-5">
                    <div class="card-head">
                        <div class="card-head-title">
                            <div class="card-head-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-heartbeat"></i></div>
                            System Health
                        </div>
                        <a href="{{ url('/admin/system') }}" class="btn-nis btn-ghost btn-sm">Details</a>
                    </div>
                    <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">
                        @foreach([
                            ['Web Server',      98,  'green', 'Nginx — Response 142ms'],
                            ['Database',        100, 'green', 'MySQL — Queries: 1,240/min'],
                            ['File Storage',    71,  'gold',  '3.55 TB / 5 TB used'],
                            ['API Gateway',     95,  'green', 'Rate: 0.02% errors'],
                            ['Backup Service',  100, 'green', 'Last run: 06:00 WAT today'],
                            ['Email Queue',     88,  'green', '12 msgs/min — 0 bounced'],
                        ] as [$service, $pct, $color, $detail])
                        <div>
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                                <span style="font-size:.82rem;font-weight:600;color:var(--gray-700);">{{ $service }}</span>
                                <span style="font-size:.72rem;color:var(--gray-400);">{{ $detail }}</span>
                            </div>
                            <div class="progress-nis" style="height:6px;">
                                <div class="progress-bar-nis {{ $color }}" data-width="{{ $pct }}%" style="width:0%;"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Audit Events -->
                <div class="redas-card delay-6">
                    <div class="card-head">
                        <div class="card-head-title">
                            <div class="card-head-icon" style="background:#ede9fe;color:#7c3aed;"><i class="fas fa-shield-alt"></i></div>
                            Recent Security Events
                        </div>
                        <a href="{{ url('/admin/audit-log') }}" class="btn-nis btn-ghost btn-sm">Full Log</a>
                    </div>
                    <div class="card-body no-pad">
                        @foreach([
                            ['danger', 'fas fa-lock',       'Account locked — 5 failed attempts',  '102.89.34.x',  '2h ago'],
                            ['success','fas fa-sign-in-alt','Admin login — ACI M.O. Oke',           '10.0.0.4',     '3h ago'],
                            ['info',   'fas fa-download',   'Report exported — PRS Analyst',        '10.0.0.12',    '5h ago'],
                            ['warning','fas fa-user-plus',  'New user created — DCI A. Okafor',     '10.0.0.4',     '8h ago'],
                        ] as [$type, $icon, $action, $ip, $time])
                        <div style="display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid var(--gray-100);">
                            <div style="width:28px;height:28px;border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;font-size:.75rem;flex-shrink:0;background:{{ $type === 'danger' ? '#fee2e2' : ($type === 'warning' ? '#fef9c3' : ($type === 'success' ? '#dcfce7' : '#dbeafe')) }};color:{{ $type === 'danger' ? '#dc2626' : ($type === 'warning' ? '#a16207' : ($type === 'success' ? '#15803d' : '#1d4ed8')) }};">
                                <i class="{{ $icon }}"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:.78rem;font-weight:600;color:var(--gray-800);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $action }}</div>
                                <div style="font-size:.7rem;color:var(--gray-400);">IP: {{ $ip }}</div>
                            </div>
                            <div style="font-size:.7rem;color:var(--gray-400);flex-shrink:0;">{{ $time }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </main>

@include ('partials.footer3')