@extends('user.directorates._layout')

{{-- This view renders its own tab bar; the Preview panel and action buttons
     come from the shared layout. --}}
@section('directorate-tabs', '1')

@section('directorate-sections')

@php
$cadreRows = [
    ['comptroller','Comptroller Cadre'],
    ['superintendent','Superintendent Cadre'],
    ['inspectorate','Inspectorate Cadre'],
    ['assistant','Assistant Cadre'],
];

$rankRows = [
    ['dcg','Deputy Comptroller General (DCG)'],
    ['acg','Assistant Comptroller General (ACG)'],
    ['cis','Comptroller of Immigration (CIS)'],
    ['dci','Deputy Comptroller of Immigration (DCI)'],
    ['aci','Assistant Comptroller of Immigration (ACI)'],
    ['csi','Chief Superintendent of Immigration (CSI)'],
    ['si','Superintendent of Immigration (SI)'],
    ['dsi','Deputy Superintendent of Immigration (DSI)'],
    ['asi1','Assistant Superintendent of Immigration 1 (ASI 1)'],
    ['asi2','Assistant Superintendent of Immigration 2 (ASI 2)'],
    ['ii','Inspector of Immigration (II)'],
    ['aii','Assistant Inspector of Immigration (AII)'],
    ['ia1','Immigration Assistant 1 (IA 1)'],
    ['ia2','Immigration Assistant 2 (IA 2)'],
    ['ia3','Immigration Assistant 3 (IA 3)'],
];

$libraryRows = [
    ['laws_extant','Laws and Extant Laws'],
    ['annual_report','Annual Report'],
    ['reports','Reports'],
    ['general_immigration_knowledge','General Immigration Knowledge'],
    ['mous','MOUs'],
];

$nominalRollRows = [
    ['change_of_name','Change of name'],
    ['change_of_next_of_kin','Change of next of kin'],
    ['mandatory_retirement','Mandatory retirement'],
    ['academic_records_update','Academic records update'],
    ['harmonization_of_records','Harmonization of records'],
    ['deceased','Deceased'],
];

$meRows = [
    ['personnel_exit','Number of personnel exit for the period'],
    ['personnel_trained','Number of personnel trained for the period'],
    ['personnel_award','Personnel award for the period'],
    ['project_implementation','Project implementation for the period'],
    ['amount_generated','Amount generated from operations/services for the period'],
    ['projects_completed','Number of projects completed for the period'],
    ['expenditure_overhead','Amount of expenditure on overhead'],
    ['expenditure_capital','Amount of expenditure on capital'],
];
@endphp

{{-- The shared layout provides the <form>; do not nest another one here. --}}
<div class="hrm-tabs-wrap">
    <div class="entry-tabs-wrap" style="margin-bottom:0;">
        <div class="entry-tabs" id="entryTabs">
            @php $tabs = [
                ['cadre','fas fa-users','1. Cadre'],
                ['rank','fas fa-star','2. Rank'],
                ['research','fas fa-flask','3. Research & Stats'],
                ['library','fas fa-book','4. Library'],
                ['nominal-roll','fas fa-list','5. Nominal Roll'],
                ['monitoring','fas fa-chart-line','6. M&E'],
                ['general-report','fas fa-file-alt','7. General Report'],
                ['preview','fas fa-eye','8. Preview'],
            ]; @endphp
            @foreach($tabs as $i => [$id,$icon,$label])
            <button type="button" class="entry-tab {{ $i === 0 ? 'active' : '' }}" data-tab="{{ $id }}">
                <span class="tab-dot"></span>
                <i class="{{ $icon }}" style="font-size:.78rem;"></i>
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>
</div>

