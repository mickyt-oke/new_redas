<div class="visa-card" id="etwpCard">
    <button type="button" class="visa-accordion-header" id="etwpToggle">
        <div class="visa-section-title">
            <div class="visa-section-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <div style="text-align: left;">
                <h5>e-TWP (Temporary Work Permit)</h5>
                <small>Annual e-TWP Statistics</small>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px;">
            <span id="etwpStatus" class="section-status status-progress">
                In Progress
            </span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <div class="visa-card-body">
        <div class="table-toolbar" style="display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:14px;flex-wrap:wrap;">
            <div class="table-toolbar-left">
                <strong style="color:var(--gray-700);font-size:.9rem;">e-TWP (Temporary Work Permit)</strong>
            </div>
            <div class="table-toolbar-right">
                <button type="button" id="addEtwpRow" class="btn-nis btn-primary-nis">
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
                <tbody id="etwpBody">
                    @php
                    $etwpClasses = ['R10', 'R11'];
                    $fields = ['applications', 'approved', 'rejected', 'pending'];
                    @endphp
                    @foreach($etwpClasses as $cls)
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">{{ $loop->iteration }}</td>
                        <td>
                            <input type="text" name="etwp[{{ $cls }}][class]" value="{{ $cls }}" class="ni" style="font-weight:600;">
                        </td>
                        @foreach($fields as $field)
                        <td>
                            <input type="number" min="0" value="0" name="etwp[{{ $cls }}][{{ $field }}]" class="ni etwp-input">
                        </td>
                        @endforeach
                        <td>
                            <input type="number" readonly value="0" class="ni total-input etwp-row-total">
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
                        <th><input readonly id="etwpApplicationsTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="etwpApprovedTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="etwpRejectedTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="etwpPendingTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="etwpGrandTotal" class="ni total-input" value="0"></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary -->
        <div class="section-footer">
            <div class="section-summary">
                Total e-TWP Applications : <strong id="etwpSummary">0 Applications</strong>
            </div>
            <div class="section-buttons">
                <button type="button" id="resetETWP" class="btn-nis btn-ghost">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
                <button type="button" id="saveETWP" class="btn-nis btn-primary-nis">
                    <i class="fas fa-check-circle"></i> Save Section
                </button>
            </div>
        </div>
    </div>
</div>
