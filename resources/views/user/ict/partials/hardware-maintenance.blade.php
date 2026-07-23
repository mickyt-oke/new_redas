{{-- ============================================================
    11. HARDWARE MAINTENANCE
============================================================= --}}

<div id="hardwareMaintenanceCard" class="visa-card animate-fade-up">

    <!-- Section Header -->
    <button type="button" id="hardwareMaintenanceToggle" class="visa-accordion-header">
        <div class="visa-section-title">
            <div class="visa-section-icon">
                <i class="fas fa-tools"></i>
            </div>
            <div style="text-align: left;">
                <h5>Hardware Maintenance</h5>
                <small>Record server room infrastructure, computer terminals, printers, scanning devices and active maintenance tickets</small>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px;">
            <span class="section-status status-progress" id="hardwareMaintenanceStatus">In Progress</span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <!-- Section Body -->
    <div class="visa-card-body">
        <div class="table-toolbar">
            <div class="table-toolbar-left">
                <strong>Maintenance Log</strong>
            </div>
            <div class="table-toolbar-right">
                <button type="button" class="btn-nis btn-primary-nis btn-sm"
                        data-ict-target="maintenance_tbody"
                        data-ict-prefix="maintenance"
                        data-ict-fields='[{"name":"equipment_type","type":"text"},{"name":"product","type":"text"},{"name":"status","type":"select","options":["Serviceable","Unserviceable","Under Repair"]},{"name":"location","type":"text"}]'>
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="visa-table">
                <thead>
                    <tr>
                        <th style="width:50px;">S/N</th>
                        <th>Equipment Type</th>
                        <th>Product</th>
                        <th style="width:160px;">Status</th>
                        <th>Location</th>
                        <th style="width:60px;">Action</th>
                    </tr>
                </thead>
                <tbody id="maintenance_tbody">
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">1</td>
                        <td><input type="text" name="maintenance[0][equipment_type]" class="ni"></td>
                        <td><input type="text" name="maintenance[0][product]" class="ni"></td>
                        <td>
                            <select name="maintenance[0][status]" class="ni ni-select">
                                <option value="Serviceable">Serviceable</option>
                                <option value="Unserviceable">Unserviceable</option>
                                <option value="Under Repair">Under Repair</option>
                            </select>
                        </td>
                        <td><input type="text" name="maintenance[0][location]" class="ni"></td>
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
                        <th colspan="3"><input readonly id="maintenanceTotal" class="ni total-input" value="0" style="width:100px;"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary -->
        <div class="section-footer">
            <div class="section-summary">
                Total Maintenance Logs : <strong id="maintenanceSummary">0 Records</strong>
            </div>
            <div class="section-buttons">
                <button type="button" class="btn-nis btn-ghost" id="resetMaintenance">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
                <button type="button" class="btn-nis btn-primary-nis">
                    <i class="fas fa-check-circle"></i> Save Section
                </button>
            </div>
        </div>
    </div>

</div>
