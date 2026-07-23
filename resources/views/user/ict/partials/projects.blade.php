{{-- ============================================================
    2. PROJECT/PROGRAMME ACTIVITIES
============================================================= --}}

<div id="projectsCard" class="visa-card animate-fade-up">

    <!-- Section Header -->
    <button type="button" id="projectsToggle" class="visa-accordion-header">
        <div class="visa-section-title">
            <div class="visa-section-icon">
                <i class="fas fa-bars-progress"></i>
            </div>
            <div style="text-align: left;">
                <h5>Project/Programme Activities</h5>
                <small>Record technology systems, project tracking, status reports &amp; remarks</small>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px;">
            <span class="section-status status-progress" id="projectsStatus">In Progress</span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <!-- Section Body -->
    <div class="visa-card-body">
        <div class="table-toolbar">
            <div class="table-toolbar-left">
                <strong>Project Log</strong>
            </div>
            <div class="table-toolbar-right">
                <button type="button" class="btn-nis btn-primary-nis btn-sm"
                        data-ict-target="projects_tbody"
                        data-ict-prefix="projects"
                        data-ict-fields='[{"name":"project_name","type":"text"},{"name":"system_type","type":"text"},{"name":"status_report","type":"select","options":["Not Started","In Progress","Completed","Abandoned"]},{"name":"remarks","type":"text"}]'>
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="visa-table">
                <thead>
                    <tr>
                        <th style="width:50px;">S/N</th>
                        <th>Project/Programme</th>
                        <th>Type Of System</th>
                        <th style="width:160px;">Status Report</th>
                        <th>Remarks</th>
                        <th style="width:60px;">Action</th>
                    </tr>
                </thead>
                <tbody id="projects_tbody">
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">1</td>
                        <td><input type="text" name="projects[0][project_name]" class="ni"></td>
                        <td><input type="text" name="projects[0][system_type]" class="ni"></td>
                        <td>
                            <select name="projects[0][status_report]" class="ni ni-select">
                                <option value="Not Started">Not Started</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="Abandoned">Abandoned</option>
                            </select>
                        </td>
                        <td><input type="text" name="projects[0][remarks]" class="ni"></td>
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
                        <th colspan="3"><input readonly id="projectsTotal" class="ni total-input" value="0" style="width:100px;"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary -->
        <div class="section-footer">
            <div class="section-summary">
                Total Projects : <strong id="projectsSummary">0 Items</strong>
            </div>
            <div class="section-buttons">
                <button type="button" class="btn-nis btn-ghost" id="resetProjects">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
                <button type="button" class="btn-nis btn-primary-nis">
                    <i class="fas fa-check-circle"></i> Save Section
                </button>
            </div>
        </div>
    </div>

</div>
