@include ('partials.header2')
    <!-- ─── Page Content ─── -->
    <main class="redas-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Supervisor Dashboard</h1>
                <p class="page-subtitle">
                    {{ auth()->user()->role === 'zonal' ? 'Zonal Command Overview' : 'State Command Overview' }} —
                    {{ now()->format('l, d F Y') }}
                </p>
            </div>
            <div style="display:flex;gap:8px;">
                <a href="{{ url('/supervisor/reports/generate') }}" class="btn-nis btn-ghost">
                    <i class="fas fa-file-export"></i> Export Report
                </a>
                <a href="{{ url('/supervisor/approvals') }}" class="btn-nis btn-primary-nis">
                    <i class="fas fa-tasks"></i> Review Queue (5)
                </a>
            </div>
        </div>

        <!-- ── Stats ── -->
        <div class="stats-grid animate-fade-up">
            <div class="stat-card info">
                <div class="stat-header">
                    <span class="stat-label">Formations in Zone</span>
                    <span class="stat-icon"><i class="fas fa-map-marker-alt"></i></span>
                </div>
                <div class="stat-value" data-count="12">0</div>
                <div class="stat-change neutral"><i class="fas fa-building"></i> State commands</div>
            </div>
            <div class="stat-card green">
                <div class="stat-header">
                    <span class="stat-label">Returns Submitted</span>
                    <span class="stat-icon"><i class="fas fa-paper-plane"></i></span>
                </div>
                <div class="stat-value" data-count="9">0</div>
                <div class="stat-change up"><i class="fas fa-arrow-up"></i> 75% compliance</div>
            </div>
            <div class="stat-card gold">
                <div class="stat-header">
                    <span class="stat-label">Pending Your Review</span>
                    <span class="stat-icon"><i class="fas fa-hourglass-half"></i></span>
                </div>
                <div class="stat-value" data-count="5">0</div>
                <div class="stat-change down" style="color:var(--color-danger);"><i class="fas fa-exclamation-triangle"></i> Needs action</div>
            </div>
            <div class="stat-card danger">
                <div class="stat-header">
                    <span class="stat-label">Overdue / Non-Compliant</span>
                    <span class="stat-icon"><i class="fas fa-clock"></i></span>
                </div>
                <div class="stat-value" data-count="3">0</div>
                <div class="stat-change down"><i class="fas fa-arrow-up"></i> Follow up required</div>
            </div>
        </div>

        <!-- ── Main Grid ── -->
        <div class="grid-2" style="margin-bottom:24px;">

            <!-- Pending Approvals Queue -->
            <div class="redas-card delay-2">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#fee2e2;color:#dc2626;"><i class="fas fa-inbox"></i></div>
                        Pending Approval Queue
                        <span class="status-badge badge-rejected" style="margin-left:8px;">5 Awaiting</span>
                    </div>
                    <a href="{{ url('/supervisor/approvals') }}" class="btn-nis btn-ghost btn-sm">View All</a>
                </div>
                <div class="card-body no-pad">
                    @foreach([
                        ['Abuja State Command',    'May 2025', 'Monthly',   'DCI Amaka Okafor',   '2025-05-03'],
                        ['Kano State Command',     'May 2025', 'Monthly',   'DCI Ibrahim Yusuf',  '2025-05-04'],
                        ['Lagos State Command',    'May 2025', 'Monthly',   'DCI Chidi Eze',      '2025-05-01'],
                        ['Rivers State Command',   'May 2025', 'Monthly',   'DCI Ngozi Obi',      '2025-05-02'],
                        ['Ogun State Command',     'May 2025', 'Monthly',   'DCI Bayo Adeyemi',   '2025-05-05'],
                    ] as [$formation, $period, $type, $officer, $date])
                    <div class="approval-item">
                        <div style="width:36px;height:36px;border-radius:var(--radius-md);background:var(--nis-50);color:var(--nis-600);display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="approval-item-info">
                            <div class="approval-item-title">{{ $formation }}</div>
                            <div class="approval-item-meta">{{ $period }} {{ $type }} Return &bull; {{ $officer }} &bull; {{ $date }}</div>
                        </div>
                        <div class="approval-actions">
                            <button class="btn-nis btn-sm" style="background:#dcfce7;color:#15803d;border:1px solid #86efac;" data-action="approve" data-id="{{ $loop->index }}" title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn-nis btn-sm" style="background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;" data-action="reject" data-id="{{ $loop->index }}" title="Return with comments">
                                <i class="fas fa-times"></i>
                            </button>
                            <button class="btn-nis btn-ghost btn-sm" title="View report">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Formation Compliance -->
            <div class="redas-card delay-3">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);"><i class="fas fa-chart-pie"></i></div>
                        Formation Compliance — May 2025
                    </div>
                    <span style="font-size:.72rem;color:var(--gray-400);">{{ now()->format('F Y') }}</span>
                </div>
                <div class="card-body">
                    @foreach([
                        ['Abuja State',     95, 'green'],
                        ['Kano State',      88, 'green'],
                        ['Lagos State',     92, 'green'],
                        ['Rivers State',    75, 'gold'],
                        ['Ogun State',      80, 'green'],
                        ['Kaduna State',    0,  'danger'],
                        ['Benue State',     65, 'gold'],
                        ['Niger State',     72, 'gold'],
                        ['Plateau State',   90, 'green'],
                        ['Kwara State',     55, 'danger'],
                        ['Enugu State',     85, 'green'],
                        ['Imo State',       0,  'danger'],
                    ] as [$name, $pct, $color])
                    <div class="compliance-row">
                        <div class="compliance-name">{{ $name }}</div>
                        <div class="compliance-bar-wrap">
                            <div class="progress-nis">
                                <div class="progress-bar-nis {{ $color }}" data-width="{{ $pct }}%" style="width:0%;"></div>
                            </div>
                        </div>
                        <div class="compliance-pct" style="color:{{ $pct >= 80 ? 'var(--color-success)' : ($pct >= 60 ? 'var(--color-warning)' : 'var(--color-danger)') }};">
                            @if($pct === 0) <span style="font-size:.68rem;">Not submitted</span>
                            @else {{ $pct }}%
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ── Second Row ── -->
        <div class="grid-2">
            <!-- Analytics Chart -->
            <div class="redas-card delay-4">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);"><i class="fas fa-chart-bar"></i></div>
                        Zone Compliance by Formation
                    </div>
                </div>
                <div class="card-body" style="height:240px;">
                    <canvas id="complianceChart"></canvas>
                </div>
            </div>

            <!-- Submission Timeline -->
            <div class="redas-card delay-5">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:var(--gold-100);color:var(--gold-500);"><i class="fas fa-history"></i></div>
                        Recently Approved
                    </div>
                    <a href="{{ url('/supervisor/approved') }}" class="btn-nis btn-ghost btn-sm">All Approved</a>
                </div>
                <div class="card-body no-pad">
                    <table class="redas-table">
                        <thead>
                            <tr>
                                <th>Formation</th>
                                <th>Period</th>
                                <th>Approved</th>
                                <th>By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach([
                                ['Abuja State',    'Apr 2025', '01 May', 'Self'],
                                ['Kano State',     'Apr 2025', '30 Apr', 'Self'],
                                ['Lagos State',    'Apr 2025', '02 May', 'Self'],
                                ['Rivers State',   'Mar 2025', '02 Apr', 'Self'],
                                ['Ogun State',     'Mar 2025', '03 Apr', 'Self'],
                            ] as [$f, $p, $d, $by])
                            <tr>
                                <td style="font-weight:600;font-size:.84rem;">{{ $f }}</td>
                                <td><span class="status-badge badge-draft">{{ $p }}</span></td>
                                <td style="font-size:.78rem;color:var(--gray-500);">{{ $d }}</td>
                                <td style="font-size:.78rem;color:var(--gray-500);">{{ $by }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>


<!-- ─── Reject/Return Modal ─── -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius-lg);border:none;overflow:hidden;">
            <div class="modal-header" style="background:#dc2626;color:white;border:none;padding:14px 20px;">
                <h5 class="modal-title" style="font-weight:700;font-size:.95rem;">
                    <i class="fas fa-undo me-2"></i>Return Report for Correction
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <input type="hidden" id="rejectTargetId">
                <div class="auth-form-group">
                    <label class="form-label-nis">Reason for Returning <span style="color:var(--color-danger);">*</span></label>
                    <select class="form-control-nis" id="rejectReason">
                        <option value="">Select a reason…</option>
                        <option value="inconsistent">Data inconsistent with previous period</option>
                        <option value="incomplete">Mandatory fields incomplete</option>
                        <option value="wrong_period">Wrong reporting period selected</option>
                        <option value="validation">Figures fail validation checks</option>
                        <option value="other">Other (specify below)</option>
                    </select>
                </div>
                <div class="auth-form-group" style="margin-top:14px;">
                    <label class="form-label-nis">Detailed Comments</label>
                    <textarea class="form-control-nis" id="rejectComment" rows="4" placeholder="Provide clear guidance to the officer on what to correct…"></textarea>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--gray-100);padding:14px 20px;display:flex;gap:8px;justify-content:flex-end;">
                <button class="btn-nis btn-ghost btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button class="btn-nis btn-sm" style="background:#dc2626;color:white;" onclick="submitReject()">
                    <i class="fas fa-undo"></i> Return to Officer
                </button>
            </div>
        </div>
    </div>
</div>

@include ('partials.footer2')