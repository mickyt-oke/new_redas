<div class="visa-card" id="visaSummaryCard">
    <button type="button" class="visa-accordion-header" id="visaSummaryToggle">
        <div class="visa-section-title">
            <div class="visa-section-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div style="text-align: left;">
                <h5>Visa Applications Summary</h5>
                <small>Overview of all visa applications, approvals, rejections, and pending cases</small>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px;">
            <span id="visaSummaryStatus" class="section-status status-complete">
                Complete
            </span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <div class="visa-card-body">
        <div class="table-responsive">
            <table class="visa-table">
                <thead>
                    <tr>
                        <th style="width:60px;">S/N</th>
                        <th style="text-align:left;">Visa Type</th>
                        <th>Applications</th>
                        <th>Approved</th>
                        <th>Rejected</th>
                        <th>Pending</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody id="visaSummaryBody">
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">1</td>
                        <td style="text-align:left;font-weight:600;">e-visa (short visit visa)</td>
                        <td><input readonly id="sum_evisa_apps" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_evisa_appr" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_evisa_rej" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_evisa_pend" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_evisa_total" class="ni total-input" value="0"></td>
                    </tr>
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">2</td>
                        <td style="text-align:left;font-weight:600;">Regular visa(short visit visa)</td>
                        <td><input readonly id="sum_svv_apps" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_svv_appr" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_svv_rej" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_svv_pend" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_svv_total" class="ni total-input" value="0"></td>
                    </tr>
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">3</td>
                        <td style="text-align:left;font-weight:600;">Temporary Residence Visa (TRV)</td>
                        <td><input readonly id="sum_trv_apps" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_trv_appr" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_trv_rej" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_trv_pend" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_trv_total" class="ni total-input" value="0"></td>
                    </tr>
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">4</td>
                        <td style="text-align:left;font-weight:600;">Permanent Residence Visa (PRV)</td>
                        <td><input readonly id="sum_prv_apps" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_prv_appr" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_prv_rej" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_prv_pend" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_prv_total" class="ni total-input" value="0"></td>
                    </tr>
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">5</td>
                        <td style="text-align:left;font-weight:600;">e-TWP (Temporary Work Permit)</td>
                        <td><input readonly id="sum_etwp_apps" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_etwp_appr" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_etwp_rej" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_etwp_pend" class="ni total-input" value="0"></td>
                        <td><input readonly id="sum_etwp_total" class="ni total-input" value="0"></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="visa-total-row">
                        <th colspan="2" style="text-align:left;">TOTAL</th>
                        <th><input readonly id="sumGrandApps" class="ni total-input" value="0"></th>
                        <th><input readonly id="sumGrandAppr" class="ni total-input" value="0"></th>
                        <th><input readonly id="sumGrandRej" class="ni total-input" value="0"></th>
                        <th><input readonly id="sumGrandPend" class="ni total-input" value="0"></th>
                        <th><input readonly id="sumGrandTotal" class="ni total-input" value="0"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="section-footer" style="margin-top: 20px;">
            <div class="section-summary"></div>
            <div class="section-buttons">
                <button
                    type="submit"
                    name="action"
                    value="draft"
                    class="btn-nis btn-success">
                    <i class="fas fa-save"></i>
                    Save Section
                </button>
            </div>
        </div>
    </div>
</div>