<div class="tab-content">

    <!-- TAB 1: Service Personnel Strength by Cadre -->
    <div class="tab-panel active" id="tab-cadre">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-users"></i></div>
                    1. Service Personnel Strength
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th>CADRE</th><th style="width:130px;">MALE</th><th style="width:130px;">FEMALE</th><th style="width:130px;">TOTAL</th></tr>
                        </thead>
                        <tbody>
                            @foreach($cadreRows as [$key,$label])
                            <tr>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="prs[cadre][{{ $key }}][male]" class="ni prs-cadre-m" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('prs.cadre.'.$key.'.male') }}"></td>
                                <td><input type="number" name="prs[cadre][{{ $key }}][female]" class="ni prs-cadre-f" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('prs.cadre.'.$key.'.female') }}"></td>
                                <td><input type="number" id="prs-cadre-total-{{ $key }}" class="ni" readonly placeholder="0"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><input type="number" id="prs-cadre-grand-male" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="prs-cadre-grand-female" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="prs-cadre-grand-total" class="ni" readonly placeholder="0"></td>
                            </tr>
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

    <!-- TAB 2: Personnel Strength by Rank -->
    <div class="tab-panel" id="tab-rank">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-star"></i></div>
                    Personnel Strength by Rank
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th>RANKS</th><th style="width:130px;">MALE</th><th style="width:130px;">FEMALE</th><th style="width:130px;">TOTAL</th></tr>
                        </thead>
                        <tbody>
                            @foreach($rankRows as [$key,$label])
                            <tr>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="prs[rank][{{ $key }}][male]" class="ni prs-rank-m" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('prs.rank.'.$key.'.male') }}"></td>
                                <td><input type="number" name="prs[rank][{{ $key }}][female]" class="ni prs-rank-f" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('prs.rank.'.$key.'.female') }}"></td>
                                <td><input type="number" id="prs-rank-total-{{ $key }}" class="ni" readonly placeholder="0"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><input type="number" id="prs-rank-grand-male" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="prs-rank-grand-female" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="prs-rank-grand-total" class="ni" readonly placeholder="0"></td>
                            </tr>
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

    <!-- TAB 3: Research and Statistics -->
    <div class="tab-panel" id="tab-research">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-flask"></i></div>
                    2. Research and Statistics
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th style="width:50px;">S/N</th><th>RESEARCH TOPIC APPROVED</th><th>STATUS OF RESEARCH</th><th>SCOPE OF RESEARCH</th><th style="width:40px;"></th></tr>
                        </thead>
                        <tbody id="prsResearchBody">
                            <tr class="data-row">
                                <td>1</td>
                                <td><input type="text" name="prs[research][0][topic]" class="ni" placeholder="Research topic approved" value="{{ old('prs.research.0.topic') }}"></td>
                                <td><input type="text" name="prs[research][0][status]" class="ni" placeholder="e.g. On-going, Completed" value="{{ old('prs.research.0.status') }}"></td>
                                <td><input type="text" name="prs[research][0][scope]" class="ni" placeholder="Scope of research" value="{{ old('prs.research.0.scope') }}"></td>
                                <td><button type="button" class="btn-nis btn-ghost btn-sm hrm-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="prsAddResearchRow"><i class="fas fa-plus"></i> Add Row</button>
            </div>
        </div>

        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-user-tie"></i></div>
                    Research by Stakeholders
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th style="width:50px;">S/N</th><th>RESEARCH TOPIC</th><th>NAME OF THE RESEARCHER</th><th>NAME OF THE INSTITUTION</th><th>RESEARCH OBJECTIVE</th><th>REMARK</th><th style="width:40px;"></th></tr>
                        </thead>
                        <tbody id="prsStakeholdersBody">
                            <tr class="data-row">
                                <td>1</td>
                                <td><input type="text" name="prs[stakeholders][0][topic]" class="ni" placeholder="Research topic" value="{{ old('prs.stakeholders.0.topic') }}"></td>
                                <td><input type="text" name="prs[stakeholders][0][researcher]" class="ni" placeholder="Researcher" value="{{ old('prs.stakeholders.0.researcher') }}"></td>
                                <td><input type="text" name="prs[stakeholders][0][institution]" class="ni" placeholder="Institution" value="{{ old('prs.stakeholders.0.institution') }}"></td>
                                <td><input type="text" name="prs[stakeholders][0][objective]" class="ni" placeholder="Objective" value="{{ old('prs.stakeholders.0.objective') }}"></td>
                                <td><input type="text" name="prs[stakeholders][0][remark]" class="ni" placeholder="Remark" value="{{ old('prs.stakeholders.0.remark') }}"></td>
                                <td><button type="button" class="btn-nis btn-ghost btn-sm hrm-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="prsAddStakeholderRow"><i class="fas fa-plus"></i> Add Row</button>
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

    <!-- TAB 4: Library -->
    <div class="tab-panel" id="tab-library">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-book"></i></div>
                    3. Library
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th style="width:50px;">S/N</th><th>CATEGORY</th><th style="width:180px;">NUMBERS OF BOOKS</th></tr>
                        </thead>
                        <tbody>
                            @foreach($libraryRows as $i => [$key,$label])
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="prs[library][{{ $key }}]" class="ni prs-library-input" min="0" placeholder="0" value="{{ old('prs.library.'.$key) }}"></td>
                            </tr>
                            @endforeach
                            <tr>
                                <td>{{ count($libraryRows) + 1 }}</td>
                                <td><input type="text" name="prs[library_other][category]" class="ni" placeholder="Other category (specify)" value="{{ old('prs.library_other.category') }}"></td>
                                <td><input type="number" name="prs[library_other][books]" class="ni prs-library-input" min="0" placeholder="0" value="{{ old('prs.library_other.books') }}"></td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td><input type="number" id="prs-library-grand-total" class="ni" readonly placeholder="0"></td>
                            </tr>
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

    <!-- TAB 5: Update on Nominal Roll -->
    <div class="tab-panel" id="tab-nominal-roll">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-list"></i></div>
                    4. Update on Nominal Roll
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th style="width:50px;">S/NO.</th><th>ACTIVITIES</th><th style="width:180px;">NO. OF CASES TREATED</th></tr>
                        </thead>
                        <tbody>
                            @foreach($nominalRollRows as $i => [$key,$label])
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="prs[nominal_roll][{{ $key }}]" class="ni prs-nominal-input" min="0" placeholder="0" value="{{ old('prs.nominal_roll.'.$key) }}"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td><input type="number" id="prs-nominal-grand-total" class="ni" readonly placeholder="0"></td>
                            </tr>
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

    <!-- TAB 6: Monitoring and Evaluation -->
    <div class="tab-panel" id="tab-monitoring">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-chart-line"></i></div>
                    5. Monitoring and Evaluation: Personnel/Financial Report
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th style="width:50px;">S/N</th><th>ACTIVITIES</th><th style="width:160px;">NUMBER</th><th style="width:220px;">REMARK</th></tr>
                        </thead>
                        <tbody>
                            @foreach($meRows as $i => [$key,$label])
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $label }}</td>
                                <td><input type="text" name="prs[monitoring][{{ $key }}][number]" class="ni prs-monitoring-number" placeholder="0" value="{{ old('prs.monitoring.'.$key.'.number') }}"></td>
                                <td><input type="text" name="prs[monitoring][{{ $key }}][remark]" class="ni" placeholder="Remark" value="{{ old('prs.monitoring.'.$key.'.remark') }}"></td>
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

    <!-- TAB 7: GENERAL REPORT -->
    <div class="tab-panel" id="tab-general-report">
        <div class="redas-card" style="margin-bottom:16px;">
            <div class="card-head"><div class="card-head-title">GENERAL REPORT</div></div>
            <div class="card-body">
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">i. Other Reports</label>
                    <textarea name="general_report[other_reports]" class="ni" rows="3">{{ old('general_report.other_reports') }}</textarea>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">ii. Challenges</label>
                    <textarea name="general_report[challenges]" class="ni" rows="3">{{ old('general_report.challenges') }}</textarea>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">iii. Recommendations / Way Forward</label>
                    <textarea name="general_report[recommendations]" class="ni" rows="3">{{ old('general_report.recommendations') }}</textarea>
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">iv. Conclusion</label>
                    <textarea name="general_report[conclusion]" class="ni" rows="3">{{ old('general_report.conclusion') }}</textarea>
                </div>
            </div>
        </div>

        <div class="redas-card" style="margin-bottom:16px;">
            <div class="card-head" style="display:flex; justify-content:space-between; align-items:center;">
                <div class="card-head-title">SUPPORTING DOCUMENTS (Optional)</div>
                <button type="button" class="btn-nis btn-ghost btn-sm" id="prsAddDocumentRow">
                    <i class="fas fa-plus"></i> Add Document
                </button>
            </div>
            <div class="card-body">
                <p style="font-size:0.85rem;color:var(--gray-500);margin-bottom:12px;">You can upload supporting documents or photos (PDF, Excel, PNG, JPG, JPEG).</p>
                <div id="documents-body">
                    <div class="auth-form-group" style="margin-bottom:12px;">
                        <input type="file" name="supporting_documents[]" class="ni" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg">
                    </div>
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
    const form = document.querySelector('main form') || document.querySelector('form[action*="directorates"]');

    /* Tab navigation (previous/next), draft save/restore and submit are wired
       globally in user.directorates._layout for all directorate forms. */

    /* Helpers */
    function val(el) { return parseInt(el?.value || 0) || 0; }
    function set(id, v) { const el = document.getElementById(id); if (el) el.value = v; }

    /* Section 1: Cadre */
    function recomputeCadre() {
        let mT = 0, fT = 0;
        document.querySelectorAll('.prs-cadre-m').forEach(m => {
            const row = m.dataset.row;
            const mV = val(m), fV = val(document.querySelector(`.prs-cadre-f[data-row="${row}"]`));
            set(`prs-cadre-total-${row}`, mV + fV);
            mT += mV; fT += fV;
        });
        set('prs-cadre-grand-male', mT);
        set('prs-cadre-grand-female', fT);
        set('prs-cadre-grand-total', mT + fT);
    }

    /* Section 2: Rank */
    function recomputeRank() {
        let mT = 0, fT = 0;
        document.querySelectorAll('.prs-rank-m').forEach(m => {
            const row = m.dataset.row;
            const mV = val(m), fV = val(document.querySelector(`.prs-rank-f[data-row="${row}"]`));
            set(`prs-rank-total-${row}`, mV + fV);
            mT += mV; fT += fV;
        });
        set('prs-rank-grand-male', mT);
        set('prs-rank-grand-female', fT);
        set('prs-rank-grand-total', mT + fT);
    }

    /* Sections 4-5: single numeric column totals */
    function recomputeSingleCol(inputClass, grandId) {
        let total = 0;
        document.querySelectorAll(inputClass).forEach(el => total += val(el));
        set(grandId, total);
    }

    /* Section 3: dynamic research rows */
    function addResearchRow() {
        const tbody = document.getElementById('prsResearchBody');
        const idx = tbody.querySelectorAll('.data-row').length;
        const tr = document.createElement('tr'); tr.className = 'data-row';
        tr.innerHTML = `
            <td></td>
            <td><input type="text" name="prs[research][${idx}][topic]" class="ni" placeholder="Research topic approved"></td>
            <td><input type="text" name="prs[research][${idx}][status]" class="ni" placeholder="e.g. On-going, Completed"></td>
            <td><input type="text" name="prs[research][${idx}][scope]" class="ni" placeholder="Scope of research"></td>
            <td><button type="button" class="btn-nis btn-ghost btn-sm hrm-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>
        `;
        tbody.appendChild(tr);
        renumberAndRecompute(tbody);
    }

    function addStakeholderRow() {
        const tbody = document.getElementById('prsStakeholdersBody');
        const idx = tbody.querySelectorAll('.data-row').length;
        const tr = document.createElement('tr'); tr.className = 'data-row';
        tr.innerHTML = `
            <td></td>
            <td><input type="text" name="prs[stakeholders][${idx}][topic]" class="ni" placeholder="Research topic"></td>
            <td><input type="text" name="prs[stakeholders][${idx}][researcher]" class="ni" placeholder="Researcher"></td>
            <td><input type="text" name="prs[stakeholders][${idx}][institution]" class="ni" placeholder="Institution"></td>
            <td><input type="text" name="prs[stakeholders][${idx}][objective]" class="ni" placeholder="Objective"></td>
            <td><input type="text" name="prs[stakeholders][${idx}][remark]" class="ni" placeholder="Remark"></td>
            <td><button type="button" class="btn-nis btn-ghost btn-sm hrm-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>
        `;
        tbody.appendChild(tr);
        renumberAndRecompute(tbody);
    }

    /* Row numbering and recompute after add/remove */
    function renumberAndRecompute(tbody) {
        tbody.querySelectorAll('.data-row').forEach((row, i) => {
            row.querySelector('td:first-child').textContent = i + 1;
            row.querySelectorAll('input, select').forEach(el => {
                el.name = el.name.replace(/\[\d+\]/, `[${i}]`);
            });
        });
        recomputeAll();
    }

    /* Add/remove row wiring */
    document.getElementById('prsAddResearchRow')?.addEventListener('click', addResearchRow);
    document.getElementById('prsAddStakeholderRow')?.addEventListener('click', addStakeholderRow);

    document.addEventListener('click', e => {
        const btn = e.target.closest('.hrm-remove-row');
        if (!btn) return;
        const row = btn.closest('tr');
        const tbody = row.closest('tbody');
        if (tbody.querySelectorAll('.data-row').length <= 1) { alert('At least one row is required.'); return; }
        row.remove();
        renumberAndRecompute(tbody);
    });

    /* Section 7: Supporting documents */
    function addDocumentRow() {
        const container = document.getElementById('documents-body');
        const div = document.createElement('div');
        div.className = 'auth-form-group';
        div.style.marginBottom = '12px';
        div.innerHTML = '<input type="file" name="supporting_documents[]" class="ni" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg">';
        container.appendChild(div);
    }
    document.getElementById('prsAddDocumentRow')?.addEventListener('click', addDocumentRow);

    /* Preview builder helpers */
    function previewSectionTitle(num, title) {
        return `<div class="hrm-preview-section-title">${num}. ${title}</div>`;
    }
    function previewTable(rows, headers = ['Item', 'Value']) {
        let thead = headers.map(h => `<th style="padding:6px 8px;border:1px solid var(--gray-200);background:#f8fafc;">${h}</th>`).join('');
        let tbody = rows.map(r => `<tr>${r.map(c => `<td style="padding:6px 8px;border:1px solid var(--gray-200);">${c}</td>`).join('')}</tr>`).join('');
        return `<table class="hrm-preview-table" style="margin-bottom:12px;"><thead><tr>${thead}</tr></thead><tbody>${tbody}</tbody></table>`;
    }
    function getVal(name) { return document.querySelector(`[name="${name}"]`)?.value || '0'; }
    function getValById(id) { return document.getElementById(id)?.value || '0'; }
    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, character => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        }[character]));
    }

    function buildPreview() {
        const container = document.getElementById('hrmPreviewBody');
        if (!container) return;
        let html = '';

        /* 1. Cadre */
        let cadreRows = [];
        document.querySelectorAll('.prs-cadre-m').forEach(m => {
            const row = m.dataset.row;
            const label = m.closest('tr').querySelector('td').textContent;
            cadreRows.push([
                esc(label),
                getValById(`prs-cadre-total-${row}`),
                `M: ${m.value || '0'}, F: ${document.querySelector(`.prs-cadre-f[data-row="${row}"]`)?.value || '0'}`
            ]);
        });
        cadreRows.push(['<strong>Grand Total</strong>', getValById('prs-cadre-grand-total'), `M: ${getValById('prs-cadre-grand-male')}, F: ${getValById('prs-cadre-grand-female')}`]);
        html += previewSectionTitle(1, 'Service Personnel Strength');
        html += previewTable(cadreRows, ['Cadre', 'Total', 'Breakdown']);

        /* 2. Rank */
        let rankRows = [];
        document.querySelectorAll('.prs-rank-m').forEach(m => {
            const row = m.dataset.row;
            const label = m.closest('tr').querySelector('td').textContent;
            rankRows.push([
                esc(label),
                getValById(`prs-rank-total-${row}`),
                `M: ${m.value || '0'}, F: ${document.querySelector(`.prs-rank-f[data-row="${row}"]`)?.value || '0'}`
            ]);
        });
        rankRows.push(['<strong>Grand Total</strong>', getValById('prs-rank-grand-total'), `M: ${getValById('prs-rank-grand-male')}, F: ${getValById('prs-rank-grand-female')}`]);
        html += previewSectionTitle(2, 'Personnel Strength by Rank');
        html += previewTable(rankRows, ['Rank', 'Total', 'Breakdown']);

        /* 3. Research and Statistics */
        let researchRows = [];
        document.querySelectorAll('#prsResearchBody .data-row').forEach(row => {
            const inputs = row.querySelectorAll('input');
            const vals = Array.from(inputs).map(i => esc(i.value) || '—');
            if (vals.some(v => v !== '—')) researchRows.push(vals);
        });
        html += previewSectionTitle(3, 'Research and Statistics');
        html += previewTable(researchRows, ['Research Topic Approved', 'Status of Research', 'Scope of Research']);

        let stakeRows = [];
        document.querySelectorAll('#prsStakeholdersBody .data-row').forEach(row => {
            const inputs = row.querySelectorAll('input');
            const vals = Array.from(inputs).map(i => esc(i.value) || '—');
            if (vals.some(v => v !== '—')) stakeRows.push(vals);
        });
        html += previewSectionTitle('', 'Research by Stakeholders');
        html += previewTable(stakeRows, ['Research Topic', 'Researcher', 'Institution', 'Objective', 'Remark']);

        /* 4. Library */
        let libraryRows = [];
        document.querySelectorAll('#tab-library .prs-library-input').forEach(el => {
            const cell = el.closest('tr').querySelector('td:nth-child(2)');
            const labelInput = cell.querySelector('input');
            const label = labelInput ? (labelInput.value || 'Other') : cell.textContent;
            libraryRows.push([esc(label), el.value || '0']);
        });
        libraryRows.push(['<strong>Total</strong>', getValById('prs-library-grand-total')]);
        html += previewSectionTitle(4, 'Library');
        html += previewTable(libraryRows, ['Category', 'Numbers of Books']);

        /* 5. Update on Nominal Roll */
        let nominalRows = [];
        document.querySelectorAll('.prs-nominal-input').forEach(el => {
            nominalRows.push([esc(el.closest('tr').querySelector('td:nth-child(2)').textContent), el.value || '0']);
        });
        nominalRows.push(['<strong>Total</strong>', getValById('prs-nominal-grand-total')]);
        html += previewSectionTitle(5, 'Update on Nominal Roll');
        html += previewTable(nominalRows, ['Activity', 'No. of Cases Treated']);

        /* 6. Monitoring and Evaluation */
        let meRows = [];
        document.querySelectorAll('#tab-monitoring .nis-table tbody tr').forEach(tr => {
            const cells = tr.querySelectorAll('td');
            if (cells.length < 4) return;
            const label = cells[1].textContent.trim();
            const number = cells[2].querySelector('input')?.value || '—';
            const remark = cells[3].querySelector('input')?.value || '—';
            meRows.push([esc(label), esc(number), esc(remark)]);
        });
        html += previewSectionTitle(6, 'Monitoring and Evaluation: Personnel/Financial Report');
        html += previewTable(meRows, ['Activity', 'Number', 'Remark']);

        /* 7. General Report and Supporting Documents */
        html += previewSectionTitle(7, 'General Report and Supporting Documents');
        const generalReportFields = [
            ['Other Reports', 'general_report[other_reports]', 'No other reports.'],
            ['Challenges', 'general_report[challenges]', 'No challenges reported.'],
            ['Recommendations', 'general_report[recommendations]', 'No recommendations provided.'],
            ['Conclusion', 'general_report[conclusion]', 'No conclusion provided.']
        ];
        generalReportFields.forEach(([label, name, emptyText]) => {
            html += `<div style="margin-top:12px;"><strong>${label}:</strong></div>`;
            const value = document.querySelector(`[name="${name}"]`)?.value.trim() || '';
            if (!value) {
                html += `<p style="font-size:.82rem;color:var(--gray-600);margin-top:4px;">${emptyText}</p>`;
            } else {
                html += `<p style="font-size:.82rem;color:var(--gray-800);margin-top:4px;white-space:pre-wrap;">${esc(value)}</p>`;
            }
        });
        html += `<div style="margin-top:12px;"><strong>Supporting Documents:</strong></div>`;
        const docInputs = Array.from(document.querySelectorAll('#documents-body input[type="file"]'))
            .filter(input => input.files && input.files.length > 0);
        if (docInputs.length === 0) {
            html += `<p style="font-size:.82rem;color:var(--gray-600);margin-top:4px;">No supporting documents uploaded.</p>`;
        } else {
            html += `<ul style="margin-top:4px;">`;
            docInputs.forEach((input, idx) => {
                html += `<li style="font-size:.82rem;color:var(--gray-800);">Document ${idx + 1}: ${esc(input.files[0].name)}</li>`;
            });
            html += `</ul>`;
        }

        container.innerHTML = html;
    }

    /* Master recompute */
    function recomputeAll() {
        recomputeCadre();
        recomputeRank();
        recomputeSingleCol('.prs-library-input', 'prs-library-grand-total');
        recomputeSingleCol('.prs-nominal-input', 'prs-nominal-grand-total');
        buildPreview();
    }

    /* Listen for input */
    if (form) form.addEventListener('input', recomputeAll);

    /* Let the layout's preview tab use this page-specific renderer. */
    window.buildDirectoratePreview = buildPreview;

    /* Init */
    recomputeAll();
})();
</script>

@endsection
