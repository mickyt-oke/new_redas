<div class="visa-card" id="cerpacCard">
    <button type="button" class="visa-accordion-header" id="cerpacToggle">
        <div class="visa-section-title">
            <div class="visa-section-icon">
                <i class="fas fa-address-card"></i>
            </div>
            <div style="text-align: left;">
                <h5>CERPAC Production</h5>
                <small>Annual CERPAC Production Statistics</small>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px;">
            <span id="cerpacStatus" class="section-status status-progress">
                In Progress
            </span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <div class="visa-card-body">
        <div class="table-toolbar" style="display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:14px;flex-wrap:wrap;">
            <div class="table-toolbar-left">
                <strong style="color:var(--gray-700);font-size:.9rem;">CERPAC Production Records</strong>
            </div>
            <div class="table-toolbar-right">
                <button type="button" id="addCerpacRow" class="btn-nis btn-primary-nis">
                    <i class="fas fa-plus"></i> + Add Row
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="visa-table">
                <thead>
                    <tr>
                        <th style="width:60px;">S/N</th>
                        <th>Centre</th>
                        <th>Card Supplied</th>
                        <th>Card Produced</th>
                        <th>Card Damaged</th>
                        <th>Card Issued</th>
                        <th>Total</th>
                        <th width="60">Action</th>
                    </tr>
                </thead>
                <tbody id="cerpacBody">
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">1</td>
                        <td>
                            <input type="text" class="ni" name="cerpac[0][centre]" placeholder="Enter Centre">
                        </td>
                        <td>
                            <input type="number" min="0" value="0" class="ni cerpac-input" id="cerpac_supplied" name="cerpac[0][supplied]">
                        </td>
                        <td>
                            <input type="number" min="0" value="0" class="ni cerpac-input" id="cerpac_produced" name="cerpac[0][produced]">
                        </td>
                        <td>
                            <input type="number" min="0" value="0" class="ni cerpac-input" id="cerpac_damaged" name="cerpac[0][damaged]">
                        </td>
                        <td>
                            <input type="number" min="0" value="0" class="ni cerpac-input" id="cerpac_issued" name="cerpac[0][issued]">
                        </td>
                        <td>
                            <input type="number" readonly value="0" class="ni total-input cerpac-row-total" id="cerpac_total">
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
                        <th colspan="2" style="text-align:left;">TOTAL</th>
                        <th><input readonly id="cerpacSuppliedTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="cerpacProducedTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="cerpacDamagedTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="cerpacIssuedTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="cerpacGrandTotal" class="ni total-input" value="0"></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary -->
        <div class="section-footer">
            <div class="section-summary">
                Total CERPAC Issued : <strong id="cerpacSummary">0 Cards</strong>
            </div>
            <div class="section-buttons">
                <button
                    type="submit"
                    name="action"
                    value="draft"
                    class="btn-nis btn-success">
                    <i class="fas fa-save"></i>
                    Save Section
                </button>
                <button type="button" id="resetCERPAC" class="btn-nis btn-ghost">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>
