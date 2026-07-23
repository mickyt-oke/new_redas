{{-- ============================================================
    12. SOFTWARE AND DATA MANAGEMENT
============================================================= --}}

<div id="softwareDataCard" class="visa-card animate-fade-up">

    <!-- Section Header -->
    <button type="button" id="softwareDataToggle" class="visa-accordion-header">
        <div class="visa-section-title">
            <div class="visa-section-icon">
                <i class="fas fa-database"></i>
            </div>
            <div style="text-align: left;">
                <h5>Software &amp; Data Management</h5>
                <small>Record custom-built portals, database registers and data security incidents</small>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px;">
            <span class="section-status status-progress" id="softwareDataStatus">In Progress</span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <!-- Section Body -->
    <div class="visa-card-body">
        
        <!-- a. SOFTWARE -->
        <div class="table-toolbar">
            <div class="table-toolbar-left">
                <strong>Software Development Log</strong>
            </div>
            <div class="table-toolbar-right">
                <button type="button" class="btn-nis btn-primary-nis btn-sm"
                        data-ict-target="software_tbody"
                        data-ict-prefix="software"
                        data-ict-fields='[{"name":"software_developed","type":"text"},{"name":"description","type":"textarea"},{"name":"status","type":"select","options":["Not Started","In Progress","Completed","Abandoned"]},{"name":"deployment","type":"text"}]'>
                    <i class="fas fa-plus"></i> Add Software Row
                </button>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="visa-table">
                <thead>
                    <tr>
                        <th style="width:50px;">S/N</th>
                        <th>Software Developed</th>
                        <th>Description</th>
                        <th style="width:160px;">Status</th>
                        <th>Deployment Site</th>
                        <th style="width:60px;">Action</th>
                    </tr>
                </thead>
                <tbody id="software_tbody">
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">1</td>
                        <td><input type="text" name="software[0][software_developed]" class="ni"></td>
                        <td><textarea name="software[0][description]" class="ni" rows="1" style="min-height:38px;padding:8px;resize:vertical;"></textarea></td>
                        <td>
                            <select name="software[0][status]" class="ni ni-select">
                                <option value="Not Started">Not Started</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="Abandoned">Abandoned</option>
                            </select>
                        </td>
                        <td><input type="text" name="software[0][deployment]" class="ni"></td>
                        <td class="text-center">
                            <button type="button" class="btn-nis btn-ghost btn-sm btn-delete-row" style="color:var(--color-danger);padding:4px 8px;">
                                <i class="fas fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="visa-total-row">
                        <th colspan="3" style="text-align:left;">TOTAL</th>
                        <th colspan="3"><input readonly id="softwareTotal" class="ni total-input" value="0" style="width:100px;"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <hr style="border:0;border-top:1px solid var(--gray-200);margin:24px 0;">

        <!-- b. DATA -->
        <div class="table-toolbar">
            <div class="table-toolbar-left">
                <strong>Data Leakage &amp; Breach Log</strong>
            </div>
            <div class="table-toolbar-right">
                <button type="button" class="btn-nis btn-primary-nis btn-sm"
                        data-ict-target="data_breach_tbody"
                        data-ict-prefix="data_breach"
                        data-ict-fields='[{"name":"data_breached_incident","type":"text"},{"name":"no_of_incident","type":"number"},{"name":"status","type":"select","options":["Investigating","Under Review","Mitigated","Unresolved"]},{"name":"breach_location","type":"text"}]'>
                    <i class="fas fa-plus"></i> Add Breach Row
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="visa-table">
                <thead>
                    <tr>
                        <th style="width:50px;">S/N</th>
                        <th>Data Breached Incident</th>
                        <th>No. Of Incidents</th>
                        <th style="width:160px;">Status</th>
                        <th>Breach Location</th>
                        <th style="width:60px;">Action</th>
                    </tr>
                </thead>
                <tbody id="data_breach_tbody">
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">1</td>
                        <td><input type="text" name="data_breach[0][data_breached_incident]" class="ni"></td>
                        <td><input type="number" name="data_breach[0][no_of_incident]" class="ni" min="0" value="0"></td>
                        <td>
                            <select name="data_breach[0][status]" class="ni ni-select">
                                <option value="Investigating">Investigating</option>
                                <option value="Under Review">Under Review</option>
                                <option value="Mitigated">Mitigated</option>
                                <option value="Unresolved">Unresolved</option>
                            </select>
                        </td>
                        <td><input type="text" name="data_breach[0][breach_location]" class="ni"></td>
                        <td class="text-center">
                            <button type="button" class="btn-nis btn-ghost btn-sm btn-delete-row" style="color:var(--color-danger);padding:4px 8px;">
                                <i class="fas fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="visa-total-row">
                        <th colspan="2" style="text-align:left;">TOTAL</th>
                        <th><input readonly id="dataBreachCountTotal" class="ni total-input" value="0" style="width:100px;"></th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary -->
        <div class="section-footer">
            <div class="section-summary">
                Total Software &amp; Breach Logs : <strong id="softwareDataSummary">0 Records</strong>
            </div>
            <div class="section-buttons">
                <button type="button" class="btn-nis btn-ghost" id="resetSoftwareData">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
                <button type="button" class="btn-nis btn-primary-nis">
                    <i class="fas fa-check-circle"></i> Save Section
                </button>
            </div>
        </div>
    </div>

</div>
