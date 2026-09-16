@extends('user.directorates._layout')

{{-- This view renders its own tab bar; the Preview panel and action buttons
     come from the shared layout. --}}
@section('directorate-tabs', '1')

@section('directorate-sections')

@php

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


$locationCols = [
    ['shq','SHQ'],
    ['zone_a',"ZONE 'A'"],
    ['zone_b',"ZONE 'B'"],
    ['zone_c',"ZONE 'C'"],
    ['zone_d',"ZONE 'D'"],
    ['zone_e',"ZONE 'E'"],
    ['zone_f',"ZONE 'F'"],
    ['zone_g',"ZONE 'G'"],
    ['zone_h',"ZONE 'H'"],
    ['icsc','ICSC'],
    ['itsk','ITSK'],
    ['nitsol','NITSOL'],
    ['nitsa','NITSA'],
];

$cadreRows = [
    ['comptroller','Comptroller Cadre'],
    ['superintendent','Superintendent Cadre'],
    ['inspectorate','Inspectorate Cadre'],
    ['assistant','Assistant Cadre'],
];

$incidentCategories = [
    ['hardware','a. Hardware'],
    ['software','b. Software'],
    ['network','c. Network'],
    ['cybersecurity','d. Cybersecurity'],
    ['power_supply','e. Power Supply System'],
    ['communication','f. Communication'],
    ['surveillance','g. Surveillance'],
];
@endphp

