<div class="visa-card" id="ftzCard">
    <button type="button" class="visa-accordion-header" id="ftzToggle">
        <div class="visa-section-title">
            <div class="visa-section-icon">
                <i class="fas fa-warehouse"></i>
            </div>
            <div>
                <h5>Free Trade Zone (FTZ)</h5>
                <small>Annual Free Trade Zone Statistics</small>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px;">
            <span id="ftzStatus" class="section-status status-progress">
                In Progress
            </span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <div class="visa-card-body">
        <div class="table-toolbar" style="display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:14px;flex-wrap:wrap;">
            <div class="table-toolbar-left">
                <strong style="color:var(--gray-700);font-size:.9rem;">Free Trade Zone (FTZ) Records</strong>
            </div>
            <div class="table-toolbar-right">
                <button type="button" id="addFtzRow" class="btn-nis btn-primary-nis">
                    <i class="fas fa-plus"></i> + Add Row
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="visa-table">
                <thead>
                    <tr>
                        <th style="width:60px;">S/N</th>
                        <th>Name of Free Zone</th>
                        <th>No. Of Enterprises</th>
                        <th>No. Of Expatriates</th>
                        <th>Regularization</th>
                        <th>Renewal</th>
                        <th>Redesignation</th>
                        <th>COE</th>
                        <th>COS</th>
                        <th>Total</th>
                        <th width="60">Action</th>
                    </tr>
                </thead>
                <tbody id="ftzBody">
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">1</td>
                        <td>
                            <input type="text" class="ni" name="ftz[0][zones]" placeholder="Enter Free Zone">
                        </td>
                        <td>
                            <input type="number" min="0" value="0" class="ni ftz-input" id="ftz_enterprises" name="ftz[0][enterprises]">
                        </td>
                        <td>
                            <input type="number" min="0" value="0" class="ni ftz-input" id="ftz_expatriates" name="ftz[0][expatriates]">
                        </td>
                        <td>
                            <input type="number" min="0" value="0" class="ni ftz-input" id="ftz_regularization" name="ftz[0][regularization]">
                        </td>
                        <td>
                            <input type="number" min="0" value="0" class="ni ftz-input" id="ftz_renewal" name="ftz[0][renewal]">
                        </td>
                        <td>
                            <input type="number" min="0" value="0" class="ni ftz-input" id="ftz_redesignation" name="ftz[0][redesignation]">
                        </td>
                        <td>
                            <input type="number" min="0" value="0" class="ni ftz-input" id="ftz_coe" name="ftz[0][coe]">
                        </td>
                        <td>
                            <input type="number" min="0" value="0" class="ni ftz-input" id="ftz_cos" name="ftz[0][cos]">
                        </td>
                        <td>
                            <input type="number" readonly value="0" class="ni total-input ftz-row-total" id="ftz_total">
                        </td>
                        <td>
                            <button type="button" class="btn-nis btn-danger btn-delete-row" title="Delete Row">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="visa-total-row">
                        <th colspan="2">TOTAL</th>
                        <th><input readonly id="ftzEnterprisesTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="ftzExpatriatesTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="ftzRegularizationTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="ftzRenewalTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="ftzRedesignationTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="ftzCoeTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="ftzCosTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="ftzGrandTotal" class="ni total-input" value="0"></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary -->
        <div class="section-footer">
            <div class="section-summary">
                Total FTZ Activities : <strong id="ftzSummary">0 Activities</strong>
            </div>
            <div class="section-buttons">
                <button type="button" id="resetFTZ" class="btn-nis btn-ghost">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
                <button type="button" id="saveFTZ" class="btn-nis btn-primary-nis">
                    <i class="fas fa-check-circle"></i> Save Section
                </button>
            </div>
        </div>
    </div>
</div>
