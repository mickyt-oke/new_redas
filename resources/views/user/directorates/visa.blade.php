@extends('user.directorates._layout')

{{-- This view renders its own tab bar; the Preview panel and action buttons
     come from the shared layout. --}}
@section('directorate-tabs', '1')

@section('directorate-sections')

@php
$cadreRows = [
    ['comptroller', 'Comptroller Cadre'],
    ['superintendent', 'Superintendent Cadre'],
    ['inspectorate', 'Inspectorate Cadre'],
    ['assistant', 'Assistant Cadre'],
];

$permitCols = [
    ['male', 'Male'],
    ['female', 'Female'],
    ['principal', 'Principal'],
    ['dependent', 'Dependent'],
    ['regularization', 'Regularization'],
    ['renewals', 'Renewals'],
    ['redesignations', 'Redesignations'],
    ['reclassification', 'Reclassification'],
    ['coe', 'COE'],
    ['cos', 'COS'],
];

$trpRows = [
    ['diplomat', 'Diplomat (Accredited)'],
    ['go', 'GO'],
    ['ingo', 'INGO'],
    ['intern', 'Intern'],
    ['cleric', 'Cleric'],
    ['student', 'Student'],
    ['expatriates_quota', 'Expatriates (on Quota)'],
    ['expatriates_ftz', 'Expatriates (FTZ)'],
];

$prpCols = [
    ['male', 'Male'],
    ['female', 'Female'],
    ['principal', 'Principal'],
    ['dependent', 'Dependent'],
    ['regularization', 'Regularization'],
    ['renewals', 'Renewals'],
];

$prpRows = [
    ['spouses', 'Spouses of Nigeria'],
    ['investors', 'Investors'],
    ['retired_nigeria', 'Retired in Nigeria'],
    ['retiree_abroad', 'Retiree from Abroad'],
    ['highly_skilled', 'Highly Skilled Migrants'],
    ['renounced', 'Nigerian by Birth who Renounced Nigerian Citizenship'],
];

$emigrantNumCols = [
    ['regular', 'Regular'],
    ['irregular', 'Irregular'],
    ['male', 'Male'],
    ['female', 'Female'],
    ['employed', 'Employed'],
    ['self_employed', 'Self Employed'],
    ['student', 'Student'],
    ['spouse', 'Spouse'],
    ['dependant', 'Dependant'],
];

$quotaCols = [
    ['quota_positions', 'Quota Positions'],
    ['utilized_quota', 'Utilized Quota'],
    ['expatriate_deleted', 'Expatriate Deleted'],
];

$counterCols = [
    ['extensions', 'No. Of Extension'],
    ['spouse_male', 'Male'],
    ['spouse_female', 'Female'],
];

$visaGroups = [
    'evisa' => ['label' => 'Short Visit Visa — e-Visa', 'codes' => ['F3B','F4A','F5A','F6A','F7E','F7F','F7G','F7H','F7I','F7K','F9A','F9B']],
    'svv'   => ['label' => 'Regular Visa — SVV', 'codes' => ['F2A','F3A','F4B','F4C','F6B','F7A','F7B','F7C','F7D','F7J','F7L','F7M']],
    'trv'   => ['label' => 'TRV', 'codes' => ['R1A','R4A','R5A','R7A','R8A','R9A']],
    'prv'   => ['label' => 'PRV', 'codes' => ['N1A','N2A','N3','N4A','N5A','N5B']],
    'etwp'  => ['label' => 'e-TWP', 'codes' => ['R10','R11']],
];

$visaStatCols = [
    ['applications', 'Applications'],
    ['approved', 'Approved'],
    ['rejected', 'Rejected'],
    ['pending', 'Pending'],
];
@endphp

