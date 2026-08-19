<div class="visa-card" id="africanAffairsCard">
    <button type="button" class="visa-accordion-header" id="africanAffairsToggle">
        <div class="visa-section-title">
            <div class="visa-section-icon">
                <i class="fas fa-earth-africa"></i>
            </div>
            <div style="text-align: left;">
                <h5>African Affairs</h5>
                <small>Annual African Affairs Statistics</small>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px;">
            <span id="africanStatus" class="section-status status-progress">
                In Progress
            </span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <div class="visa-card-body">
        <div class="table-toolbar" style="display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:14px;flex-wrap:wrap;">
            <div class="table-toolbar-left">
                <strong style="color:var(--gray-700);font-size:.9rem;">African Affairs Records</strong>
            </div>
            <div class="table-toolbar-right">
                <button type="button" id="addAfricanAffairsRow" class="btn-nis btn-primary-nis">
                    <i class="fas fa-plus"></i> + Add Row
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="visa-table">
                <thead>
                    <tr>
                        <th style="width:60px;">S/N</th>
                        <th>Nationality</th>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Principal</th>
                        <th>Dependent</th>
                        <th>Regularization</th>
                        <th>Renewals</th>
                        <th>Redesignations</th>
                        <th>Reclassification</th>
                        <th>COE</th>
                        <th>COS</th>
                        <th>Total</th>
                        <th width="60"></th>
                    </tr>
                </thead>
                <tbody id="africanBody">
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">1</td>
                        <td>
                            <input type="text" class="ni african-nationality" name="african[0][nationality]" placeholder="Enter Nationality">
                        </td>
                        @php
                        $fields = ['male', 'female', 'principal', 'dependent', 'regularization', 'renewals', 'redesignation', 'reclassification', 'coe', 'cos'];
                        @endphp
                        @foreach($fields as $field)
                        <td>
                            <input type="number" min="0" value="0" name="african[0][{{ $field }}]" class="ni african-number african-input">
                        </td>
                        @endforeach
                        <td>
                            <input type="number" readonly value="0" class="ni total-input african-row-total">
                        </td>
                        <td>
                            <button type="button" class="btn-icon btn-danger removeAfrican">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="visa-total-row">
                        <th colspan="2">TOTAL</th>
                        <th><input readonly id="africanMaleTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="africanFemaleTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="africanPrincipalTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="africanDependentTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="africanRegularizationTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="africanRenewalsTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="africanRedesignationsTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="africanReclassificationTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="africanCoeTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="africanCosTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="africanGrandTotal" class="ni total-input" value="0"></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary -->
        <div class="section-footer">
            <div class="section-summary">
                Total African Affairs Records : <strong id="africanSummary">0 Persons</strong>
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
                <button type="button" id="resetAfricanAffairs" class="btn-nis btn-ghost">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>
