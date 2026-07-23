<div class="visa-card" id="residenceCard">

    <div class="visa-accordion-header" id="residenceToggle">

        <div class="visa-section-title">

            <div class="visa-section-icon">
                <i class="fas fa-id-card"></i>
            </div>

            <div>
                <h5>Residence Permit</h5>
                <small>Annual Residence Permit Statistics</small>
            </div>

        </div>

        <div style="display:flex;align-items:center;gap:15px;">

            <span id="residenceStatus" class="status-progress">
                In Progress
            </span>

            <i class="fas fa-chevron-down accordion-icon"></i>

        </div>

    </div>

    <div class="visa-card-body">

        {{-- ========================================= --}}
        {{-- TEMPORARY RESIDENCE PERMIT --}}
        {{-- ========================================= --}}

        <div class="table-toolbar" style="display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:14px;flex-wrap:wrap;">
            <div class="table-toolbar-left">
                <strong style="color:var(--gray-700);font-size:.9rem;">Temporary Residence Permit</strong>
            </div>
            <div class="table-toolbar-right">
                <button type="button" id="addTempResidenceRow" class="btn-nis btn-primary-nis">
                    <i class="fas fa-plus"></i> + Add Row
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="visa-table">
                <thead>
                    <tr>
                        <th style="width:60px;">S/N</th>
                        <th>Permit Type</th>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Principal</th>
                        <th>Dependent</th>
                        <th>Regularization</th>
                        <th>Renewals</th>
                        <th>Redesignation</th>
                        <th>Reclassification</th>
                        <th>COE</th>
                        <th>COS</th>
                        <th>Total</th>
                        <th width="60">Action</th>
                    </tr>
                </thead>

                <tbody id="tempResidenceBody">
                    @php
                    $temporary = [
                        'Diplomat (Accredited)',
                        'GO',
                        'INGO',
                        'Intern',
                        'Cleric',
                        'Student',
                        'Expatriates (on Quota)',
                        'Expatriates (FTZ)'
                    ];
                    $tempFields = ['male', 'female', 'principal', 'dependent', 'regularization', 'renewals', 'redesignation', 'reclassification', 'coe', 'cos'];
                    @endphp

                    @foreach($temporary as $item)
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">{{ $loop->iteration }}</td>
                        <td>
                            <input type="text" name="residence_temporary[{{ $item }}][type]" value="{{ $item }}" class="ni" style="font-weight:600;">
                        </td>
                        @foreach($tempFields as $field)
                        <td>
                            <input
                                type="number"
                                min="0"
                                value="0"
                                name="residence_temporary[{{ $item }}][{{ $field }}]"
                                class="ni residence-input">
                        </td>
                        @endforeach
                        <td>
                            <input
                                type="number"
                                readonly
                                value="0"
                                class="ni total-input residence-row-total">
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
                        <th><input readonly id="tempResidenceMaleTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="tempResidenceFemaleTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="tempResidencePrincipalTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="tempResidenceDependentTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="tempResidenceRegularizationTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="tempResidenceRenewalsTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="tempResidenceRedesignationTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="tempResidenceReclassificationTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="tempResidenceCoeTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="tempResidenceCosTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="tempResidenceGrandTotal" class="ni total-input" value="0"></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <br>

        {{-- ========================================= --}}
        {{-- PERMANENT RESIDENCE PERMIT --}}
        {{-- ========================================= --}}

        <div class="table-toolbar" style="display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:14px;flex-wrap:wrap;">
            <div class="table-toolbar-left">
                <strong style="color:var(--gray-700);font-size:.9rem;">Permanent Residence Permit</strong>
            </div>
            <div class="table-toolbar-right">
                <button type="button" id="addPermResidenceRow" class="btn-nis btn-primary-nis">
                    <i class="fas fa-plus"></i> + Add Row
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="visa-table">
                <thead>
                    <tr>
                        <th style="width:60px;">S/N</th>
                        <th>Permit Type</th>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Principal</th>
                        <th>Dependent</th>
                        <th>Regularization</th>
                        <th>Renewals</th>
                        <th>Total</th>
                        <th width="60">Action</th>
                    </tr>
                </thead>

                <tbody id="permResidenceBody">
                    @php
                    $permanent = [
                        'Spouses of Nigerian',
                        'Investors',
                        'Retired in Nigeria',
                        'Retiree from Abroad',
                        'Highly Skilled Migrants',
                        'Nigerian by Birth who Renounced Nigerian Citizenship'
                    ];
                    $permFields = ['male', 'female', 'principal', 'dependent', 'regularization', 'renewals'];
                    @endphp

                    @foreach($permanent as $item)
                    <tr>
                        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">{{ $loop->iteration }}</td>
                        <td>
                            <input type="text" name="residence_permanent[{{ $item }}][type]" value="{{ $item }}" class="ni" style="font-weight:600;">
                        </td>
                        @foreach($permFields as $field)
                        <td>
                            <input
                                type="number"
                                min="0"
                                value="0"
                                name="residence_permanent[{{ $item }}][{{ $field }}]"
                                class="ni residence-input">
                        </td>
                        @endforeach
                        <td>
                            <input
                                type="number"
                                readonly
                                value="0"
                                class="ni total-input residence-row-total">
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
                        <th><input readonly id="permResidenceMaleTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="permResidenceFemaleTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="permResidencePrincipalTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="permResidenceDependentTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="permResidenceRegularizationTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="permResidenceRenewalsTotal" class="ni total-input" value="0"></th>
                        <th><input readonly id="permResidenceGrandTotal" class="ni total-input" value="0"></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary -->
        <div class="section-footer">
            <div class="section-summary">
                Total Residence Permits : <strong id="residenceSummary">0 Permits</strong>
            </div>
            <div class="section-buttons">
                <button
                    type="button"
                    id="resetResidence"
                    class="btn-nis btn-ghost">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
                <button
                    type="button"
                    id="saveResidence"
                    class="btn-nis btn-primary-nis">
                    <i class="fas fa-check-circle"></i> Save Section
                </button>
            </div>
        </div>

    </div>

</div>