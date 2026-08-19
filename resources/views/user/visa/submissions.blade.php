@include('partials.visa-header')

<main class="redas-content">
    <!-- Status message -->
    @if(session('status'))
        <div style="background:#ecfdf5;border:1px solid #86efac;color:#166534;border-radius:12px;padding:12px 14px;margin-bottom:18px;">
            <i class="fas fa-check-circle"></i>
            {{ session('status') }}
        </div>
    @endif

    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="fas fa-folder-open"></i>
                Submitted Reports
            </h1>
            <p class="page-subtitle">Track the status of all your Visa & Residence Directorate Annual Operational Reports.</p>
        </div>
        <a href="{{ route('visa.report') }}" class="btn-nis btn-primary-nis">
            <i class="fas fa-plus"></i> New Report
        </a>
    </div>

    <!-- Stats row -->
    <div class="stats-grid animate-fade-up" style="margin-bottom:20px;">
        @php
        $totalCount = $submissions->count();
        $pendingCount = $submissions->where('status', 'pending')->count();
        $approvedCount = $submissions->whereIn('status', ['approved', 'submitted'])->count();
        $draftCount = $submissions->where('status', 'draft')->count();

        $subStats = [
            ['Total Reports', $totalCount, 'fas fa-copy', 'green', 'All entries'],
            ['Pending Approval', $pendingCount, 'fas fa-hourglass-half', 'gold', 'Awaiting supervisor'],
            ['Approved / Submitted', $approvedCount, 'fas fa-check-circle', 'green', 'Finalized reports'],
            ['Drafts', $draftCount, 'fas fa-pencil-alt', 'warning', 'Work in progress'],
        ];
        @endphp
        @foreach($subStats as [$label, $val, $icon, $color, $subText])
        <div class="stat-card {{ $color }}">
            <div class="stat-header">
                <span class="stat-label">{{ $label }}</span>
                <span class="stat-icon"><i class="{{ $icon }}"></i></span>
            </div>
            <div class="stat-value">{{ $val }}</div>
            <div class="stat-change neutral">{{ $subText }}</div>
        </div>
        @endforeach
    </div>

    <!-- Submissions table -->
    <div class="redas-card animate-fade-up">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:var(--purple-50);color:var(--purple-600);">
                    <i class="fas fa-list-ul"></i>
                </div>
                Reports List
            </div>
        </div>
        <div class="card-body no-pad">
            @if($submissions->isEmpty())
                <div style="padding: 40px; text-align: center; color: var(--gray-400);">
                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 12px; display: block;"></i>
                    No reports submitted yet. Click "New Report" to start.
                </div>
            @else
                <table class="redas-table">
                    <thead>
                        <tr>
                            <th style="width:40px;"></th>
                            <th>Reporting Period</th>
                            <th>Return Type</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th>Supervisor Remarks</th>
                            <th style="width:120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $statusConfig = [
                            'pending'   => ['badge-pending',  'Awaiting Approval', 'fas fa-hourglass-half'],
                            'approved'  => ['badge-approved', 'Approved',          'fas fa-check-circle'],
                            'queried'   => ['badge-review',   'Queried',           'fas fa-question-circle'],
                            'rejected'  => ['badge-rejected', 'Rejected',          'fas fa-times-circle'],
                            'draft'     => ['badge-draft',    'Draft',             'fas fa-pencil-alt'],
                            'submitted' => ['badge-approved', 'Submitted',         'fas fa-check-double'],
                        ];
                        @endphp
                        @foreach($submissions as $i => $sub)
                        @php 
                        [$badgeClass, $statusLabel, $statusIcon] = $statusConfig[$sub->status] ?? ['badge-draft','Unknown','fas fa-circle']; 
                        $data = $sub->return_data ?? [];
                        @endphp
                        <tr class="sub-main-row">
                            <td>
                                <button class="btn-nis btn-ghost btn-sm" onclick="toggleDetails({{ $sub->id }})" style="padding:3px 6px;color:var(--gray-400);" title="Toggle Details">
                                    <i class="fas fa-chevron-right" id="chevron-{{ $sub->id }}"></i>
                                </button>
                            </td>
                            <td><strong>Year {{ $sub->period }}</strong></td>
                            <td style="font-size:.8rem;">Visa & Residence Annual Report</td>
                            <td>
                                <span class="status-badge {{ $badgeClass }}">
                                    <i class="{{ $statusIcon }}" style="font-size:.65rem;"></i> {{ $statusLabel }}
                                </span>
                            </td>
                            <td style="font-size:.78rem;color:var(--gray-500);">{{ $sub->updated_at->format('d M Y, H:i') }}</td>
                            <td style="font-size:.78rem;color:var(--gray-600);max-width:200px;">
                                @if($sub->comments)
                                    <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $sub->comments }}</span>
                                @else
                                    <span style="color:var(--gray-300);">—</span>
                                @endif
                            </td>
                            <td>
                                 <div style="display:flex;gap:4px;align-items:center;">
                                      @if(in_array($sub->status, ['draft', 'queried']) && auth()->user()->user_category === 'directorate_user')
                                          <a href="{{ route('visa.report', ['year' => $sub->period, 'id' => $sub->id]) }}" class="btn-nis btn-sm" style="background:var(--gold-50);border:1px solid var(--gold-300);color:var(--gold-700);padding:4px 8px;" title="Edit Report">
                                              <i class="fas fa-edit"></i>
                                          </a>
                                      @endif

                                      @if(in_array($sub->status, ['pending', 'approved', 'submitted']) || auth()->user()->user_category === 'directorate_admin' || auth()->user()->role === 'admin')
                                          <a href="{{ route('visa.report', ['year' => $sub->period, 'id' => $sub->id]) }}" class="btn-nis btn-sm" style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:4px 8px;display:inline-flex;align-items:center;" title="View Details">
                                              <i class="fas fa-eye"></i>
                                          </a>
                                      @endif

                                    @if($sub->status === 'approved')
                                        <form action="{{ route('visa.submit', $sub->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn-nis btn-sm" style="background:#dcfce7;border:1px solid #86efac;color:#15803d;padding:4px 8px;font-weight:700;" title="Submit to Headquarters">
                                                <i class="fas fa-paper-plane"></i> Submit
                                            </button>
                                        </form>
                                    @endif

                                    @if(auth()->user()->user_category === 'directorate_admin' || auth()->user()->role === 'admin')
                                        @if($sub->status === 'pending')
                                            <form action="{{ route('visa.approve', $sub->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn-nis btn-sm" style="background:#dcfce7;border:1px solid #86efac;color:#15803d;padding:4px 8px;" title="Approve Report">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                            <button class="btn-nis btn-sm" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;padding:4px 8px;" onclick="openQueryModal('visa', {{ $sub->id }})" title="Query Report">
                                                <i class="fas fa-question-circle"></i> Query
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Expandable Details Row -->
                        <tr id="details-{{ $sub->id }}" style="display: none; background: #fafafa;">
                            <td colspan="7" style="padding: 20px;">
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
                                    <!-- Staff Strength -->
                                    <div class="redas-card" style="margin: 0; box-shadow: none; border: 1px solid var(--gray-200);">
                                        <div class="card-head" style="padding: 8px 12px; background: #f1f5f9;"><small><strong>Staff Strength</strong></small></div>
                                        <div class="card-body" style="padding: 10px; font-size: 0.8rem;">
                                            @php 
                                            $staff = $data['staff'] ?? [];
                                            $totalStaff = 0;
                                            $rankLabels = [
                                                'dcg'=>'DCG', 'acg'=>'ACG', 'cis'=>'CIS', 'dci'=>'DCI', 'aci'=>'ACI',
                                                'csi'=>'CSI', 'si'=>'SI', 'dsi'=>'DSI', 'asi_1'=>'ASI 1', 'asi_2'=>'ASI 2',
                                                'ii'=>'II', 'aii'=>'AII', 'ia1'=>'IA1', 'ia2'=>'IA2', 'ia3'=>'IA3',
                                                'comptroller'=>'Comptroller Cadre', 'superintendent'=>'Superintendent Cadre',
                                                'inspectorate'=>'Inspectorate Cadre', 'assistant'=>'Assistant Cadre'
                                            ];
                                            @endphp
                                            @foreach($staff as $key => $genders)
                                                @php
                                                $sum = ((int)($genders['male'] ?? 0)) + ((int)($genders['female'] ?? 0));
                                                $totalStaff += $sum;
                                                $label = $rankLabels[$key] ?? ($genders['cadre'] ?? strtoupper($key));
                                                @endphp
                                                <div>{{ $label }}: {{ $sum }}</div>
                                            @endforeach
                                            <hr style="margin: 6px 0;">
                                            <strong>Total: {{ $totalStaff }} Officers</strong>
                                        </div>
                                    </div>

                                    <!-- e-Migrant statistics -->
                                    <div class="redas-card" style="margin: 0; box-shadow: none; border: 1px solid var(--gray-200);">
                                        <div class="card-head" style="padding: 8px 12px; background: #f1f5f9;"><small><strong>e-Migrant Centre</strong></small></div>
                                        <div class="card-body" style="padding: 10px; font-size: 0.8rem;">
                                            @php
                                            $emigrants = $data['emigrant'] ?? [];
                                            $totalEmigrants = 0;
                                            foreach($emigrants as $em) {
                                                $totalEmigrants += ((int)($em['regular'] ?? 0)) + ((int)($em['irregular'] ?? 0));
                                            }
                                            @endphp
                                            <div>Nationalities Recorded: {{ count($emigrants) }}</div>
                                            <hr style="margin: 6px 0;">
                                            <strong>Total Migrants: {{ $totalEmigrants }}</strong>
                                        </div>
                                    </div>

                                    <!-- CERPAC Production -->
                                    <div class="redas-card" style="margin: 0; box-shadow: none; border: 1px solid var(--gray-200);">
                                        <div class="card-head" style="padding: 8px 12px; background: #f1f5f9;"><small><strong>CERPAC Production</strong></small></div>
                                        <div class="card-body" style="padding: 10px; font-size: 0.8rem;">
                                            @php $cerpac = $data['cerpac'] ?? []; @endphp
                                            <div>Supplied: {{ $cerpac['supplied'] ?? 0 }}</div>
                                            <div>Produced: {{ $cerpac['produced'] ?? 0 }}</div>
                                            <div>Damaged: {{ $cerpac['damaged'] ?? 0 }}</div>
                                            <div>Issued: {{ $cerpac['issued'] ?? 0 }}</div>
                                        </div>
                                    </div>

                                    <!-- Free Trade Zone -->
                                    <div class="redas-card" style="margin: 0; box-shadow: none; border: 1px solid var(--gray-200);">
                                        <div class="card-head" style="padding: 8px 12px; background: #f1f5f9;"><small><strong>Free Trade Zone</strong></small></div>
                                        <div class="card-body" style="padding: 10px; font-size: 0.8rem;">
                                            @php $ftz = $data['ftz'] ?? []; @endphp
                                            <div>Free Zones: {{ $ftz['zones'] ?? 0 }}</div>
                                            <div>Enterprises: {{ $ftz['enterprises'] ?? 0 }}</div>
                                            <div>Expatriates: {{ $ftz['expatriates'] ?? 0 }}</div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</main>

