<div class="visa-card" id="prvCard">
    <button type="button" class="visa-accordion-header" id="prvToggle">
        <div class="visa-section-title">
            <div class="visa-section-icon">
                <i class="fas fa-home"></i>
            </div>
            <div>
                <h5>Permanent Residence Visa (PRV)</h5>
                <small>Annual Permanent Residence Visa Statistics</small>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px;">
            <span id="prvStatus" class="section-status status-progress">
                In Progress
            </span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <div class="visa-card-body">
        <div class="table-toolbar" style="display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:14px;flex-wrap:wrap;">
            <div class="table-toolbar-left">
                <strong style="color:var(--gray-700);font-size:.9rem;">Permanent Residence Visa (PRV)</strong>
            </div>
            <div class="table-toolbar-right">
                <button type="button" id="addPrvRow" class="btn-nis btn-primary-nis">
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
                <tbody id="prvBody">
                    @php
                    $prvClasses = ['N1A', 'N2A', 'N3', 'N4A', 'N5A', 'N5B'];
                    $fields = ['applications', 'approved', 'rejected', 'pending'];
                    @endphp
                    @foreach($prvClasses as $cls)
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">{{ $loop->iteration }}</td>
                        <td>
                            <input type="text" name="prv[{{ $cls }}][class]" value="{{ $cls }}" class="ni" style="font-weight:600;">
                        </td>
                        @foreach($fields as $field)
                        <td>
                            <input type="number" min="0" value="0" name="prv[{{ $cls }}][{{ $field }}]" class="ni prv-input">
                        </td>
                        @endforeach
                        <td>
                            <input type="number" readonly value="0" class="ni total-input prv-row-total">
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
                        <th><input readonly id="prvApplicationsTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="prvApprovedTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="prvRejectedTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="prvPendingTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="prvGrandTotal" class="ni total-input" value="0"></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary -->
        <div class="section-footer">
            <div class="section-summary">
                Total PRV Applications : <strong id="prvSummary">0 Applications</strong>
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
                <button type="button" id="resetPRV" class="btn-nis btn-ghost">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>
