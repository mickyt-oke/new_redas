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
                Submitted Reports
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
                            <th>Reporting Period</th>
                            <th>Return Type</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th>Supervisor Remarks</th>
                            <th>Actions</th>
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
                            <td style="white-space: nowrap;">
                                 <div style="display:flex;gap:4px;align-items:center;">
                                      @if(in_array($sub->status, ['draft', 'queried']) && auth()->user()->user_category === 'directorate_user')
                                          <a href="{{ route('ict.report', ['year' => $sub->period, 'id' => $sub->id]) }}" class="btn-nis btn-sm" style="background:var(--gold-50);border:1px solid var(--gold-300);color:var(--gold-700);padding:4px 8px;" title="Edit Report">
                                              <i class="fas fa-edit"></i>
                                          </a>
                                      @endif

                                      @if(in_array($sub->status, ['pending', 'approved', 'submitted']) || auth()->user()->user_category === 'directorate_admin' || auth()->user()->role === 'admin')
                                          <a href="{{ route('ict.report', ['year' => $sub->period, 'id' => $sub->id]) }}" class="btn-nis btn-sm" style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:4px 8px;display:inline-flex;align-items:center;" title="View Details">
                                              <i class="fas fa-eye"></i>
                                          </a>
                                      @endif

                                    @if($sub->status === 'approved')
                                        <form action="{{ route('ict.submit', $sub->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn-nis btn-sm" style="background:#dcfce7;border:1px solid #86efac;color:#15803d;padding:4px 8px;font-weight:700;" title="Submit to Headquarters">
                                                <i class="fas fa-paper-plane"></i> Submit
                                            </button>
                                        </form>
                                    @endif

                                    @if(auth()->user()->user_category === 'directorate_admin' || auth()->user()->role === 'admin')
                                        @if($sub->status === 'pending')
                                            <form action="{{ route('ict.approve', $sub->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn-nis btn-sm" style="background:#dcfce7;border:1px solid #86efac;color:#15803d;padding:4px 8px;" title="Approve Report">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                            <button class="btn-nis btn-sm" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;padding:4px 8px;" onclick="openQueryModal('ict', {{ $sub->id }})" title="Query Report">
                                                <i class="fas fa-question-circle"></i> Query
                                            </button>
                                        @endif
                                    @endif
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
    
    if (data.projects) {
        metricsHtml += `<tr>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0;"><i class="fas fa-bars-progress" style="width: 20px; color:#475569; margin-right:8px;"></i> Project Activities</td>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 600; color:#0f172a;">${data.projects.length} Projects</td>
        </tr>`;
    }

    let totalIncidents = 0;
    const incTypes = ['incidents_hardware', 'incidents_software', 'incidents_network', 'incidents_cybersecurity', 'incidents_power', 'incidents_communication', 'incidents_surveillance', 'incidents_providers'];
    incTypes.forEach(type => {
        if (data[type]) totalIncidents += data[type].length;
    });
    metricsHtml += `<tr>
        <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0;"><i class="fas fa-triangle-exclamation" style="width: 20px; color:#475569; margin-right:8px;"></i> Technical Incidents</td>
        <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 600; color:#0f172a;">${totalIncidents} Incidents</td>
    </tr>`;

    if (data.maintenance) {
        metricsHtml += `<tr>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0;"><i class="fas fa-tools" style="width: 20px; color:#475569; margin-right:8px;"></i> Maintenance Work Orders</td>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 600; color:#0f172a;">${data.maintenance.length} Assets</td>
        </tr>`;
    }

    if (data.software) {
        metricsHtml += `<tr>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0;"><i class="fas fa-code" style="width: 20px; color:#475569; margin-right:8px;"></i> Software Developed</td>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 600; color:#0f172a;">${data.software.length} Solutions</td>
        </tr>`;
    }
    
    if (data.cybersecurity) {
        metricsHtml += `<tr>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0;"><i class="fas fa-user-shield" style="width: 20px; color:#475569; margin-right:8px;"></i> Security Deployments</td>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 600; color:#0f172a;">${data.cybersecurity.length} Controls</td>
        </tr>`;
    }

    if (data.midas) {
        metricsHtml += `<tr>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0;"><i class="fas fa-server" style="width: 20px; color:#475569; margin-right:8px;"></i> MIDAS Deployment Sites</td>
            <td style="padding: 12px 14px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 600; color:#0f172a;">${data.midas.length} Commands</td>
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