<!-- Details Modal -->
<div id="subModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; width: 100%; max-width: 650px; border-radius: var(--radius-lg); overflow: hidden; display: flex; flex-direction: column; max-height: 90vh; box-shadow: var(--shadow-2xl);">
        <div style="background: var(--nis-700); color: #fff; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h5 style="margin: 0; font-weight: 700; font-size: 1rem;">Report Data Summary</h5>
            <button onclick="closeModal()" style="background: none; border: none; color: #fff; cursor: pointer; font-size: 1.2rem;">&times;</button>
        </div>
        <div style="padding: 20px; overflow-y: auto; flex: 1;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 20px; background: #f8fafc; padding: 14px; border-radius: var(--radius-md);">
                <div>
                    <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--gray-400);">Reporting Year</div>
                    <strong id="mPeriod" style="font-size: 0.9rem;">—</strong>
                </div>
                <div>
                    <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--gray-400);">Status</div>
                    <strong id="mStatus" style="font-size: 0.9rem;">—</strong>
                </div>
            </div>
            
            <table class="summary-metrics-table" style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 0.88rem; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                <thead>
                    <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 12px 14px; text-align: left; font-size: 0.74rem; font-weight: 600; text-transform: uppercase; color: #475569;">Metric Category</th>
                        <th style="padding: 12px 14px; text-align: right; font-size: 0.74rem; font-weight: 600; text-transform: uppercase; color: #475569;">Value / Count</th>
                    </tr>
                </thead>
                <tbody id="mMetricsTableBody">
                    <!-- Filled dynamically -->
                </tbody>
            </table>

            <h6 style="font-weight: 700; color: var(--gray-700); margin: 0 0 10px 0;">Remarks / Comments</h6>
            <div id="mRemarks" style="background: #f8fafc; border: 1px solid var(--gray-200); border-radius: var(--radius-md); padding: 10px; font-size: 0.84rem; color: var(--gray-700);">
                —
            </div>
        </div>
        <div style="padding: 12px 20px; border-top: 1px solid var(--gray-100); display: flex; justify-content: flex-end; gap: 10px; background: #f8fafc;">
            <button onclick="closeModal()" class="btn-nis btn-ghost btn-sm">Close</button>
        </div>
    </div>
