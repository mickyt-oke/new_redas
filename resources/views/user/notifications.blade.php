@include ('partials.header')
    <main class="redas-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Notifications</h1>
                <p class="page-subtitle">System alerts, workflow updates, and deadline reminders.</p>
            </div>
            <button class="btn-nis btn-ghost btn-sm" id="markAllReadBtn">
                <i class="fas fa-check-double"></i> Mark all read
            </button>
        </div>

        <!-- Filter bar -->
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
            <button class="notif-filter-btn active" data-filter="all">All <span id="cntAll" style="background:rgba(0,0,0,.12);border-radius:999px;padding:1px 7px;font-size:.68rem;margin-left:3px;">8</span></button>
            <button class="notif-filter-btn" data-filter="unread">Unread <span id="cntUnread" style="background:#dc2626;color:#fff;border-radius:999px;padding:1px 6px;font-size:.68rem;margin-left:3px;">2</span></button>
            <button class="notif-filter-btn" data-filter="danger">Urgent</button>
            <button class="notif-filter-btn" data-filter="warning">Deadline</button>
            <button class="notif-filter-btn" data-filter="success">Approvals</button>
            <button class="notif-filter-btn" data-filter="info">System</button>
        </div>

        <!-- Notification list -->
        <div id="notifList">
        @php
        $notifications = [
            /* [type, icon, title, desc, time, isUnread, tag, action, actionUrl] */
            ['danger',  'fas fa-exclamation-triangle', 'April Return Overdue',
             'Your April 2025 monthly operational return has not been submitted. The deadline has passed 2 days ago. Submit immediately to avoid penalties and disciplinary action.',
             '2 hours ago', true, 'Urgent', 'Submit Now', '/user/returns/create'],

            ['warning', 'fas fa-clock', 'May Return Due in 5 Days',
             'Your May 2025 monthly return is due on ' . now()->endOfMonth()->format('d F Y') . '. Ensure your data is compiled and submitted before the deadline.',
             '1 day ago', true, 'Deadline', 'Start Entry', '/user/returns/create'],

            ['success', 'fas fa-check-circle', 'March Return Approved',
             'Your March 2025 monthly return (Zone A Lagos) has been reviewed and approved by ACI A. Bello. The return is now archived and available for download.',
             '3 days ago', false, 'Approved', 'View Return', '/user/submissions'],

            ['success', 'fas fa-check-circle', 'Q1 2025 Quarterly Return Approved',
             'Your Q1 2025 quarterly operational return has been approved by your zonal supervisor. Excellent compliance record maintained.',
             '5 days ago', false, 'Approved', 'View Return', '/user/submissions'],

            ['warning', 'fas fa-question-circle', 'April Return Queried',
             'Your April 2025 monthly return has been queried by ACI A. Bello. Reason: "Passport figures inconsistent with previous month." Please review Section 6 (Passport Revenue) and resubmit.',
             '2 days ago', false, 'Action Required', 'Edit & Resubmit', '/user/returns/create'],

            ['info', 'fas fa-wrench', 'Scheduled Maintenance',
             'NIS-REDAS will undergo scheduled maintenance on Sunday from 04:00 to 06:00 WAT. Please plan your submissions accordingly. The system will be unavailable during this window.',
             '1 week ago', false, 'System', null, null],

            ['info', 'fas fa-bell', 'New Reporting Template Available',
             'The ICT Directorate has released an updated reporting template for Q2 2025. Please use the new form for all submissions from 01 April 2025.',
             '1 week ago', false, 'System', 'View Template', '/user/returns/create'],

            ['info', 'fas fa-shield-alt', 'Security Policy Update',
             'Please review the updated NIS data handling and confidentiality policy circulated by the ICT Directorate. Compliance is mandatory for all officers.',
             '2 weeks ago', false, 'Policy', null, null],
        ];

        $typeColors = [
            'danger'  => ['bg' => '#fee2e2', 'color' => '#dc2626'],
            'warning' => ['bg' => '#fef9c3', 'color' => '#a16207'],
            'success' => ['bg' => '#dcfce7', 'color' => '#15803d'],
            'info'    => ['bg' => '#dbeafe', 'color' => '#1d4ed8'],
        ];
        $tagColors = [
            'Urgent'          => 'background:#fee2e2;color:#dc2626;',
            'Deadline'        => 'background:#fef9c3;color:#a16207;',
            'Approved'        => 'background:#dcfce7;color:#15803d;',
            'Action Required' => 'background:#ffedd5;color:#c2410c;',
            'System'          => 'background:#dbeafe;color:#1d4ed8;',
            'Policy'          => 'background:#f3e8ff;color:#7c3aed;',
        ];
        @endphp

        @foreach($notifications as [$type, $icon, $title, $desc, $time, $unread, $tag, $action, $actionUrl])
        @php $c = $typeColors[$type] ?? $typeColors['info']; $ts = $tagColors[$tag] ?? 'background:var(--gray-100);color:var(--gray-600);'; @endphp
        <div class="notif-card {{ $unread ? 'unread type-'.$type : '' }}" data-type="{{ $type }}" data-unread="{{ $unread ? '1' : '0' }}" onclick="markRead(this)">
            <div class="notif-card-body">
                <div class="notif-card-icon" style="background:{{ $c['bg'] }};color:{{ $c['color'] }};"><i class="{{ $icon }}"></i></div>
                <div class="notif-card-content">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                        @if($unread)<span class="unread-dot"></span>@endif
                        <div class="notif-card-title">{{ $title }}</div>
                        <span class="notif-tag" style="{{ $ts }}">{{ $tag }}</span>
                    </div>
                    <div class="notif-card-desc">{{ $desc }}</div>
                    <div class="notif-card-meta">
                        <i class="fas fa-clock" style="font-size:.65rem;"></i> {{ $time }}
                        @if($action && $actionUrl)
                        <span style="margin-left:4px;">•</span>
                        <a href="{{ url($actionUrl) }}" style="color:var(--nis-600);font-weight:600;text-decoration:none;font-size:.74rem;" onclick="event.stopPropagation();">
                            {{ $action }} <i class="fas fa-arrow-right" style="font-size:.6rem;"></i>
                        </a>
                        @endif
                    </div>
                </div>
                <div class="notif-card-time">{{ $time }}</div>
            </div>
        </div>
        @endforeach
        </div>

        <!-- Empty state (hidden by default) -->
        <div id="emptyState" style="display:none;text-align:center;padding:60px 20px;">
            <i class="fas fa-bell-slash" style="font-size:3rem;color:var(--gray-200);margin-bottom:16px;display:block;"></i>
            <div style="font-size:.95rem;font-weight:700;color:var(--gray-400);margin-bottom:6px;">No notifications</div>
            <div style="font-size:.82rem;color:var(--gray-400);">You're all caught up.</div>
        </div>
    </main>
</div>

@include('partials.footer')