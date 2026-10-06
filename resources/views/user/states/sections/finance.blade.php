<div class="hrm-tabs-wrap">
    <div class="entry-tabs-wrap" style="margin-bottom:0;">
        <div class="entry-tabs" id="entryTabs">
            @php $tabs = [
                ['revenue','fas fa-coins','1. Revenue Performance'],
                ['expenditure','fas fa-receipt','2. Expenditure & Budget'],
                ['airlines','fas fa-plane','3. Airlines Indebted'],
                ['general-report','fas fa-file-alt','4. General Report'],
            ]; @endphp
            @foreach($tabs as $i => [$id,$icon,$label])
            <button type="button" class="entry-tab {{ $i === 0 ? 'active' : '' }}" data-tab="finance-{{ $id }}">
                <span class="tab-dot"></span>
                <i class="{{ $icon }}" style="font-size:.78rem;"></i>
                {{ $label }}
            </button>
            @endforeach
            @if($financeIncludePreviewTab ?? false)
            <button type="button" class="entry-tab" data-tab="preview">
                <span class="tab-dot"></span>
                <i class="fas fa-eye" style="font-size:.78rem;"></i>
                5. Preview
            </button>
            @endif
        </div>
    </div>
</div>

<div class="tab-content">

    <!-- TAB 1: Revenue Performance -->
    <div class="tab-panel active" id="tab-finance-revenue">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-coins"></i>
                    </div>
                    LOCAL Revenue Performance Report
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/NO</th>
                                <th>SERVICE AND FACILITIES</th>
                                <th style="width:200px;">AMOUNT (N)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $localRows = [
                                'local_revenue_passport' => 'Passport',
                                'local_revenue_residence_permit_ecowas' => 'Residence Permit (ECOWAS & AFRICAN AFFAIRS)',
                                'local_revenue_admin_fees' => 'Non-refundable Administrative Fees for Operations',
                                'local_revenue_residence_permit_non_africans' => 'RESIDENCE Permit for Non-Africans (CERPAC)',
                                'local_revenue_extension_visitors' => 'Extension of Visitors Pass (E-PASS)',
                                'local_revenue_other' => 'Other Revenue (IGR)',
                            ];
                            @endphp
                            @foreach($localRows as $key => $label)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="{{ $key }}" class="ni local-rev-input" min="0" placeholder="0" value="{{ old($key) }}"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td colspan="2"><strong style="text-align:right;">TOTAL</strong></td>
                                <td><input type="number" name="local_revenue_total" id="local-rev-total" class="ni" readonly placeholder="0" value="{{ old('local_revenue_total') }}"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-globe"></i>
                    </div>
                    FOREIGN Revenue Performance Report
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/NO</th>
                                <th>SERVICES AND FACILITIES</th>
                                <th style="width:200px;">AMOUNT ($)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $foreignRows = [
                                'foreign_revenue_passport_visa' => 'Passport & Visa (Direct to JP Morgan Account)',
                                'foreign_revenue_carrier_liability' => 'Carrier Liability/Visa on Arrival',
                            ];
                            @endphp
                            @foreach($foreignRows as $key => $label)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="{{ $key }}" class="ni foreign-rev-input" min="0" placeholder="0" value="{{ old($key) }}"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn" disabled><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    <!-- TAB 2: Expenditure & Budget -->
    <div class="tab-panel" id="tab-finance-expenditure">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-receipt"></i>
                    </div>
                    EXPENDITURE PROFILE
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/NO</th>
                                <th>STAKEHOLDERS/SERVICE PROVIDER</th>
                                <th style="width:200px;">AMOUNT (N)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $expenditureRows = [
                                'expenditure_iris' => 'IRIS SMART TECHNOLOGIES',
                                'expenditure_newworks' => 'NEWWORKS SOLUTIONS LTD.',
                                'expenditure_national_egov' => 'NATIONAL E-GOVERNMENT STRATEGIES',
                                'expenditure_greater_washington' => 'GREATER WASHINGTON',
                                'expenditure_contec_cerpac' => 'CONTEC (CERPAC)',
                                'expenditure_contec_epass' => 'CONTEC (E-PASS)',
                                'expenditure_fmi' => 'FMI (CERPAC/ E-PASS)',
                                'expenditure_firs' => 'FIRS',
                                'expenditure_msmpc' => 'MSMPC',
                                'expenditure_nis_iptelcom' => 'NIS (after Payment to) IPTELCOM',
                                'expenditure_iptelcome' => 'IPTELCOME',
                            ];
                            @endphp
                            @foreach($expenditureRows as $key => $label)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="{{ $key }}" class="ni exp-profile-input" min="0" placeholder="0" value="{{ old($key) }}"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td colspan="2"><strong style="text-align:right;">TOTAL</strong></td>
                                <td><input type="number" name="expenditure_profile_total" id="exp-profile-total" class="ni" readonly placeholder="0" value="{{ old('expenditure_profile_total') }}"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-calculator"></i>
                    </div>
                    BUDGET AND RELEASES
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/NO</th>
                                <th>Nature of Expenditure</th>
                                <th style="width:150px;">Amount Budgeted (N)</th>
                                <th style="width:150px;">Amount Released (N)</th>
                                <th style="width:120px;">Percentage of Releases</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $budgetRows = [
                                'personnel' => 'Personnel Expenditure',
                                'overhead' => 'Overhead Cost',
                                'capital' => 'Capital',
                            ];
                            @endphp
                            @foreach($budgetRows as $key => $label)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="budget[{{ $key }}][budgeted]" class="ni budget-budgeted" min="0" placeholder="0" value="{{ old('budget.'.$key.'.budgeted') }}"></td>
                                <td><input type="number" name="budget[{{ $key }}][released]" class="ni budget-released" min="0" placeholder="0" value="{{ old('budget.'.$key.'.released') }}"></td>
                                <td><input type="text" name="budget[{{ $key }}][percentage]" class="ni budget-percentage" readonly placeholder="0%" value="{{ old('budget.'.$key.'.percentage') }}"></td>
                                <td><input type="text" name="budget[{{ $key }}][remark]" class="ni" placeholder="..." value="{{ old('budget.'.$key.'.remark') }}"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    <!-- TAB 3: Airlines Indebted -->
    <div class="tab-panel" id="tab-finance-airlines">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-plane"></i>
                    </div>
                    AIRLINES INDEBTED TO THE SERVICE
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="airlines-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/NO</th>
                                <th>NAME OF AIRLINE</th>
                                <th style="width:200px;">AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody id="airlines-body">
                            <tr>
                                <td>1</td>
                                <td><input type="text" name="airlines_indebted[0][name]" class="ni" placeholder="Airline name" value="{{ old('airlines_indebted.0.name') }}"></td>
                                <td><input type="number" name="airlines_indebted[0][amount]" class="ni" min="0" placeholder="0" value="{{ old('airlines_indebted.0.amount') }}"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top:12px;">
                    <button type="button" class="btn-nis btn-ghost btn-sm" id="addAirlineRow">
                        <i class="fas fa-plus"></i> Add Airline
                    </button>
                </div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    <!-- TAB 4: General Report -->
    <div class="tab-panel" id="tab-finance-general">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    General Report & Challenges
                </div>
            </div>
            <div class="card-body">
                <div style="grid-column:1 / -1;">
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Challenges & Remarks</label>
                    <textarea name="finance_challenges" class="ni" rows="6" placeholder="Provide details on challenges encountered and other general reports..." style="width:100%;">{{ old('finance_challenges') }}</textarea>
                </div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next: Preview <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>
