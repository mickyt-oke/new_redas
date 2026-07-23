document.addEventListener('DOMContentLoaded', () => {

    initAccordion();

    initStaffStrength();

    initEMigrant();

    initQuota();

    initResidence();

    initFTZ();

    initCERPAC();

    initVisaApplications();

    initTRV();

    initPRV();

    initETWP();

    initECOWAS();

    initAfricanAffairs();

    if (typeof window.updateAllCalculations === 'function') {
        window.updateAllCalculations();
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


/*=========================================================================
    ACCORDION
=========================================================================*/

function initAccordion() {

    const sections = [

        {
            button: 'generalToggle',
            card: 'generalCard'
        },

        {
            button: 'staffStrengthToggle',
            card: 'staffStrengthCard'
        },

        {
            button: 'emigrantToggle',
            card: 'emigrantCard'
        },

        {
            button: 'quotaToggle',
            card: 'quotaCard'
        },

        {
            button: 'residenceToggle',
            card: 'residenceCard'
        },

        {
            button: 'ftzToggle',
            card: 'ftzCard'
        },

        {
            button: 'cerpacToggle',
            card: 'cerpacCard'
        },

        {
            button: 'visaApplicationToggle',
            card: 'visaApplicationCard'
        },

        {
            button: 'trvToggle',
            card: 'trvCard'
        },

        {
            button: 'prvToggle',
            card: 'prvCard'
        },

        {
            button: 'etwpToggle',
            card: 'etwpCard'
        },

        {
            button: 'visaCounterToggle',
            card: 'visaCounterCard'
        },

        {
            button: 'ecowasToggle',
            card: 'ecowasCard'
        },

        {
            button: 'africanAffairsToggle',
            card: 'africanAffairsCard'
        }

    ];

    sections.forEach(section => {

        const toggle = document.getElementById(section.button);

        const card = document.getElementById(section.card);

        if (!toggle || !card) return;

        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            e.stopImmediatePropagation();

            card.classList.toggle('collapsed');

            const icon = this.querySelector('.accordion-icon');

            if (icon) {

                icon.classList.toggle('fa-chevron-down');

                icon.classList.toggle('fa-chevron-right');

            }

        });

    });

    // Automatically expand card when scrolled/jumped to via hash link
    const handleHashLink = (hash) => {
        if (!hash) return;
        const targetSection = document.querySelector(hash);
        if (targetSection) {
            const card = targetSection.querySelector('.visa-card');
            if (card && card.classList.contains('collapsed')) {
                card.classList.remove('collapsed');
                const icon = card.querySelector('.accordion-icon');
                if (icon) {
                    icon.classList.remove('fa-chevron-right');
                    icon.classList.add('fa-chevron-down');
                }
            }
        }
    };

    window.addEventListener('hashchange', () => {
        handleHashLink(window.location.hash);
    });

    setTimeout(() => {
        handleHashLink(window.location.hash);
    }, 100);

}

/*=========================================================================
    HELPERS
=========================================================================*/

function getNumber(id){

    const input=document.getElementById(id);

    if(!input){

        return 0;

    }

    return parseInt(input.value)||0;

}


function setValue(id,value){

    const input=document.getElementById(id);

    if(input){

        input.value=value;

    }

}


function updateStatus(id,completed){

    const badge=document.getElementById(id);

    if(!badge){

        return;

    }

    badge.classList.remove(

        'status-progress',

        'status-complete'

    );

    if(completed){

        badge.classList.add('status-complete');

        badge.textContent="Completed";

    }else{

        badge.classList.add('status-progress');

        badge.textContent="In Progress";

    }

}

/*=========================================================================
    STAFF STRENGTH
=========================================================================*/

function initStaffStrength(){

    const inputs=document.querySelectorAll(

        '#staffStrengthCard .male, #staffStrengthCard .female'

    );

    inputs.forEach(input=>{

        input.addEventListener('input',calculateStaffStrength);

        input.addEventListener('change',calculateStaffStrength);

        input.addEventListener('keyup',calculateStaffStrength);

        input.addEventListener('click',function(){

            this.select();

        });

    });

    const resetButton=document.getElementById(

        'resetStaffStrength'

    );

    if(resetButton){

        resetButton.addEventListener(

            'click',

            resetStaffStrength

        );

    }

    const saveButton=document.getElementById(

        'saveStaffStrength'

    );

    if(saveButton){

        saveButton.addEventListener(

            'click',

            saveStaffStrength

        );

    }

    calculateStaffStrength();

}


/*=========================================================================
    CALCULATE STAFF STRENGTH
=========================================================================*/

function calculateStaffStrength(){

    let grandMale=0;

    let grandFemale=0;

    let grandTotal=0;

    const tbody = document.getElementById('staff_strength_tbody');
    if (tbody) {
        tbody.querySelectorAll('tr').forEach(row => {
            const maleInput = row.querySelector('.staff-input.male, .male');
            const femaleInput = row.querySelector('.staff-input.female, .female');
            const totalInput = row.querySelector('.total-input');

            if (maleInput && femaleInput) {
                let mVal = Math.max(0, parseInt(maleInput.value) || 0);
                let fVal = Math.max(0, parseInt(femaleInput.value) || 0);
                maleInput.value = mVal;
                femaleInput.value = fVal;
                let rTot = mVal + fVal;
                if (totalInput) totalInput.value = rTot;

                grandMale += mVal;
                grandFemale += fVal;
                grandTotal += rTot;
            }
        });
    }

    setValue(

        'grandMale',

        grandMale

    );

    setValue(

        'grandFemale',

        grandFemale

    );

    setValue(

        'grandTotal',

        grandTotal

    );

    const summary=document.getElementById(

        'staffSummary'

    );

    if(summary){

        summary.textContent=

            grandTotal+" Officers";

    }

    updateStatus(

        'staffStatus',

        grandTotal>0

    );

    updateProgress();

}


/*=========================================================================
    RESET
=========================================================================*/

function resetStaffStrength(){

    document.querySelectorAll(

        '#staffStrengthCard input[type=number]'

    ).forEach(input=>{

        if(!input.hasAttribute('readonly')){

            input.value=0;

        }

    });

    calculateStaffStrength();

}


/*=========================================================================
    SAVE SECTION
=========================================================================*/

function saveStaffStrength(){

    const button=document.getElementById(

        'saveStaffStrength'

    );

    if(!button){

        return;

    }

    const oldText=button.innerHTML;

    button.innerHTML=

        '<i class="fas fa-spinner fa-spin"></i> Saving...';

    button.disabled=true;

    setTimeout(()=>{

        button.innerHTML=

            '<i class="fas fa-check"></i> Saved';

        button.disabled=false;

        setTimeout(()=>{

            button.innerHTML=oldText;

        },1500);

    },800);

}


/*=========================================================================
    REPORT PROGRESS
=========================================================================*/

function updateProgress(){

    let completed=0;

    let total=13;

    const statuses=[

        'staffStatus',

        'emigrantStatus',

        'quotaStatus',

        'residenceStatus',

        'ftzStatus',

        'cerpacStatus',

        'visaStatus',

        'trvStatus',

        'prvStatus',

        'etwpStatus',

        'visaCounterStatus',

        'ecowasStatus',

        'africanStatus'

    ];

    statuses.forEach(id=>{

        const badge=document.getElementById(id);

        if(

            badge &&

            badge.classList.contains(

                'status-complete'

            )

        ){

            completed++;

        }

    });

    const percentage=Math.round(

        (completed/total)*100

    );

    const bar=document.getElementById(

        'reportProgressBar'

    );

    const text=document.getElementById(

        'reportProgressText'

    );

    if(bar){

        bar.style.width=

            percentage+"%";

    }

    if(text){

        text.textContent=

            completed+

            " / "+

            total+

            " Sections ("+

            percentage+

            "%)";

    }

}

/*=========================================================================
    E-MIGRANT CENTRE
=========================================================================*/

function initEMigrant(){

    attachEMigrantEvents();



    const resetButton=document.getElementById(

        "resetEMigrant"

    );

    if(resetButton){

        resetButton.addEventListener(

            "click",

            resetEMigrant

        );

    }

    const saveButton=document.getElementById(

        "saveEMigrant"

    );

    if(saveButton){

        saveButton.addEventListener(

            "click",

            saveEMigrant

        );

    }

    calculateEMigrant();

}


/*=========================================================================
    ATTACH EVENTS
=========================================================================*/

function attachEMigrantEvents(){

    document.querySelectorAll(

        "#emigrantBody .em-number"

    ).forEach(input=>{

        input.removeEventListener(

            "input",

            calculateEMigrant

        );

        input.addEventListener(

            "input",

            calculateEMigrant

        );

        input.addEventListener(

            "change",

            calculateEMigrant

        );

        input.addEventListener(

            "keyup",

            calculateEMigrant

        );

        input.addEventListener(

            "click",

            function(){

                this.select();

            }

        );

    });

}


/*=========================================================================
    CALCULATE
=========================================================================*/

function calculateEMigrant(){

    let overall=0;

    const rows=document.querySelectorAll(

        "#emigrantBody tr"

    );

    rows.forEach(row=>{

        let regular=parseInt(

            row.querySelector(".regular")?.value

        )||0;

        let irregular=parseInt(

            row.querySelector(".irregular")?.value

        )||0;

        let male=parseInt(

            row.querySelector(".male")?.value

        )||0;

        let female=parseInt(

            row.querySelector(".female")?.value

        )||0;

        regular=Math.max(0,regular);

        irregular=Math.max(0,irregular);

        male=Math.max(0,male);

        female=Math.max(0,female);

        row.querySelector(".regular").value=regular;

        row.querySelector(".irregular").value=irregular;

        row.querySelector(".male").value=male;

        row.querySelector(".female").value=female;

        const total=regular+irregular;

        const totalInput=row.querySelector(

            ".emigrant-total"

        );

        if(totalInput){

            totalInput.value=total;

        }

        overall+=total;

    });

    const overallLabel=document.getElementById(

        "overallMigrants"

    );

    if(overallLabel){

        overallLabel.textContent=overall;

    }

    updateStatus(

        "emigrantStatus",

        overall>0

    );

    updateProgress();

}


/*=========================================================================
    ADD NATIONALITY
=========================================================================*/

function addNationalityRow(){

    const tbody=document.getElementById("emigrantBody");

    if(!tbody){
        return;
    }

    const index=tbody.querySelectorAll("tr").length;

    const row=document.createElement("tr");

    row.innerHTML=`
<td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${index + 1}</td>
<td>
<input type="text" name="emigrant[${index}][center]" class="ni" placeholder="Enter Center">
</td>
<td>
<input type="text" name="emigrant[${index}][nationality]" class="ni" placeholder="Enter Nationality">
</td>
<td>
<select class="ni" name="emigrant[${index}][region]">
<option value="">Select</option>
<option>ECOWAS</option>
<option>Africa</option>
<option>Europe</option>
<option>Asia</option>
<option>America</option>
<option>Middle East</option>
</select>
</td>
<td>
<input type="number" name="emigrant[${index}][regular]" class="ni em-number regular" value="0" min="0">
</td>
<td>
<input type="number" name="emigrant[${index}][irregular]" class="ni em-number irregular" value="0" min="0">
</td>
<td>
<input type="number" name="emigrant[${index}][male]" class="ni em-number male" value="0" min="0">
</td>
<td>
<input type="number" name="emigrant[${index}][female]" class="ni em-number female" value="0" min="0">
</td>
<td>
<input readonly class="ni total-input emigrant-total" value="0">
</td>
<td>
<input type="number" name="emigrant[${index}][employed]" class="ni em-number" value="0" min="0">
</td>
<td>
<input type="number" name="emigrant[${index}][self_employed]" class="ni em-number" value="0" min="0">
</td>
<td>
<input type="number" name="emigrant[${index}][student]" class="ni em-number" value="0" min="0">
</td>
<td>
<input type="number" name="emigrant[${index}][spouse]" class="ni em-number" value="0" min="0">
</td>
<td>
<input type="number" name="emigrant[${index}][dependant]" class="ni em-number" value="0" min="0">
</td>
<td style="text-align:center;">
<button type="button" class="btn-nis btn-danger remove-row">
<i class="fas fa-trash"></i>
</button>
</td>
`;

    tbody.appendChild(row);

    row.querySelector(".remove-row")
        .addEventListener(
            "click",
            function(){
                row.remove();
                reindexSnCells(tbody);
                calculateEMigrant();
            }
        );

    attachEMigrantEvents();

    reindexSnCells(tbody);

    calculateEMigrant();

}


/*=========================================================================
    RESET
=========================================================================*/

function resetEMigrant(){

    document.querySelectorAll(

        "#emigrantBody input[type=number]"

    ).forEach(input=>{

        if(!input.hasAttribute("readonly")){

            input.value=0;

        }

    });

    calculateEMigrant();

}


/*=========================================================================
    SAVE
=========================================================================*/

function saveEMigrant(){

    const button=document.getElementById(

        "saveEMigrant"

    );

    if(!button){

        return;

    }

    const oldText=button.innerHTML;

    button.disabled=true;

    button.innerHTML=

        '<i class="fas fa-spinner fa-spin"></i> Saving...';

    setTimeout(()=>{

        button.innerHTML=

            '<i class="fas fa-check"></i> Saved';

        button.disabled=false;

        setTimeout(()=>{

            button.innerHTML=oldText;

        },1500);

    },800);

}

/*=========================================================================
    QUOTA ADMINISTRATION
=========================================================================*/

initQuota();

function initQuota() {

    attachQuotaEvents();

    calculateQuota();



    const resetBtn = document.getElementById('resetQuota');

    if(resetBtn){

        resetBtn.addEventListener('click', resetQuota);

    }

    const saveBtn = document.getElementById('saveQuota');

    if(saveBtn){

        saveBtn.addEventListener('click', saveQuota);

    }

}

function attachQuotaEvents(){

    document.querySelectorAll('#quotaBody .quota-number').forEach(input=>{

        input.removeEventListener('input',calculateQuota);

        input.addEventListener('input',calculateQuota);

    });

    document.querySelectorAll('.removeQuota').forEach(button=>{

        button.onclick=function(){

            if(document.querySelectorAll('#quotaBody tr').length===1){

                return;

            }

            const tbody = this.closest('tbody');
            this.closest('tr').remove();
            reindexSnCells(tbody);

            calculateQuota();

        };

    });

}

function calculateQuota(){

    let totalPosition=0;

    let totalUtilized=0;

    let totalDeleted=0;

    let grandTotal=0;

    document.querySelectorAll('#quotaBody tr').forEach(row=>{

        const position=parseInt(row.querySelector('.quota-position').value)||0;

        const utilized=parseInt(row.querySelector('.quota-utilized').value)||0;

        const deleted=parseInt(row.querySelector('.quota-deleted').value)||0;

        const rowTotal = position + utilized + deleted;

        const rowTotalInput = row.querySelector('.quota-row-total');

        if(rowTotalInput) {

            rowTotalInput.value = rowTotal;

        }

        totalPosition+=position;

        totalUtilized+=utilized;

        totalDeleted+=deleted;

        grandTotal+=rowTotal;

    });

    document.getElementById('quotaPositionsTotal').value=totalPosition;

    document.getElementById('quotaUtilizedTotal').value=totalUtilized;

    document.getElementById('quotaDeletedTotal').value=totalDeleted;

    const grandTotalInput = document.getElementById('quotaGrandTotal');

    if(grandTotalInput) {

        grandTotalInput.value = grandTotal;

    }

    const status=document.getElementById('quotaStatus');

    if(status){

        if(totalPosition>0){

            status.classList.remove('status-progress');

            status.classList.add('status-complete');

            status.innerHTML='Completed';

        }else{

            status.classList.remove('status-complete');

            status.classList.add('status-progress');

            status.innerHTML='In Progress';

        }

    }

}

function addQuotaRow(){

    const tbody=document.getElementById('quotaBody');

    const index=tbody.querySelectorAll('tr').length;

    const row=document.createElement('tr');

    row.innerHTML=`
        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${index + 1}</td>
        <td>

            <input
                type="text"
                class="ni quota-company"
                name="quota[${index}][company]"
                placeholder="Company / Enterprise">

        </td>

        <td>

            <input
                type="number"
                min="0"
                value="0"
                class="ni quota-number quota-position"
                name="quota[${index}][positions]">

        </td>

        <td>

            <input
                type="number"
                min="0"
                value="0"
                class="ni quota-number quota-utilized"
                name="quota[${index}][utilized]">

        </td>

        <td>

            <input
                type="number"
                min="0"
                value="0"
                class="ni quota-number quota-deleted"
                name="quota[${index}][deleted]">

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

    `;

    tbody.appendChild(row);

    reindexSnCells(tbody);

    attachQuotaEvents();

    calculateQuota();

}

function resetQuota(){

    document.querySelectorAll('#quotaBody tr').forEach((row,index)=>{

        if(index>0){

            row.remove();

        }

    });

    const first=document.querySelector('#quotaBody tr');

    if(first){

        first.querySelector('.quota-company').value='';

        first.querySelector('.quota-position').value=0;
        first.querySelector('.quota-utilized').value=0;
        first.querySelector('.quota-deleted').value=0;

    }

    calculateQuota();

}

function saveQuota(){

    mockSaveButton('saveQuota');

}


/*=========================================================================
    RESIDENCE PERMIT
=========================================================================*/

initResidence();

function initResidence(){

    attachResidenceEvents();

    calculateResidence();

    const reset=document.getElementById('resetResidence');

    if(reset){

        reset.addEventListener('click',resetResidence);

    }

    const save=document.getElementById('saveResidence');

    if(save){

        save.addEventListener('click',saveResidence);

    }

}

function attachResidenceEvents(){

    document.querySelectorAll('#residenceCard .residence-input').forEach(input=>{

        input.removeEventListener('input',calculateResidence);

        input.addEventListener('input',calculateResidence);

        input.addEventListener('click',function(){

            this.select();

        });

    });

}

function calculateResidence(){

    let overall=0;

    document.querySelectorAll('#residenceCard tbody tr').forEach(row=>{

        let total=0;

        row.querySelectorAll('.residence-input').forEach(input=>{

            const value=parseInt(input.value)||0;

            input.value=Math.max(0,value);

            total+=Math.max(0,value);

        });

        const totalInput=row.querySelector('.residence-row-total');

        if(totalInput){

            totalInput.value=total;

        }

        overall+=total;

    });

    const status=document.getElementById('residenceStatus');

    if(status){

        if(overall>0){

            status.classList.remove('status-progress');

            status.classList.add('status-complete');

            status.innerHTML='Completed';

        }else{

            status.classList.remove('status-complete');

            status.classList.add('status-progress');

            status.innerHTML='In Progress';

        }

    }

}

function resetResidence(){

    document.querySelectorAll('#residenceCard .residence-input').forEach(input=>{

        input.value=0;

    });

    calculateResidence();

}

function saveResidence(){

    mockSaveButton('saveResidence');

}

/*=========================================================================
    GENERIC MOCK SAVE BUTTON HELPER
=========================================================================*/
function mockSaveButton(id) {
    const button = document.getElementById(id);
    if (!button) return;
    const oldText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check"></i> Saved';
        button.disabled = false;
        setTimeout(() => {
            button.innerHTML = oldText;
        }, 1500);
    }, 800);
}

