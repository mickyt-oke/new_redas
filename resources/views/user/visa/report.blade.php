{{-- ==========================================================
     Visa & Residence Directorate
     Annual Report Workspace
========================================================== --}}

@include('partials.visa-header')

<div class="page-header">

    <div>

        <h1 class="page-title">

            <i class="fas fa-file-alt"></i>

            Annual Visa & Residence Report

        </h1>

        <p class="page-subtitle">

            Complete every reporting section before submitting the Annual Report.

        </p>

    </div>



</div>


@if(auth()->user()->user_category === 'directorate_admin' || auth()->user()->role === 'admin')
    <div style="background:#eff6ff;border:1px solid #bfdbfe;color:#1e3a8a;padding:16px;border-radius:12px;margin-bottom:20px;display:flex;gap:12px;align-items:center;">
        <i class="fas fa-info-circle" style="font-size:1.5rem;color:#3b82f6;"></i>
        <div>
            <strong style="font-size:1.05rem;">Review Mode (Read-only)</strong><br>
            <span style="font-size:0.9rem;">You are viewing the report submitted by the desk officer for Period {{ $period }}. The current status is: <strong>{{ ucfirst($application->status ?? 'Not Started') }}</strong>.</span>
        </div>
    </div>
@endif

@if($application)
    @if($application->status === 'approved')
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:16px;border-radius:12px;margin-bottom:20px;display:flex;gap:12px;align-items:center;">
            <i class="fas fa-check-circle" style="font-size:1.5rem;color:#22c55e;"></i>
            <div>
                <strong style="font-size:1.05rem;">Report Approved!</strong><br>
                <span style="font-size:0.9rem;">Your report for Period {{ $application->period }} has been reviewed and approved by the Admin. Please submit it using the button at the bottom of the page.</span>
            </div>
        </div>
    @elseif($application->status === 'submitted')
        <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;padding:16px;border-radius:12px;margin-bottom:20px;display:flex;gap:12px;align-items:center;">
            <i class="fas fa-check-double" style="font-size:1.5rem;color:#10b981;"></i>
            <div>
                <strong style="font-size:1.05rem;">Report Submitted</strong><br>
                <span style="font-size:0.9rem;">This report has been officially submitted to Headquarters. No further modifications are allowed.</span>
            </div>
        </div>
    @elseif($application->status === 'pending')
        <div style="background:#fefce8;border:1px solid #fef08a;color:#713f12;padding:16px;border-radius:12px;margin-bottom:20px;display:flex;gap:12px;align-items:center;">
            <i class="fas fa-hourglass-half" style="font-size:1.5rem;color:#eab308;"></i>
            <div>
                <strong style="font-size:1.05rem;">Awaiting Approval</strong><br>
                <span style="font-size:0.9rem;">This report is currently pending approval. You will receive a notification once it is approved.</span>
            </div>
        </div>
    @elseif($application->status === 'queried')
        <div style="background:#fff5f5;border:1px solid #fed7d7;color:#9b2c2c;padding:16px;border-radius:12px;margin-bottom:20px;display:flex;gap:12px;align-items:center;">
            <i class="fas fa-exclamation-circle" style="font-size:1.5rem;color:#e53e3e;"></i>
            <div>
                <strong style="font-size:1.05rem;">Report Queried</strong><br>
                <span style="font-size:0.9rem;"><strong>Admin Remarks:</strong> {{ $application->comments ?? 'Please review operational figures.' }}</span>
            </div>
        </div>
    @endif
@endif

<form
    id="visaAnnualReport"
    method="POST"
    action="{{ route('visa.store') }}">

    @csrf



    {{-- =========================================
        GENERAL INFORMATION
    ========================================== --}}

    <section id="general-information">

        @include('user.visa.partials.general-information')

    </section>



    {{-- =========================================
        STAFF STRENGTH
    ========================================== --}}

    <section id="staff-strength">

        @include('user.visa.partials.staff-strength')

    </section>



    {{-- =========================================
        E-MIGRANT CENTRE
    ========================================== --}}

    <section id="e-migrant">

        @include('user.visa.partials.e-migrant')

    </section>



    {{-- =========================================
        QUOTA ADMINISTRATION
    ========================================== --}}

    <section id="quota">

        @include('user.visa.partials.quota')

    </section>



    {{-- =========================================
        RESIDENCE PERMIT
    ========================================== --}}

    <section id="residence">

        @include('user.visa.partials.residence')

    </section>



    {{-- =========================================
        FREE TRADE ZONE
    ========================================== --}}

    <section id="ftz">

        @include('user.visa.partials.ftz')

    </section>



    {{-- =========================================
        CERPAC PRODUCTION
    ========================================== --}}

    <section id="cerpac">

        @include('user.visa.partials.cerpac')

    </section>



    {{-- =========================================
        VISA APPLICATIONS
    ========================================== --}}

    <section id="visa-applications">

        @include('user.visa.partials.visa')

    </section>



    {{-- =========================================
        TRV
    ========================================== --}}

    <section id="trv">

        @include('user.visa.partials.trv')

    </section>



    {{-- =========================================
        PRV
    ========================================== --}}

    <section id="prv">

        @include('user.visa.partials.prv')

    </section>



    {{-- =========================================
        E-TWP
    ========================================== --}}

    <section id="etwp">

        @include('user.visa.partials.e-twp')

    </section>



    {{-- =========================================
        VISA APPLICATIONS SUMMARY
    ========================================== --}}

    <section id="visa-summary">

        @include('user.visa.partials.visa-counter')

    </section>



    {{-- =========================================
        ECOWAS
    ========================================== --}}

    <section id="ecowas">

        @include('user.visa.partials.ecowas')

    </section>



    {{-- =========================================
        AFRICAN AFFAIRS
    ========================================== --}}

    <section id="african-affairs">

        @include('user.visa.partials.african-affairs')

    </section>





    <div class="redas-card mt-4">

        <div class="card-body">

            <div class="visa-actions" style="display:flex;justify-content:flex-end;align-items:center;gap:12px;width:100%;">

                @if(auth()->user()->user_category === 'directorate_admin' || auth()->user()->role === 'admin')
                    <div style="background:#f3f4f6;color:#374151;padding:10px 20px;border-radius:8px;font-weight:700;display:flex;align-items:center;gap:8px;border:1px solid #d1d5db;">
                        <i class="fas fa-eye"></i> Viewing Desk Officer Return (Read-only Mode)
                    </div>
                @elseif($application && $application->status === 'approved')
                    <button type="button" class="btn-nis btn-primary-nis" style="background:#22c55e;border:1px solid #16a34a;color:white;padding:12px 24px;font-size:1rem;font-weight:700;" onclick="event.preventDefault(); document.getElementById('visaFinalSubmitForm').submit();">
                        <i class="fas fa-paper-plane"></i> Submit Final Return to Headquarters
                    </button>
                @elseif($application && $application->status === 'submitted')
                    <div style="background:#ecfdf5;color:#065f46;padding:10px 20px;border-radius:8px;font-weight:700;display:flex;align-items:center;gap:8px;border:1px solid #a7f3d0;">
                        <i class="fas fa-check-double"></i> Report Officially Submitted to Headquarters
                    </div>
                @elseif($application && $application->status === 'pending')
                    <div style="background:#fefce8;color:#713f12;padding:10px 20px;border-radius:8px;font-weight:700;display:flex;align-items:center;gap:8px;border:1px solid #fef08a;">
                        <i class="fas fa-hourglass-half"></i> Pending Awaiting Approval
                    </div>
                @else
                    <button
                        type="button"
                        id="clearReportFormBtn"
                        class="btn-nis btn-ghost">
                        <i class="fas fa-rotate-left"></i>
                        Reset
                    </button>

                    <button
                        type="submit"
                        name="action"
                        value="draft"
                        class="btn-nis btn-success">
                        <i class="fas fa-save"></i>
                        Save Draft
                    </button>

                    <button
                        type="submit"
                        name="action"
                        value="submit"
                        class="btn-nis btn-primary-nis">
                        <i class="fas fa-paper-plane"></i>
                        Send for Approval
                    </button>
                @endif

            </div>

        </div>

    </div>

