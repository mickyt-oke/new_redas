<div class="visa-card" id="visaApplicationCard">
    <button type="button" class="visa-accordion-header" id="visaApplicationToggle">
        <div class="visa-section-title">
            <div class="visa-section-icon">
                <i class="fas fa-passport"></i>
            </div>
            <div>
                <h5>e-visa applications(svv)</h5>
                <small>Annual e-Visa(svv) Statistics</small>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px;">
            <span id="visaStatus" class="section-status status-progress">
                In Progress
            </span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <div class="visa-card-body">
        {{-- e-Visa Table --}}
        <div class="table-toolbar" style="display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:14px;flex-wrap:wrap;">
            <div class="table-toolbar-left">
                <strong style="color:var(--gray-700);font-size:.9rem;">e-visa (short visit visa)</strong>
            </div>
            <div class="table-toolbar-right">
                <button type="button" id="addEVisaRow" class="btn-nis btn-primary-nis">
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
                <tbody id="eVisaBody">
                    @php
                    $eVisaClasses = ['F3B', 'F4A', 'F5A', 'F6A', 'F7E', 'F7F', 'F7G', 'F7H', 'F7I', 'F7K', 'F9A', 'F9B'];
                    $fields = ['applications', 'approved', 'rejected', 'pending'];
                    @endphp
                    @foreach($eVisaClasses as $cls)
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">{{ $loop->iteration }}</td>
                        <td>
                            <input type="text" name="visa_applications[{{ $cls }}][class]" value="{{ $cls }}" class="ni" style="font-weight:600;">
                        </td>
                        @foreach($fields as $field)
                        <td>
                            <input type="number" min="0" value="0" name="visa_applications[{{ $cls }}][{{ $field }}]" class="ni visa-app-input">
                        </td>
                        @endforeach
                        <td>
                            <input type="number" readonly value="0" class="ni total-input visa-app-row-total">
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
                        <th><input readonly id="eVisaApplicationsTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="eVisaApprovedTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="eVisaRejectedTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="eVisaPendingTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="eVisaGrandTotal" class="ni total-input" value="0"></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <br>

        {{-- SVV Table --}}
        <div class="table-toolbar" style="display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:14px;flex-wrap:wrap;">
            <div class="table-toolbar-left">
                <strong style="color:var(--gray-700);font-size:.9rem;">Regular visa(short visit visa)</strong>
            </div>
            <div class="table-toolbar-right">
                <button type="button" id="addSvvRow" class="btn-nis btn-primary-nis">
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
                <tbody id="svvBody">
                    @php
                    $svvClasses = ['F2A', 'F3A', 'F4B', 'F4C', 'F6B', 'F7A', 'F7B', 'F7C', 'F7D', 'F7J', 'F7L', 'F7M'];
                    $fields = ['applications', 'approved', 'rejected', 'pending'];
                    @endphp
                    @foreach($svvClasses as $cls)
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">{{ $loop->iteration }}</td>
                        <td>
                            <input type="text" name="visa_applications[{{ $cls }}][class]" value="{{ $cls }}" class="ni" style="font-weight:600;">
                        </td>
                        @foreach($fields as $field)
                        <td>
                            <input type="number" min="0" value="0" name="visa_applications[{{ $cls }}][{{ $field }}]" class="ni visa-app-input">
                        </td>
                        @endforeach
                        <td>
                            <input type="number" readonly value="0" class="ni total-input visa-app-row-total">
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
                        <th><input readonly id="svvApplicationsTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="svvApprovedTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="svvRejectedTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="svvPendingTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="svvGrandTotal" class="ni total-input" value="0"></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary -->
        <div class="section-footer">
            <div class="section-summary">
                Total Visa Applications : <strong id="visaSummary">0 Applications</strong>
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
                <button type="button" id="resetVisaApplications" class="btn-nis btn-ghost">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>