{{-- The shared layout provides the <form>; do not nest another one here. --}}
<div class="hrm-tabs-wrap">
    <div class="entry-tabs-wrap" style="margin-bottom:0;">
        <div class="entry-tabs" id="entryTabs">
            @php $tabs = [
                ['cadre','fas fa-users','1. Cadre'],
                ['emigrant','fas fa-globe','2. e-Migrant'],
                ['quota','fas fa-briefcase','3. Quota Admin'],
                ['trp','fas fa-id-card','4. Temp Residence'],
                ['prp','fas fa-id-card-alt','5. Perm Residence'],
                ['ftz','fas fa-industry','6. FTZ'],
                ['cerpac','fas fa-credit-card','7. CERPAC'],
                ['visas','fas fa-stamp','8. Visa'],
                ['counter','fas fa-desktop','9. Visa Counter'],
                ['ecowas','fas fa-flag','10. ECOWAS/African'],
                ['general-report','fas fa-file-alt','11. General Report'],
                ['preview','fas fa-eye','12. Preview'],
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

    <!-- TAB 1: Personnel Strength by Cadre (Reporting Template) -->
    <div class="tab-panel active" id="tab-cadre">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-users"></i></div>
                    1. Personnel Strength by Cadre
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th>CADRES</th><th style="width:130px;">MALE</th><th style="width:130px;">FEMALE</th><th style="width:130px;">TOTAL</th></tr>
                        </thead>
                        <tbody>
                            @foreach($cadreRows as [$key,$label])
                            <tr>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="visa[cadre][{{ $key }}][male]" class="ni visa-cadre-m" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('visa.cadre.'.$key.'.male') }}"></td>
                                <td><input type="number" name="visa[cadre][{{ $key }}][female]" class="ni visa-cadre-f" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('visa.cadre.'.$key.'.female') }}"></td>
                                <td><input type="number" id="visa-cadre-total-{{ $key }}" class="ni" readonly placeholder="0"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><input type="number" id="visa-cadre-grand-male" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="visa-cadre-grand-female" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="visa-cadre-grand-total" class="ni" readonly placeholder="0"></td>
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

    <!-- TAB 2: e-Migrant -->
    <div class="tab-panel" id="tab-emigrant">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-globe"></i></div>
                    2. e-Migrant
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">S/N</th>
                                <th>Center</th>
                                <th>Nationality</th>
                                @foreach($emigrantNumCols as [$col,$label])
                                <th style="width:90px;">{{ $label }}</th>
                                @if($col === 'female')<th style="width:90px;">Total</th>@endif
                                @endforeach
                                <th>Region</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="visaEmigrantBody">
                            <tr class="data-row">
                                <td class="visa-sn">1</td>
                                <td><input type="text" name="visa[emigrant][0][center]" class="ni" placeholder="Center" value="{{ old('visa.emigrant.0.center') }}"></td>
                                <td><input type="text" name="visa[emigrant][0][nationality]" class="ni" placeholder="Nationality" value="{{ old('visa.emigrant.0.nationality') }}"></td>
                                @foreach($emigrantNumCols as [$col,$label])
                                <td><input type="number" name="visa[emigrant][0][{{ $col }}]" class="ni visa-emigrant-{{ $col }}" min="0" placeholder="0" value="{{ old('visa.emigrant.0.'.$col) }}"></td>
                                @if($col === 'female')<td><input type="number" id="visa-emigrant-row-total-0" class="ni" readonly placeholder="0"></td>@endif
                                @endforeach
                                <td><input type="text" name="visa[emigrant][0][region]" class="ni" placeholder="Region" value="{{ old('visa.emigrant.0.region') }}"></td>
                                <td><button type="button" class="btn-nis btn-ghost btn-sm visa-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="3"><strong>TOTAL</strong></td>
                                @foreach($emigrantNumCols as [$col,$label])
                                <td><input type="number" id="visa-emigrant-grand-{{ $col }}" class="ni" readonly placeholder="0"></td>
                                @if($col === 'female')<td><input type="number" id="visa-emigrant-grand-total" class="ni" readonly placeholder="0"></td>@endif
                                @endforeach
                                <td colspan="2"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="visaAddEmigrantRow"><i class="fas fa-plus"></i> Add Row</button>
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

    <!-- TAB 3: Quota Administration -->
    <div class="tab-panel" id="tab-quota">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-briefcase"></i></div>
                    3. Quota Administration
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">S/N</th>
                                <th>No. Of Company/Enterprise</th>
                                @foreach($quotaCols as [$col,$label])<th style="width:140px;">{{ $label }}</th>@endforeach
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="visaQuotaBody">
                            <tr class="data-row">
                                <td class="visa-sn">1</td>
                                <td><input type="text" name="visa[quota][0][company]" class="ni" placeholder="Company/Enterprise" value="{{ old('visa.quota.0.company') }}"></td>
                                @foreach($quotaCols as [$col,$label])
                                <td><input type="number" name="visa[quota][0][{{ $col }}]" class="ni visa-quota-{{ $col }}" min="0" placeholder="0" value="{{ old('visa.quota.0.'.$col) }}"></td>
                                @endforeach
                                <td><button type="button" class="btn-nis btn-ghost btn-sm visa-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                @foreach($quotaCols as [$col,$label])
                                <td><input type="number" id="visa-quota-grand-{{ $col }}" class="ni" readonly placeholder="0"></td>
                                @endforeach
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="visaAddQuotaRow"><i class="fas fa-plus"></i> Add Row</button>
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

    <!-- TAB 4: Residence Permit — Temporary Residence Permit (TRP) -->
    <div class="tab-panel" id="tab-trp">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-id-card"></i></div>
                    4. Residence Permit — Temporary Residence Permit
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th>Temporary Residence Permit</th>
                                @foreach($permitCols as [$col,$label])<th style="width:80px;">{{ $label }}</th>@endforeach
                                <th style="width:90px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trpRows as [$rowKey,$rowLabel])
                            <tr>
                                <td>{{ $rowLabel }}</td>
                                @foreach($permitCols as [$col,$label])
                                <td><input type="number" name="visa[trp][{{ $rowKey }}][{{ $col }}]" class="ni visa-trp-cell" data-row="{{ $rowKey }}" data-col="{{ $col }}" min="0" placeholder="0" value="{{ old('visa.trp.'.$rowKey.'.'.$col) }}"></td>
                                @endforeach
                                <td><input type="number" id="visa-trp-total-{{ $rowKey }}" class="ni" readonly placeholder="0"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                @foreach($permitCols as [$col,$label])
                                <td><input type="number" id="visa-trp-col-total-{{ $col }}" class="ni" readonly placeholder="0"></td>
                                @endforeach
                                <td><input type="number" id="visa-trp-grand-total" class="ni" readonly placeholder="0"></td>
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

    <!-- TAB 5: Residence Permit — Permanent Residence Permit (PRP) -->
    <div class="tab-panel" id="tab-prp">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-id-card-alt"></i></div>
                    5. Residence Permit — Permanent Residence Permit
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th>Permanent Residence Permit</th>
                                @foreach($prpCols as [$col,$label])<th style="width:90px;">{{ $label }}</th>@endforeach
                                <th style="width:90px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prpRows as [$rowKey,$rowLabel])
                            <tr>
                                <td>{{ $rowLabel }}</td>
                                @foreach($prpCols as [$col,$label])
                                <td><input type="number" name="visa[prp][{{ $rowKey }}][{{ $col }}]" class="ni visa-prp-cell" data-row="{{ $rowKey }}" data-col="{{ $col }}" min="0" placeholder="0" value="{{ old('visa.prp.'.$rowKey.'.'.$col) }}"></td>
                                @endforeach
                                <td><input type="number" id="visa-prp-total-{{ $rowKey }}" class="ni" readonly placeholder="0"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                @foreach($prpCols as [$col,$label])
                                <td><input type="number" id="visa-prp-col-total-{{ $col }}" class="ni" readonly placeholder="0"></td>
                                @endforeach
                                <td><input type="number" id="visa-prp-grand-total" class="ni" readonly placeholder="0"></td>
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

    <!-- TAB 6: Free Trade Zone (FTZ) -->
    <div class="tab-panel" id="tab-ftz">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-industry"></i></div>
                    6. Free Trade Zone (FTZ)
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th>No. Of Free Zones</th>
                                <th>No. Of Enterprises</th>
                                <th>No. Of Expatriates</th>
                                <th>Regularization</th>
                                <th>Renewal</th>
                                <th>Redesignation</th>
                                <th>COE</th>
                                <th>COS</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="number" name="visa[ftz][free_zones]" class="ni visa-ftz-free_zones" min="0" placeholder="0" value="{{ old('visa.ftz.free_zones') }}"></td>
                                <td><input type="number" name="visa[ftz][enterprises]" class="ni visa-ftz-enterprises" min="0" placeholder="0" value="{{ old('visa.ftz.enterprises') }}"></td>
                                <td><input type="number" name="visa[ftz][expatriates]" class="ni visa-ftz-expatriates" min="0" placeholder="0" value="{{ old('visa.ftz.expatriates') }}"></td>
                                <td><input type="number" name="visa[ftz][regularization]" class="ni visa-ftz-regularization" min="0" placeholder="0" value="{{ old('visa.ftz.regularization') }}"></td>
                                <td><input type="number" name="visa[ftz][renewal]" class="ni visa-ftz-renewal" min="0" placeholder="0" value="{{ old('visa.ftz.renewal') }}"></td>
                                <td><input type="number" name="visa[ftz][redesignation]" class="ni visa-ftz-redesignation" min="0" placeholder="0" value="{{ old('visa.ftz.redesignation') }}"></td>
                                <td><input type="number" name="visa[ftz][coe]" class="ni visa-ftz-coe" min="0" placeholder="0" value="{{ old('visa.ftz.coe') }}"></td>
                                <td><input type="number" name="visa[ftz][cos]" class="ni visa-ftz-cos" min="0" placeholder="0" value="{{ old('visa.ftz.cos') }}"></td>
                                <td><input type="number" id="visa-ftz-total" class="ni" readonly placeholder="0"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p style="font-size:.78rem;color:var(--gray-500);margin-top:8px;">Total = Regularization + Renewal + Redesignation + COE + COS.</p>
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

    <!-- TAB 7: CERPAC Production -->
    <div class="tab-panel" id="tab-cerpac">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-credit-card"></i></div>
                    7. CERPAC Production
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th>Card Supplied</th>
                                <th>Card Produced</th>
                                <th>Card Damaged</th>
                                <th>Card Issued</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="number" name="visa[cerpac][card_supplied]" class="ni visa-cerpac-card_supplied" min="0" placeholder="0" value="{{ old('visa.cerpac.card_supplied') }}"></td>
                                <td><input type="number" name="visa[cerpac][card_produced]" class="ni visa-cerpac-card_produced" min="0" placeholder="0" value="{{ old('visa.cerpac.card_produced') }}"></td>
                                <td><input type="number" name="visa[cerpac][card_damaged]" class="ni visa-cerpac-card_damaged" min="0" placeholder="0" value="{{ old('visa.cerpac.card_damaged') }}"></td>
                                <td><input type="number" name="visa[cerpac][card_issued]" class="ni visa-cerpac-card_issued" min="0" placeholder="0" value="{{ old('visa.cerpac.card_issued') }}"></td>
                                <td><input type="number" id="visa-cerpac-total" class="ni" readonly placeholder="0"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p style="font-size:.78rem;color:var(--gray-500);margin-top:8px;">Total = Card Produced − Card Damaged.</p>
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

    <!-- TAB 8: Visa (e-Visa / SVV / TRV / PRV / e-TWP) -->
    <div class="tab-panel" id="tab-visas">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-stamp"></i></div>
                    8. Visa
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th>Visa Category</th>
                                <th style="width:70px;">Code</th>
                                @foreach($visaStatCols as [$col,$label])<th style="width:110px;">{{ $label }}</th>@endforeach
                                <th style="width:110px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visaGroups as $groupKey => $group)
                            <tr class="total-row" style="background:#f8fafc;">
                                <td colspan="7"><strong>{{ $group['label'] }}</strong></td>
                            </tr>
                            @foreach($group['codes'] as $code)
                            @php $codeKey = strtolower($code); @endphp
                            <tr>
                                <td></td>
                                <td style="text-align:center;font-weight:600;">{{ $code }}</td>
                                @foreach($visaStatCols as [$col,$label])
                                <td><input type="number" name="visa[visas][{{ $groupKey }}][{{ $codeKey }}][{{ $col }}]" class="ni visa-visas-cell" data-group="{{ $groupKey }}" data-code="{{ $codeKey }}" data-col="{{ $col }}" min="0" placeholder="0" value="{{ old('visa.visas.'.$groupKey.'.'.$codeKey.'.'.$col) }}"></td>
                                @endforeach
                                <td><input type="number" id="visa-visas-total-{{ $groupKey }}-{{ $codeKey }}" class="ni" readonly placeholder="0"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row" style="background:#f1f5f9;">
                                <td colspan="2"><strong>{{ $group['label'] }} — Subtotal</strong></td>
                                @foreach($visaStatCols as [$col,$label])
                                <td><input type="number" id="visa-visas-subtotal-{{ $groupKey }}-{{ $col }}" class="ni" readonly placeholder="0"></td>
                                @endforeach
                                <td><input type="number" id="visa-visas-subtotal-{{ $groupKey }}-total" class="ni" readonly placeholder="0"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td colspan="2"><strong>GRAND TOTAL</strong></td>
                                @foreach($visaStatCols as [$col,$label])
                                <td><input type="number" id="visa-visas-grand-{{ $col }}" class="ni" readonly placeholder="0"></td>
                                @endforeach
                                <td><input type="number" id="visa-visas-grand-total" class="ni" readonly placeholder="0"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p style="font-size:.78rem;color:var(--gray-500);margin-top:8px;">Row Total = Approved + Rejected + Pending.</p>
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

    <!-- TAB 9: Visa Counter -->
    <div class="tab-panel" id="tab-counter">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-desktop"></i></div>
                    9. Visa Counter
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">S/N</th>
                                <th>Visa Type</th>
                                <th style="width:140px;">No. Of Extension</th>
                                <th colspan="2" style="text-align:center;">Spouse of Nigerian Endorsement</th>
                                <th style="width:40px;"></th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th style="width:130px;">Male</th>
                                <th style="width:130px;">Female</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="visaCounterBody">
                            <tr class="data-row">
                                <td class="visa-sn">1</td>
                                <td><input type="text" name="visa[counter][0][visa_type]" class="ni" placeholder="Visa type" value="{{ old('visa.counter.0.visa_type') }}"></td>
                                @foreach($counterCols as [$col,$label])
                                <td><input type="number" name="visa[counter][0][{{ $col }}]" class="ni visa-counter-{{ $col }}" min="0" placeholder="0" value="{{ old('visa.counter.0.'.$col) }}"></td>
                                @endforeach
                                <td><button type="button" class="btn-nis btn-ghost btn-sm visa-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                @foreach($counterCols as [$col,$label])
                                <td><input type="number" id="visa-counter-grand-{{ $col }}" class="ni" readonly placeholder="0"></td>
                                @endforeach
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="visaAddCounterRow"><i class="fas fa-plus"></i> Add Row</button>
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

    <!-- TAB 10: ECOWAS & African Affairs -->
    <div class="tab-panel" id="tab-ecowas">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-flag"></i></div>
                    10. ECOWAS &amp; African Affairs — ECOWAS
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">S/N</th>
                                <th>Nationality</th>
                                @foreach($permitCols as [$col,$label])<th style="width:80px;">{{ $label }}</th>@endforeach
                                <th style="width:90px;">Total</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="visaEcowasBody">
                            <tr class="data-row">
                                <td class="visa-sn">1</td>
                                <td><input type="text" name="visa[ecowas][0][nationality]" class="ni" placeholder="Nationality" value="{{ old('visa.ecowas.0.nationality') }}"></td>
                                @foreach($permitCols as [$col,$label])
                                <td><input type="number" name="visa[ecowas][0][{{ $col }}]" class="ni visa-ecowas-{{ $col }}" min="0" placeholder="0" value="{{ old('visa.ecowas.0.'.$col) }}"></td>
                                @endforeach
                                <td><input type="number" id="visa-ecowas-row-total-0" class="ni" readonly placeholder="0"></td>
                                <td><button type="button" class="btn-nis btn-ghost btn-sm visa-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                @foreach($permitCols as [$col,$label])
                                <td><input type="number" id="visa-ecowas-grand-{{ $col }}" class="ni" readonly placeholder="0"></td>
                                @endforeach
                                <td><input type="number" id="visa-ecowas-grand-total" class="ni" readonly placeholder="0"></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="visaAddEcowasRow"><i class="fas fa-plus"></i> Add Row</button>
            </div>
        </div>

        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-flag"></i></div>
                    10. ECOWAS &amp; African Affairs — African Affairs
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">S/N</th>
                                <th>Nationality</th>
                                @foreach($permitCols as [$col,$label])<th style="width:80px;">{{ $label }}</th>@endforeach
                                <th style="width:90px;">Total</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="visaAfricanBody">
                            <tr class="data-row">
                                <td class="visa-sn">1</td>
                                <td><input type="text" name="visa[african_affairs][0][nationality]" class="ni" placeholder="Nationality" value="{{ old('visa.african_affairs.0.nationality') }}"></td>
                                @foreach($permitCols as [$col,$label])
                                <td><input type="number" name="visa[african_affairs][0][{{ $col }}]" class="ni visa-african-{{ $col }}" min="0" placeholder="0" value="{{ old('visa.african_affairs.0.'.$col) }}"></td>
                                @endforeach
                                <td><input type="number" id="visa-african-row-total-0" class="ni" readonly placeholder="0"></td>
                                <td><button type="button" class="btn-nis btn-ghost btn-sm visa-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                @foreach($permitCols as [$col,$label])
                                <td><input type="number" id="visa-african-grand-{{ $col }}" class="ni" readonly placeholder="0"></td>
                                @endforeach
                                <td><input type="number" id="visa-african-grand-total" class="ni" readonly placeholder="0"></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="visaAddAfricanRow"><i class="fas fa-plus"></i> Add Row</button>
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

    <!-- TAB 11: GENERAL REPORT -->
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
                <button type="button" class="btn-nis btn-ghost btn-sm" id="passportAddDocumentRow">
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
    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    const PERMIT_COLS = ['male','female','principal','dependent','regularization','renewals','redesignations','reclassification','coe','cos'];
    const PRP_COLS = ['male','female','principal','dependent','regularization','renewals'];
    const EMIGRANT_NUM = ['regular','irregular','male','female','employed','self_employed','student','spouse','dependant'];
    const QUOTA_COLS = ['quota_positions','utilized_quota','expatriate_deleted'];
    const COUNTER_COLS = ['extensions','spouse_male','spouse_female'];
    const VISA_STAT_COLS = ['applications','approved','rejected','pending'];
    const VISA_GROUPS = {
        evisa: ['f3b','f4a','f5a','f6a','f7e','f7f','f7g','f7h','f7i','f7k','f9a','f9b'],
        svv:   ['f2a','f3a','f4b','f4c','f6b','f7a','f7b','f7c','f7d','f7j','f7l','f7m'],
        trv:   ['r1a','r4a','r5a','r7a','r8a','r9a'],
        prv:   ['n1a','n2a','n3','n4a','n5a','n5b'],
        etwp:  ['r10','r11'],
    };
    const VISA_GROUP_LABELS = {
        evisa: 'Short Visit Visa — e-Visa',
        svv: 'Regular Visa — SVV',
        trv: 'TRV',
        prv: 'PRV',
        etwp: 'e-TWP',
    };
    const REMOVE_BTN = '<button type="button" class="btn-nis btn-ghost btn-sm visa-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button>';

    /* Section 1: Cadre */
    function recomputeCadre() {
        let mT = 0, fT = 0;
        document.querySelectorAll('.visa-cadre-m').forEach(m => {
            const row = m.dataset.row;
            const mV = val(m), fV = val(document.querySelector(`.visa-cadre-f[data-row="${row}"]`));
            set(`visa-cadre-total-${row}`, mV + fV);
            mT += mV; fT += fV;
        });
        set('visa-cadre-grand-male', mT);
        set('visa-cadre-grand-female', fT);
        set('visa-cadre-grand-total', mT + fT);
    }

    /* Section 2: e-Migrant */
    function recomputeEmigrant() {
        const colTotals = {}; EMIGRANT_NUM.forEach(c => colTotals[c] = 0);
        let grandTotal = 0;
        document.querySelectorAll('#visaEmigrantBody .data-row').forEach((row, idx) => {
            const m = val(row.querySelector('.visa-emigrant-male'));
            const f = val(row.querySelector('.visa-emigrant-female'));
            set(`visa-emigrant-row-total-${idx}`, m + f);
            grandTotal += m + f;
            EMIGRANT_NUM.forEach(c => colTotals[c] += val(row.querySelector(`.visa-emigrant-${c}`)));
        });
        EMIGRANT_NUM.forEach(c => set(`visa-emigrant-grand-${c}`, colTotals[c]));
        set('visa-emigrant-grand-total', grandTotal);
    }
    function addEmigrantRow() {
        const tbody = document.getElementById('visaEmigrantBody');
        const idx = tbody.querySelectorAll('.data-row').length;
        const tr = document.createElement('tr'); tr.className = 'data-row';
        let html = `<td class="visa-sn"></td>
            <td><input type="text" name="visa[emigrant][${idx}][center]" class="ni" placeholder="Center"></td>
            <td><input type="text" name="visa[emigrant][${idx}][nationality]" class="ni" placeholder="Nationality"></td>`;
        EMIGRANT_NUM.forEach(c => {
            html += `<td><input type="number" name="visa[emigrant][${idx}][${c}]" class="ni visa-emigrant-${c}" min="0" placeholder="0"></td>`;
            if (c === 'female') html += `<td><input type="number" id="visa-emigrant-row-total-${idx}" class="ni" readonly placeholder="0"></td>`;
        });
        html += `<td><input type="text" name="visa[emigrant][${idx}][region]" class="ni" placeholder="Region"></td>
            <td>${REMOVE_BTN}</td>`;
        tr.innerHTML = html;
        tbody.insertBefore(tr, tbody.lastElementChild);
        renumberAndRecompute(tbody);
    }

    /* Section 3: Quota Administration */
    function recomputeDynamicSimple(bodyId, classPrefix, cols, grandPrefix) {
        const totals = {}; cols.forEach(c => totals[c] = 0);
        document.querySelectorAll(`#${bodyId} .data-row`).forEach(row => {
            cols.forEach(c => totals[c] += val(row.querySelector(`.${classPrefix}${c}`)));
        });
        cols.forEach(c => set(`${grandPrefix}${c}`, totals[c]));
    }
    function addQuotaRow() {
        const tbody = document.getElementById('visaQuotaBody');
        const idx = tbody.querySelectorAll('.data-row').length;
        const tr = document.createElement('tr'); tr.className = 'data-row';
        let html = `<td class="visa-sn"></td>
            <td><input type="text" name="visa[quota][${idx}][company]" class="ni" placeholder="Company/Enterprise"></td>`;
        QUOTA_COLS.forEach(c => {
            html += `<td><input type="number" name="visa[quota][${idx}][${c}]" class="ni visa-quota-${c}" min="0" placeholder="0"></td>`;
        });
        html += `<td>${REMOVE_BTN}</td>`;
        tr.innerHTML = html;
        tbody.insertBefore(tr, tbody.lastElementChild);
        renumberAndRecompute(tbody);
    }

    /* Sections 4-5: Residence Permits (static rows) */
    function recomputePermitStatic(prefix, cols) {
        const colTotals = {}; cols.forEach(c => colTotals[c] = 0);
        const rowsSeen = new Set();
        let grand = 0;
        document.querySelectorAll(`.visa-${prefix}-cell`).forEach(cell => {
            colTotals[cell.dataset.col] += val(cell);
            rowsSeen.add(cell.dataset.row);
        });
        rowsSeen.forEach(r => {
            let t = 0;
            document.querySelectorAll(`.visa-${prefix}-cell[data-row="${r}"]`).forEach(c => t += val(c));
            set(`visa-${prefix}-total-${r}`, t);
            grand += t;
        });
        cols.forEach(c => set(`visa-${prefix}-col-total-${c}`, colTotals[c]));
        set(`visa-${prefix}-grand-total`, grand);
    }

    /* Section 6: FTZ */
    function recomputeFtz() {
        let t = 0;
        ['regularization','renewal','redesignation','coe','cos'].forEach(c => {
            t += val(document.querySelector(`.visa-ftz-${c}`));
        });
        set('visa-ftz-total', t);
    }

    /* Section 7: CERPAC */
    function recomputeCerpac() {
        const produced = val(document.querySelector('.visa-cerpac-card_produced'));
        const damaged = val(document.querySelector('.visa-cerpac-card_damaged'));
        set('visa-cerpac-total', produced - damaged);
    }

    /* Section 8: Visa groups */
    function recomputeVisas() {
        const grand = {}; VISA_STAT_COLS.concat(['total']).forEach(c => grand[c] = 0);
        Object.keys(VISA_GROUPS).forEach(g => {
            const sub = {}; VISA_STAT_COLS.concat(['total']).forEach(c => sub[c] = 0);
            VISA_GROUPS[g].forEach(code => {
                let rowTotal = 0;
                VISA_STAT_COLS.forEach(c => {
                    const v = val(document.querySelector(`[name="visa[visas][${g}][${code}][${c}]"]`));
                    sub[c] += v;
                    if (c !== 'applications') rowTotal += v;
                });
                sub.total += rowTotal;
                set(`visa-visas-total-${g}-${code}`, rowTotal);
            });
            VISA_STAT_COLS.concat(['total']).forEach(c => {
                set(`visa-visas-subtotal-${g}-${c}`, sub[c]);
                grand[c] += sub[c];
            });
        });
        VISA_STAT_COLS.concat(['total']).forEach(c => set(`visa-visas-grand-${c}`, grand[c]));
    }

    /* Section 9: Visa Counter */
    function addCounterRow() {
        const tbody = document.getElementById('visaCounterBody');
        const idx = tbody.querySelectorAll('.data-row').length;
        const tr = document.createElement('tr'); tr.className = 'data-row';
        let html = `<td class="visa-sn"></td>
            <td><input type="text" name="visa[counter][${idx}][visa_type]" class="ni" placeholder="Visa type"></td>`;
        COUNTER_COLS.forEach(c => {
            html += `<td><input type="number" name="visa[counter][${idx}][${c}]" class="ni visa-counter-${c}" min="0" placeholder="0"></td>`;
        });
        html += `<td>${REMOVE_BTN}</td>`;
        tr.innerHTML = html;
        tbody.insertBefore(tr, tbody.lastElementChild);
        renumberAndRecompute(tbody);
    }

    /* Section 10: ECOWAS & African Affairs */
    function recomputePermitDynamic(bodyId, classPrefix, grandPrefix, rowTotalPrefix) {
        const totals = {}; PERMIT_COLS.forEach(c => totals[c] = 0);
        let grand = 0;
        document.querySelectorAll(`#${bodyId} .data-row`).forEach((row, idx) => {
            let t = 0;
            PERMIT_COLS.forEach(c => {
                const v = val(row.querySelector(`.${classPrefix}${c}`));
                totals[c] += v; t += v;
            });
            set(`${rowTotalPrefix}${idx}`, t);
            grand += t;
        });
        PERMIT_COLS.forEach(c => set(`${grandPrefix}${c}`, totals[c]));
        set(`${grandPrefix}total`, grand);
    }
    function addPermitRow(bodyId, namePrefix, classPrefix, rowTotalPrefix) {
        const tbody = document.getElementById(bodyId);
        const idx = tbody.querySelectorAll('.data-row').length;
        const tr = document.createElement('tr'); tr.className = 'data-row';
        let html = `<td class="visa-sn"></td>
            <td><input type="text" name="${namePrefix}[${idx}][nationality]" class="ni" placeholder="Nationality"></td>`;
        PERMIT_COLS.forEach(c => {
            html += `<td><input type="number" name="${namePrefix}[${idx}][${c}]" class="ni ${classPrefix}${c}" min="0" placeholder="0"></td>`;
        });
        html += `<td><input type="number" id="${rowTotalPrefix}${idx}" class="ni" readonly placeholder="0"></td>
            <td>${REMOVE_BTN}</td>`;
        tr.innerHTML = html;
        tbody.insertBefore(tr, tbody.lastElementChild);
        renumberAndRecompute(tbody);
    }

    /* Row numbering and recompute after add/remove */
    function renumberAndRecompute(tbody) {
        tbody.querySelectorAll('.data-row').forEach((row, i) => {
            const sn = row.querySelector('.visa-sn');
            if (sn) sn.textContent = i + 1;
            row.querySelectorAll('input, select').forEach(el => {
                if (el.name) el.name = el.name.replace(/\[\d+\]/, `[${i}]`);
                if (el.id) el.id = el.id.replace(/-\d+$/, `-${i}`);
            });
        });
        recomputeAll();
    }

    /* Add/remove row wiring */
    document.getElementById('visaAddEmigrantRow')?.addEventListener('click', addEmigrantRow);
    document.getElementById('visaAddQuotaRow')?.addEventListener('click', addQuotaRow);
    document.getElementById('visaAddCounterRow')?.addEventListener('click', addCounterRow);
    document.getElementById('visaAddEcowasRow')?.addEventListener('click', () => addPermitRow('visaEcowasBody', 'visa[ecowas]', 'visa-ecowas-', 'visa-ecowas-row-total-'));
    document.getElementById('visaAddAfricanRow')?.addEventListener('click', () => addPermitRow('visaAfricanBody', 'visa[african_affairs]', 'visa-african-', 'visa-african-row-total-'));

    document.addEventListener('click', e => {
        const btn = e.target.closest('.visa-remove-row');
        if (!btn) return;
        const row = btn.closest('tr');
        const tbody = row.closest('tbody');
        if (tbody.querySelectorAll('.data-row').length <= 1) { alert('At least one row is required.'); return; }
        row.remove();
        renumberAndRecompute(tbody);
    });

    /* General Report: supporting documents */
    function addDocumentRow() {
        const container = document.getElementById('documents-body');
        const div = document.createElement('div');
        div.className = 'auth-form-group';
        div.style.marginBottom = '12px';
        div.innerHTML = '<input type="file" name="supporting_documents[]" class="ni" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg">';
        container.appendChild(div);
    }
    document.getElementById('passportAddDocumentRow')?.addEventListener('click', addDocumentRow);

    /* Master recompute */
    function recomputeAll() {
        recomputeCadre();
        recomputeEmigrant();
        recomputeDynamicSimple('visaQuotaBody', 'visa-quota-', QUOTA_COLS, 'visa-quota-grand-');
        recomputePermitStatic('trp', PERMIT_COLS);
        recomputePermitStatic('prp', PRP_COLS);
        recomputeFtz();
        recomputeCerpac();
        recomputeVisas();
        recomputeDynamicSimple('visaCounterBody', 'visa-counter-', COUNTER_COLS, 'visa-counter-grand-');
        recomputePermitDynamic('visaEcowasBody', 'visa-ecowas-', 'visa-ecowas-grand-', 'visa-ecowas-row-total-');
        recomputePermitDynamic('visaAfricanBody', 'visa-african-', 'visa-african-grand-', 'visa-african-row-total-');
        buildPreview();
    }

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

    function buildPreview() {
        const container = document.getElementById('hrmPreviewBody');
        if (!container) return;
        let html = '';

        /* 1. Cadre */
        let cadreRows = [];
        document.querySelectorAll('.visa-cadre-m').forEach(m => {
            const row = m.dataset.row;
            const label = m.closest('tr').querySelector('td').textContent;
            cadreRows.push([
                esc(label),
                m.value || '0',
                document.querySelector(`.visa-cadre-f[data-row="${row}"]`)?.value || '0',
                getValById(`visa-cadre-total-${row}`)
            ]);
        });
        cadreRows.push(['<strong>Grand Total</strong>', getValById('visa-cadre-grand-male'), getValById('visa-cadre-grand-female'), getValById('visa-cadre-grand-total')]);
        html += previewSectionTitle(1, 'Personnel Strength by Cadre');
        html += previewTable(cadreRows, ['Cadre', 'Male', 'Female', 'Total']);

        /* 2. e-Migrant */
        let emigrantRows = [];
        document.querySelectorAll('#visaEmigrantBody .data-row').forEach((row, idx) => {
            const vals = [
                row.querySelector('[name$="[center]"]')?.value || '—',
                row.querySelector('[name$="[nationality]"]')?.value || '—',
            ];
            EMIGRANT_NUM.forEach(c => vals.push(row.querySelector(`.visa-emigrant-${c}`)?.value || '0'));
            vals.splice(6, 0, getValById(`visa-emigrant-row-total-${idx}`));
            vals.push(row.querySelector('[name$="[region]"]')?.value || '—');
            if (vals.some(v => v !== '—' && v !== '0')) emigrantRows.push(vals.map(v => esc(v)));
        });
        html += previewSectionTitle(2, 'e-Migrant');
        html += previewTable(emigrantRows, ['Center', 'Nationality', 'Regular', 'Irregular', 'Male', 'Female', 'Total', 'Employed', 'Self Emp.', 'Student', 'Spouse', 'Dependant', 'Region']);
        html += previewTable([[
            '<strong>Total</strong>',
            ...EMIGRANT_NUM.slice(0, 4).map(c => getValById(`visa-emigrant-grand-${c}`)),
            getValById('visa-emigrant-grand-total'),
            ...EMIGRANT_NUM.slice(4).map(c => getValById(`visa-emigrant-grand-${c}`)),
            ''
        ]], ['', 'Regular', 'Irregular', 'Male', 'Female', 'Total', 'Employed', 'Self Emp.', 'Student', 'Spouse', 'Dependant', '']);

        /* 3. Quota Administration */
        let quotaRows = [];
        document.querySelectorAll('#visaQuotaBody .data-row').forEach(row => {
            const vals = [row.querySelector('[name$="[company]"]')?.value || '—'];
            QUOTA_COLS.forEach(c => vals.push(row.querySelector(`.visa-quota-${c}`)?.value || '0'));
            if (vals.some(v => v !== '—' && v !== '0')) quotaRows.push(vals.map(v => esc(v)));
        });
        html += previewSectionTitle(3, 'Quota Administration');
        html += previewTable(quotaRows, ['Company/Enterprise', 'Quota Positions', 'Utilized Quota', 'Expatriate Deleted']);
        html += previewTable([['<strong>Total</strong>', ...QUOTA_COLS.map(c => getValById(`visa-quota-grand-${c}`))]], ['', 'Quota Positions', 'Utilized Quota', 'Expatriate Deleted']);

        /* 4. Temporary Residence Permit */
        let trpRows = [];
        const trpSeen = new Set();
        document.querySelectorAll('.visa-trp-cell').forEach(c => trpSeen.add(c.dataset.row));
        trpSeen.forEach(r => {
            const label = document.querySelector(`.visa-trp-cell[data-row="${r}"]`)?.closest('tr').querySelector('td').textContent || r;
            const vals = [esc(label)];
            PERMIT_COLS.forEach(c => vals.push(document.querySelector(`.visa-trp-cell[data-row="${r}"][data-col="${c}"]`)?.value || '0'));
            vals.push(getValById(`visa-trp-total-${r}`));
            trpRows.push(vals);
        });
        trpRows.push(['<strong>Total</strong>', ...PERMIT_COLS.map(c => getValById(`visa-trp-col-total-${c}`)), getValById('visa-trp-grand-total')]);
        html += previewSectionTitle(4, 'Residence Permit — Temporary Residence Permit');
        html += previewTable(trpRows, ['Category', 'Male', 'Female', 'Principal', 'Dependent', 'Regularization', 'Renewals', 'Redesignations', 'Reclassification', 'COE', 'COS', 'Total']);

        /* 5. Permanent Residence Permit */
        let prpRows = [];
        const prpSeen = new Set();
        document.querySelectorAll('.visa-prp-cell').forEach(c => prpSeen.add(c.dataset.row));
        prpSeen.forEach(r => {
            const label = document.querySelector(`.visa-prp-cell[data-row="${r}"]`)?.closest('tr').querySelector('td').textContent || r;
            const vals = [esc(label)];
            PRP_COLS.forEach(c => vals.push(document.querySelector(`.visa-prp-cell[data-row="${r}"][data-col="${c}"]`)?.value || '0'));
            vals.push(getValById(`visa-prp-total-${r}`));
            prpRows.push(vals);
        });
        prpRows.push(['<strong>Total</strong>', ...PRP_COLS.map(c => getValById(`visa-prp-col-total-${c}`)), getValById('visa-prp-grand-total')]);
        html += previewSectionTitle(5, 'Residence Permit — Permanent Residence Permit');
        html += previewTable(prpRows, ['Category', 'Male', 'Female', 'Principal', 'Dependent', 'Regularization', 'Renewals', 'Total']);

        /* 6. FTZ */
        html += previewSectionTitle(6, 'Free Trade Zone (FTZ)');
        html += previewTable([[
            getVal('visa[ftz][free_zones]'),
            getVal('visa[ftz][enterprises]'),
            getVal('visa[ftz][expatriates]'),
            getVal('visa[ftz][regularization]'),
            getVal('visa[ftz][renewal]'),
            getVal('visa[ftz][redesignation]'),
            getVal('visa[ftz][coe]'),
            getVal('visa[ftz][cos]'),
            getValById('visa-ftz-total')
        ]], ['Free Zones', 'Enterprises', 'Expatriates', 'Regularization', 'Renewal', 'Redesignation', 'COE', 'COS', 'Total']);

        /* 7. CERPAC */
        html += previewSectionTitle(7, 'CERPAC Production');
        html += previewTable([[
            getVal('visa[cerpac][card_supplied]'),
            getVal('visa[cerpac][card_produced]'),
            getVal('visa[cerpac][card_damaged]'),
            getVal('visa[cerpac][card_issued]'),
            getValById('visa-cerpac-total')
        ]], ['Card Supplied', 'Card Produced', 'Card Damaged', 'Card Issued', 'Total']);

        /* 8. Visa */
        html += previewSectionTitle(8, 'Visa');
        Object.keys(VISA_GROUPS).forEach(g => {
            let rows = [];
            VISA_GROUPS[g].forEach(code => {
                const vals = [code.toUpperCase()];
                VISA_STAT_COLS.forEach(c => vals.push(getVal(`visa[visas][${g}][${code}][${c}]`)));
                vals.push(getValById(`visa-visas-total-${g}-${code}`));
                rows.push(vals);
            });
            rows.push(['<strong>Subtotal</strong>', ...VISA_STAT_COLS.map(c => getValById(`visa-visas-subtotal-${g}-${c}`)), getValById(`visa-visas-subtotal-${g}-total`)]);
            html += `<div style="font-size:.84rem;font-weight:700;margin:10px 0 6px;">${VISA_GROUP_LABELS[g]}</div>`;
            html += previewTable(rows, ['Code', 'Applications', 'Approved', 'Rejected', 'Pending', 'Total']);
        });
        html += previewTable([['<strong>Grand Total</strong>', ...VISA_STAT_COLS.map(c => getValById(`visa-visas-grand-${c}`)), getValById('visa-visas-grand-total')]], ['', 'Applications', 'Approved', 'Rejected', 'Pending', 'Total']);

        /* 9. Visa Counter */
        let counterRows = [];
        document.querySelectorAll('#visaCounterBody .data-row').forEach(row => {
            const vals = [row.querySelector('[name$="[visa_type]"]')?.value || '—'];
            COUNTER_COLS.forEach(c => vals.push(row.querySelector(`.visa-counter-${c}`)?.value || '0'));
            if (vals.some(v => v !== '—' && v !== '0')) counterRows.push(vals.map(v => esc(v)));
        });
        html += previewSectionTitle(9, 'Visa Counter');
        html += previewTable(counterRows, ['Visa Type', 'No. Of Extension', 'Spouse (Male)', 'Spouse (Female)']);
        html += previewTable([['<strong>Total</strong>', ...COUNTER_COLS.map(c => getValById(`visa-counter-grand-${c}`))]], ['', 'No. Of Extension', 'Spouse (Male)', 'Spouse (Female)']);

        /* 10. ECOWAS & African Affairs */
        function permitDynamicPreview(bodyId, classPrefix, rowTotalPrefix, grandPrefix, title) {
            let rows = [];
            document.querySelectorAll(`#${bodyId} .data-row`).forEach((row, idx) => {
                const vals = [row.querySelector('[name$="[nationality]"]')?.value || '—'];
                PERMIT_COLS.forEach(c => vals.push(row.querySelector(`.${classPrefix}${c}`)?.value || '0'));
                vals.push(getValById(`${rowTotalPrefix}${idx}`));
                if (vals.some(v => v !== '—' && v !== '0')) rows.push(vals.map(v => esc(v)));
            });
            let out = `<div style="font-size:.84rem;font-weight:700;margin:10px 0 6px;">${title}</div>`;
            out += previewTable(rows, ['Nationality', 'Male', 'Female', 'Principal', 'Dependent', 'Regularization', 'Renewals', 'Redesignations', 'Reclassification', 'COE', 'COS', 'Total']);
            out += previewTable([['<strong>Total</strong>', ...PERMIT_COLS.map(c => getValById(`${grandPrefix}${c}`)), getValById(`${grandPrefix}total`)]], ['', 'Male', 'Female', 'Principal', 'Dependent', 'Regularization', 'Renewals', 'Redesignations', 'Reclassification', 'COE', 'COS', 'Total']);
            return out;
        }
        html += previewSectionTitle(10, 'ECOWAS & African Affairs');
        html += permitDynamicPreview('visaEcowasBody', 'visa-ecowas-', 'visa-ecowas-row-total-', 'visa-ecowas-grand-', 'ECOWAS');
        html += permitDynamicPreview('visaAfricanBody', 'visa-african-', 'visa-african-row-total-', 'visa-african-grand-', 'African Affairs');

        /* 11. General Report and Supporting Documents */
        html += previewSectionTitle(11, 'General Reports and Supporting Documents');
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

    /* Submit from preview is wired globally in the shared layout. */

    /* Listen for input */
    if (form) form.addEventListener('input', recomputeAll);

    /* Let the layout's preview tab use this page-specific renderer. */
    window.buildDirectoratePreview = buildPreview;

    /* Init */
    recomputeAll();
})();
</script>

@endsection
