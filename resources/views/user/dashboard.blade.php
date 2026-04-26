@include('partials.header')
    <!-- ─── Page Content ─── -->
    <main class="redas-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Officer Dashboard</h1>
                <p class="page-subtitle">
                    Welcome back, <strong>{{ auth()->user()->name ?? 'Officer' }}</strong> —
                    {{ now()->format('l, d F Y') }}
                </p>
            </div>
            <a href="{{ url('/user/returns/create') }}" class="btn-nis btn-primary-nis">
                <i class="fas fa-plus"></i> Submit New Return
            </a>
        </div>

        <!-- ── Deadline Alert ── -->
        <div style="background:linear-gradient(135deg,#fef3c7,#fde68a);border:1px solid #f59e0b;border-radius:var(--radius-md);padding:14px 18px;display:flex;align-items:center;gap:14px;margin-bottom:24px;animation:fadeInUp .4s ease both;" class="animate-fade-up">
            <div style="width:40px;height:40px;background:#f59e0b;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:white;flex-shrink:0;">
                <i class="fas fa-calendar-exclamation"></i>
            </div>
            <div style="flex:1;">
                <strong style="color:#92400e;font-size:.9rem;">Monthly Return Due — {{ now()->endOfMonth()->format('d F Y') }}</strong>
                <p style="color:#b45309;font-size:.8rem;margin:0;">Submit your {{ now()->format('F Y') }} operational return before the deadline to avoid penalties.</p>
            </div>
            <a href="{{ url('/user/returns/create') }}" style="background:#f59e0b;color:white;padding:8px 16px;border-radius:var(--radius-sm);font-size:.82rem;font-weight:700;text-decoration:none;flex-shrink:0;" onmouseover="this.style.background='#d97706'" onmouseout="this.style.background='#f59e0b'">
                Submit Now
            </a>
        </div>

        <!-- ── Stats ── -->
        <div class="stats-grid animate-fade-up delay-1">
            <div class="stat-card green">
                <div class="stat-header">
                    <span class="stat-label">Total Submitted</span>
                    <span class="stat-icon"><i class="fas fa-paper-plane"></i></span>
                </div>
                <div class="stat-value" data-count="12">0</div>
                <div class="stat-change up"><i class="fas fa-arrow-up"></i> 2 this month</div>
            </div>
            <div class="stat-card gold">
                <div class="stat-header">
                    <span class="stat-label">Pending Review</span>
                    <span class="stat-icon"><i class="fas fa-hourglass-half"></i></span>
                </div>
                <div class="stat-value" data-count="3">0</div>
                <div class="stat-change neutral"><i class="fas fa-minus"></i> Awaiting supervisor</div>
            </div>
            <div class="stat-card green">
                <div class="stat-header">
                    <span class="stat-label">Approved</span>
                    <span class="stat-icon"><i class="fas fa-check-circle"></i></span>
                </div>
                <div class="stat-value" data-count="8">0</div>
                <div class="stat-change up"><i class="fas fa-arrow-up"></i> 1 this week</div>
            </div>
            <div class="stat-card danger">
                <div class="stat-header">
                    <span class="stat-label">Returned / Queried</span>
                    <span class="stat-icon"><i class="fas fa-exclamation-circle"></i></span>
                </div>
                <div class="stat-value" data-count="1">0</div>
                <div class="stat-change down"><i class="fas fa-arrow-down"></i> Action required</div>
            </div>
        </div>

        <!-- ── Main Grid ── -->
        <div class="grid-2" style="margin-bottom:24px;">

            <!-- Recent Submissions -->
            <div class="redas-card delay-2">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);"><i class="fas fa-inbox"></i></div>
                        Recent Submissions
                    </div>
                    <a href="{{ url('/user/submissions') }}" class="btn-nis btn-ghost btn-sm">View All</a>
                </div>
                <div class="card-body no-pad">
                    <table class="redas-table searchable-table" id="submissionsTable">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Report Type</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach([
                                ['May 2025',   'Monthly Return',   'pending',  '01 May 2025',  'Awaiting supervisor review'],
                                ['Apr 2025',   'Monthly Return',   'rejected', '02 Apr 2025',  'Supervisor: Passport figures inconsistent with previous month'],
                                ['Mar 2025',   'Monthly Return',   'approved', '01 Mar 2025',  'Approved by ACI Bello'],
                                ['Q1 2025',    'Quarterly Return', 'approved', '03 Apr 2025',  'Approved by ACI Bello'],
                                ['Feb 2025',   'Monthly Return',   'approved', '01 Feb 2025',  'Approved by ACI Bello'],
                                ['Jan 2025',   'Monthly Return',   'approved', '31 Jan 2025',  'Approved by ACI Bello'],
                            ] as [$period, $type, $status, $date, $remarks])
                            <tr>
                                <td><strong>{{ $period }}</strong></td>
                                <td style="font-size:.8rem;">{{ $type }}</td>
                                <td>
                                    <span class="status-badge
                                        @if($status === 'approved') badge-approved
                                        @elseif($status === 'pending') badge-pending
                                        @elseif($status === 'rejected') badge-rejected
                                        @else badge-draft @endif">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td style="font-size:.78rem;color:var(--gray-500);">{{ $date }}</td>
                                <td>
                                    <button class="btn-nis btn-ghost btn-sm"
                                        data-action="view"
                                        data-title="{{ $period }} {{ $type }}"
                                        data-date="{{ $date }}"
                                        data-type="{{ $type }}"
                                        data-status="{{ ucfirst($status) }}"
                                        data-remarks="{{ $remarks }}"
                                        data-bs-toggle="modal" data-bs-target="#viewModal">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column: Mini chart + Notifications -->
            <div style="display:flex;flex-direction:column;gap:20px;">

                <!-- Submission trend -->
                <div class="redas-card delay-3">
                    <div class="card-head">
                        <div class="card-head-title">
                            <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);"><i class="fas fa-chart-line"></i></div>
                            Submission History
                        </div>
                        <span style="font-size:.72rem;color:var(--gray-400);">Last 6 months</span>
                    </div>
                    <div class="card-body" style="height:160px;padding-bottom:8px;">
                        <canvas id="miniTrend"></canvas>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="redas-card delay-4">
                    <div class="card-head">
                        <div class="card-head-title">
                            <div class="card-head-icon" style="background:var(--gold-100);color:var(--gold-500);"><i class="fas fa-bolt"></i></div>
                            Quick Actions
                        </div>
                    </div>
                    <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
                        <a href="{{ url('/user/returns/create') }}" class="btn-nis btn-primary-nis full-width">
                            <i class="fas fa-plus-circle"></i> Submit Monthly Return
                        </a>
                        <a href="{{ url('/user/returns/create?type=quarterly') }}" class="btn-nis btn-outline-nis full-width">
                            <i class="fas fa-calendar-alt"></i> Submit Quarterly Return
                        </a>
                        <a href="{{ url('/user/archive/upload') }}" class="btn-nis btn-ghost full-width">
                            <i class="fas fa-upload"></i> Upload Archive Document
                        </a>
                        <a href="{{ url('/user/profile') }}" class="btn-nis btn-ghost full-width">
                            <i class="fas fa-user-cog"></i> Update Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Notifications ── -->
        <div class="redas-card animate-fade-up delay-5">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#fee2e2;color:#dc2626;"><i class="fas fa-bell"></i></div>
                    Recent Notifications
                </div>
                <a href="{{ url('/user/notifications') }}" class="btn-nis btn-ghost btn-sm">View All</a>
            </div>
            <div class="card-body no-pad">
                @foreach([
                    ['danger', 'fas fa-exclamation-triangle', 'April Return Overdue',      'Your April 2025 monthly return has not been submitted. Please submit immediately.', '2 hours ago'],
                    ['warning','fas fa-clock',                'Deadline Reminder',          'May 2025 monthly return is due in 5 days ({{ now()->endOfMonth()->format("d M") }}).', '1 day ago'],
                    ['success','fas fa-check-circle',         'Return Approved',            'Your March 2025 monthly return was approved by ACI A. Bello.', '3 days ago'],
                    ['info',   'fas fa-info-circle',          'System Maintenance Notice', 'Scheduled maintenance: Sunday 04:00–06:00 WAT. Plan submissions accordingly.', '5 days ago'],
                ] as [$type, $icon, $title, $desc, $time])
                <div class="notif-item {{ $loop->index < 2 ? 'unread' : '' }}">
                    <div class="notif-icon" style="background:{{ $type === 'danger' ? '#fee2e2' : ($type === 'warning' ? '#fef9c3' : ($type === 'success' ? '#dcfce7' : '#dbeafe')) }};color:{{ $type === 'danger' ? '#dc2626' : ($type === 'warning' ? '#a16207' : ($type === 'success' ? '#15803d' : '#1d4ed8')) }};">
                        <i class="{{ $icon }}"></i>
                    </div>
                    <div class="notif-content">
                        <div class="notif-title">{{ $title }}</div>
                        <div class="notif-desc">{{ $desc }}</div>
                    </div>
                    <div class="notif-time" style="flex-shrink:0;">{{ $time }}</div>
                </div>
                @endforeach
            </div>
        </div>

    </main>