</div>

<script>
function toggleDetails(id) {
    const row = document.getElementById('details-' + id);
    const chevron = document.getElementById('chevron-' + id);
    if (row.style.display === 'none') {
        row.style.display = 'table-row';
        chevron.className = 'fas fa-chevron-down';
    } else {
        row.style.display = 'none';
        chevron.className = 'fas fa-chevron-right';
    }
}

function viewModal(sub) {
    document.getElementById('mPeriod').innerText = "Year " + sub.period;
    document.getElementById('mStatus').innerText = sub.status.toUpperCase();
    document.getElementById('mRemarks').innerText = sub.comments || "No supervisor remarks/comments yet.";
    
    // Parse metric data
    let metricsHtml = '';
    const data = sub.return_data || {};
    
    if (data.staff) {
        let staffTotal = 0;
        Object.keys(data.staff).forEach(cadre => {
            staffTotal += parseInt(data.staff[cadre].male || 0) + parseInt(data.staff[cadre].female || 0);
        });
        metricsHtml += `<tr>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0;"><i class="fas fa-users" style="width: 20px; color:#475569; margin-right:8px;"></i> Staff Strength</td>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 600; color:#0f172a;">${staffTotal} Officers</td>
        </tr>`;
    }
    
    if (data.emigrant) {
        let migrantTotal = 0;
        data.emigrant.forEach(em => {
            migrantTotal += parseInt(em.regular || 0) + parseInt(em.irregular || 0);
        });
        metricsHtml += `<tr>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0;"><i class="fas fa-globe-africa" style="width: 20px; color:#475569; margin-right:8px;"></i> e-Migrant Centre</td>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 600; color:#0f172a;">${migrantTotal} Migrants (${data.emigrant.length} nat.)</td>
        </tr>`;
    }

    if (data.quota) {
        let quotaTotal = 0;
        data.quota.forEach(q => {
            quotaTotal += parseInt(q.positions || 0);
        });
        metricsHtml += `<tr>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0;"><i class="fas fa-users-cog" style="width: 20px; color:#475569; margin-right:8px;"></i> Quota Administration</td>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 600; color:#0f172a;">${quotaTotal} Positions (${data.quota.length} companies)</td>
        </tr>`;
    }

    if (data.cerpac) {
        metricsHtml += `<tr>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0;"><i class="fas fa-address-card" style="width: 20px; color:#475569; margin-right:8px;"></i> CERPAC Cards Issued</td>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 600; color:#0f172a;">${data.cerpac.issued || 0} Cards</td>
        </tr>`;
    }

    if (data.ftz) {
        metricsHtml += `<tr>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0;"><i class="fas fa-warehouse" style="width: 20px; color:#475569; margin-right:8px;"></i> FTZ Enterprises</td>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 600; color:#0f172a;">${data.ftz.enterprises || 0} Enterprises (${data.ftz.zones || 0} Zones)</td>
        </tr>`;
    }
    
    document.getElementById('mMetricsTableBody').innerHTML = metricsHtml || '<tr><td colspan="2" style="text-align:center; padding:12px; color:var(--gray-400);">No operational metrics saved.</td></tr>';
    
    const modal = document.getElementById('subModal');
    modal.style.display = 'flex';
}

function closeModal() {
    document.getElementById('subModal').style.display = 'none';
}

function openQueryModal(type, id) {
    const remarks = prompt("Enter supervisor remarks / query reason:", "Please review this report.");
    if (remarks !== null) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/user/directorate/${type}/submissions/${id}/query`;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
            || '{{ csrf_token() }}';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = csrfToken;
        form.appendChild(tokenInput);
        
        const remarksInput = document.createElement('input');
        remarksInput.type = 'hidden';
        remarksInput.name = 'remarks';
        remarksInput.value = remarks;
        form.appendChild(remarksInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-view-details').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const sub = JSON.parse(this.getAttribute('data-report'));
                viewModal(sub);
            } catch (e) {
                console.error("Error parsing report data:", e);
            }
        });
    });
});
</script>

@include('partials.footer')