</form>

@if($application && $application->status === 'approved')
    <form id="visaFinalSubmitForm" action="{{ route('visa.submit', $application->id) }}" method="POST" style="display:none;">
        @csrf
    </form>
@endif

<script>
(function() {
    console.log("Visa Report workspace script loaded.");

    function calculateStaffStrength() {
        let grandMale = 0, grandFemale = 0, grandTotal = 0;
        document.querySelectorAll('#staff_strength_tbody tr').forEach(row => {
            const maleInput = row.querySelector('.male');
            const femaleInput = row.querySelector('.female');
            const totalInput = row.querySelector('.total-input');
            const maleVal = parseInt(maleInput ? maleInput.value : 0) || 0;
            const femaleVal = parseInt(femaleInput ? femaleInput.value : 0) || 0;
            const totalVal = maleVal + femaleVal;
            if (totalInput) totalInput.value = totalVal;
            grandMale += maleVal;
            grandFemale += femaleVal;
            grandTotal += totalVal;
        });
        const gM = document.getElementById('grandMale');
        const gF = document.getElementById('grandFemale');
        const gT = document.getElementById('grandTotal');
        const sS = document.getElementById('staffSummary');
        if (gM) gM.value = grandMale;
        if (gF) gF.value = grandFemale;
        if (gT) gT.value = grandTotal;
        if (sS) sS.textContent = grandTotal + ' Officers';
    }

    function calculateEmigrant() {
        let regular = 0, irregular = 0, male = 0, female = 0;
        let employed = 0, self_employed = 0, student = 0, spouse = 0, dependant = 0;
        let overall = 0;
        
        document.querySelectorAll('#emigrantBody tr').forEach(row => {
            const regVal = parseInt(row.querySelector('.regular')?.value || 0) || 0;
            const irregVal = parseInt(row.querySelector('.irregular')?.value || 0) || 0;
            const maleVal = parseInt(row.querySelector('.male')?.value || 0) || 0;
            const femaleVal = parseInt(row.querySelector('.female')?.value || 0) || 0;
            
            const empVal = parseInt(row.querySelector('input[name*="[employed]"]:not([name*="[self_employed]"])')?.value || 0) || 0;
            const selfEmpVal = parseInt(row.querySelector('input[name*="[self_employed]"]')?.value || 0) || 0;
            const studVal = parseInt(row.querySelector('input[name*="[student]"]')?.value || 0) || 0;
            const spouseVal = parseInt(row.querySelector('input[name*="[spouse]"]')?.value || 0) || 0;
            const depVal = parseInt(row.querySelector('input[name*="[dependant]"]')?.value || 0) || 0;
            
            const rowTotal = regVal + irregVal + maleVal + femaleVal + empVal + selfEmpVal + studVal + spouseVal + depVal;
            const totalInput = row.querySelector('.emigrant-total');
            if (totalInput) totalInput.value = rowTotal;
            
            regular += regVal;
            irregular += irregVal;
            male += maleVal;
            female += femaleVal;
            employed += empVal;
            self_employed += selfEmpVal;
            student += studVal;
            spouse += spouseVal;
            dependant += depVal;
            overall += rowTotal;
        });
        
        const regEl = document.getElementById('emigrantRegularTotal');
        const irregEl = document.getElementById('emigrantIrregularTotal');
        const maleEl = document.getElementById('emigrantMaleTotal');
        const femaleEl = document.getElementById('emigrantFemaleTotal');
        const empEl = document.getElementById('emigrantEmployedTotal');
        const selfEmpEl = document.getElementById('emigrantSelfEmployedTotal');
        const studEl = document.getElementById('emigrantStudentTotal');
        const spouseEl = document.getElementById('emigrantSpouseTotal');
        const depEl = document.getElementById('emigrantDependantTotal');
        const overallEl = document.getElementById('overallMigrants');
        const summaryEl = document.getElementById('emigrantSummary');
        
        if (regEl) regEl.value = regular;
        if (irregEl) irregEl.value = irregular;
        if (maleEl) maleEl.value = male;
        if (femaleEl) femaleEl.value = female;
        if (empEl) empEl.value = employed;
        if (selfEmpEl) selfEmpEl.value = self_employed;
        if (studEl) studEl.value = student;
        if (spouseEl) spouseEl.value = spouse;
        if (depEl) depEl.value = dependant;
        if (overallEl) overallEl.value = overall;
        if (summaryEl) summaryEl.textContent = overall;
    }

    function calculateQuota() {
        let pTotal = 0, uTotal = 0, dTotal = 0, grandTotal = 0;
        document.querySelectorAll('#quotaBody tr').forEach(row => {
            const p = parseInt(row.querySelector('.quota-position')?.value || 0) || 0;
            const u = parseInt(row.querySelector('.quota-utilized')?.value || 0) || 0;
            const d = parseInt(row.querySelector('.quota-deleted')?.value || 0) || 0;
            const rowTotal = p + u + d;
            
            const rowTotalInput = row.querySelector('.quota-row-total');
            if (rowTotalInput) rowTotalInput.value = rowTotal;
            
            pTotal += p; 
            uTotal += u; 
            dTotal += d;
            grandTotal += rowTotal;
        });
        const pEl = document.getElementById('quotaPositionsTotal');
        const uEl = document.getElementById('quotaUtilizedTotal');
        const dEl = document.getElementById('quotaDeletedTotal');
        const grandEl = document.getElementById('quotaGrandTotal');
        const qSum = document.getElementById('quotaSummary');
        if (pEl) pEl.value = pTotal;
        if (uEl) uEl.value = uTotal;
        if (dEl) dEl.value = dTotal;
        if (grandEl) grandEl.value = grandTotal;
        if (qSum) qSum.innerHTML = `<strong>${pTotal} Positions</strong>`;
    }

    function calculateResidenceColumnTotals() {
        let tempM = 0, tempF = 0, tempP = 0, tempDep = 0, tempReg = 0, tempRen = 0, tempRedes = 0, tempRecl = 0, tempCoe = 0, tempCos = 0, tempGrand = 0;
        document.querySelectorAll('#tempResidenceBody tr').forEach(row => {
            const rowInputs = row.querySelectorAll('.residence-input');
            if (rowInputs.length >= 10) {
                tempM += parseInt(rowInputs[0].value || 0);
                tempF += parseInt(rowInputs[1].value || 0);
                tempP += parseInt(rowInputs[2].value || 0);
                tempDep += parseInt(rowInputs[3].value || 0);
                tempReg += parseInt(rowInputs[4].value || 0);
                tempRen += parseInt(rowInputs[5].value || 0);
                tempRedes += parseInt(rowInputs[6].value || 0);
                tempRecl += parseInt(rowInputs[7].value || 0);
                tempCoe += parseInt(rowInputs[8].value || 0);
                tempCos += parseInt(rowInputs[9].value || 0);
            }
            const rowTot = row.querySelector('.residence-row-total');
            if (rowTot) {
                tempGrand += parseInt(rowTot.value || 0);
            }
        });
        const tmEl = document.getElementById('tempResidenceMaleTotal');
        const tfEl = document.getElementById('tempResidenceFemaleTotal');
        const tpEl = document.getElementById('tempResidencePrincipalTotal');
        const tdEl = document.getElementById('tempResidenceDependentTotal');
        const trEl = document.getElementById('tempResidenceRegularizationTotal');
        const trnEl = document.getElementById('tempResidenceRenewalsTotal');
        const trdEl = document.getElementById('tempResidenceRedesignationTotal');
        const trcEl = document.getElementById('tempResidenceReclassificationTotal');
        const tcoeEl = document.getElementById('tempResidenceCoeTotal');
        const tcosEl = document.getElementById('tempResidenceCosTotal');
        const tgEl = document.getElementById('tempResidenceGrandTotal');
        
        if (tmEl) tmEl.value = tempM;
        if (tfEl) tfEl.value = tempF;
        if (tpEl) tpEl.value = tempP;
        if (tdEl) tdEl.value = tempDep;
        if (trEl) trEl.value = tempReg;
        if (trnEl) trnEl.value = tempRen;
        if (trdEl) trdEl.value = tempRedes;
        if (trcEl) trcEl.value = tempRecl;
        if (tcoeEl) tcoeEl.value = tempCoe;
        if (tcosEl) tcosEl.value = tempCos;
        if (tgEl) tgEl.value = tempGrand;

        let permM = 0, permF = 0, permP = 0, permDep = 0, permReg = 0, permRen = 0, permGrand = 0;
        document.querySelectorAll('#permResidenceBody tr').forEach(row => {
            const rowInputs = row.querySelectorAll('.residence-input');
            if (rowInputs.length >= 6) {
                permM += parseInt(rowInputs[0].value || 0);
                permF += parseInt(rowInputs[1].value || 0);
                permP += parseInt(rowInputs[2].value || 0);
                permDep += parseInt(rowInputs[3].value || 0);
                permReg += parseInt(rowInputs[4].value || 0);
                permRen += parseInt(rowInputs[5].value || 0);
            }
            const rowTot = row.querySelector('.residence-row-total');
            if (rowTot) {
                permGrand += parseInt(rowTot.value || 0);
            }
        });
        const pmEl = document.getElementById('permResidenceMaleTotal');
        const pfEl = document.getElementById('permResidenceFemaleTotal');
        const ppEl = document.getElementById('permResidencePrincipalTotal');
        const pdEl = document.getElementById('permResidenceDependentTotal');
        const prEl = document.getElementById('permResidenceRegularizationTotal');
        const prnEl = document.getElementById('permResidenceRenewalsTotal');
        const pgEl = document.getElementById('permResidenceGrandTotal');
        
        if (pmEl) pmEl.value = permM;
        if (pfEl) pfEl.value = permF;
        if (ppEl) ppEl.value = permP;
        if (pdEl) pdEl.value = permDep;
        if (prEl) prEl.value = permReg;
        if (prnEl) prnEl.value = permRen;
        if (pgEl) pgEl.value = permGrand;

        const rSum = document.getElementById('residenceSummary');
        if (rSum) rSum.innerHTML = `<strong>${tempGrand + permGrand} Permits</strong>`;
    }

    function calculateAppTableColumnTotals(tbodySelector, inputClass, prefix) {
        let app = 0, appr = 0, rej = 0, pend = 0, grand = 0;
        document.querySelectorAll(tbodySelector + ' tr').forEach(row => {
            const rowInputs = row.querySelectorAll('.' + inputClass);
            if (rowInputs.length >= 4) {
                app += parseInt(rowInputs[0].value || 0);
                appr += parseInt(rowInputs[1].value || 0);
                rej += parseInt(rowInputs[2].value || 0);
                pend += parseInt(rowInputs[3].value || 0);
            }
            const rowTot = row.querySelector('.total-input');
            if (rowTot) {
                grand += parseInt(rowTot.value || 0);
            }
        });
        const appEl = document.getElementById(prefix + 'ApplicationsTotal');
        const apprEl = document.getElementById(prefix + 'ApprovedTotal');
        const rejEl = document.getElementById(prefix + 'RejectedTotal');
        const pendEl = document.getElementById(prefix + 'PendingTotal');
        const grandEl = document.getElementById(prefix + 'GrandTotal');
        
        if (appEl) appEl.value = app;
        if (apprEl) apprEl.value = appr;
        if (rejEl) rejEl.value = rej;
        if (pendEl) pendEl.value = pend;
        if (grandEl) grandEl.value = grand;
        
        return grand;
    }

    function calculateAffairsTableColumnTotals(tbodySelector, inputClass, prefix) {
        let m = 0, f = 0, p = 0, dep = 0, reg = 0, ren = 0, redes = 0, recl = 0, coe = 0, cos = 0, grand = 0;
        document.querySelectorAll(tbodySelector + ' tr').forEach(row => {
            const rowInputs = row.querySelectorAll('.' + inputClass);
            if (rowInputs.length >= 10) {
                m += parseInt(rowInputs[0].value || 0);
                f += parseInt(rowInputs[1].value || 0);
                p += parseInt(rowInputs[2].value || 0);
                dep += parseInt(rowInputs[3].value || 0);
                reg += parseInt(rowInputs[4].value || 0);
                ren += parseInt(rowInputs[5].value || 0);
                redes += parseInt(rowInputs[6].value || 0);
                recl += parseInt(rowInputs[7].value || 0);
                coe += parseInt(rowInputs[8].value || 0);
                cos += parseInt(rowInputs[9].value || 0);
            }
            const rowTot = row.querySelector('.total-input');
            if (rowTot) {
                grand += parseInt(rowTot.value || 0);
            }
        });
        const mEl = document.getElementById(prefix + 'MaleTotal');
        const fEl = document.getElementById(prefix + 'FemaleTotal');
        const pEl = document.getElementById(prefix + 'PrincipalTotal');
        const depEl = document.getElementById(prefix + 'DependentTotal');
        const regEl = document.getElementById(prefix + 'RegularizationTotal');
        const renEl = document.getElementById(prefix + 'RenewalsTotal');
        const redesEl = document.getElementById(prefix + 'RedesignationsTotal');
        const reclEl = document.getElementById(prefix + 'ReclassificationTotal');
        const coeEl = document.getElementById(prefix + 'CoeTotal');
        const cosEl = document.getElementById(prefix + 'CosTotal');
        const grandEl = document.getElementById(prefix + 'GrandTotal');
        
        if (mEl) mEl.value = m;
        if (fEl) fEl.value = f;
        if (pEl) pEl.value = p;
        if (depEl) depEl.value = dep;
        if (regEl) regEl.value = reg;
        if (renEl) renEl.value = ren;
        if (redesEl) redesEl.value = redes;
        if (reclEl) reclEl.value = recl;
        if (coeEl) coeEl.value = coe;
        if (cosEl) cosEl.value = cos;
        if (grandEl) grandEl.value = grand;
        
        return grand;
    }

    function calculateFtzColumnTotals() {
        let enterprises = 0, expat = 0, reg = 0, ren = 0, redes = 0, coe = 0, cos = 0, grand = 0;
        document.querySelectorAll('#ftzBody tr').forEach(row => {
            const rowInputs = row.querySelectorAll('.ftz-input');
            if (rowInputs.length >= 7) {
                enterprises += parseInt(rowInputs[0].value || 0);
                expat += parseInt(rowInputs[1].value || 0);
                reg += parseInt(rowInputs[2].value || 0);
                ren += parseInt(rowInputs[3].value || 0);
                redes += parseInt(rowInputs[4].value || 0);
                coe += parseInt(rowInputs[5].value || 0);
                cos += parseInt(rowInputs[6].value || 0);
            }
            const rowTot = row.querySelector('.ftz-row-total');
            if (rowTot) {
                grand += parseInt(rowTot.value || 0);
            }
        });
        
        const entEl = document.getElementById('ftzEnterprisesTotal');
        const expEl = document.getElementById('ftzExpatriatesTotal');
        const regEl = document.getElementById('ftzRegularizationTotal');
        const renEl = document.getElementById('ftzRenewalTotal');
        const redesEl = document.getElementById('ftzRedesignationTotal');
        const coeEl = document.getElementById('ftzCoeTotal');
        const cosEl = document.getElementById('ftzCosTotal');
        const grandEl = document.getElementById('ftzGrandTotal');
        
        if (entEl) entEl.value = enterprises;
        if (expEl) expEl.value = expat;
        if (regEl) regEl.value = reg;
        if (renEl) renEl.value = ren;
        if (redesEl) redesEl.value = redes;
        if (coeEl) coeEl.value = coe;
        if (cosEl) cosEl.value = cos;
        if (grandEl) grandEl.value = grand;
        
        return grand;
    }

    function calculateRowTotals(tbodySelector, inputClass, totalClass) {
        let totalSum = 0;
        document.querySelectorAll(tbodySelector + ' tr').forEach(row => {
            const inputs = row.querySelectorAll('.' + inputClass + ', input[type="number"]:not([readonly])');
            let sum = 0;
            inputs.forEach(i => { sum += (parseInt(i.value) || 0); });
            const totalInput = row.querySelector('.' + totalClass + ', .total-input');
            if (totalInput && !totalInput.id) totalInput.value = sum;
            totalSum += sum;
        });
        return totalSum;
    }

    function updateAllCalculations() {
        try {
            calculateStaffStrength();
        } catch(e) {
            console.error("Staff Strength calculation failed:", e);
        }

        try {
            calculateEmigrant();
        } catch(e) {
            console.error("Emigrant calculation failed:", e);
        }

        try {
            calculateQuota();
        } catch(e) {
            console.error("Quota calculation failed:", e);
        }
        
        try {
            calculateRowTotals('#tempResidenceBody', 'residence-input', 'residence-row-total');
            calculateRowTotals('#permResidenceBody', 'residence-input', 'residence-row-total');
            calculateResidenceColumnTotals();
        } catch(e) {
            console.error("Residence calculation failed:", e);
        }

        try {
            const ftzTot = calculateRowTotals('#ftzBody', 'ftz-input', 'ftz-row-total');
            calculateFtzColumnTotals();
            const ftzSum = document.getElementById('ftzSummary');
            if (ftzSum) ftzSum.innerHTML = `<strong>${ftzTot} Activities</strong>`;
        } catch(e) {
            console.error("FTZ calculation failed:", e);
        }

        try {
            let cerpacIssuedSum = 0, cerpacSuppliedSum = 0, cerpacProducedSum = 0, cerpacDamagedSum = 0;
            document.querySelectorAll('#cerpacBody tr').forEach(r => {
                cerpacSuppliedSum += parseInt(r.querySelector('#cerpac_supplied, input[name*="[supplied]"]')?.value || 0) || 0;
                cerpacProducedSum += parseInt(r.querySelector('#cerpac_produced, input[name*="[produced]"]')?.value || 0) || 0;
                cerpacDamagedSum += parseInt(r.querySelector('#cerpac_damaged, input[name*="[damaged]"]')?.value || 0) || 0;
                cerpacIssuedSum += parseInt(r.querySelector('#cerpac_issued, input[name*="[issued]"]')?.value || 0) || 0;
            });
            const cerpacTot = calculateRowTotals('#cerpacBody', 'cerpac-input', 'cerpac-row-total');
            const cSup = document.getElementById('cerpacSuppliedTotal');
            const cPro = document.getElementById('cerpacProducedTotal');
            const cDam = document.getElementById('cerpacDamagedTotal');
            const cIss = document.getElementById('cerpacIssuedTotal');
            const cG = document.getElementById('cerpacGrandTotal');
            const cSum = document.getElementById('cerpacSummary');
            
            if (cSup) cSup.value = cerpacSuppliedSum;
            if (cPro) cPro.value = cerpacProducedSum;
            if (cDam) cDam.value = cerpacDamagedSum;
            if (cIss) cIss.value = cerpacIssuedSum;
            if (cG) cG.value = cerpacTot;
            if (cSum) cSum.innerHTML = `<strong>${cerpacIssuedSum} Cards</strong>`;
        } catch(e) {
            console.error("CERPAC calculation failed:", e);
        }

        try {
            calculateRowTotals('#eVisaBody', 'visa-app-input', 'visa-app-row-total');
            calculateRowTotals('#svvBody', 'visa-app-input', 'visa-app-row-total');
            const eVisaTot = calculateAppTableColumnTotals('#eVisaBody', 'visa-app-input', 'eVisa');
            const svvTot = calculateAppTableColumnTotals('#svvBody', 'visa-app-input', 'svv');
            const vSum = document.getElementById('visaSummary');
            if (vSum) vSum.innerHTML = `<strong>${eVisaTot + svvTot} Applications</strong>`;
        } catch(e) {
            console.error("eVisa/SVV calculation failed:", e);
        }

        try {
            calculateRowTotals('#trvBody', 'trv-input', 'trv-row-total');
            const trvTot = calculateAppTableColumnTotals('#trvBody', 'trv-input', 'trv');
            const trvSum = document.getElementById('trvSummary');
            if (trvSum) trvSum.innerHTML = `<strong>${trvTot} Applications</strong>`;
        } catch(e) {
            console.error("TRV calculation failed:", e);
        }

        try {
            calculateRowTotals('#prvBody', 'prv-input', 'prv-row-total');
            const prvTot = calculateAppTableColumnTotals('#prvBody', 'prv-input', 'prv');
            const prvSum = document.getElementById('prvSummary');
            if (prvSum) prvSum.innerHTML = `<strong>${prvTot} Applications</strong>`;
        } catch(e) {
            console.error("PRV calculation failed:", e);
        }

        try {
            calculateRowTotals('#etwpBody', 'etwp-input', 'etwp-row-total');
            const etwpTot = calculateAppTableColumnTotals('#etwpBody', 'etwp-input', 'etwp');
            const etwpSum = document.getElementById('etwpSummary');
            if (etwpSum) etwpSum.innerHTML = `<strong>${etwpTot} Applications</strong>`;
        } catch(e) {
            console.error("e-TWP calculation failed:", e);
        }

        try {
            // calculateVisaSummary
            const evisaApps = parseInt(document.getElementById('eVisaApplicationsTotal')?.value || 0) || 0;
            const evisaAppr = parseInt(document.getElementById('eVisaApprovedTotal')?.value || 0) || 0;
            const evisaRej = parseInt(document.getElementById('eVisaRejectedTotal')?.value || 0) || 0;
            const evisaPend = parseInt(document.getElementById('eVisaPendingTotal')?.value || 0) || 0;
            const evisaTot = parseInt(document.getElementById('eVisaGrandTotal')?.value || 0) || 0;

            const sumEvisaApps = document.getElementById('sum_evisa_apps');
            const sumEvisaAppr = document.getElementById('sum_evisa_appr');
            const sumEvisaRej = document.getElementById('sum_evisa_rej');
            const sumEvisaPend = document.getElementById('sum_evisa_pend');
            const sumEvisaTotal = document.getElementById('sum_evisa_total');

            if (sumEvisaApps) sumEvisaApps.value = evisaApps;
            if (sumEvisaAppr) sumEvisaAppr.value = evisaAppr;
            if (sumEvisaRej) sumEvisaRej.value = evisaRej;
            if (sumEvisaPend) sumEvisaPend.value = evisaPend;
            if (sumEvisaTotal) sumEvisaTotal.value = evisaTot;

            const svvApps = parseInt(document.getElementById('svvApplicationsTotal')?.value || 0) || 0;
            const svvAppr = parseInt(document.getElementById('svvApprovedTotal')?.value || 0) || 0;
            const svvRej = parseInt(document.getElementById('svvRejectedTotal')?.value || 0) || 0;
            const svvPend = parseInt(document.getElementById('svvPendingTotal')?.value || 0) || 0;
            const svvTot = parseInt(document.getElementById('svvGrandTotal')?.value || 0) || 0;

            const sumSvvApps = document.getElementById('sum_svv_apps');
            const sumSvvAppr = document.getElementById('sum_svv_appr');
            const sumSvvRej = document.getElementById('sum_svv_rej');
            const sumSvvPend = document.getElementById('sum_svv_pend');
            const sumSvvTotal = document.getElementById('sum_svv_total');

            if (sumSvvApps) sumSvvApps.value = svvApps;
            if (sumSvvAppr) sumSvvAppr.value = svvAppr;
            if (sumSvvRej) sumSvvRej.value = svvRej;
            if (sumSvvPend) sumSvvPend.value = svvPend;
            if (sumSvvTotal) sumSvvTotal.value = svvTot;

            const trvApps = parseInt(document.getElementById('trvApplicationsTotal')?.value || 0) || 0;
            const trvAppr = parseInt(document.getElementById('trvApprovedTotal')?.value || 0) || 0;
            const trvRej = parseInt(document.getElementById('trvRejectedTotal')?.value || 0) || 0;
            const trvPend = parseInt(document.getElementById('trvPendingTotal')?.value || 0) || 0;
            const trvTot = parseInt(document.getElementById('trvGrandTotal')?.value || 0) || 0;

            const sumTrvApps = document.getElementById('sum_trv_apps');
            const sumTrvAppr = document.getElementById('sum_trv_appr');
            const sumTrvRej = document.getElementById('sum_trv_rej');
            const sumTrvPend = document.getElementById('sum_trv_pend');
            const sumTrvTotal = document.getElementById('sum_trv_total');

            if (sumTrvApps) sumTrvApps.value = trvApps;
            if (sumTrvAppr) sumTrvAppr.value = trvAppr;
            if (sumTrvRej) sumTrvRej.value = trvRej;
            if (sumTrvPend) sumTrvPend.value = trvPend;
            if (sumTrvTotal) sumTrvTotal.value = trvTot;

            const prvApps = parseInt(document.getElementById('prvApplicationsTotal')?.value || 0) || 0;
            const prvAppr = parseInt(document.getElementById('prvApprovedTotal')?.value || 0) || 0;
            const prvRej = parseInt(document.getElementById('prvRejectedTotal')?.value || 0) || 0;
            const prvPend = parseInt(document.getElementById('prvPendingTotal')?.value || 0) || 0;
            const prvTot = parseInt(document.getElementById('prvGrandTotal')?.value || 0) || 0;

            const sumPrvApps = document.getElementById('sum_prv_apps');
            const sumPrvAppr = document.getElementById('sum_prv_appr');
            const sumPrvRej = document.getElementById('sum_prv_rej');
            const sumPrvPend = document.getElementById('sum_prv_pend');
            const sumPrvTotal = document.getElementById('sum_prv_total');

            if (sumPrvApps) sumPrvApps.value = prvApps;
            if (sumPrvAppr) sumPrvAppr.value = prvAppr;
            if (sumPrvRej) sumPrvRej.value = prvRej;
            if (sumPrvPend) sumPrvPend.value = prvPend;
            if (sumPrvTotal) sumPrvTotal.value = prvTot;

            const etwpApps = parseInt(document.getElementById('etwpApplicationsTotal')?.value || 0) || 0;
            const etwpAppr = parseInt(document.getElementById('etwpApprovedTotal')?.value || 0) || 0;
            const etwpRej = parseInt(document.getElementById('etwpRejectedTotal')?.value || 0) || 0;
            const etwpPend = parseInt(document.getElementById('etwpPendingTotal')?.value || 0) || 0;
            const etwpTot = parseInt(document.getElementById('etwpGrandTotal')?.value || 0) || 0;

            const sumEtwpApps = document.getElementById('sum_etwp_apps');
            const sumEtwpAppr = document.getElementById('sum_etwp_appr');
            const sumEtwpRej = document.getElementById('sum_etwp_rej');
            const sumEtwpPend = document.getElementById('sum_etwp_pend');
            const sumEtwpTotal = document.getElementById('sum_etwp_total');

            if (sumEtwpApps) sumEtwpApps.value = etwpApps;
            if (sumEtwpAppr) sumEtwpAppr.value = etwpAppr;
            if (sumEtwpRej) sumEtwpRej.value = etwpRej;
            if (sumEtwpPend) sumEtwpPend.value = etwpPend;
            if (sumEtwpTotal) sumEtwpTotal.value = etwpTot;

            const grandApps = evisaApps + svvApps + trvApps + prvApps + etwpApps;
            const grandAppr = evisaAppr + svvAppr + trvAppr + prvAppr + etwpAppr;
            const grandRej = evisaRej + svvRej + trvRej + prvRej + etwpRej;
            const grandPend = evisaPend + svvPend + trvPend + prvPend + etwpPend;
            const grandTotal = evisaTot + svvTot + trvTot + prvTot + etwpTot;

            const sumGrandAppsEl = document.getElementById('sumGrandApps');
            const sumGrandApprEl = document.getElementById('sumGrandAppr');
            const sumGrandRejEl = document.getElementById('sumGrandRej');
            const sumGrandPendEl = document.getElementById('sumGrandPend');
            const sumGrandTotalEl = document.getElementById('sumGrandTotal');

            if (sumGrandAppsEl) sumGrandAppsEl.value = grandApps;
            if (sumGrandApprEl) sumGrandApprEl.value = grandAppr;
            if (sumGrandRejEl) sumGrandRejEl.value = grandRej;
            if (sumGrandPendEl) sumGrandPendEl.value = grandPend;
            if (sumGrandTotalEl) sumGrandTotalEl.value = grandTotal;
        } catch(e) {
            console.error("Summary calculation failed:", e);
        }

        try {
            calculateRowTotals('#ecowasBody', 'ecowas-input', 'ecowas-row-total');
            const ecowasTot = calculateAffairsTableColumnTotals('#ecowasBody', 'ecowas-input', 'ecowas');
            const ecoSum = document.getElementById('ecowasSummary');
            if (ecoSum) ecoSum.innerHTML = `<strong>${ecowasTot} Persons</strong>`;
        } catch(e) {
            console.error("ECOWAS calculation failed:", e);
        }

        try {
            calculateRowTotals('#africanBody', 'african-input', 'african-row-total');
            const africanTot = calculateAffairsTableColumnTotals('#africanBody', 'african-input', 'african');
            const afrSum = document.getElementById('africanSummary');
            if (afrSum) afrSum.innerHTML = `<strong>${africanTot} Persons</strong>`;
        } catch(e) {
            console.error("African Affairs calculation failed:", e);
        }
    }

    let calcTimeout;
    function debounceUpdateAllCalculations() {
        clearTimeout(calcTimeout);
        calcTimeout = setTimeout(updateAllCalculations, 50);
    }

    document.addEventListener('input', e => {
        debounceUpdateAllCalculations();
    });

    document.addEventListener('change', e => {
        debounceUpdateAllCalculations();
    });

    document.addEventListener('click', function(e) {
        // Handle section minimising / accordion toggling
        const accordionBtn = e.target.closest('.visa-accordion-header, .section-status');
        if (accordionBtn) {
            e.stopImmediatePropagation();
            const card = accordionBtn.closest('.visa-card');
            if (card) {
                card.classList.toggle('collapsed');
                const icon = card.querySelector('.accordion-icon');
                if (icon) {
                    if (card.classList.contains('collapsed')) {
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-right');
                    } else {
                        icon.classList.remove('fa-chevron-right');
                        icon.classList.add('fa-chevron-down');
                    }
                }
            }
        }

        // Handle Reset button click
        const resetBtn = e.target.closest('button[id^="reset"]');
        if (resetBtn) {
            setTimeout(() => {
                updateAllCalculations();
            }, 50);
        }

        // Handle Row Deletion
        const deleteBtn = e.target.closest('.btn-delete-row, .remove-row, .removeQuota, .removeVisaCounter, .removeEcowas, .removeAfrican');
        if (deleteBtn) {
            const tr = deleteBtn.closest('tr');
            if (tr) {
                const tbody = tr.parentElement;
                if (tbody && tbody.children.length > 1) {
                    tr.remove();
                    reindexSnCells(tbody);
                    updateAllCalculations();
                } else {
                    alert('At least one row must remain in the section table.');
                }
            }
        }
    });

    function reindexSnCells(tbody) {
        if (!tbody) return;
        tbody.querySelectorAll('tr').forEach((row, index) => {
            const snCell = row.querySelector('.sn-cell');
            if (snCell) {
                snCell.textContent = index + 1;
            }
        });
    }

    // Button mapping for + Add Row functionality
    const addRowHandlers = {
        '#addNationality': {
            tbody: '#emigrantBody',
            createRow: idx => `
                <tr>
                    <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${idx}</td>
                    <td><input type="text" name="emigrant[${idx}][center]" class="ni" placeholder="Enter Center"></td>
                    <td><input type="text" name="emigrant[${idx}][nationality]" class="ni" placeholder="Enter Nationality"></td>
                    <td><input type="text" name="emigrant[${idx}][region]" class="ni" placeholder="Enter Region"></td>
                    <td><input type="number" value="0" min="0" name="emigrant[${idx}][regular]" class="ni em-number regular"></td>
                    <td><input type="number" value="0" min="0" name="emigrant[${idx}][irregular]" class="ni em-number irregular"></td>
                    <td><input type="number" value="0" min="0" name="emigrant[${idx}][male]" class="ni em-number male"></td>
                    <td><input type="number" value="0" min="0" name="emigrant[${idx}][female]" class="ni em-number female"></td>
                    <td><input type="number" value="0" min="0" name="emigrant[${idx}][employed]" class="ni em-number"></td>
                    <td><input type="number" value="0" min="0" name="emigrant[${idx}][self_employed]" class="ni em-number"></td>
                    <td><input type="number" value="0" min="0" name="emigrant[${idx}][student]" class="ni em-number"></td>
                    <td><input type="number" value="0" min="0" name="emigrant[${idx}][spouse]" class="ni em-number"></td>
                    <td><input type="number" value="0" min="0" name="emigrant[${idx}][dependant]" class="ni em-number"></td>
                    <td><input readonly class="ni total-input emigrant-total" value="0"></td>
                    <td><button type="button" class="btn-nis btn-danger btn-delete-row"><i class="fas fa-trash"></i></button></td>
                </tr>`
        },
        '#addQuotaRow': {
            tbody: '#quotaBody',
            createRow: idx => `
                <tr>
                    <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${idx}</td>
                    <td><input type="text" class="ni quota-company" name="quota[${idx}][company]" placeholder="Company / Enterprise"></td>
                    <td><input type="number" min="0" value="0" class="ni quota-number quota-position" name="quota[${idx}][positions]"></td>
                    <td><input type="number" min="0" value="0" class="ni quota-number quota-utilized" name="quota[${idx}][utilized]"></td>
                    <td><input type="number" min="0" value="0" class="ni quota-number quota-deleted" name="quota[${idx}][deleted]"></td>
                    <td><input type="number" readonly value="0" class="ni total-input quota-row-total"></td>
                    <td><button type="button" class="btn-nis btn-danger btn-delete-row"><i class="fas fa-trash"></i></button></td>
                </tr>`
        },
        '#addTempResidenceRow': {
            tbody: '#tempResidenceBody',
            createRow: idx => `
                <tr>
                    <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${idx}</td>
                    <td><input type="text" name="residence_temporary[new_${idx}][type]" value="Custom Permit ${idx}" class="ni" style="font-weight:600;"></td>
                    <td><input type="number" min="0" value="0" name="residence_temporary[new_${idx}][male]" class="ni residence-input"></td>
                    <td><input type="number" min="0" value="0" name="residence_temporary[new_${idx}][female]" class="ni residence-input"></td>
                    <td><input type="number" min="0" value="0" name="residence_temporary[new_${idx}][principal]" class="ni residence-input"></td>
                    <td><input type="number" min="0" value="0" name="residence_temporary[new_${idx}][dependent]" class="ni residence-input"></td>
                    <td><input type="number" min="0" value="0" name="residence_temporary[new_${idx}][regularization]" class="ni residence-input"></td>
                    <td><input type="number" min="0" value="0" name="residence_temporary[new_${idx}][renewals]" class="ni residence-input"></td>
                    <td><input type="number" min="0" value="0" name="residence_temporary[new_${idx}][redesignation]" class="ni residence-input"></td>
                    <td><input type="number" min="0" value="0" name="residence_temporary[new_${idx}][reclassification]" class="ni residence-input"></td>
                    <td><input type="number" min="0" value="0" name="residence_temporary[new_${idx}][coe]" class="ni residence-input"></td>
                    <td><input type="number" min="0" value="0" name="residence_temporary[new_${idx}][cos]" class="ni residence-input"></td>
                    <td><input type="number" readonly value="0" class="ni total-input residence-row-total"></td>
                    <td><button type="button" class="btn-nis btn-danger btn-delete-row"><i class="fas fa-trash"></i></button></td>
                </tr>`
        },
        '#addPermResidenceRow': {
            tbody: '#permResidenceBody',
            createRow: idx => `
                <tr>
                    <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${idx}</td>
                    <td><input type="text" name="residence_permanent[new_${idx}][type]" value="Custom Permit ${idx}" class="ni" style="font-weight:600;"></td>
                    <td><input type="number" min="0" value="0" name="residence_permanent[new_${idx}][male]" class="ni residence-input"></td>
                    <td><input type="number" min="0" value="0" name="residence_permanent[new_${idx}][female]" class="ni residence-input"></td>
                    <td><input type="number" min="0" value="0" name="residence_permanent[new_${idx}][principal]" class="ni residence-input"></td>
                    <td><input type="number" min="0" value="0" name="residence_permanent[new_${idx}][dependent]" class="ni residence-input"></td>
                    <td><input type="number" min="0" value="0" name="residence_permanent[new_${idx}][regularization]" class="ni residence-input"></td>
                    <td><input type="number" min="0" value="0" name="residence_permanent[new_${idx}][renewals]" class="ni residence-input"></td>
                    <td><input type="number" readonly value="0" class="ni total-input residence-row-total"></td>
                    <td><button type="button" class="btn-nis btn-danger btn-delete-row"><i class="fas fa-trash"></i></button></td>
                </tr>`
        },
        '#addFtzRow': {
            tbody: '#ftzBody',
            createRow: idx => `
                <tr>
                    <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${idx}</td>
                    <td><input type="text" class="ni" name="ftz[${idx}][zones]" placeholder="Enter Free Zone"></td>
                    <td><input type="number" min="0" value="0" class="ni ftz-input" name="ftz[${idx}][enterprises]"></td>
                    <td><input type="number" min="0" value="0" class="ni ftz-input" name="ftz[${idx}][expatriates]"></td>
                    <td><input type="number" min="0" value="0" class="ni ftz-input" name="ftz[${idx}][regularization]"></td>
                    <td><input type="number" min="0" value="0" class="ni ftz-input" name="ftz[${idx}][renewal]"></td>
                    <td><input type="number" min="0" value="0" class="ni ftz-input" name="ftz[${idx}][redesignation]"></td>
                    <td><input type="number" min="0" value="0" class="ni ftz-input" name="ftz[${idx}][coe]"></td>
                    <td><input type="number" min="0" value="0" class="ni ftz-input" name="ftz[${idx}][cos]"></td>
                    <td><input type="number" readonly value="0" class="ni total-input ftz-row-total"></td>
                    <td><button type="button" class="btn-nis btn-danger btn-delete-row"><i class="fas fa-trash"></i></button></td>
                </tr>`
        },
        '#addCerpacRow': {
            tbody: '#cerpacBody',
            createRow: idx => `
                <tr>
                    <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${idx}</td>
                    <td><input type="text" class="ni" name="cerpac[${idx}][centre]" placeholder="Enter Centre"></td>
                    <td><input type="number" min="0" value="0" class="ni cerpac-input" name="cerpac[${idx}][supplied]"></td>
                    <td><input type="number" min="0" value="0" class="ni cerpac-input" name="cerpac[${idx}][produced]"></td>
                    <td><input type="number" min="0" value="0" class="ni cerpac-input" name="cerpac[${idx}][damaged]"></td>
                    <td><input type="number" min="0" value="0" class="ni cerpac-input" name="cerpac[${idx}][issued]"></td>
                    <td><input type="number" readonly value="0" class="ni total-input cerpac-row-total"></td>
                    <td><button type="button" class="btn-nis btn-danger btn-delete-row"><i class="fas fa-trash"></i></button></td>
                </tr>`
        },
        '#addEVisaRow': {
            tbody: '#eVisaBody',
            createRow: idx => `
                <tr>
                    <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${idx}</td>
                    <td><input type="text" name="visa_applications[new_${idx}][class]" value="Custom Class ${idx}" class="ni" style="font-weight:600;"></td>
                    <td><input type="number" min="0" value="0" name="visa_applications[new_${idx}][applications]" class="ni visa-app-input"></td>
                    <td><input type="number" min="0" value="0" name="visa_applications[new_${idx}][approved]" class="ni visa-app-input"></td>
                    <td><input type="number" min="0" value="0" name="visa_applications[new_${idx}][rejected]" class="ni visa-app-input"></td>
                    <td><input type="number" min="0" value="0" name="visa_applications[new_${idx}][pending]" class="ni visa-app-input"></td>
                    <td><input type="number" readonly value="0" class="ni total-input visa-app-row-total"></td>
                    <td><button type="button" class="btn-nis btn-danger btn-delete-row"><i class="fas fa-trash"></i></button></td>
                </tr>`
        },
        '#addSvvRow': {
            tbody: '#svvBody',
            createRow: idx => `
                <tr>
                    <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${idx}</td>
                    <td><input type="text" name="visa_applications[new_${idx}][class]" value="Custom SVV ${idx}" class="ni" style="font-weight:600;"></td>
                    <td><input type="number" min="0" value="0" name="visa_applications[new_${idx}][applications]" class="ni visa-app-input"></td>
                    <td><input type="number" min="0" value="0" name="visa_applications[new_${idx}][approved]" class="ni visa-app-input"></td>
                    <td><input type="number" min="0" value="0" name="visa_applications[new_${idx}][rejected]" class="ni visa-app-input"></td>
                    <td><input type="number" min="0" value="0" name="visa_applications[new_${idx}][pending]" class="ni visa-app-input"></td>
                    <td><input type="number" readonly value="0" class="ni total-input visa-app-row-total"></td>
                    <td><button type="button" class="btn-nis btn-danger btn-delete-row"><i class="fas fa-trash"></i></button></td>
                </tr>`
        },
        '#addTrvRow': {
            tbody: '#trvBody',
            createRow: idx => `
                <tr>
                    <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${idx}</td>
                    <td><input type="text" name="trv[new_${idx}][class]" value="Custom TRV ${idx}" class="ni" style="font-weight:600;"></td>
                    <td><input type="number" min="0" value="0" name="trv[new_${idx}][applications]" class="ni trv-input"></td>
                    <td><input type="number" min="0" value="0" name="trv[new_${idx}][approved]" class="ni trv-input"></td>
                    <td><input type="number" min="0" value="0" name="trv[new_${idx}][rejected]" class="ni trv-input"></td>
                    <td><input type="number" min="0" value="0" name="trv[new_${idx}][pending]" class="ni trv-input"></td>
                    <td><input type="number" readonly value="0" class="ni total-input trv-row-total"></td>
                    <td><button type="button" class="btn-nis btn-danger btn-delete-row"><i class="fas fa-trash"></i></button></td>
                </tr>`
        },
        '#addPrvRow': {
            tbody: '#prvBody',
            createRow: idx => `
                <tr>
                    <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${idx}</td>
                    <td><input type="text" name="prv[new_${idx}][class]" value="Custom PRV ${idx}" class="ni" style="font-weight:600;"></td>
                    <td><input type="number" min="0" value="0" name="prv[new_${idx}][applications]" class="ni prv-input"></td>
                    <td><input type="number" min="0" value="0" name="prv[new_${idx}][approved]" class="ni prv-input"></td>
                    <td><input type="number" min="0" value="0" name="prv[new_${idx}][rejected]" class="ni prv-input"></td>
                    <td><input type="number" min="0" value="0" name="prv[new_${idx}][pending]" class="ni prv-input"></td>
                    <td><input type="number" readonly value="0" class="ni total-input prv-row-total"></td>
                    <td><button type="button" class="btn-nis btn-danger btn-delete-row"><i class="fas fa-trash"></i></button></td>
                </tr>`
        },
        '#addEtwpRow': {
            tbody: '#etwpBody',
            createRow: idx => `
                <tr>
                    <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${idx}</td>
                    <td><input type="text" name="etwp[new_${idx}][class]" value="Custom e-TWP ${idx}" class="ni" style="font-weight:600;"></td>
                    <td><input type="number" min="0" value="0" name="etwp[new_${idx}][applications]" class="ni etwp-input"></td>
                    <td><input type="number" min="0" value="0" name="etwp[new_${idx}][approved]" class="ni etwp-input"></td>
                    <td><input type="number" min="0" value="0" name="etwp[new_${idx}][rejected]" class="ni etwp-input"></td>
                    <td><input type="number" min="0" value="0" name="etwp[new_${idx}][pending]" class="ni etwp-input"></td>
                    <td><input type="number" readonly value="0" class="ni total-input etwp-row-total"></td>
                    <td><button type="button" class="btn-nis btn-danger btn-delete-row"><i class="fas fa-trash"></i></button></td>
                </tr>`
        },
        '#addEcowasRow': {
            tbody: '#ecowasBody',
            createRow: idx => `
                <tr>
                    <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${idx}</td>
                    <td><input type="text" class="ni ecowas-nationality" name="ecowas[${idx}][nationality]" placeholder="Enter Nationality"></td>
                    <td><input type="number" min="0" value="0" name="ecowas[${idx}][male]" class="ni ecowas-number ecowas-input"></td>
                    <td><input type="number" min="0" value="0" name="ecowas[${idx}][female]" class="ni ecowas-number ecowas-input"></td>
                    <td><input type="number" min="0" value="0" name="ecowas[${idx}][principal]" class="ni ecowas-number ecowas-input"></td>
                    <td><input type="number" min="0" value="0" name="ecowas[${idx}][dependent]" class="ni ecowas-number ecowas-input"></td>
                    <td><input type="number" min="0" value="0" name="ecowas[${idx}][regularization]" class="ni ecowas-number ecowas-input"></td>
                    <td><input type="number" min="0" value="0" name="ecowas[${idx}][renewals]" class="ni ecowas-number ecowas-input"></td>
                    <td><input type="number" min="0" value="0" name="ecowas[${idx}][redesignation]" class="ni ecowas-number ecowas-input"></td>
                    <td><input type="number" min="0" value="0" name="ecowas[${idx}][reclassification]" class="ni ecowas-number ecowas-input"></td>
                    <td><input type="number" min="0" value="0" name="ecowas[${idx}][coe]" class="ni ecowas-number ecowas-input"></td>
                    <td><input type="number" min="0" value="0" name="ecowas[${idx}][cos]" class="ni ecowas-number ecowas-input"></td>
                    <td><input type="number" readonly value="0" class="ni total-input ecowas-row-total"></td>
                    <td><button type="button" class="btn-nis btn-danger btn-delete-row"><i class="fas fa-trash"></i></button></td>
                </tr>`
        },
        '#addAfricanAffairsRow': {
            tbody: '#africanBody',
            createRow: idx => `
                <tr>
                    <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${idx}</td>
                    <td><input type="text" class="ni african-nationality" name="african[${idx}][nationality]" placeholder="Enter Nationality"></td>
                    <td><input type="number" min="0" value="0" name="african[${idx}][male]" class="ni african-number african-input"></td>
                    <td><input type="number" min="0" value="0" name="african[${idx}][female]" class="ni african-number african-input"></td>
                    <td><input type="number" min="0" value="0" name="african[${idx}][principal]" class="ni african-number african-input"></td>
                    <td><input type="number" min="0" value="0" name="african[${idx}][dependent]" class="ni african-number african-input"></td>
                    <td><input type="number" min="0" value="0" name="african[${idx}][regularization]" class="ni african-number african-input"></td>
                    <td><input type="number" min="0" value="0" name="african[${idx}][renewals]" class="ni african-number african-input"></td>
                    <td><input type="number" min="0" value="0" name="african[${idx}][redesignation]" class="ni african-number african-input"></td>
                    <td><input type="number" min="0" value="0" name="african[${idx}][reclassification]" class="ni african-number african-input"></td>
                    <td><input type="number" min="0" value="0" name="african[${idx}][coe]" class="ni african-number african-input"></td>
                    <td><input type="number" min="0" value="0" name="african[${idx}][cos]" class="ni african-number african-input"></td>
                    <td><input type="number" readonly value="0" class="ni total-input african-row-total"></td>
                    <td><button type="button" class="btn-nis btn-danger btn-delete-row"><i class="fas fa-trash"></i></button></td>
                </tr>`
        }
    };

    Object.keys(addRowHandlers).forEach(btnSelector => {
        const btn = document.querySelector(btnSelector);
        if (btn) {
            btn.setAttribute('type', 'button');
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                const config = addRowHandlers[btnSelector];
                const tbody = document.querySelector(config.tbody);
                if (tbody) {
                    const idx = tbody.children.length + 1;
                    tbody.insertAdjacentHTML('beforeend', config.createRow(idx));
                    reindexSnCells(tbody);
                    updateAllCalculations();
                }
                return false;
            }, true);
        }
    });

    window.updateAllCalculations = updateAllCalculations;

    // Initial calculation on load
    updateAllCalculations();
    if (document.readyState === 'loading') {
        window.addEventListener('load', updateAllCalculations);
    } else {
        setTimeout(updateAllCalculations, 100);
    }
})();
</script>

