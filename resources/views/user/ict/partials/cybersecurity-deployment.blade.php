{{-- ============================================================
    12. CYBERSECURITY (Labelled 12 in PDF document)
============================================================= --}}

<div id="cybersecurityDeploymentCard" class="visa-card animate-fade-up">

    <!-- Section Header -->
    <button type="button" id="cybersecurityDeploymentToggle" class="visa-accordion-header">
        <div class="visa-section-title">
            <div class="visa-section-icon">
                <i class="fas fa-shield-halved"></i>
            </div>
            <div style="text-align: left;">
                <h5>Cybersecurity Deployment</h5>
                <small>Record firewall rules, antivirus software, threat mitigation controls and security audits</small>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px;">
            <span class="section-status status-progress" id="cybersecurityDeploymentStatus">In Progress</span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <!-- Section Body -->
    <div class="visa-card-body">
        <div class="table-toolbar">
            <div class="table-toolbar-left">
                <strong>Security Infrastructure Log</strong>
            </div>
            <div class="table-toolbar-right">
                <button type="button" class="btn-nis btn-primary-nis btn-sm"
                        data-ict-target="cybersecurity_tbody"
                        data-ict-prefix="cybersecurity"
                        data-ict-fields='[{"name":"security_deployed","type":"text"},{"name":"type_of_security","type":"text"},{"name":"incident_report","type":"text"},{"name":"location","type":"text"},{"name":"status","type":"select","options":["Active","Inactive","Upgrading"]}]'>
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="visa-table">
                <thead>
                    <tr>
                        <th style="width:50px;">S/N</th>
                        <th>Security Deployed</th>
                        <th>Type Of Security</th>
                        <th>Incident Report</th>
                        <th>Location</th>
                        <th style="width:160px;">Status</th>
                        <th style="width:60px;">Action</th>
                    </tr>
                </thead>
                <tbody id="cybersecurity_tbody">
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">1</td>
                        <td><input type="text" name="cybersecurity[0][security_deployed]" class="ni"></td>
                        <td><input type="text" name="cybersecurity[0][type_of_security]" class="ni"></td>
                        <td><input type="text" name="cybersecurity[0][incident_report]" class="ni"></td>
                        <td><input type="text" name="cybersecurity[0][location]" class="ni"></td>
                        <td>
                            <select name="cybersecurity[0][status]" class="ni ni-select">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Upgrading">Upgrading</option>
                            </select>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn-nis btn-ghost btn-sm btn-delete-row" style="color:var(--color-danger);padding:4px 8px;">
                                <i class="fas fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="visa-total-row">
                        <th colspan="4" style="text-align:left;">TOTAL</th>
                        <th colspan="3"><input readonly id="cybersecurityTotal" class="ni total-input" value="0" style="width:100px;"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary -->
        <div class="section-footer">
            <div class="section-summary">
                Total Deployments Logged : <strong id="cybersecuritySummary">0 Deployments</strong>
            </div>
            <div class="section-buttons">
                <button type="button" class="btn-nis btn-ghost" id="resetCybersecurity">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
                <button type="button" class="btn-nis btn-primary-nis">
                    <i class="fas fa-check-circle"></i> Save Section
                </button>
            </div>
        </div>
    </div>

</div>