{{-- The shared layout provides the <form>; do not nest another one here. --}}
<div class="hrm-tabs-wrap">
    <div class="entry-tabs-wrap" style="margin-bottom:0;">
        <div class="entry-tabs" id="entryTabs">
            @php $tabs = [
                ['cadre','fas fa-users','1. Cadre'],
                ['rank','fas fa-star','2. Rank'],
                ['incidents','fas fa-exclamation-triangle','3. Incidents'],
                ['maintenance','fas fa-tools','4. Maintenance'],
                ['software','fas fa-laptop-code','5. Software & Data'],
                ['edoc','fas fa-id-card','6. E-Doc & MIDAS'],
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

    <!-- TAB 1: Staff Strength by Cadre -->
    <div class="tab-panel active" id="tab-cadre">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-users"></i></div>
                    1. Staff Strength by Cadre
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
                                <td><input type="number" name="ict[cadre][{{ $key }}][male]" class="ni ict-cadre-m" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('ict.cadre.'.$key.'.male') }}"></td>
                                <td><input type="number" name="ict[cadre][{{ $key }}][female]" class="ni ict-cadre-f" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('ict.cadre.'.$key.'.female') }}"></td>
                                <td><input type="number" id="ict-cadre-total-{{ $key }}" class="ni" readonly placeholder="0"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><input type="number" id="ict-cadre-grand-male" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="ict-cadre-grand-female" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="ict-cadre-grand-total" class="ni" readonly placeholder="0"></td>
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

    <!-- TAB 2: Staff Strength by Rank -->
    <div class="tab-panel" id="tab-rank">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-star"></i></div>
                    2. Staff Strength by Rank
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
                                <td><input type="number" name="ict[rank][{{ $key }}][male]" class="ni ict-rank-m" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('ict.rank.'.$key.'.male') }}"></td>
                                <td><input type="number" name="ict[rank][{{ $key }}][female]" class="ni ict-rank-f" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('ict.rank.'.$key.'.female') }}"></td>
                                <td><input type="number" id="ict-rank-total-{{ $key }}" class="ni" readonly placeholder="0"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><input type="number" id="ict-rank-grand-male" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="ict-rank-grand-female" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="ict-rank-grand-total" class="ni" readonly placeholder="0"></td>
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

    <!-- TAB 3: Incident Report -->
    <div class="tab-panel" id="tab-incidents">
        @foreach($incidentCategories as [$catKey,$catLabel])
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-exclamation-triangle"></i></div>
                    3. Incident Report — {{ $catLabel }}
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm ict-add-row" data-target="ict-incident-body-{{ $catKey }}">
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">SN</th>
                                <th>Incident</th>
                                <th style="width:130px;">Date/Time</th>
                                <th>Systems Affected</th>
                                <th>Cause</th>
                                <th>Action Taken</th>
                                <th>Impact Assessment</th>
                                <th>Recommendation</th>
                            </tr>
                        </thead>
                        <tbody id="ict-incident-body-{{ $catKey }}">
                            @for($i = 0; $i < 2; $i++)
                            <tr>
                                <td class="ict-sn">{{ $i + 1 }}</td>
                                <td><input type="text" name="ict[incidents][{{ $catKey }}][{{ $i }}][incident]" class="ni" value="{{ old('ict.incidents.'.$catKey.'.'.$i.'.incident') }}"></td>
                                <td><input type="text" name="ict[incidents][{{ $catKey }}][{{ $i }}][datetime]" class="ni" placeholder="DD/MM/YYYY HH:MM" value="{{ old('ict.incidents.'.$catKey.'.'.$i.'.datetime') }}"></td>
                                <td><input type="text" name="ict[incidents][{{ $catKey }}][{{ $i }}][systems_affected]" class="ni" value="{{ old('ict.incidents.'.$catKey.'.'.$i.'.systems_affected') }}"></td>
                                <td><input type="text" name="ict[incidents][{{ $catKey }}][{{ $i }}][cause]" class="ni" value="{{ old('ict.incidents.'.$catKey.'.'.$i.'.cause') }}"></td>
                                <td><input type="text" name="ict[incidents][{{ $catKey }}][{{ $i }}][action_taken]" class="ni" value="{{ old('ict.incidents.'.$catKey.'.'.$i.'.action_taken') }}"></td>
                                <td><input type="text" name="ict[incidents][{{ $catKey }}][{{ $i }}][impact]" class="ni" value="{{ old('ict.incidents.'.$catKey.'.'.$i.'.impact') }}"></td>
                                <td><input type="text" name="ict[incidents][{{ $catKey }}][{{ $i }}][recommendation]" class="ni" value="{{ old('ict.incidents.'.$catKey.'.'.$i.'.recommendation') }}"></td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach

        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-handshake"></i></div>
                    3. Incident Report — h. Technical Services Providers
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm ict-add-row" data-target="ict-tsp-body">
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">SN</th>
                                <th>Name of Service Provider</th>
                                <th>Services provided</th>
                                <th>Incident</th>
                                <th style="width:130px;">Date/Time</th>
                                <th>Systems Affected</th>
                                <th>Cause</th>
                                <th>Action Taken</th>
                                <th>Impact Assessment</th>
                                <th>Recommendation</th>
                            </tr>
                        </thead>
                        <tbody id="ict-tsp-body">
                            @for($i = 0; $i < 1; $i++)
                            <tr>
                                <td class="ict-sn">{{ $i + 1 }}</td>
                                <td><input type="text" name="ict[tsp][{{ $i }}][provider]" class="ni" value="{{ old('ict.tsp.'.$i.'.provider') }}"></td>
                                <td><input type="text" name="ict[tsp][{{ $i }}][services]" class="ni" value="{{ old('ict.tsp.'.$i.'.services') }}"></td>
                                <td><input type="text" name="ict[tsp][{{ $i }}][incident]" class="ni" value="{{ old('ict.tsp.'.$i.'.incident') }}"></td>
                                <td><input type="text" name="ict[tsp][{{ $i }}][datetime]" class="ni" placeholder="DD/MM/YYYY HH:MM" value="{{ old('ict.tsp.'.$i.'.datetime') }}"></td>
                                <td><input type="text" name="ict[tsp][{{ $i }}][systems_affected]" class="ni" value="{{ old('ict.tsp.'.$i.'.systems_affected') }}"></td>
                                <td><input type="text" name="ict[tsp][{{ $i }}][cause]" class="ni" value="{{ old('ict.tsp.'.$i.'.cause') }}"></td>
                                <td><input type="text" name="ict[tsp][{{ $i }}][action_taken]" class="ni" value="{{ old('ict.tsp.'.$i.'.action_taken') }}"></td>
                                <td><input type="text" name="ict[tsp][{{ $i }}][impact]" class="ni" value="{{ old('ict.tsp.'.$i.'.impact') }}"></td>
                                <td><input type="text" name="ict[tsp][{{ $i }}][recommendation]" class="ni" value="{{ old('ict.tsp.'.$i.'.recommendation') }}"></td>
                            </tr>
                            @endfor
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

    <!-- TAB 4: Hardware Maintenance & Project/Programme Activities -->
    <div class="tab-panel" id="tab-maintenance">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-tools"></i></div>
                    4. Hardware Maintenance
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm ict-add-row" data-target="ict-hardware-body">
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th>Equipment Type</th>
                                <th>Product</th>
                                <th>Status</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody id="ict-hardware-body">
                            @for($i = 0; $i < 2; $i++)
                            <tr>
                                <td class="ict-sn">{{ $i + 1 }}</td>
                                <td><input type="text" name="ict[hardware_maintenance][{{ $i }}][equipment_type]" class="ni" value="{{ old('ict.hardware_maintenance.'.$i.'.equipment_type') }}"></td>
                                <td><input type="text" name="ict[hardware_maintenance][{{ $i }}][product]" class="ni" value="{{ old('ict.hardware_maintenance.'.$i.'.product') }}"></td>
                                <td><input type="text" name="ict[hardware_maintenance][{{ $i }}][status]" class="ni" value="{{ old('ict.hardware_maintenance.'.$i.'.status') }}"></td>
                                <td><input type="text" name="ict[hardware_maintenance][{{ $i }}][location]" class="ni" value="{{ old('ict.hardware_maintenance.'.$i.'.location') }}"></td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-diagram-project"></i></div>
                    5. Project/Programme Activities
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm ict-add-row" data-target="ict-projects-body">
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">SN</th>
                                <th>Project/Programme</th>
                                <th>Type Of System</th>
                                <th>Status Report</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="ict-projects-body">
                            @for($i = 0; $i < 2; $i++)
                            <tr>
                                <td class="ict-sn">{{ $i + 1 }}</td>
                                <td><input type="text" name="ict[projects][{{ $i }}][name]" class="ni" value="{{ old('ict.projects.'.$i.'.name') }}"></td>
                                <td><input type="text" name="ict[projects][{{ $i }}][system_type]" class="ni" value="{{ old('ict.projects.'.$i.'.system_type') }}"></td>
                                <td><input type="text" name="ict[projects][{{ $i }}][status]" class="ni" value="{{ old('ict.projects.'.$i.'.status') }}"></td>
                                <td><input type="text" name="ict[projects][{{ $i }}][remarks]" class="ni" value="{{ old('ict.projects.'.$i.'.remarks') }}"></td>
                            </tr>
                            @endfor
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

    <!-- TAB 5: Software and Data Management -->
    <div class="tab-panel" id="tab-software">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-laptop-code"></i></div>
                    6. Software and Data Management — a. Software
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm ict-add-row" data-target="ict-software-body">
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th>Software Developed</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Deployment</th>
                            </tr>
                        </thead>
                        <tbody id="ict-software-body">
                            @for($i = 0; $i < 1; $i++)
                            <tr>
                                <td class="ict-sn">{{ $i + 1 }}</td>
                                <td><input type="text" name="ict[software][{{ $i }}][developed]" class="ni" value="{{ old('ict.software.'.$i.'.developed') }}"></td>
                                <td><input type="text" name="ict[software][{{ $i }}][description]" class="ni" value="{{ old('ict.software.'.$i.'.description') }}"></td>
                                <td><input type="text" name="ict[software][{{ $i }}][status]" class="ni" value="{{ old('ict.software.'.$i.'.status') }}"></td>
                                <td><input type="text" name="ict[software][{{ $i }}][deployment]" class="ni" value="{{ old('ict.software.'.$i.'.deployment') }}"></td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-database"></i></div>
                    6. Software and Data Management — b. Data
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm ict-add-row" data-target="ict-data-body">
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th>Data Breached Incident</th>
                                <th style="width:130px;">No. of Incident</th>
                                <th>Status</th>
                                <th>Breach Location</th>
                            </tr>
                        </thead>
                        <tbody id="ict-data-body">
                            @for($i = 0; $i < 1; $i++)
                            <tr>
                                <td class="ict-sn">{{ $i + 1 }}</td>
                                <td><input type="text" name="ict[data][{{ $i }}][incident]" class="ni" value="{{ old('ict.data.'.$i.'.incident') }}"></td>
                                <td><input type="number" name="ict[data][{{ $i }}][no_of_incident]" class="ni" min="0" placeholder="0" value="{{ old('ict.data.'.$i.'.no_of_incident') }}"></td>
                                <td><input type="text" name="ict[data][{{ $i }}][status]" class="ni" value="{{ old('ict.data.'.$i.'.status') }}"></td>
                                <td><input type="text" name="ict[data][{{ $i }}][location]" class="ni" value="{{ old('ict.data.'.$i.'.location') }}"></td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-shield-halved"></i></div>
                    6. Software and Data Management — c. Cybersecurity
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm ict-add-row" data-target="ict-cyber-body">
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th>Security Deployed</th>
                                <th>Type of Security</th>
                                <th>Incident Report</th>
                                <th>Location</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="ict-cyber-body">
                            @for($i = 0; $i < 1; $i++)
                            <tr>
                                <td class="ict-sn">{{ $i + 1 }}</td>
                                <td><input type="text" name="ict[cybersecurity][{{ $i }}][security_deployed]" class="ni" value="{{ old('ict.cybersecurity.'.$i.'.security_deployed') }}"></td>
                                <td><input type="text" name="ict[cybersecurity][{{ $i }}][type_of_security]" class="ni" value="{{ old('ict.cybersecurity.'.$i.'.type_of_security') }}"></td>
                                <td><input type="text" name="ict[cybersecurity][{{ $i }}][incident_report]" class="ni" value="{{ old('ict.cybersecurity.'.$i.'.incident_report') }}"></td>
                                <td><input type="text" name="ict[cybersecurity][{{ $i }}][location]" class="ni" value="{{ old('ict.cybersecurity.'.$i.'.location') }}"></td>
                                <td><input type="text" name="ict[cybersecurity][{{ $i }}][status]" class="ni" value="{{ old('ict.cybersecurity.'.$i.'.status') }}"></td>
                            </tr>
                            @endfor
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

    <!-- TAB 6: E-Documentation/ID Card Activities & MIDAS Deployment -->
    <div class="tab-panel" id="tab-edoc">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-id-card"></i></div>
                    7. E-Documentation/ID Card Activities
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th>RANK</th>
                                @foreach($locationCols as [$locKey,$locLabel])
                                <th style="width:80px;">{{ $locLabel }}</th>
                                @endforeach
                                <th style="width:90px;">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rankRows as [$rankKey,$rankLabel])
                            <tr>
                                <td>{{ $rankLabel }}</td>
                                @foreach($locationCols as [$locKey,$locLabel])
                                <td><input type="number" name="ict[edoc][{{ $rankKey }}][{{ $locKey }}]" class="ni ict-edoc-cell" data-rank="{{ $rankKey }}" data-loc="{{ $locKey }}" min="0" placeholder="0" value="{{ old('ict.edoc.'.$rankKey.'.'.$locKey) }}"></td>
                                @endforeach
                                <td><input type="number" id="ict-edoc-total-{{ $rankKey }}" class="ni" readonly placeholder="0"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                @foreach($locationCols as [$locKey,$locLabel])
                                <td><input type="number" id="ict-edoc-col-{{ $locKey }}" class="ni" readonly placeholder="0"></td>
                                @endforeach
                                <td><input type="number" id="ict-edoc-grand-total" class="ni" readonly placeholder="0"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-server"></i></div>
                    8. MIDAS Deployment
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm ict-add-row" data-target="ict-midas-body">
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th>State Command</th>
                                <th>Location</th>
                                <th>Location</th>
                                <th>Status/Remark</th>
                            </tr>
                        </thead>
                        <tbody id="ict-midas-body">
                            @for($i = 0; $i < 3; $i++)
                            <tr>
                                <td class="ict-sn">{{ $i + 1 }}</td>
                                <td><input type="text" name="ict[midas][{{ $i }}][state_command]" class="ni" value="{{ old('ict.midas.'.$i.'.state_command') }}"></td>
                                <td><input type="text" name="ict[midas][{{ $i }}][location_1]" class="ni" value="{{ old('ict.midas.'.$i.'.location_1') }}"></td>
                                <td><input type="text" name="ict[midas][{{ $i }}][location_2]" class="ni" value="{{ old('ict.midas.'.$i.'.location_2') }}"></td>
                                <td><input type="text" name="ict[midas][{{ $i }}][status]" class="ni" value="{{ old('ict.midas.'.$i.'.status') }}"></td>
                            </tr>
                            @endfor
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
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-list-check"></i>
                    </div>
                    9. Summary — Project and System Activities
                </div>
            </div>
            <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
                <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Project/Programme Activities</label><input type="number" name="project_programme_activities" class="ni" min="0" placeholder="0" value="{{ old('project_programme_activities') }}"></div>
                <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Systems Fully Operational</label><input type="number" name="systems_operational" class="ni" min="0" placeholder="0" value="{{ old('systems_operational') }}"></div>
                <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">MIDAS Deployment Sites</label><input type="number" name="midas_sites_deployed" class="ni" min="0" placeholder="0" value="{{ old('midas_sites_deployed') }}"></div>
                <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Software Deliverables</label><input type="number" name="software_delivered" class="ni" min="0" placeholder="0" value="{{ old('software_delivered') }}"></div>
            </div>
        </div>

        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-list-check"></i>
                    </div>
                    9. Summary — Maintenance and Security
                </div>
            </div>
            <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
                <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Hardware Maintenance Cases</label><input type="number" name="hardware_maintenance_cases" class="ni" min="0" placeholder="0" value="{{ old('hardware_maintenance_cases') }}"></div>
                <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Data Breach Incidents</label><input type="number" name="data_breach_incidents" class="ni" min="0" placeholder="0" value="{{ old('data_breach_incidents') }}"></div>
                <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Cybersecurity Incidents</label><input type="number" name="cybersecurity_incidents" class="ni" min="0" placeholder="0" value="{{ old('cybersecurity_incidents') }}"></div>
                <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Security Controls Deployed</label><input type="number" name="security_controls_deployed" class="ni" min="0" placeholder="0" value="{{ old('security_controls_deployed') }}"></div>
            </div>
        </div>

        <div class="redas-card" style="margin-bottom:16px;">
            <div class="card-head"><div class="card-head-title">GENERAL REPORT</div></div>
            <div class="card-body">
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">i. Other Reports</label>
                    <textarea name="ict_other_reports" class="ni" rows="3" placeholder="Provide details...">{{ old('ict_other_reports') }}</textarea>
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
                <button type="button" class="btn-nis btn-ghost btn-sm" id="ictAddDocumentRow">
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
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c];
        });
    }

    const LOCATION_COLS = ['shq','zone_a','zone_b','zone_c','zone_d','zone_e','zone_f','zone_g','zone_h','icsc','itsk','nitsol','nitsa'];
    const LOCATION_LABELS = ['SHQ',"ZONE 'A'","ZONE 'B'","ZONE 'C'","ZONE 'D'","ZONE 'E'","ZONE 'F'","ZONE 'G'","ZONE 'H'",'ICSC','ITSK','NITSOL','NITSA'];

    /* Section 1: Cadre */
    function recomputeCadre() {
        let mT = 0, fT = 0;
        document.querySelectorAll('.ict-cadre-m').forEach(m => {
            const row = m.dataset.row;
            const mV = val(m), fV = val(document.querySelector(`.ict-cadre-f[data-row="${row}"]`));
            set(`ict-cadre-total-${row}`, mV + fV);
            mT += mV; fT += fV;
        });
        set('ict-cadre-grand-male', mT);
        set('ict-cadre-grand-female', fT);
        set('ict-cadre-grand-total', mT + fT);
    }

     /* Section 2: Rank */
     function recomputeRank() {
        let mT = 0, fT = 0;
        document.querySelectorAll('.ict-rank-m').forEach(m => {
            const row = m.dataset.row;
            const mV = val(m), fV = val(document.querySelector(`.ict-rank-f[data-row="${row}"]`));
            set(`ict-rank-total-${row}`, mV + fV);
            mT += mV; fT += fV;
        });
        set('ict-rank-grand-male', mT);
        set('ict-rank-grand-female', fT);
        set('ict-rank-grand-total', mT + fT);
    }

    /* Section 7: rank-by-location matrix (E-Documentation/ID Card Activities) */
    function recomputeMatrix(prefix) {
        const rowTotals = {}, colTotals = {};
        let grand = 0;
        document.querySelectorAll(`.${prefix}-cell`).forEach(cell => {
            const v = val(cell);
            rowTotals[cell.dataset.rank] = (rowTotals[cell.dataset.rank] || 0) + v;
            colTotals[cell.dataset.loc] = (colTotals[cell.dataset.loc] || 0) + v;
            grand += v;
        });
        Object.keys(rowTotals).forEach(rank => set(`${prefix}-total-${rank}`, rowTotals[rank]));
        LOCATION_COLS.forEach(loc => set(`${prefix}-col-${loc}`, colTotals[loc] || 0));
        set(`${prefix}-grand-total`, grand);
    }

    /* Dynamic rows: clone the last row of a tbody, clear it and bump field indices */
    function renumber(tbody) {
        tbody.querySelectorAll('.ict-sn').forEach((cell, i) => { cell.textContent = i + 1; });
    }
    function addRow(tbodyId) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        const rows = tbody.querySelectorAll('tr');
        if (!rows.length) return;
        const clone = rows[rows.length - 1].cloneNode(true);
        const newIdx = rows.length;
        clone.querySelectorAll('input, textarea, select').forEach(el => {
            if (el.name) el.name = el.name.replace(/\[(\d+)\]/, `[${newIdx}]`);
            if (el.type === 'checkbox' || el.type === 'radio') el.checked = false;
            else el.value = '';
        });
        tbody.appendChild(clone);
        renumber(tbody);
    }

    function addDocumentRow() {
        const container = document.getElementById('documents-body');
        const div = document.createElement('div');
        div.className = 'auth-form-group';
        div.style.marginBottom = '12px';
        div.innerHTML = '<input type="file" name="supporting_documents[]" class="ni" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg">';
        container.appendChild(div);
    }

    /* Preview builder helpers */
    function previewSectionTitle(num, title) {
        return `<div class="hrm-preview-section-title">${num}. ${title}</div>`;
    }
    function previewTable(rows, headers = ['Item', 'Value']) {
        let thead = headers.map(h => `<th style="padding:6px 8px;border:1px solid var(--gray-200);background:#f8fafc;">${h}</th>`).join('');
        let tbody = rows.length
            ? rows.map(r => `<tr>${r.map(c => `<td style="padding:6px 8px;border:1px solid var(--gray-200);">${c}</td>`).join('')}</tr>`).join('')
            : `<tr><td colspan="${headers.length}" style="padding:6px 8px;border:1px solid var(--gray-200);color:var(--gray-500);">No entries.</td></tr>`;
        return `<div style="overflow-x:auto;"><table class="hrm-preview-table" style="margin-bottom:12px;"><thead><tr>${thead}</tr></thead><tbody>${tbody}</tbody></table></div>`;
    }
    function getVal(name) { return document.querySelector(`[name="${name}"]`)?.value || '0'; }
    function getValById(id) { return document.getElementById(id)?.value || '0'; }

    /* Collect non-empty rows from a dynamic tbody as arrays of escaped values */
    function tableRows(tbodyId) {
        const rows = [];
        document.querySelectorAll(`#${tbodyId} tr`).forEach(tr => {
            const inputs = tr.querySelectorAll('input, textarea, select');
            const vals = Array.from(inputs).map(i => esc(i.value.trim()));
            if (vals.some(v => v !== '')) rows.push(vals);
        });
        return rows;
    }

    function matrixPreviewRows(prefix) {
        const rows = [];
        const ranks = new Set();
        document.querySelectorAll(`.${prefix}-cell`).forEach(c => ranks.add(c.dataset.rank));
        ranks.forEach(rank => {
            const label = document.querySelector(`.${prefix}-cell[data-rank="${rank}"]`)?.closest('tr')?.querySelector('td')?.textContent.trim() || rank;
            const cells = LOCATION_COLS.map(loc => document.querySelector(`.${prefix}-cell[data-rank="${rank}"][data-loc="${loc}"]`)?.value || '0');
            rows.push([esc(label), ...cells, getValById(`${prefix}-total-${rank}`)]);
        });
        rows.push(['<strong>TOTAL</strong>', ...LOCATION_COLS.map(loc => `<strong>${getValById(`${prefix}-col-${loc}`)}</strong>`), `<strong>${getValById(`${prefix}-grand-total`)}</strong>`]);
        return rows;
    }

    function buildPreview() {
        const container = document.getElementById('hrmPreviewBody');
        if (!container) return;
        let html = '';

        /* 1. Staff Strength by Cadre */
        let cadreRows = [];
        document.querySelectorAll('.ict-cadre-m').forEach(m => {
            const row = m.dataset.row;
            const label = m.closest('tr').querySelector('td').textContent;
            cadreRows.push([
                esc(label),
                m.value || '0',
                document.querySelector(`.ict-cadre-f[data-row="${row}"]`)?.value || '0',
                getValById(`ict-cadre-total-${row}`)
            ]);
        });
        cadreRows.push(['<strong>Grand Total</strong>', `<strong>${getValById('ict-cadre-grand-male')}</strong>`, `<strong>${getValById('ict-cadre-grand-female')}</strong>`, `<strong>${getValById('ict-cadre-grand-total')}</strong>`]);
        html += previewSectionTitle(1, 'Staff Strength by Cadre');
        html += previewTable(cadreRows, ['Cadre', 'Male', 'Female', 'Total']);

        /* 2. Staff Strength by Rank */
        let rankRows = [];
        document.querySelectorAll('.ict-rank-m').forEach(m => {
            const row = m.dataset.row;
            const label = m.closest('tr').querySelector('td').textContent;
            rankRows.push([
                esc(label),
                m.value || '0',
                document.querySelector(`.ict-rank-f[data-row="${row}"]`)?.value || '0',
                getValById(`ict-rank-total-${row}`)
            ]);
        });
        rankRows.push(['<strong>Grand Total</strong>', `<strong>${getValById('ict-rank-grand-male')}</strong>`, `<strong>${getValById('ict-rank-grand-female')}</strong>`, `<strong>${getValById('ict-rank-grand-total')}</strong>`]);
        html += previewSectionTitle(2, 'Staff Strength by Rank');
        html += previewTable(rankRows, ['Rank', 'Male', 'Female', 'Total']);

        /* 3. Incident Report */
        const incidentCategories = [
            ['hardware', 'a. Hardware'],
            ['software', 'b. Software'],
            ['network', 'c. Network'],
            ['cybersecurity', 'd. Cybersecurity'],
            ['power_supply', 'e. Power Supply System'],
            ['communication', 'f. Communication'],
            ['surveillance', 'g. Surveillance']
        ];
        const incidentHeaders = ['Incident', 'Date/Time', 'Systems Affected', 'Cause', 'Action Taken', 'Impact Assessment', 'Recommendation'];
        html += previewSectionTitle(3, 'Incident Report');
        incidentCategories.forEach(([key, label]) => {
            html += `<div style="font-size:.84rem;font-weight:700;margin:10px 0 6px;">${label}</div>`;
            html += previewTable(tableRows(`ict-incident-body-${key}`), incidentHeaders);
        });
        html += `<div style="font-size:.84rem;font-weight:700;margin:10px 0 6px;">h. Technical Services Providers</div>`;
        html += previewTable(tableRows('ict-tsp-body'), ['Name of Service Provider', 'Services provided', ...incidentHeaders]);

        /* 4. Hardware Maintenance */
        html += previewSectionTitle(4, 'Hardware Maintenance');
        html += previewTable(tableRows('ict-hardware-body'), ['Equipment Type', 'Product', 'Status', 'Location']);

        /* 5. Project/Programme Activities */
        html += previewSectionTitle(5, 'Project/Programme Activities');
        html += previewTable(tableRows('ict-projects-body'), ['Project/Programme', 'Type Of System', 'Status Report', 'Remarks']);

        /* 6. Software and Data Management */
        html += previewSectionTitle(6, 'Software and Data Management');
        html += `<div style="font-size:.84rem;font-weight:700;margin:10px 0 6px;">a. Software</div>`;
        html += previewTable(tableRows('ict-software-body'), ['Software Developed', 'Description', 'Status', 'Deployment']);
        html += `<div style="font-size:.84rem;font-weight:700;margin:10px 0 6px;">b. Data</div>`;
        html += previewTable(tableRows('ict-data-body'), ['Data Breached Incident', 'No. of Incident', 'Status', 'Breach Location']);
        html += `<div style="font-size:.84rem;font-weight:700;margin:10px 0 6px;">c. Cybersecurity</div>`;
        html += previewTable(tableRows('ict-cyber-body'), ['Security Deployed', 'Type of Security', 'Incident Report', 'Location', 'Status']);

        /* 7. E-Documentation/ID Card Activities */
        html += previewSectionTitle(7, 'E-Documentation/ID Card Activities');
        html += previewTable(matrixPreviewRows('ict-edoc'), ['Rank', ...LOCATION_LABELS, 'Total']);

        /* 8. MIDAS Deployment */
        html += previewSectionTitle(8, 'MIDAS Deployment');
        html += previewTable(tableRows('ict-midas-body'), ['State Command', 'Location', 'Location', 'Status/Remark']);

        /* 9. Summary statistics + General Report */
        html += previewSectionTitle(9, 'Summary and General Report');
        html += previewTable([
            ['Project/Programme Activities', getVal('project_programme_activities')],
            ['Systems Fully Operational', getVal('systems_operational')],
            ['MIDAS Deployment Sites', getVal('midas_sites_deployed')],
            ['Software Deliverables', getVal('software_delivered')],
            ['Hardware Maintenance Cases', getVal('hardware_maintenance_cases')],
            ['Data Breach Incidents', getVal('data_breach_incidents')],
            ['Cybersecurity Incidents', getVal('cybersecurity_incidents')],
            ['Security Controls Deployed', getVal('security_controls_deployed')]
        ], ['Item', 'Value']);

        const generalReportFields = [
            ['Other Reports', 'ict_other_reports', 'No other reports.'],
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
        recomputeMatrix('ict-edoc');
        buildPreview();
    }

    /* Bindings */
    document.querySelectorAll('.ict-add-row').forEach(btn => {
        btn.addEventListener('click', () => addRow(btn.dataset.target));
    });
    document.getElementById('ictAddDocumentRow')?.addEventListener('click', addDocumentRow);

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