@if(isset($application) && $application->return_data)
    <script id="draft-data" type="application/json">
        {!! json_encode($application->return_data) !!}
    </script>
@endif

<script>
window.addEventListener('load', function() {
    const draftDataEl = document.getElementById('draft-data');
    if (!draftDataEl) return;
    const savedDraftData = JSON.parse(draftDataEl.textContent);
    if (!savedDraftData) return;

    // 1. Recreate dynamic rows first
    const visaDynamicKeys = {
        'emigrant': '#addNationality',
        'quota': '#addQuotaRow',
        'residence_temporary': '#addTempResidenceRow',
        'residence_permanent': '#addPermResidenceRow',
        'ftz': '#addFtzRow',
        'cerpac': '#addCerpacRow',
        'trv': '#addTrvRow',
        'prv': '#addPrvRow',
        'etwp': '#addEtwpRow',
        'ecowas': '#addEcowasRow',
        'african': '#addAfricanAffairsRow'
    };

    Object.keys(visaDynamicKeys).forEach(key => {
        if (savedDraftData[key]) {
            const savedRows = Object.keys(savedDraftData[key]);
            const btnSelector = visaDynamicKeys[key];
            const btn = document.querySelector(btnSelector);
            if (btn && savedRows.length > 0) {
                const handler = addRowHandlers[btnSelector];
                const tbodySelector = handler ? handler.tbody : null;
                const tbody = tbodySelector ? document.querySelector(tbodySelector) : btn.closest('.visa-card')?.querySelector('tbody');
                const currentRows = tbody ? tbody.querySelectorAll('tr').length : 1;
                const needed = savedRows.length - currentRows;
                for (let i = 0; i < needed; i++) {
                    btn.click();
                }
            }
        }
    });

    if (savedDraftData.visa_applications) {
        Object.keys(savedDraftData.visa_applications).forEach(key => {
            if (key.startsWith('new_')) {
                const val = savedDraftData.visa_applications[key];
                const isSvv = val && val.class && val.class.includes('SVV');
                if (isSvv) {
                    document.getElementById('addSvvRow')?.click();
                } else {
                    document.getElementById('addEVisaRow')?.click();
                }
            }
        });
    }

    // 2. Iterate through all form elements and populate their value
    const form = document.getElementById('visaAnnualReport');
    if (form) {
        const elements = form.querySelectorAll('input:not([type="submit"]):not([type="hidden"]):not([readonly]), textarea, select:not([readonly])');
        elements.forEach(el => {
            const name = el.name;
            if (!name) return;

            const keys = name.split(/[\[\]]+/).filter(Boolean);
            const val = getValueFromPath(savedDraftData, keys);
            if (val !== undefined && val !== null) {
                if (el.type === 'checkbox' || el.type === 'radio') {
                    el.checked = (el.value == val);
                } else {
                    el.value = val;
                }
            }
        });
    }

    function getValueFromPath(obj, keys) {
        let current = obj;
        for (let i = 0; i < keys.length; i++) {
            if (current === null || current === undefined) {
                return undefined;
            }
            current = current[keys[i]];
        }
        return current;
    }

    if (typeof window.updateAllCalculations === 'function') {
        window.updateAllCalculations();
    }
});

