@include('partials.header')
    <!-- ─── Page Content ─── -->
    <main class="redas-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <!-- Dynamic name of State by fetching from the authenticated user's state -->
                <h1 class="page-title"> {{ auth()->user()->state ?? 'State' }} Officer Dashboard</h1>
                <p class="page-subtitle">
                    Welcome back, <strong>{{ auth()->user()->name ?? 'Officer' }}</strong> —
                    {{ now()->format('l, d F Y') }}
                </p>
            </div>
            <a href="{{ route('user.returns.create') }}" class="btn-nis btn-primary-nis" id="submitReturnBtn">
                <i class="fas fa-plus"></i> <span>Submit New Return</span>
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
            <a href="{{ route('user.returns.create') }}" style="background:#f59e0b;color:white;padding:8px 16px;border-radius:var(--radius-sm);font-size:.82rem;font-weight:700;text-decoration:none;flex-shrink:0;" onmouseover="this.style.background='#d97706'" onmouseout="this.style.background='#f59e0b'">
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
                <div class="stat-value" data-count="{{ $totalSubmissions }}">0</div>
                <div class="stat-change neutral"><i class="fas fa-minus"></i> All time</div>
            </div>
            <div class="stat-card gold">
                <div class="stat-header">
                    <span class="stat-label">Pending Review</span>
                    <span class="stat-icon"><i class="fas fa-hourglass-half"></i></span>
                </div>
                <div class="stat-value" data-count="{{ $pendingSubmissions }}">0</div>
                <div class="stat-change neutral"><i class="fas fa-minus"></i> Awaiting review</div>
            </div>
            <div class="stat-card green">
                <div class="stat-header">
                    <span class="stat-label">Approved</span>
                    <span class="stat-icon"><i class="fas fa-check-circle"></i></span>
                </div>
                <div class="stat-value" data-count="{{ $approvedSubmissions }}">0</div>
                <div class="stat-change up"><i class="fas fa-arrow-up"></i> Approved returns</div>
            </div>
            <div class="stat-card danger">
                <div class="stat-header">
                    <span class="stat-label">Returned / Queried</span>
                    <span class="stat-icon"><i class="fas fa-exclamation-circle"></i></span>
                </div>
                <div class="stat-value" data-count="{{ $queriedSubmissions }}">0</div>
                <div class="stat-change down"><i class="fas fa-arrow-down"></i> Action required</div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:minmax(0,2fr) minmax(0,1fr);gap:20px;align-items:start;margin-bottom:20px;">
            <!-- Recent Submissions -->
            <div class="redas-card delay-2">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);"><i class="fas fa-inbox"></i></div>
                        Recent Submissions
                    </div>
                    <a href="{{ route('user.submissions') }}" class="btn-nis btn-ghost btn-sm">View All</a>
                </div>
                <div class="card-body no-pad">
                    @php
                    $statusConfig = [
                        'pending'  => ['badge-pending',  'Pending Review'],
                        'approved' => ['badge-approved', 'Approved'],
                        'returned' => ['badge-rejected', 'Returned'],
                        'rejected' => ['badge-rejected', 'Returned'],
                        'queried'  => ['badge-rejected', 'Returned'],
                    ];
                    $typeLabels = [
                        'monthly'   => 'Monthly Return',
                        'quarterly' => 'Quarterly Return',
                        'biannual'  => 'Bi-Annual Return',
                        'annual'    => 'Annual Return',
                        'special'   => 'Special Report',
                    ];
                    @endphp
                    <table class="redas-table" id="submissionsTable">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Report Type</th>
                                <th>Status</th>
                                <th>Stage</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submissions as $submission)
                            @php
                                $status = strtolower((string) $submission->status);
                                [$badgeClass, $statusLabel] = $statusConfig[$status] ?? ['badge-draft', ucfirst((string) ($submission->status ?? 'Draft'))];
                                $data = is_array($submission->return_data) ? $submission->return_data : [];
                                $periodRaw = $data['report_period'] ?? $data['period'] ?? null;
                                $period = $periodRaw ?: ($submission->created_at?->format('M Y') ?? '—');
                                if ($periodRaw) {
                                    try {
                                        $period = \Carbon\Carbon::createFromFormat('Y-m', $periodRaw)->format('M Y');
                                    } catch (\Throwable $e) {
                                        $period = $periodRaw;
                                    }
                                }
                                $type = $typeLabels[$data['return_type'] ?? ''] ?? ucfirst((string) ($data['return_type'] ?? '—'));
                                $stage = $submission->workflow_stage ? ucwords(str_replace('_', ' ', $submission->workflow_stage)) : '—';
                            @endphp
                            <tr>
                                <td><strong>{{ $period }}</strong></td>
                                <td style="font-size:.8rem;">{{ $type }}</td>
                                <td>
                                    <span class="status-badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                                </td>
                                <td>
                                    <span style="display:inline-block;padding:2px 8px;border-radius:999px;font-size:.7rem;font-weight:600;background:var(--gray-100);color:var(--gray-600);">{{ $stage }}</span>
                                </td>
                                <td style="font-size:.78rem;color:var(--gray-500);">{{ $submission->created_at?->format('d M Y') ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('user.returns.show', ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" class="btn-nis btn-ghost btn-sm" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align:center;padding:32px;color:var(--gray-500);font-size:.84rem;">
                                    No submissions yet. Use <strong>Submit New Return</strong> above to file your first return.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column: Mini chart + Quick Actions -->
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
                        <canvas id="miniTrend" data-labels='@json($trendLabels ?? [])' data-values='@json($trendValues ?? [])'></canvas>
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
                        <a href="{{ route('user.returns.create') }}" class="btn-nis btn-primary-nis full-width" id="qaMonthlyBtn">
                            <i class="fas fa-plus-circle"></i> <span>Submit Monthly Return</span>
                        </a>
                        <a href="{{ route('user.returns.create', ['type' => 'quarterly']) }}" class="btn-nis btn-outline-nis full-width">
                            <i class="fas fa-calendar-alt"></i> Submit Quarterly Return
                        </a>
                        <a href="{{ route('user.archive') }}" class="btn-nis btn-ghost full-width">
                            <i class="fas fa-archive"></i> Archive Documents
                        </a>
                        <a href="{{ route('user.reports') }}" class="btn-nis btn-ghost full-width">
                            <i class="fas fa-file-export"></i> Generate Report
                        </a>
                        <a href="{{ route('user.profile') }}" class="btn-nis btn-ghost full-width">
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
                    @if($unreadNotifications > 0)
                        <span style="background:#dc2626;color:#fff;border-radius:999px;padding:1px 8px;font-size:.68rem;font-weight:700;">{{ $unreadNotifications }}</span>
                    @endif
                </div>
                <a href="{{ route('user.notifications') }}" class="btn-nis btn-ghost btn-sm">View All</a>
            </div>
            <div class="card-body no-pad">
                @php
                $notifTypeConfig = [
                    'danger'  => ['fas fa-exclamation-triangle', '#fee2e2', '#dc2626'],
                    'warning' => ['fas fa-clock', '#fef9c3', '#a16207'],
                    'success' => ['fas fa-check-circle', '#dcfce7', '#15803d'],
                    'info'    => ['fas fa-info-circle', '#dbeafe', '#1d4ed8'],
                ];
                @endphp
                @forelse($notifications as $notification)
                @php
                    [$notifIcon, $notifBg, $notifColor] = $notifTypeConfig[$notification->type] ?? ['fas fa-bell', 'var(--gray-100)', 'var(--gray-600)'];
                @endphp
                <div class="notif-item {{ $notification->is_read ? '' : 'unread' }}">
                    <div class="notif-icon" style="background:{{ $notifBg }};color:{{ $notifColor }};">
                        <i class="{{ $notifIcon }}"></i>
                    </div>
                    <div class="notif-content">
                        <div class="notif-title">{{ $notification->title }}</div>
                        <div class="notif-desc">{{ $notification->description }}</div>
                    </div>
                    <div class="notif-time" style="flex-shrink:0;">{{ $notification->created_at?->diffForHumans() ?? '' }}</div>
                </div>
                @empty
                <div style="text-align:center;padding:36px 20px;color:var(--gray-400);font-size:.84rem;">
                    <i class="fas fa-bell-slash" style="font-size:1.6rem;color:var(--gray-200);display:block;margin-bottom:10px;"></i>
                    No notifications yet. You're all caught up.
                </div>
                @endforelse
            </div>
        </div>

    </main>

{{-- A saved state return draft lives in the browser's localStorage under a single
     key (redas_state_draft, written by the combined return form). Surface it here
     so the officer can resume or discard it without opening the form first. --}}
<script>
(function () {
    var DRAFT_KEY = 'redas_state_draft';
    var resumeUrl = @json(route('user.returns.create'));

    var saved = null;
    try { saved = localStorage.getItem(DRAFT_KEY); } catch (e) {}
    if (!saved) return;

    var data;
    try { data = JSON.parse(saved); } catch (e) { return; }

    function esc(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    /* Flip the two primary submission buttons into a "Resume Draft" state. */
    ['submitReturnBtn', 'qaMonthlyBtn'].forEach(function (id) {
        var btn = document.getElementById(id);
        if (!btn) return;
        var label = btn.querySelector('span');
        var icon = btn.querySelector('i');
        if (label) label.textContent = 'Resume Draft Return';
        if (icon) icon.className = 'fas fa-rotate-right';
        btn.title = 'Continue your saved draft return';
    });

    /* Prepend a resumable row to the Recent Submissions table. */
    var tbody = document.querySelector('#submissionsTable tbody');
    if (!tbody) return;

    var typeLabels = {
        monthly: 'Monthly Return', quarterly: 'Quarterly Return',
        biannual: 'Bi-Annual Return', annual: 'Annual Return', special: 'Special Report'
    };
    var periodLabel = '—';
    if (data.period) {
        var d = new Date(data.period + '-01');
        periodLabel = isNaN(d) ? data.period : d.toLocaleDateString(undefined, { month: 'short', year: 'numeric' });
    }
    var typeLabel = typeLabels[data.return_type] || 'Return';
    var savedLabel = 'Saved locally';
    if (data.__saved_at) {
        var sd = new Date(data.__saved_at);
        if (!isNaN(sd)) savedLabel = sd.toLocaleDateString(undefined, { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' + sd.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
    }

    var emptyRow = tbody.querySelector('td[colspan]');
    if (emptyRow) emptyRow.closest('tr').style.display = 'none';

    var tr = document.createElement('tr');
    tr.innerHTML =
        '<td><strong>' + esc(periodLabel) + '</strong></td>' +
        '<td style="font-size:.8rem;">' + esc(typeLabel) + '</td>' +
        '<td><span class="status-badge badge-draft">Saved Draft</span></td>' +
        '<td><span style="display:inline-block;padding:2px 8px;border-radius:999px;font-size:.7rem;font-weight:600;background:var(--gray-100);color:var(--gray-600);">' + esc(savedLabel) + '</span></td>' +
        '<td style="font-size:.78rem;color:var(--gray-500);">Not yet submitted</td>' +
        '<td><div style="display:flex;gap:6px;">' +
            '<a href="' + resumeUrl + '" class="btn-nis btn-ghost btn-sm" title="Resume draft"><i class="fas fa-play"></i></a>' +
            '<button type="button" class="btn-nis btn-ghost btn-sm draft-discard-btn" style="color:#b91c1c;" title="Discard draft"><i class="fas fa-trash"></i></button>' +
        '</div></td>';

    tr.querySelector('.draft-discard-btn').addEventListener('click', function () {
        if (!window.confirm('Discard this saved draft? This cannot be undone.')) return;
        try { localStorage.removeItem(DRAFT_KEY); } catch (e) {}
        tr.remove();
        var emptyTr = emptyRow ? emptyRow.closest('tr') : null;
        var remaining = Array.from(tbody.querySelectorAll('tr')).filter(function (r) { return r !== emptyTr; });
        if (emptyTr && remaining.length === 0) emptyTr.style.display = '';
        ['submitReturnBtn', 'qaMonthlyBtn'].forEach(function (id) {
            var btn = document.getElementById(id);
            if (!btn) return;
            var label = btn.querySelector('span');
            var icon = btn.querySelector('i');
            if (id === 'submitReturnBtn') {
                if (label) label.textContent = 'Submit New Return';
                if (icon) icon.className = 'fas fa-plus';
            } else {
                if (label) label.textContent = 'Submit Monthly Return';
                if (icon) icon.className = 'fas fa-plus-circle';
            }
            btn.removeAttribute('title');
        });
    });

    tbody.insertBefore(tr, tbody.firstChild);
})();
</script>

@include('partials.footer')
