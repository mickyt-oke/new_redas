<div class="visa-card" id="trvCard">
    <button type="button" class="visa-accordion-header" id="trvToggle">
        <div class="visa-section-title">
            <div class="visa-section-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <h5>Temporary Residence Visa (TRV)</h5>
                <small>Annual Temporary Residence Visa Statistics</small>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px;">
            <span id="trvStatus" class="section-status status-progress">
                In Progress
            </span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <div class="visa-card-body">
        <div class="table-toolbar" style="display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:14px;flex-wrap:wrap;">
            <div class="table-toolbar-left">
                <strong style="color:var(--gray-700);font-size:.9rem;">Temporary Residence Visa (TRV)</strong>
            </div>
            <div class="table-toolbar-right">
                <button type="button" id="addTrvRow" class="btn-nis btn-primary-nis">
                    <i class="fas fa-plus"></i> + Add Row
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="visa-table">
                <thead>
                    <tr>
                        <th style="width:60px;">S/N</th>
                        <th>Visa Class</th>
                        <th>Applications</th>
                        <th>Approved</th>
                        <th>Rejected</th>
                        <th>Pending</th>
                        <th>Total</th>
                        <th width="60">Action</th>
                    </tr>
                </thead>
                <tbody id="trvBody">
                    @php
                    $trvClasses = ['R1A', 'R4A', 'R5A', 'R7A', 'R8A', 'R9A'];
                    $fields = ['applications', 'approved', 'rejected', 'pending'];
                    @endphp
                    @foreach($trvClasses as $cls)
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">{{ $loop->iteration }}</td>
                        <td>
                            <input type="text" name="trv[{{ $cls }}][class]" value="{{ $cls }}" class="ni" style="font-weight:600;">
                        </td>
                        @foreach($fields as $field)
                        <td>
                            <input type="number" min="0" value="0" name="trv[{{ $cls }}][{{ $field }}]" class="ni trv-input">
                        </td>
                        @endforeach
                        <td>
                            <input type="number" readonly value="0" class="ni total-input trv-row-total">
                        </td>
                        <td>
                            <button type="button" class="btn-nis btn-danger btn-delete-row" title="Delete Row">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="visa-total-row">
                        <th colspan="2">TOTAL</th>
                        <th><input readonly id="trvApplicationsTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="trvApprovedTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="trvRejectedTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="trvPendingTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="trvGrandTotal" class="ni total-input" value="0"></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary -->
        <div class="section-footer">
            <div class="section-summary">
                Total TRV Applications : <strong id="trvSummary">0 Applications</strong>
            </div>
            <div class="section-buttons">
                <button type="button" id="resetTRV" class="btn-nis btn-ghost">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
                <button type="button" id="saveTRV" class="btn-nis btn-primary-nis">
                    <i class="fas fa-check-circle"></i> Save Section
                </button>
            </div>
        </div>
    </div>
</div>