/*=========================================================================
    FREE TRADE ZONE (FTZ)
=========================================================================*/
function initFTZ() {
    attachFTZEvents();
    calculateFTZ();
    const reset = document.getElementById('resetFTZ');
    if (reset) reset.addEventListener('click', resetFTZ);
    const save = document.getElementById('saveFTZ');
    if (save) save.addEventListener('click', saveFTZ);
}
function attachFTZEvents() {
    document.querySelectorAll('#ftzCard .ftz-input').forEach(input => {
        input.removeEventListener('input', calculateFTZ);
        input.addEventListener('input', calculateFTZ);
        input.addEventListener('click', function() { this.select(); });
    });
}
function calculateFTZ() {
    let enterprises = 0, expat = 0, reg = 0, ren = 0, redes = 0, coe = 0, cos = 0, grand = 0;
    document.querySelectorAll('#ftzBody tr').forEach(row => {
        const rowInputs = row.querySelectorAll('.ftz-input');
        if (rowInputs.length >= 7) {
            const ent = Math.max(0, parseInt(rowInputs[0].value || 0));
            const exp = Math.max(0, parseInt(rowInputs[1].value || 0));
            const rVal = Math.max(0, parseInt(rowInputs[2].value || 0));
            const renVal = Math.max(0, parseInt(rowInputs[3].value || 0));
            const redesVal = Math.max(0, parseInt(rowInputs[4].value || 0));
            const coeVal = Math.max(0, parseInt(rowInputs[5].value || 0));
            const cosVal = Math.max(0, parseInt(rowInputs[6].value || 0));
            
            rowInputs[0].value = ent;
            rowInputs[1].value = exp;
            rowInputs[2].value = rVal;
            rowInputs[3].value = renVal;
            rowInputs[4].value = redesVal;
            rowInputs[5].value = coeVal;
            rowInputs[6].value = cosVal;
            
            const rowTotal = ent + exp + rVal + renVal + redesVal + coeVal + cosVal;
            const totalInput = row.querySelector('.ftz-row-total');
            if (totalInput) totalInput.value = rowTotal;
            
            enterprises += ent;
            expat += exp;
            reg += rVal;
            ren += renVal;
            redes += redesVal;
            coe += coeVal;
            cos += cosVal;
            grand += rowTotal;
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
    const ftzSum = document.getElementById('ftzSummary');
    
    if (entEl) entEl.value = enterprises;
    if (expEl) expEl.value = expat;
    if (regEl) regEl.value = reg;
    if (renEl) renEl.value = ren;
    if (redesEl) redesEl.value = redes;
    if (coeEl) coeEl.value = coe;
    if (cosEl) cosEl.value = cos;
    if (grandEl) grandEl.value = grand;
    if (ftzSum) ftzSum.innerHTML = `<strong>${grand} Activities</strong>`;
    
    updateStatus('ftzStatus', grand > 0);
    updateProgress();
}
function resetFTZ() {
    document.querySelectorAll('#ftzBody tr').forEach((row, idx) => {
        if (idx > 0) row.remove();
    });
    const first = document.querySelector('#ftzBody tr');
    if (first) {
        first.querySelectorAll('input').forEach(input => {
            if (input.type === 'number') input.value = 0;
            else if (input.type === 'text') input.value = '';
        });
    }
    calculateFTZ();
}
function saveFTZ() {
    mockSaveButton('saveFTZ');
}

/*=========================================================================
    CERPAC PRODUCTION
=========================================================================*/
function initCERPAC() {
    attachCERPACEvents();
    calculateCERPAC();
    const reset = document.getElementById('resetCERPAC');
    if (reset) reset.addEventListener('click', resetCERPAC);
    const save = document.getElementById('saveCERPAC');
    if (save) save.addEventListener('click', saveCERPAC);
}
function attachCERPACEvents() {
    document.querySelectorAll('#cerpacCard .cerpac-input').forEach(input => {
        input.removeEventListener('input', calculateCERPAC);
        input.addEventListener('input', calculateCERPAC);
        input.addEventListener('click', function() { this.select(); });
    });
}
function calculateCERPAC() {
    let supplied = 0, produced = 0, damaged = 0, issued = 0, grand = 0;
    document.querySelectorAll('#cerpacBody tr').forEach(row => {
        const rowInputs = row.querySelectorAll('.cerpac-input');
        if (rowInputs.length >= 4) {
            const supVal = Math.max(0, parseInt(rowInputs[0].value || 0));
            const proVal = Math.max(0, parseInt(rowInputs[1].value || 0));
            const damVal = Math.max(0, parseInt(rowInputs[2].value || 0));
            const issVal = Math.max(0, parseInt(rowInputs[3].value || 0));
            
            rowInputs[0].value = supVal;
            rowInputs[1].value = proVal;
            rowInputs[2].value = damVal;
            rowInputs[3].value = issVal;
            
            const rowTotal = supVal + proVal + damVal + issVal;
            const totalInput = row.querySelector('.cerpac-row-total');
            if (totalInput) totalInput.value = rowTotal;
            
            supplied += supVal;
            produced += proVal;
            damaged += damVal;
            issued += issVal;
            grand += rowTotal;
        }
    });
    
    const supEl = document.getElementById('cerpacSuppliedTotal');
    const proEl = document.getElementById('cerpacProducedTotal');
    const damEl = document.getElementById('cerpacDamagedTotal');
    const issEl = document.getElementById('cerpacIssuedTotal');
    const grandEl = document.getElementById('cerpacGrandTotal');
    const cerpacSum = document.getElementById('cerpacSummary');
    
    if (supEl) supEl.value = supplied;
    if (proEl) proEl.value = produced;
    if (damEl) damEl.value = damaged;
    if (issEl) issEl.value = issued;
    if (grandEl) grandEl.value = grand;
    if (cerpacSum) cerpacSum.innerHTML = `<strong>${issued} Cards</strong>`;
    
    updateStatus('cerpacStatus', grand > 0);
    updateProgress();
}
function resetCERPAC() {
    document.querySelectorAll('#cerpacBody tr').forEach((row, idx) => {
        if (idx > 0) row.remove();
    });
    const first = document.querySelector('#cerpacBody tr');
    if (first) {
        first.querySelectorAll('input').forEach(input => {
            if (input.type === 'number') input.value = 0;
            else if (input.type === 'text') input.value = '';
        });
    }
    calculateCERPAC();
}
function saveCERPAC() {
    mockSaveButton('saveCERPAC');
}

/*=========================================================================
    VISA APPLICATIONS (e-Visa & SVV)
=========================================================================*/
function initVisaApplications() {
    attachVisaApplicationsEvents();
    calculateVisaApplications();
    const reset = document.getElementById('resetVisaApplications');
    if (reset) reset.addEventListener('click', resetVisaApplications);
    const save = document.getElementById('saveVisaApplications');
    if (save) save.addEventListener('click', saveVisaApplications);
}
function attachVisaApplicationsEvents() {
    document.querySelectorAll('#visaApplicationCard .visa-app-input').forEach(input => {
        input.removeEventListener('input', calculateVisaApplications);
        input.addEventListener('input', calculateVisaApplications);
        input.addEventListener('click', function() { this.select(); });
    });
}
function calculateVisaApplications() {
    let overall = 0;
    document.querySelectorAll('#visaApplicationCard tbody tr').forEach(row => {
        let total = 0;
        row.querySelectorAll('.visa-app-input').forEach(input => {
            let val = parseInt(input.value) || 0;
            input.value = Math.max(0, val);
            total += Math.max(0, val);
        });
        const rowTotal = row.querySelector('.visa-app-row-total');
        if (rowTotal) rowTotal.value = total;
        overall += total;
    });
    updateStatus('visaStatus', overall > 0);
    updateProgress();
    if (typeof window.updateAllCalculations === 'function') {
        window.updateAllCalculations();
    }
}
function resetVisaApplications() {
    document.querySelectorAll('#visaApplicationCard .visa-app-input').forEach(input => {
        input.value = 0;
    });
    calculateVisaApplications();
}
function saveVisaApplications() {
    mockSaveButton('saveVisaApplications');
}

/*=========================================================================
    TEMPORARY RESIDENCE VISA (TRV)
=========================================================================*/
function initTRV() {
    attachTRVEvents();
    calculateTRV();
    const reset = document.getElementById('resetTRV');
    if (reset) reset.addEventListener('click', resetTRV);
    const save = document.getElementById('saveTRV');
    if (save) save.addEventListener('click', saveTRV);
}
function attachTRVEvents() {
    document.querySelectorAll('#trvCard .trv-input').forEach(input => {
        input.removeEventListener('input', calculateTRV);
        input.addEventListener('input', calculateTRV);
        input.addEventListener('click', function() { this.select(); });
    });
}
function calculateTRV() {
    let overall = 0;
    document.querySelectorAll('#trvCard tbody tr').forEach(row => {
        let total = 0;
        row.querySelectorAll('.trv-input').forEach(input => {
            let val = parseInt(input.value) || 0;
            input.value = Math.max(0, val);
            total += Math.max(0, val);
        });
        const rowTotal = row.querySelector('.trv-row-total');
        if (rowTotal) rowTotal.value = total;
        overall += total;
    });
    updateStatus('trvStatus', overall > 0);
    updateProgress();
    if (typeof window.updateAllCalculations === 'function') {
        window.updateAllCalculations();
    }
}
function resetTRV() {
    document.querySelectorAll('#trvCard .trv-input').forEach(input => {
        input.value = 0;
    });
    calculateTRV();
}
function saveTRV() {
    mockSaveButton('saveTRV');
}

/*=========================================================================
    PERMANENT RESIDENCE VISA (PRV)
=========================================================================*/
function initPRV() {
    attachPRVEvents();
    calculatePRV();
    const reset = document.getElementById('resetPRV');
    if (reset) reset.addEventListener('click', resetPRV);
    const save = document.getElementById('savePRV');
    if (save) save.addEventListener('click', savePRV);
}
function attachPRVEvents() {
    document.querySelectorAll('#prvCard .prv-input').forEach(input => {
        input.removeEventListener('input', calculatePRV);
        input.addEventListener('input', calculatePRV);
        input.addEventListener('click', function() { this.select(); });
    });
}
function calculatePRV() {
    let overall = 0;
    document.querySelectorAll('#prvCard tbody tr').forEach(row => {
        let total = 0;
        row.querySelectorAll('.prv-input').forEach(input => {
            let val = parseInt(input.value) || 0;
            input.value = Math.max(0, val);
            total += Math.max(0, val);
        });
        const rowTotal = row.querySelector('.prv-row-total');
        if (rowTotal) rowTotal.value = total;
        overall += total;
    });
    updateStatus('prvStatus', overall > 0);
    updateProgress();
    if (typeof window.updateAllCalculations === 'function') {
        window.updateAllCalculations();
    }
}
function resetPRV() {
    document.querySelectorAll('#prvCard .prv-input').forEach(input => {
        input.value = 0;
    });
    calculatePRV();
}
function savePRV() {
    mockSaveButton('savePRV');
}

/*=========================================================================
    e-TWP (TEMPORARY WORK PERMIT)
=========================================================================*/
function initETWP() {
    attachETWPEvents();
    calculateETWP();
    const reset = document.getElementById('resetETWP');
    if (reset) reset.addEventListener('click', resetETWP);
    const save = document.getElementById('saveETWP');
    if (save) save.addEventListener('click', saveETWP);
}
function attachETWPEvents() {
    document.querySelectorAll('#etwpCard .etwp-input').forEach(input => {
        input.removeEventListener('input', calculateETWP);
        input.addEventListener('input', calculateETWP);
        input.addEventListener('click', function() { this.select(); });
    });
}
function calculateETWP() {
    let overall = 0;
    document.querySelectorAll('#etwpCard tbody tr').forEach(row => {
        let total = 0;
        row.querySelectorAll('.etwp-input').forEach(input => {
            let val = parseInt(input.value) || 0;
            input.value = Math.max(0, val);
            total += Math.max(0, val);
        });
        const rowTotal = row.querySelector('.etwp-row-total');
        if (rowTotal) rowTotal.value = total;
        overall += total;
    });
    updateStatus('etwpStatus', overall > 0);
    updateProgress();
    if (typeof window.updateAllCalculations === 'function') {
        window.updateAllCalculations();
    }
}
function resetETWP() {
    document.querySelectorAll('#etwpCard .etwp-input').forEach(input => {
        input.value = 0;
    });
    calculateETWP();
}
function saveETWP() {
    mockSaveButton('saveETWP');
}


/*=========================================================================
    ECOWAS AFFAIRS
=========================================================================*/
function initECOWAS() {
    attachECOWASEvents();
    calculateECOWAS();

    const reset = document.getElementById('resetECOWAS');
    if (reset) reset.addEventListener('click', resetECOWAS);
    const save = document.getElementById('saveECOWAS');
    if (save) save.addEventListener('click', saveECOWAS);
}
function attachECOWASEvents() {
    document.querySelectorAll('#ecowasBody .ecowas-input').forEach(input => {
        input.removeEventListener('input', calculateECOWAS);
        input.addEventListener('input', calculateECOWAS);
        input.addEventListener('click', function() { this.select(); });
    });
    document.querySelectorAll('#ecowasBody .removeEcowas').forEach(button => {
        button.onclick = function() {
            if (document.querySelectorAll('#ecowasBody tr').length === 1) return;
            const tbody = this.closest('tbody');
            this.closest('tr').remove();
            reindexSnCells(tbody);
            calculateECOWAS();
        };
    });
}
function calculateECOWAS() {
    let overall = 0;
    document.querySelectorAll('#ecowasBody tr').forEach(row => {
        let rowTotal = 0;
        row.querySelectorAll('.ecowas-input').forEach(input => {
            let val = parseInt(input.value) || 0;
            input.value = Math.max(0, val);
            rowTotal += Math.max(0, val);
        });
        const rowTotalInput = row.querySelector('.ecowas-row-total');
        if (rowTotalInput) rowTotalInput.value = rowTotal;
        overall += rowTotal;
    });
    updateStatus('ecowasStatus', overall > 0);
    updateProgress();
}
function addEcowasRow() {
    const tbody = document.getElementById('ecowasBody');
    if (!tbody) return;
    const index = tbody.querySelectorAll('tr').length;
    const row = document.createElement('tr');
    row.innerHTML = `
        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${index + 1}</td>
        <td>
            <select class="ni ecowas-nationality" name="ecowas[${index}][nationality]">
                <option value="">Select</option>
                <option>Ghana</option>
                <option>Niger</option>
                <option>Benin</option>
                <option>Togo</option>
                <option>Cote d'Ivoire</option>
                <option>Liberia</option>
                <option>Sierra Leone</option>
                <option>Guinea</option>
                <option>Senegal</option>
                <option>Gambia</option>
                <option>Mali</option>
                <option>Burkina Faso</option>
                <option>Guinea-Bissau</option>
                <option>Cape Verde</option>
            </select>
        </td>
        <td><input type="number" min="0" value="0" name="ecowas[${index}][male]" class="ni ecowas-number ecowas-input"></td>
        <td><input type="number" min="0" value="0" name="ecowas[${index}][female]" class="ni ecowas-number ecowas-input"></td>
        <td><input type="number" min="0" value="0" name="ecowas[${index}][principal]" class="ni ecowas-number ecowas-input"></td>
        <td><input type="number" min="0" value="0" name="ecowas[${index}][dependent]" class="ni ecowas-number ecowas-input"></td>
        <td><input type="number" min="0" value="0" name="ecowas[${index}][regularization]" class="ni ecowas-number ecowas-input"></td>
        <td><input type="number" min="0" value="0" name="ecowas[${index}][renewals]" class="ni ecowas-number ecowas-input"></td>
        <td><input type="number" min="0" value="0" name="ecowas[${index}][redesignation]" class="ni ecowas-number ecowas-input"></td>
        <td><input type="number" min="0" value="0" name="ecowas[${index}][reclassification]" class="ni ecowas-number ecowas-input"></td>
        <td><input type="number" min="0" value="0" name="ecowas[${index}][coe]" class="ni ecowas-number ecowas-input"></td>
        <td><input type="number" min="0" value="0" name="ecowas[${index}][cos]" class="ni ecowas-number ecowas-input"></td>
        <td><input type="number" readonly value="0" class="ni total-input ecowas-row-total"></td>
        <td>
            <button type="button" class="btn-icon btn-danger removeEcowas">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    reindexSnCells(tbody);
    attachECOWASEvents();
    calculateECOWAS();
}
function resetECOWAS() {
    document.querySelectorAll('#ecowasBody tr').forEach((row, idx) => {
        if (idx > 0) row.remove();
    });
    const first = document.querySelector('#ecowasBody tr');
    if (first) {
        first.querySelector('.ecowas-nationality').value = '';
        first.querySelectorAll('.ecowas-input').forEach(input => { input.value = 0; });
        first.querySelector('.ecowas-row-total').value = 0;
    }
    calculateECOWAS();
}
function saveECOWAS() {
    mockSaveButton('saveECOWAS');
}

/*=========================================================================
    AFRICAN AFFAIRS
=========================================================================*/
function initAfricanAffairs() {
    attachAfricanAffairsEvents();
    calculateAfricanAffairs();

    const reset = document.getElementById('resetAfricanAffairs');
    if (reset) reset.addEventListener('click', resetAfricanAffairs);
    const save = document.getElementById('saveAfricanAffairs');
    if (save) save.addEventListener('click', saveAfricanAffairs);
}
function attachAfricanAffairsEvents() {
    document.querySelectorAll('#africanBody .african-input').forEach(input => {
        input.removeEventListener('input', calculateAfricanAffairs);
        input.addEventListener('input', calculateAfricanAffairs);
        input.addEventListener('click', function() { this.select(); });
    });
    document.querySelectorAll('#africanBody .removeAfrican').forEach(button => {
        button.onclick = function() {
            if (document.querySelectorAll('#africanBody tr').length === 1) return;
            const tbody = this.closest('tbody');
            this.closest('tr').remove();
            reindexSnCells(tbody);
            calculateAfricanAffairs();
        };
    });
}
function calculateAfricanAffairs() {
    let overall = 0;
    document.querySelectorAll('#africanBody tr').forEach(row => {
        let rowTotal = 0;
        row.querySelectorAll('.african-input').forEach(input => {
            let val = parseInt(input.value) || 0;
            input.value = Math.max(0, val);
            rowTotal += Math.max(0, val);
        });
        const rowTotalInput = row.querySelector('.african-row-total');
        if (rowTotalInput) rowTotalInput.value = rowTotal;
        overall += rowTotal;
    });
    updateStatus('africanStatus', overall > 0);
    updateProgress();
}
function addAfricanAffairsRow() {
    const tbody = document.getElementById('africanBody');
    if (!tbody) return;
    const index = tbody.querySelectorAll('tr').length;
    const row = document.createElement('tr');
    row.innerHTML = `
        <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${index + 1}</td>
        <td>
            <select class="ni african-nationality" name="african[${index}][nationality]">
                <option value="">Select</option>
                <option>Cameroon</option>
                <option>Chad</option>
                <option>Central African Republic</option>
                <option>Congo</option>
                <option>Democratic Republic of Congo</option>
                <option>Equatorial Guinea</option>
                <option>Gabon</option>
                <option>Kenya</option>
                <option>Uganda</option>
                <option>Tanzania</option>
                <option>Rwanda</option>
                <option>Burundi</option>
                <option>Ethiopia</option>
                <option>Somalia</option>
                <option>Sudan</option>
                <option>South Sudan</option>
                <option>Egypt</option>
                <option>Libya</option>
                <option>Tunisia</option>
                <option>Algeria</option>
                <option>Morocco</option>
                <option>South Africa</option>
                <option>Angola</option>
                <option>Mozambique</option>
                <option>Zimbabwe</option>
                <option>Zambia</option>
                <option>Malawi</option>
                <option>Namibia</option>
                <option>Botswana</option>
                <option>Lesotho</option>
                <option>Eswatini</option>
            </select>
        </td>
        <td><input type="number" min="0" value="0" name="african[${index}][male]" class="ni african-number african-input"></td>
        <td><input type="number" min="0" value="0" name="african[${index}][female]" class="ni african-number african-input"></td>
        <td><input type="number" min="0" value="0" name="african[${index}][principal]" class="ni african-number african-input"></td>
        <td><input type="number" min="0" value="0" name="african[${index}][dependent]" class="ni african-number african-input"></td>
        <td><input type="number" min="0" value="0" name="african[${index}][regularization]" class="ni african-number african-input"></td>
        <td><input type="number" min="0" value="0" name="african[${index}][renewals]" class="ni african-number african-input"></td>
        <td><input type="number" min="0" value="0" name="african[${index}][redesignation]" class="ni african-number african-input"></td>
        <td><input type="number" min="0" value="0" name="african[${index}][reclassification]" class="ni african-number african-input"></td>
        <td><input type="number" min="0" value="0" name="african[${index}][coe]" class="ni african-number african-input"></td>
        <td><input type="number" min="0" value="0" name="african[${index}][cos]" class="ni african-number african-input"></td>
        <td><input type="number" readonly value="0" class="ni total-input african-row-total"></td>
        <td>
            <button type="button" class="btn-icon btn-danger removeAfrican">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    reindexSnCells(tbody);
    attachAfricanAffairsEvents();
    calculateAfricanAffairs();
}
function resetAfricanAffairs() {
    document.querySelectorAll('#africanBody tr').forEach((row, idx) => {
        if (idx > 0) row.remove();
    });
    const first = document.querySelector('#africanBody tr');
    if (first) {
        first.querySelector('.african-nationality').value = '';
        first.querySelectorAll('.african-input').forEach(input => { input.value = 0; });
        first.querySelector('.african-row-total').value = 0;
    }
    calculateAfricanAffairs();
}
function saveAfricanAffairs() {
    mockSaveButton('saveAfricanAffairs');
}