</div>

<script>
(function() {
    // Calculate Local Revenue Total
    function calcLocalTotal() {
        let total = 0;
        document.querySelectorAll('.local-rev-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('local-rev-total').value = total.toFixed(2);
    }

    // Calculate Expenditure Profile Total
    function calcExpTotal() {
        let total = 0;
        document.querySelectorAll('.exp-profile-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('exp-profile-total').value = total.toFixed(2);
    }

    // Calculate Budget Percentage
    function calcBudgetPercentage() {
        document.querySelectorAll('.budget-released').forEach(input => {
            const row = input.closest('tr');
            const budgeted = parseFloat(row.querySelector('.budget-budgeted').value) || 0;
            const released = parseFloat(input.value) || 0;
            const percentageField = row.querySelector('.budget-percentage');
            if (budgeted > 0) {
                percentageField.value = ((released / budgeted) * 100).toFixed(2) + '%';
            } else {
                percentageField.value = '0%';
            }
        });
    }

    // Add Airline Row
    function addAirlineRow() {
        const body = document.getElementById('airlines-body');
        const rowCount = body.querySelectorAll('tr').length;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${rowCount + 1}</td>
            <td><input type="text" name="airlines_indebted[${rowCount}][name]" class="ni" placeholder="Airline name"></td>
            <td><input type="number" name="airlines_indebted[${rowCount}][amount]" class="ni" min="0" placeholder="0"></td>
        `;
        body.appendChild(tr);
    }

    // Event Listeners
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('local-rev-input')) calcLocalTotal();
        if (e.target.classList.contains('exp-profile-input')) calcExpTotal();
        if (e.target.classList.contains('budget-budgeted') || e.target.classList.contains('budget-released')) calcBudgetPercentage();
    });

    document.getElementById('addAirlineRow')?.addEventListener('click', addAirlineRow);

    // Initial calculations
    calcLocalTotal();
    calcExpTotal();
    calcBudgetPercentage();
})();
</script>
