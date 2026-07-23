<div class="visa-card" id="quotaCard">

    <div class="visa-accordion-header" id="quotaToggle">

        <div class="visa-section-title">

            <div class="visa-section-icon">
                <i class="fas fa-users-cog"></i>
            </div>

            <div>

                <h5>Quota Administration</h5>

                <small>Annual Quota Administration Statistics</small>

            </div>

        </div>

        <div style="display:flex;align-items:center;gap:15px;">

            <span id="quotaStatus" class="status-progress">
                In Progress
            </span>

            <i class="fas fa-chevron-down accordion-icon"></i>

        </div>

    </div>

    <div class="visa-card-body">
        <div class="table-toolbar" style="display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:14px;flex-wrap:wrap;">
            <div class="table-toolbar-left">
                <strong style="color:var(--gray-700);font-size:.9rem;">Quota Administration</strong>
            </div>
            <div class="table-toolbar-right">
                <button
                    type="button"
                    id="addQuotaRow"
                    class="btn-nis btn-primary-nis">
                    <i class="fas fa-plus"></i> + Add Row
                </button>
            </div>
        </div>

        <div class="table-responsive">

            <table class="visa-table">

                <thead>

                    <tr>
                        <th style="width:60px;">S/N</th>
                        <th>No. Of Company / Enterprise</th>

                        <th>Quota Positions</th>

                        <th>Utilized Quota</th>

                        <th>Expatriate Deleted</th>

                        <th>TOTAL</th>

                        <th width="60"></th>

                    </tr>

                </thead>

                <tbody id="quotaBody">
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">1</td>
                        <td>

                            <input
                                type="text"
                                class="ni quota-company"
                                name="quota[0][company]"
                                placeholder="Company / Enterprise">

                        </td>

                        <td>

                            <input
                                type="number"
                                min="0"
                                value="0"
                                class="ni quota-number quota-position"
                                name="quota[0][positions]">

                        </td>

                        <td>

                            <input
                                type="number"
                                min="0"
                                value="0"
                                class="ni quota-number quota-utilized"
                                name="quota[0][utilized]">

                        </td>

                        <td>

                            <input
                                type="number"
                                min="0"
                                value="0"
                                class="ni quota-number quota-deleted"
                                name="quota[0][deleted]">

                        </td>

                        <td>

                            <input
                                type="number"
                                readonly
                                value="0"
                                class="ni total-input quota-row-total">

                        </td>

                        <td>

                            <button
                                type="button"
                                class="btn-icon btn-danger removeQuota">

                                <i class="fas fa-trash"></i>

                            </button>

                        </td>

                    </tr>

                </tbody>

                <tfoot>

                    <tr class="visa-total-row">
                        <th colspan="2">TOTAL</th>

                        <th>

                            <input
                                readonly
                                id="quotaPositionsTotal"
                                class="ni total-input"
                                value="0">

                        </th>

                        <th>

                            <input
                                readonly
                                id="quotaUtilizedTotal"
                                class="ni total-input"
                                value="0">

                        </th>

                        <th>

                            <input
                                readonly
                                id="quotaDeletedTotal"
                                class="ni total-input"
                                value="0">

                        </th>

                        <th>

                            <input
                                readonly
                                id="quotaGrandTotal"
                                class="ni total-input"
                                value="0">

                        </th>

                        <th></th>

                    </tr>

                </tfoot>

            </table>

        </div>

        <!-- Summary -->
        <div class="section-footer">
            <div class="section-summary">
                Total Quota Positions : <strong id="quotaSummary">0 Positions</strong>
            </div>
            <div class="section-buttons">
                <button
                    type="button"
                    id="resetQuota"
                    class="btn-nis btn-ghost">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
                <button
                    type="button"
                    id="saveQuota"
                    class="btn-nis btn-primary-nis">
                    <i class="fas fa-check-circle"></i> Save Section
                </button>
            </div>
        </div>

    </div>

</div>