// Redirect when year changes
document.getElementById('report_year')?.addEventListener('change', function() {
    const year = this.value;
    const url = new URL(window.location.href);
    url.searchParams.set('year', year);
    window.location.href = url.toString();
});

// Clear/Reset entire report form
document.getElementById('clearReportFormBtn')?.addEventListener('click', function(e) {
    e.preventDefault();
    if (!confirm('Are you sure you want to clear and reset the entire report form?')) return;
    
    const form = this.closest('form');
    if (!form) return;

    // Reset standard inputs (except those that should remain)
    form.querySelectorAll('input:not([readonly]):not([type="hidden"]), textarea').forEach(input => {
        if (input.type === 'number') {
            input.value = 0;
        } else {
            input.value = '';
        }
    });

    // Reset selects (except report_year)
    form.querySelectorAll('select:not([readonly])').forEach(select => {
        if (select.id !== 'report_year' && select.name !== 'report_year') {
            select.selectedIndex = 0;
        }
    });

    // Reset dynamic rows (remove all except first)
    form.querySelectorAll('tbody').forEach(tbody => {
        if (tbody.id === 'staff_strength_tbody') {
            tbody.querySelectorAll('input:not([readonly])').forEach(input => {
                input.value = 0;
            });
        } else {
            const rows = tbody.querySelectorAll('tr');
            rows.forEach((row, idx) => {
                if (idx > 0) {
                    row.remove();
                } else {
                    row.querySelectorAll('input:not([readonly]):not([type="hidden"]), textarea, select').forEach(input => {
                        if (input.type === 'number') {
                            input.value = 0;
                        } else if (input.tagName === 'SELECT') {
                            input.selectedIndex = 0;
                        } else {
                            input.value = '';
                        }
                    });
                }
            });
        }
    });
    
    // Clear localStorage for this year
    const officer = document.querySelector('[name="reporting_officer"]')?.value || 'officer';
    const year = document.getElementById('report_year')?.value || '2026';
    const draftKey = `redas_draft_${officer}_${year}`;
    localStorage.removeItem(draftKey);
    
    // Trigger input event to update calculations
    const ev = new Event('input', { bubbles: true });
    form.dispatchEvent(ev);
});
// Disable fields when report status is pending, approved or submitted, or user is admin/supervisor (except year selector)
window.addEventListener('DOMContentLoaded', function() {
    @if(($application && in_array($application->status, ['pending', 'approved', 'submitted'])) || auth()->user()->user_category === 'directorate_admin' || auth()->user()->role === 'admin')
        const form = document.getElementById('visaAnnualReport');
        if (form) {
            form.querySelectorAll('input, textarea, select, button').forEach(el => {
                if (el.id !== 'report_year' && el.name !== 'report_year') {
                    el.disabled = true;
                }
            });
        }
    @endif
});
</script>

@include('partials.footer')