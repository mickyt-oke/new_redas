{{-- ============================================================
    15. MIDAS DEPLOYMENT
============================================================= --}}

<div id="midasDeploymentCard" class="visa-card animate-fade-up">

    <!-- Section Header -->
    <button type="button" id="midasDeploymentToggle" class="visa-accordion-header">
        <div class="visa-section-title">
            <div class="visa-section-icon">
                <i class="fas fa-server"></i>
            </div>
            <div style="text-align: left;">
                <h5>MIDAS Deployment</h5>
                <small>Record state command borders and airport deployment points</small>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px;">
            <span class="section-status status-progress" id="midasDeploymentStatus">In Progress</span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <!-- Section Body -->
    <div class="visa-card-body">
        <div class="table-toolbar">
            <div class="table-toolbar-left">
                <strong>Deployment List</strong>
            </div>
            <div class="table-toolbar-right">
                <button type="button" class="btn-nis btn-primary-nis btn-sm"
                        data-ict-target="midas_tbody"
                        data-ict-prefix="midas"
                        data-ict-fields='[{"name":"state_command","type":"text"},{"name":"location","type":"text"}]'>
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="visa-table">
                <thead>
                    <tr>
                        <th style="width:50px;">S/N</th>
                        <th>State Command</th>
                        <th>Location</th>
                        <th style="width:60px;">Action</th>
                    </tr>
                </thead>
                <tbody id="midas_tbody">
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">1</td>
                        <td><input type="text" name="midas[0][state_command]" class="ni"></td>
                        <td><input type="text" name="midas[0][location]" class="ni"></td>
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
                        <th colspan="2"><input readonly id="midasTotal" class="ni total-input" value="0" style="width:100px;"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary -->
        <div class="section-footer">
            <div class="section-summary">
                Total MIDAS Deployments : <strong id="midasSummary">0 Locations</strong>
            </div>
            <div class="section-buttons">
                <button type="button" class="btn-nis btn-ghost" id="resetMidas">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
                <button type="button" class="btn-nis btn-primary-nis">
                    <i class="fas fa-check-circle"></i> Save Section
                </button>
            </div>
        </div>
    </div>

</div>
