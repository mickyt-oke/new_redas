@include('partials.header')

    <main class="redas-content">
        <!-- Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">My Submissions</h1>
                <p class="page-subtitle">Track the status of all your operational returns.</p>
            </div>
            <a href="{{ url('/user/returns/create') }}" class="btn-nis btn-primary-nis">
                <i class="fas fa-plus"></i> New Return
            </a>
        </div>

        <!-- Stats row -->
        <div class="stats-grid animate-fade-up" style="margin-bottom:20px;">
            @php
            $subStats = [
                ['Total Submitted', 12, 'fas fa-paper-plane', 'green', '2 this month', 'up'],
                ['Pending Review',   3, 'fas fa-hourglass-half', 'gold', 'Awaiting supervisor', 'neutral'],
                ['Approved',         8, 'fas fa-check-circle', 'green', '1 this week', 'up'],
                ['Queried / Returned',1,'fas fa-question-circle','warning','Action required','down'],
            ];
            @endphp
            @foreach($subStats as [$label,$val,$icon,$color,$change,$dir])
            <div class="stat-card {{ $color }}">
                <div class="stat-header"><span class="stat-label">{{ $label }}</span><span class="stat-icon"><i class="{{ $icon }}"></i></span></div>
                <div class="stat-value" data-count="{{ $val }}">0</div>
                <div class="stat-change {{ $dir }}"><i class="fas fa-{{ $dir === 'up' ? 'arrow-up' : ($dir === 'down' ? 'arrow-down' : 'minus') }}"></i> {{ $change }}</div>
            </div>
            @endforeach
        </div>

        <!-- Filters + Search -->
        <div class="redas-card animate-fade-up delay-1" style="margin-bottom:16px;">
            <div class="card-body" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <input type="text" id="subSearch" placeholder="Search period, type…" class="ni" style="max-width:240px;padding:8px 12px;border:1px solid var(--gray-200);border-radius:var(--radius-sm);">
                <div style="display:flex;gap:6px;flex-wrap:wrap;" id="statusFilters">
                    <button class="filter-chip active" data-filter="all">All <span style="background:rgba(0,0,0,.1);border-radius:999px;padding:1px 7px;font-size:.68rem;margin-left:2px;">12</span></button>
                    <button class="filter-chip" data-filter="pending" style="border-color:#fde68a;color:#a16207;"><i class="fas fa-circle" style="font-size:.45rem;color:#f59e0b;"></i> Pending <span style="background:rgba(0,0,0,.07);border-radius:999px;padding:1px 6px;font-size:.68rem;margin-left:2px;">3</span></button>
                    <button class="filter-chip" data-filter="approved" style="border-color:#86efac;color:#15803d;"><i class="fas fa-circle" style="font-size:.45rem;color:#22c55e;"></i> Approved <span style="background:rgba(0,0,0,.07);border-radius:999px;padding:1px 6px;font-size:.68rem;margin-left:2px;">8</span></button>
                    <button class="filter-chip" data-filter="queried" style="border-color:#fdba74;color:#c2410c;"><i class="fas fa-circle" style="font-size:.45rem;color:#f97316;"></i> Queried <span style="background:rgba(0,0,0,.07);border-radius:999px;padding:1px 6px;font-size:.68rem;margin-left:2px;">1</span></button>
                    <button class="filter-chip" data-filter="rejected" style="border-color:#fca5a5;color:#dc2626;"><i class="fas fa-circle" style="font-size:.45rem;color:#ef4444;"></i> Rejected <span style="background:rgba(0,0,0,.07);border-radius:999px;padding:1px 6px;font-size:.68rem;margin-left:2px;">0</span></button>
                </div>
                <div style="margin-left:auto;display:flex;gap:8px;">
                    <select class="ni ni-select" id="periodFilter" style="padding:6px 28px 6px 10px;font-size:.78rem;border:1px solid var(--gray-200);border-radius:var(--radius-sm);">
                        <option value="">All Periods</option>
                        <option>2025</option><option>2024</option>
                    </select>
                    <select class="ni ni-select" id="typeFilter" style="padding:6px 28px 6px 10px;font-size:.78rem;border:1px solid var(--gray-200);border-radius:var(--radius-sm);">
                        <option value="">All Types</option>
                        <option>Monthly Return</option>
                        <option>Quarterly Return</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Submissions table -->
        <div class="redas-card animate-fade-up delay-2">
            <div class="card-body no-pad">
                <table class="redas-table" id="submissionsTable">
                    <thead>
                        <tr>
                            <th style="width:36px;"></th>
                            <th>Period</th>
                            <th>Return Type</th>
                            <th>Command</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Supervisor Remarks</th>
                            <th style="width:120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $submissions = [
                            ['May 2025',   'Monthly Return',   'Zone A Lagos',    'pending',  '01 May 2025', null,                                             null, 'zonal'],
                            ['Apr 2025',   'Monthly Return',   'Zone A Lagos',    'queried',  '02 Apr 2025', 'ACI A. Bello',  'Passport figures inconsistent with March data. Please review Section 6.', 'zonal'],
                            ['Mar 2025',   'Monthly Return',   'Zone A Lagos',    'approved', '01 Mar 2025', 'ACI A. Bello',  'Approved. All figures verified and accurate.',                           'zonal'],
                            ['Q1 2025',    'Quarterly Return', 'Zone A Lagos',    'approved', '03 Apr 2025', 'ACI A. Bello',  'Quarterly return approved. Good compliance.',                            'zonal'],
                            ['Feb 2025',   'Monthly Return',   'Zone A Lagos',    'approved', '01 Feb 2025', 'ACI A. Bello',  'Approved.',                                                              'zonal'],
                            ['Jan 2025',   'Monthly Return',   'Zone A Lagos',    'approved', '31 Jan 2025', 'ACI A. Bello',  'Approved.',                                                              'zonal'],
                            ['Dec 2024',   'Monthly Return',   'Zone A Lagos',    'approved', '30 Dec 2024', 'ACI A. Bello',  'Approved.',                                                              'zonal'],
                            ['Nov 2024',   'Monthly Return',   'Zone A Lagos',    'approved', '01 Dec 2024', 'ACI A. Bello',  'Approved.',                                                              'zonal'],
                        ];
                        $statusConfig = [
                            'pending'  => ['badge-pending',  'Pending Review',  'fas fa-hourglass-half'],
                            'approved' => ['badge-approved', 'Approved',        'fas fa-check-circle'],
                            'queried'  => ['badge-review',   'Queried',         'fas fa-question-circle'],
                            'rejected' => ['badge-rejected', 'Rejected',        'fas fa-times-circle'],
                            'draft'    => ['badge-draft',    'Draft',           'fas fa-pencil-alt'],
                        ];
                        @endphp
                        @foreach($submissions as $i => [$period, $type, $cmd, $status, $date, $reviewer, $remarks, $path])
                        @php [$badgeClass, $statusLabel, $statusIcon] = $statusConfig[$status] ?? ['badge-draft','Unknown','fas fa-circle']; @endphp
                        <tr class="sub-main-row" data-status="{{ $status }}" data-type="{{ strtolower($type) }}" data-period="{{ $period }}">
                            <td>
                                <button class="btn-nis btn-ghost btn-sm expand-btn" data-row="{{ $i }}" style="padding:3px 6px;color:var(--gray-400);" title="Expand">
                                    <i class="fas fa-chevron-right" id="chevron-{{ $i }}"></i>
                                </button>
                            </td>
                            <td><strong>{{ $period }}</strong></td>
                            <td style="font-size:.8rem;">{{ $type }}</td>
                            <td style="font-size:.78rem;color:var(--gray-600);">{{ $cmd }}</td>
                            <td>
                                <span class="status-badge {{ $badgeClass }}">
                                    <i class="{{ $statusIcon }}" style="font-size:.65rem;"></i> {{ $statusLabel }}
                                </span>
                            </td>
                            <td style="font-size:.78rem;color:var(--gray-500);">{{ $date }}</td>
                            <td style="font-size:.78rem;color:var(--gray-600);max-width:200px;">
                                @if($remarks)
                                <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $remarks }}</span>
                                @else
                                <span style="color:var(--gray-300);">—</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:4px;">
                                    <button class="btn-nis btn-ghost btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#detailModal"
                                        data-period="{{ $period }}" data-type="{{ $type }}"
                                        data-status="{{ $statusLabel }}" data-badge="{{ $badgeClass }}"
                                        data-date="{{ $date }}" data-reviewer="{{ $reviewer ?? 'Pending' }}"
                                        data-remarks="{{ $remarks ?? 'No remarks yet.' }}"
                                        data-path="{{ $path }}"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($status === 'queried')
                                    <a href="{{ url('/user/returns/create') }}" class="btn-nis btn-sm" style="background:var(--gold-50);border:1px solid var(--gold-300);color:var(--gold-700);padding:4px 8px;" title="Edit & Resubmit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    @if($status === 'approved')
                                    <button class="btn-nis btn-ghost btn-sm" title="Download PDF" onclick="REDAS.showToast('Preparing PDF…','info')">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <!-- Expand row: workflow timeline -->
                        <tr class="sub-row-expand" id="expand-{{ $i }}">
                            <td colspan="8" style="padding:16px 20px 20px;">
                                <div style="display:flex;align-items:flex-start;gap:32px;flex-wrap:wrap;">
                                    <!-- Timeline -->
                                    <div>
                                        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--gray-400);margin-bottom:12px;letter-spacing:.05em;">Workflow Status</div>
                                        <div class="status-timeline">
                                            @php
                                            $steps = [
                                                ['Drafted',      'fas fa-pencil-alt', $status !== 'draft' ? 'done' : 'active'],
                                                ['Submitted',    'fas fa-paper-plane', in_array($status,['pending','approved','queried','rejected']) ? 'done' : 'pending'],
                                                ['Under Review', 'fas fa-search',      in_array($status,['approved','queried','rejected']) ? ($status==='queried'?'queried':'done') : ($status==='pending'?'active':'pending')],
                                                ['Decision',     'fas fa-gavel',       $status==='approved'?'done':($status==='rejected'?'rejected':($status==='queried'?'queried':'pending'))],
                                                ['Closed',       'fas fa-check-double',$status==='approved'?'done':'pending'],
                                            ];
                                            @endphp
                                            @foreach($steps as $si => [$slabel, $sicon, $sstate])
                                            <div class="tl-step {{ $sstate }}">
                                                <div class="step-dot"><i class="{{ $sicon }}"></i></div>
                                                <div class="step-label">{{ $slabel }}</div>
                                            </div>
                                            @if(!$loop->last)
                                            <div class="tl-connector {{ in_array($sstate,['done']) ? 'done' : '' }}"></div>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    <!-- Detail -->
                                    <div style="flex:1;min-width:200px;">
                                        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--gray-400);margin-bottom:8px;letter-spacing:.05em;">Routing</div>
                                        <div style="font-size:.82rem;color:var(--gray-700);">
                                            <i class="fas fa-route" style="color:var(--nis-600);width:16px;"></i>
                                            {{ auth()->user()->name }} → {{ $path === 'hq' ? 'HQ Coordinator' : 'Zonal Coordinator' }}
                                            @if($reviewer) → <strong>{{ $reviewer }}</strong> @endif
                                        </div>
                                        @if($remarks)
                                        <div style="margin-top:10px;background:{{ $status === 'queried' ? '#fff7ed' : ($status === 'approved' ? '#f0fdf4' : '#f9fafb') }};border:1px solid {{ $status === 'queried' ? '#fed7aa' : ($status === 'approved' ? '#bbf7d0' : '#e5e7eb') }};border-radius:var(--radius-sm);padding:10px 12px;">
                                            <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--gray-400);margin-bottom:4px;">Supervisor Remarks</div>
                                            <div style="font-size:.82rem;color:var(--gray-700);">{{ $remarks }}</div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Detail Modal -->
{{-- <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:var(--radius-lg);border:none;overflow:hidden;">
            <div class="modal-header" style="background:var(--nis-700);color:#fff;border:none;padding:14px 20px;">
                <div>
                    <h5 class="modal-title" style="font-weight:700;font-size:.95rem;" id="detailTitle">Return Details</h5>
                    <div style="font-size:.74rem;opacity:.8;" id="detailSub">—</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div><div style="font-size:.68rem;font-weight:700;text-transform:uppercase;color:var(--gray-400);margin-bottom:3px;">Return Type</div><div style="font-weight:600;" id="dType">—</div></div>
                    <div><div style="font-size:.68rem;font-weight:700;text-transform:uppercase;color:var(--gray-400);margin-bottom:3px;">Date Submitted</div><div style="font-weight:600;" id="dDate">—</div></div>
                    <div><div style="font-size:.68rem;font-weight:700;text-transform:uppercase;color:var(--gray-400);margin-bottom:3px;">Status</div><div id="dStatus">—</div></div>
                    <div><div style="font-size:.68rem;font-weight:700;text-transform:uppercase;color:var(--gray-400);margin-bottom:3px;">Reviewed By</div><div style="font-weight:600;" id="dReviewer">—</div></div>
                </div>
                <!-- Notification / status message -->
                <div id="dNotification" style="display:none;border-radius:var(--radius-md);padding:12px 16px;font-size:.84rem;margin-bottom:16px;display:flex;gap:10px;align-items:flex-start;">
                    <i class="fas" id="dNotifIcon" style="margin-top:2px;flex-shrink:0;"></i>
                    <div><strong id="dNotifTitle"></strong><br><span id="dNotifMsg"></span></div>
                </div>
                <div>
                    <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;color:var(--gray-400);margin-bottom:6px;">Supervisor Remarks</div>
                    <div style="background:var(--gray-50);border-radius:var(--radius-md);padding:12px;font-size:.875rem;color:var(--gray-700);border:1px solid var(--gray-200);" id="dRemarks">—</div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--gray-100);padding:12px 20px;gap:8px;">
                <button class="btn-nis btn-ghost btn-sm" data-bs-dismiss="modal">Close</button>
                <button class="btn-nis btn-outline-nis btn-sm" id="downloadBtn"><i class="fas fa-download"></i> Download PDF</button>
                <a id="editBtn" href="{{ url('/user/returns/create') }}" class="btn-nis btn-sm" style="display:none;background:var(--gold-50);border:1px solid var(--gold-400);color:var(--gold-700);">
                    <i class="fas fa-edit"></i> Edit &amp; Resubmit
                </a>
            </div>
        </div>
    </div>
</div> --}}

@include('partials.footer')