@include('partials.ict-header')

<main class="redas-content animate-fade-up">
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
                Submitted Returns
            </h1>
            <p class="page-subtitle">Track the status of all your ICT &amp; Cybersecurity Directorate Annual Operational Reports.</p>
        </div>
        <a href="{{ route('ict.report') }}" class="btn-nis btn-primary-nis">
            <i class="fas fa-plus"></i> New Report
        </a>
    </div>

    <!-- Stats row -->
    <div class="stats-grid animate-fade-up" style="margin-bottom:20px;">
        @php
        $totalCount = $submissions->count();
        $pendingCount = $submissions->where('status', 'pending')->count();
        $approvedCount = $submissions->where('status', 'approved')->count();
        $draftCount = $submissions->where('status', 'draft')->count();

        $subStats = [
            ['Total Reports', $totalCount, 'fas fa-copy', 'green', 'All entries'],
            ['Pending Review', $pendingCount, 'fas fa-hourglass-half', 'gold', 'Awaiting supervisor'],
            ['Approved', $approvedCount, 'fas fa-check-circle', 'green', 'Finalized reports'],
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
                            'pending'  => ['badge-pending',  'Pending Review',  'fas fa-hourglass-half'],
                            'approved' => ['badge-approved', 'Approved',        'fas fa-check-circle'],
                            'queried'  => ['badge-review',   'Queried',         'fas fa-question-circle'],
                            'rejected' => ['badge-rejected', 'Rejected',        'fas fa-times-circle'],
                            'draft'    => ['badge-draft',    'Draft',           'fas fa-pencil-alt'],
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
                            <td style="font-size:.8rem;">ICT &amp; Cybersecurity Annual Report</td>
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
                                <div style="display:flex;gap:4px;">
                                    <button class="btn-nis btn-ghost btn-sm" onclick="viewModal({{ e(json_encode($sub)) }})" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if(in_array($sub->status, ['draft', 'queried']))
                                        <a href="{{ route('ict.report') }}" class="btn-nis btn-sm" style="background:var(--gold-50);border:1px solid var(--gold-300);color:var(--gold-700);padding:4px 8px;" title="Edit Report">
                                            <i class="fas fa-edit"></i>
                                        </a>
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

                                    <!-- Projects -->
                                    <div class="redas-card" style="margin: 0; box-shadow: none; border: 1px solid var(--gray-200);">
                                        <div class="card-head" style="padding: 8px 12px; background: #f1f5f9;"><small><strong>Projects &amp; Systems</strong></small></div>
                                        <div class="card-body" style="padding: 10px; font-size: 0.8rem;">
                                            @php $projects = $data['projects'] ?? []; @endphp
                                            <div>Total System Projects: <strong>{{ count($projects) }}</strong></div>
                                        </div>
                                    </div>

                                    <!-- Cybersecurity Deployed -->
                                    <div class="redas-card" style="margin: 0; box-shadow: none; border: 1px solid var(--gray-200);">
                                        <div class="card-head" style="padding: 8px 12px; background: #f1f5f9;"><small><strong>Cybersecurity Controls</strong></small></div>
                                        <div class="card-body" style="padding: 10px; font-size: 0.8rem;">
                                            @php $cyber = $data['cybersecurity'] ?? []; @endphp
                                            <div>Security Deployments: <strong>{{ count($cyber) }} Controls</strong></div>
                                        </div>
                                    </div>

                                    <!-- MIDAS Deployments -->
                                    <div class="redas-card" style="margin: 0; box-shadow: none; border: 1px solid var(--gray-200);">
                                        <div class="card-head" style="padding: 8px 12px; background: #f1f5f9;"><small><strong>MIDAS Deployments</strong></small></div>
                                        <div class="card-body" style="padding: 10px; font-size: 0.8rem;">
                                            @php $midas = $data['midas'] ?? []; @endphp
                                            <div>MIDAS Site Commands: <strong>{{ count($midas) }} Commands</strong></div>
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
            
            <h6 style="font-weight: 700; color: var(--gray-700); margin: 0 0 10px 0;">Summary Metrics</h6>
            <div id="mMetrics" style="font-size: 0.86rem; color: var(--gray-600); display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px;">
                <!-- Filled dynamically -->
            </div>

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
        metricsHtml += `<div><i class="fas fa-users" style="width: 20px;"></i> Staff Strength: <strong>${staffTotal} Officers</strong></div>`;
    }
    
    if (data.projects) {
        metricsHtml += `<div><i class="fas fa-bars-progress" style="width: 20px;"></i> Project Activities: <strong>${data.projects.length} Projects</strong></div>`;
    }

    let totalIncidents = 0;
    const incTypes = ['incidents_hardware', 'incidents_software', 'incidents_network', 'incidents_cybersecurity', 'incidents_power', 'incidents_communication', 'incidents_surveillance', 'incidents_providers'];
    incTypes.forEach(type => {
        if (data[type]) totalIncidents += data[type].length;
    });
    metricsHtml += `<div><i class="fas fa-triangle-exclamation" style="width: 20px;"></i> System Incidents: <strong>${totalIncidents} Recorded</strong></div>`;

    if (data.maintenance) {
        metricsHtml += `<div><i class="fas fa-tools" style="width: 20px;"></i> Maintenance Work Orders: <strong>${data.maintenance.length} Assets</strong></div>`;
    }

    if (data.software) {
        metricsHtml += `<div><i class="fas fa-code" style="width: 20px;"></i> Software Developed: <strong>${data.software.length} Solutions</strong></div>`;
    }
    
    if (data.cybersecurity) {
        metricsHtml += `<div><i class="fas fa-user-shield" style="width: 20px;"></i> Security Deployments: <strong>${data.cybersecurity.length} Controls</strong></div>`;
    }

    if (data.midas) {
        metricsHtml += `<div><i class="fas fa-server" style="width: 20px;"></i> MIDAS Deployment Sites: <strong>${data.midas.length} Commands</strong></div>`;
    }
    
    document.getElementById('mMetrics').innerHTML = metricsHtml || '<div>No operational metrics saved.</div>';
    
    const modal = document.getElementById('subModal');
    modal.style.display = 'flex';
}

function closeModal() {
    document.getElementById('subModal').style.display = 'none';
}
</script>

@include('partials.footer')