<!-- ─── View Report Modal ─── -->
{{-- <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius-lg);border:none;overflow:hidden;">
            <div class="modal-header" style="background:var(--nis-700);color:white;border:none;padding:14px 20px;">
                <h5 class="modal-title" style="font-weight:700;font-size:.95rem;" id="viewModalTitle">Return Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div>
                        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--gray-400);margin-bottom:4px;">Period</div>
                        <div style="font-weight:700;color:var(--gray-800);" id="modalTitle">—</div>
                    </div>
                    <div>
                        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--gray-400);margin-bottom:4px;">Date Submitted</div>
                        <div style="font-weight:600;color:var(--gray-700);" id="modalDate">—</div>
                    </div>
                    <div>
                        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--gray-400);margin-bottom:4px;">Report Type</div>
                        <div style="font-weight:600;color:var(--gray-700);" id="modalType">—</div>
                    </div>
                    <div>
                        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--gray-400);margin-bottom:4px;">Status</div>
                        <div id="modalStatus">—</div>
                    </div>
                </div>
                <div>
                    <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--gray-400);margin-bottom:6px;">Supervisor Remarks</div>
                    <div style="background:var(--gray-50);border-radius:var(--radius-md);padding:12px;font-size:.875rem;color:var(--gray-700);border:1px solid var(--gray-200);" id="modalRemarks">—</div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--gray-100);padding:14px 20px;display:flex;gap:8px;justify-content:flex-end;">
                <button class="btn-nis btn-ghost btn-sm" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn-nis btn-outline-nis btn-sm"><i class="fas fa-download"></i> Download PDF</a>
            </div>
        </div>
    </div>
</div> --}}

@include('partials.footer')