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

$promotionRows = [
    ['acg_dcg','ACG-DCG'],
    ['cis_acg','CIS-ACG'],
    ['dci_cis','DCI-CIS'],
    ['aci_dci','ACI-DCI'],
    ['csi_aci','CSI-ACI'],
    ['si_csi','SI-CSI'],
    ['dsi_si','DSI-SI'],
    ['asi1_dsi','ASI1-DSI'],
    ['asi2_asi1','ASI2-ASI1'],
    ['ii_asi2','II-ASI2'],
    ['aii_ii','AII-II'],
    ['ia1_ii','IA1-II'],
    ['ia2_ia1','IA2-IA1'],
    ['ia3_ia2','IA3-IA2'],
];

$disciplineRows = [
    'dismissal' => 'Dismissal',
    'suspension' => 'Suspension',
    'termination' => 'Termination',
    'ongoing' => 'On-going cases',
    'salary_stopped' => 'Salary Stopped',
    'queries' => 'Queries',
    'warned' => 'Warned',
    'deranked' => 'De-Ranked',
    'interdiction' => 'Interdiction',
    'reinstatement' => 'Reinstatement',
    'lifting_suspension' => 'Lifting of suspension',
    'prohibition_promotion' => 'Prohibition of promotion',
    'discharged' => 'Discharged',
    'reprimanded' => 'Reprimanded',
    'compulsory_retirement' => 'Compulsory Retirement',
    'others' => 'Others',
];

$disciplineRankCols = ['acg','cis','dci','aci','csi','si','dsi','asi1','asi2','ii','aii','cia','sia','ia1','ia2','ia3'];

$zoneCommands = [
    'A' => ['LASC','SEME BC','IDBC','LSPMC','LAPC','MMIA','OGSC'],
    'B' => ['KDSC','KNSC','ITSK','MAKIA','KTSC','JSBC','SOSC','ICSC','ILLELA BC','ZMSC','JGSC'],
    'C' => ['BASC','YBSC','BOSC','ADSC','GMSC','PLSC'],
    'D' => ['FCTC','NAIA','KWSC'],
    'E' => ['NGSC','KBSC','ABSC','AKSC','CRSC','MFUM BC','EBSC','IMSC','NITSOL','RVSC','PHIA','NITSA','RVMC'],
    'F' => ['OYSC','OSSC','ODSC','EKSC'],
    'G' => ['ANSC','BYSC','DTSC','ENSC','AIIA','EDSC'],
    'H' => ['BNSC','KGSC','TRSC','NASC'],
];

$states = ['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara','FCT'];
$specialCommands = ['MMIA Lagos','NAIA Abuja','MAKIA Kano','PHIA Port Harcourt','Seme Border Command','Idiroko Border Command','Lagos Passport Command','NIS HQ Abuja'];
@endphp

{{-- The shared layout provides the <form>; do not nest another one here. --}}
<div class="hrm-tabs-wrap">
    <div class="entry-tabs-wrap" style="margin-bottom:0;">
        <div class="entry-tabs" id="entryTabs">
            @php $tabs = [
                ['cadre','fas fa-users','1. Cadre'],
                ['rank','fas fa-star','2. Rank'],
                ['zones','fas fa-map-marker-alt','3. Zones'],
                ['gender-cadre','fas fa-venus-mars','4. Gender by Cadre'],
                ['gender-zone','fas fa-globe','5. Gender by Zone'],
                ['gender-rank','fas fa-user-tag','6. Gender by Rank'],
                ['training','fas fa-chalkboard-teacher','7. Training'],
                ['records','fas fa-folder-open','8. Records'],
                ['registry','fas fa-envelope','9. Registry'],
                ['apu','fas fa-user-plus','10. APU'],
                ['recruitment','fas fa-user-check','11. Recruitment'],
                ['career','fas fa-chart-line','12. Career'],
                ['officer-promotion','fas fa-medal','13. Officer Promo'],
                ['upgrading-conversion','fas fa-exchange-alt','14. Upgr/Conv'],
                ['upgrading','fas fa-arrow-up','15. Upgrading'],
                ['promotion-eligibility','fas fa-clipboard-check','16. Promo Elig'],
                ['permission-study','fas fa-book-reader','17. Study'],
                ['pension','fas fa-hand-holding-usd','18. Pension'],
                ['discipline','fas fa-gavel','19. Discipline'],
                ['nimcos','fas fa-credit-card','20. NIMCOS'],
                ['general-report','fas fa-file-alt','21. General Report'],
                ['preview','fas fa-eye','22. Preview'],
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
                    1. Service Personnel Strength by Cadre
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th>CADRE</th><th style="width:130px;">MALE</th><th style="width:130px;">FEMALE</th><th style="width:130px;">TOTAL</th></tr>
                        </thead>
                        <tbody>
                            @foreach([['comptroller','Comptroller Cadre'],['superintendent','Superintendent Cadre'],['inspectorate','Inspectorate Cadre'],['assistant','Assistant Cadre']] as [$key,$label])
                            <tr>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="hrm[cadre][{{ $key }}][male]" class="ni hrm-cadre-m" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('hrm.cadre.'.$key.'.male') }}"></td>
                                <td><input type="number" name="hrm[cadre][{{ $key }}][female]" class="ni hrm-cadre-f" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('hrm.cadre.'.$key.'.female') }}"></td>
                                <td><input type="number" id="hrm-cadre-total-{{ $key }}" class="ni" readonly placeholder="0"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><input type="number" id="hrm-cadre-grand-male" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-cadre-grand-female" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-cadre-grand-total" class="ni" readonly placeholder="0"></td>
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
                    2. Personnel Strength by Rank
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
                                <td><input type="number" name="hrm[rank][{{ $key }}][male]" class="ni hrm-rank-m" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('hrm.rank.'.$key.'.male') }}"></td>
                                <td><input type="number" name="hrm[rank][{{ $key }}][female]" class="ni hrm-rank-f" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('hrm.rank.'.$key.'.female') }}"></td>
                                <td><input type="number" id="hrm-rank-total-{{ $key }}" class="ni" readonly placeholder="0"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><input type="number" id="hrm-rank-grand-male" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-rank-grand-female" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-rank-grand-total" class="ni" readonly placeholder="0"></td>
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

    <!-- TAB 3: Personnel Strength by Zones and Command -->
    <div class="tab-panel" id="tab-zones">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-map-marker-alt"></i></div>
                    3. Personnel Strength by Zones and Command
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th>ZONE</th>
                                <th>COMMAND</th>
                                <th style="width:110px;">COMPT. CADRE</th>
                                <th style="width:110px;">SUPERINTENDENT CADRE</th>
                                <th style="width:110px;">INSPECTORATE CADRE</th>
                                <th style="width:110px;">ASSISTANT CADRE</th>
                                <th style="width:110px;">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($zoneCommands as $zone => $commands)
                                @foreach($commands as $cIndex => $command)
                                @php $cmdKey = strtolower(str_replace(' ','_', $command)); @endphp
                                <tr>
                                    @if($cIndex === 0)
                                    <td rowspan="{{ count($commands) + 1 }}" style="font-weight:700;vertical-align:middle;">ZONE {{ $zone }}</td>
                                    @endif
                                    <td>{{ $command }}</td>
                                    <td><input type="number" name="hrm[zones][{{ strtolower($zone) }}][{{ $cmdKey }}][comptroller]" class="ni hrm-zone-comptroller" data-zone="{{ $zone }}" data-command="{{ $cmdKey }}" min="0" placeholder="0" value="{{ old('hrm.zones.'.strtolower($zone).'.'.$cmdKey.'.comptroller') }}"></td>
                                    <td><input type="number" name="hrm[zones][{{ strtolower($zone) }}][{{ $cmdKey }}][superintendent]" class="ni hrm-zone-superintendent" data-zone="{{ $zone }}" data-command="{{ $cmdKey }}" min="0" placeholder="0" value="{{ old('hrm.zones.'.strtolower($zone).'.'.$cmdKey.'.superintendent') }}"></td>
                                    <td><input type="number" name="hrm[zones][{{ strtolower($zone) }}][{{ $cmdKey }}][inspectorate]" class="ni hrm-zone-inspectorate" data-zone="{{ $zone }}" data-command="{{ $cmdKey }}" min="0" placeholder="0" value="{{ old('hrm.zones.'.strtolower($zone).'.'.$cmdKey.'.inspectorate') }}"></td>
                                    <td><input type="number" name="hrm[zones][{{ strtolower($zone) }}][{{ $cmdKey }}][assistant]" class="ni hrm-zone-assistant" data-zone="{{ $zone }}" data-command="{{ $cmdKey }}" min="0" placeholder="0" value="{{ old('hrm.zones.'.strtolower($zone).'.'.$cmdKey.'.assistant') }}"></td>
                                    <td><input type="number" id="hrm-zone-total-{{ $zone }}-{{ $cmdKey }}" class="ni" readonly placeholder="0"></td>
                                </tr>
                                @endforeach
                                <tr class="total-row" style="background:#f1f5f9;">
                                    <td><strong>ZONE {{ $zone }} TOTAL</strong></td>
                                    <td><input type="number" id="hrm-zone-subtotal-{{ $zone }}-comptroller" class="ni" readonly placeholder="0"></td>
                                    <td><input type="number" id="hrm-zone-subtotal-{{ $zone }}-superintendent" class="ni" readonly placeholder="0"></td>
                                    <td><input type="number" id="hrm-zone-subtotal-{{ $zone }}-inspectorate" class="ni" readonly placeholder="0"></td>
                                    <td><input type="number" id="hrm-zone-subtotal-{{ $zone }}-assistant" class="ni" readonly placeholder="0"></td>
                                    <td><input type="number" id="hrm-zone-subtotal-{{ $zone }}-total" class="ni" readonly placeholder="0"></td>
                                </tr>
                            @endforeach
                            <tr class="total-row">
                                <td colspan="2"><strong>GRAND TOTAL</strong></td>
                                <td><input type="number" id="hrm-zone-grand-comptroller" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-zone-grand-superintendent" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-zone-grand-inspectorate" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-zone-grand-assistant" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-zone-grand-total" class="ni" readonly placeholder="0"></td>
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

    <!-- TAB 4: Gender Distribution by Cadre -->
    <div class="tab-panel" id="tab-gender-cadre">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-venus-mars"></i></div>
                    4. Gender Distribution by Cadre
                </div>
            </div>
            <div class="card-body">
                <div class="fg" style="margin-bottom:14px;">
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">SELECT COMMANDS (ALL STATES &amp; SPECIAL COMMANDS)</label>
                    <select name="hrm[gender_cadre][command]" class="ni ni-select">
                        <option value="">Select Command</option>
                        <optgroup label="States">
                            @foreach($states as $s)
                            <option value="{{ $s }} State" {{ old('hrm.gender_cadre.command') == $s.' State' ? 'selected' : '' }}>{{ $s }} State</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Special Commands">
                            @foreach($specialCommands as $c)
                            <option value="{{ $c }}" {{ old('hrm.gender_cadre.command') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th>CADRES</th><th style="width:130px;">MALE</th><th style="width:130px;">FEMALE</th><th style="width:130px;">TOTAL</th></tr>
                        </thead>
                        <tbody>
                            @foreach([['comptroller','Comptroller Cadre'],['superintendent','Superintendent Cadre'],['inspectorate','Inspectorate Cadre'],['assistant','Assistant Cadre']] as [$key,$label])
                            <tr>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="hrm[gender_cadre][{{ $key }}][male]" class="ni hrm-gender-cadre-m" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('hrm.gender_cadre.'.$key.'.male') }}"></td>
                                <td><input type="number" name="hrm[gender_cadre][{{ $key }}][female]" class="ni hrm-gender-cadre-f" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('hrm.gender_cadre.'.$key.'.female') }}"></td>
                                <td><input type="number" id="hrm-gender-cadre-total-{{ $key }}" class="ni" readonly placeholder="0"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><input type="number" id="hrm-gender-cadre-grand-male" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-gender-cadre-grand-female" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-gender-cadre-grand-total" class="ni" readonly placeholder="0"></td>
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

    <!-- TAB 5: Gender Distribution by Zone -->
    <div class="tab-panel" id="tab-gender-zone">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-globe"></i></div>
                    5. Gender Distribution by Zone
                </div>
            </div>
            <div class="card-body">
                <div class="fg" style="margin-bottom:14px;">
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">SELECT ZONE (A–H)</label>
                    <select name="hrm[gender_zone][zone]" class="ni ni-select">
                        <option value="">Select Zone</option>
                        @foreach(['A','B','C','D','E','F','G','H'] as $z)
                        <option value="Zone {{ $z }}" {{ old('hrm.gender_zone.zone') == 'Zone '.$z ? 'selected' : '' }}>Zone {{ $z }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th>CADRES</th><th style="width:130px;">MALE</th><th style="width:130px;">FEMALE</th><th style="width:130px;">TOTAL</th></tr>
                        </thead>
                        <tbody>
                            @foreach([['comptroller','Comptroller Cadre'],['superintendent','Superintendent Cadre'],['inspectorate','Inspectorate Cadre'],['assistant','Assistant Cadre']] as [$key,$label])
                            <tr>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="hrm[gender_zone][{{ $key }}][male]" class="ni hrm-gender-zone-m" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('hrm.gender_zone.'.$key.'.male') }}"></td>
                                <td><input type="number" name="hrm[gender_zone][{{ $key }}][female]" class="ni hrm-gender-zone-f" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('hrm.gender_zone.'.$key.'.female') }}"></td>
                                <td><input type="number" id="hrm-gender-zone-total-{{ $key }}" class="ni" readonly placeholder="0"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><input type="number" id="hrm-gender-zone-grand-male" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-gender-zone-grand-female" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-gender-zone-grand-total" class="ni" readonly placeholder="0"></td>
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

    <!-- TAB 6: Gender Distribution by Rank -->
    <div class="tab-panel" id="tab-gender-rank">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-user-tag"></i></div>
                    6. Gender Distribution by Rank
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
                                <td><input type="number" name="hrm[gender_rank][{{ $key }}][male]" class="ni hrm-gender-rank-m" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('hrm.gender_rank.'.$key.'.male') }}"></td>
                                <td><input type="number" name="hrm[gender_rank][{{ $key }}][female]" class="ni hrm-gender-rank-f" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('hrm.gender_rank.'.$key.'.female') }}"></td>
                                <td><input type="number" id="hrm-gender-rank-total-{{ $key }}" class="ni" readonly placeholder="0"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><input type="number" id="hrm-gender-rank-grand-male" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-gender-rank-grand-female" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-gender-rank-grand-total" class="ni" readonly placeholder="0"></td>
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

    <!-- TAB 7: Training and Staff Development -->
    <div class="tab-panel" id="tab-training">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-chalkboard-teacher"></i></div>
                    7. Training and Staff Development
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th>S/N</th><th>ZONE</th><th>COMMAND</th><th>DESCRIPTION OF TRAINING</th><th>LOCATION</th><th style="width:120px;">NO. OF PARTICIPANTS</th><th>DURATION</th><th></th></tr>
                        </thead>
                        <tbody id="hrmTrainingBody">
                            <tr class="data-row">
                                <td>1</td>
                                <td><input type="text" name="hrm[training][0][zone]" class="ni" placeholder="Zone"></td>
                                <td><input type="text" name="hrm[training][0][command]" class="ni" placeholder="Command"></td>
                                <td><input type="text" name="hrm[training][0][description]" class="ni" placeholder="Description"></td>
                                <td><input type="text" name="hrm[training][0][location]" class="ni" placeholder="Location"></td>
                                <td><input type="number" name="hrm[training][0][participants]" class="ni hrm-training-participants" min="0" placeholder="0" value="{{ old('hrm.training.0.participants') }}"></td>
                                <td><input type="text" name="hrm[training][0][duration]" class="ni" placeholder="e.g. 3 days"></td>
                                <td><button type="button" class="btn-nis btn-ghost btn-sm hrm-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="5"><strong>TOTAL</strong></td>
                                <td><input type="number" id="hrm-training-grand-total" class="ni" readonly placeholder="0"></td>
                                <td colspan="2"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="hrmAddTrainingRow"><i class="fas fa-plus"></i> Add Row</button>
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

    <!-- TAB 8: Records Unit (Statistics) -->
    <div class="tab-panel" id="tab-records">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-folder-open"></i></div>
                    8. Records Unit (Statistics)
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th style="width:50px;">S/N</th><th>RECORDS</th><th style="width:160px;">NUMBER</th></tr>
                        </thead>
                        <tbody>
                            @php $records = [['permission_to_marry','Permission to Marry'],['change_of_name','Change of Name'],['change_of_next_of_kin','Change of Next of Kin'],['change_of_state_of_origin','Change of State of Origin'],['updating_of_records','Updating of Records']]; @endphp
                            @foreach($records as $i => [$key,$label])
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="hrm[records][{{ $key }}]" class="ni hrm-records-input" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('hrm.records.'.$key) }}"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td></td>
                                <td><strong>TOTAL</strong></td>
                                <td><input type="number" id="hrm-records-grand-total" class="ni" readonly placeholder="0"></td>
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

    <!-- TAB 9: Registry Activities (Statistics) -->
    <div class="tab-panel" id="tab-registry">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-envelope"></i></div>
                    9. Registry Activities (Statistics)
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th style="width:50px;">S/N</th><th>ACTIVITIES</th><th style="width:160px;">NUMBER</th></tr>
                        </thead>
                        <tbody>
                            @php $registry = [['incoming_personal_mails','Incoming personal mails'],['outgoing_mails','Outgoing mails'],['incoming_policy_mails','Incoming policy mails'],['pending_mails','Pending mails'],['incoming_personal_files','Incoming Personal files'],['outgoing_policy_files','Outgoing policy files'],['incoming_policy_files','Incoming Policy files'],['outgoing_policy_files','Outgoing Policy files']]; @endphp
                            @foreach($registry as $i => [$key,$label])
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="hrm[registry][{{ $key }}]" class="ni hrm-registry-input" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('hrm.registry.'.$key) }}"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td></td>
                                <td><strong>TOTAL</strong></td>
                                <td><input type="number" id="hrm-registry-grand-total" class="ni" readonly placeholder="0"></td>
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

    <!-- TAB 10: Appointment, Promotion and Upgrading (APU) -->
    <div class="tab-panel" id="tab-apu">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-user-plus"></i></div>
                    10. Appointment, Promotion and Upgrading (APU)
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th>S/N</th><th>RANKS</th><th style="width:160px;">TOTAL NUMBER</th><th></th></tr>
                        </thead>
                        <tbody id="hrmApuBody">
                            <tr class="data-row">
                                <td>1</td>
                                <td><input type="text" name="hrm[apu][0][rank]" class="ni" placeholder="Rank"></td>
                                <td><input type="number" name="hrm[apu][0][total]" class="ni hrm-apu-total" min="0" placeholder="0" value="{{ old('hrm.apu.0.total') }}"></td>
                                <td><button type="button" class="btn-nis btn-ghost btn-sm hrm-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td><input type="number" id="hrm-apu-grand-total" class="ni" readonly placeholder="0"></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="hrmAddApuRow"><i class="fas fa-plus"></i> Add Row</button>
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

    <!-- TAB 11: Recruitment (if any) -->
    <div class="tab-panel" id="tab-recruitment">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-user-check"></i></div>
                    11. Recruitment (if any)
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th>S/N</th><th>RANKS</th><th style="width:160px;">TOTAL NUMBER</th><th></th></tr>
                        </thead>
                        <tbody id="hrmRecruitmentBody">
                            <tr class="data-row">
                                <td>1</td>
                                <td><input type="text" name="hrm[recruitment][0][rank]" class="ni" placeholder="Rank"></td>
                                <td><input type="number" name="hrm[recruitment][0][total]" class="ni hrm-recruitment-total" min="0" placeholder="0" value="{{ old('hrm.recruitment.0.total') }}"></td>
                                <td><button type="button" class="btn-nis btn-ghost btn-sm hrm-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td><input type="number" id="hrm-recruitment-grand-total" class="ni" readonly placeholder="0"></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="hrmAddRecruitmentRow"><i class="fas fa-plus"></i> Add Row</button>
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

    <!-- TAB 12: Career Progression -->
    <div class="tab-panel" id="tab-career">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-chart-line"></i></div>
                    12. Career Progression
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th>S/N</th><th>RANKS</th><th style="width:160px;">TOTAL NUMBER</th><th></th></tr>
                        </thead>
                        <tbody id="hrmCareerBody">
                            <tr class="data-row">
                                <td>1</td>
                                <td><input type="text" name="hrm[career][0][rank]" class="ni" placeholder="Rank"></td>
                                <td><input type="number" name="hrm[career][0][total]" class="ni hrm-career-total" min="0" placeholder="0" value="{{ old('hrm.career.0.total') }}"></td>
                                <td><button type="button" class="btn-nis btn-ghost btn-sm hrm-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td><input type="number" id="hrm-career-grand-total" class="ni" readonly placeholder="0"></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="hrmAddCareerRow"><i class="fas fa-plus"></i> Add Row</button>
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

    <!-- TAB 13: Officer Promotion (Stats) -->
    <div class="tab-panel" id="tab-officer-promotion">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-medal"></i></div>
                    13. Officer Promotion (Stats)
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th style="width:50px;">S/N</th><th>PROMOTION</th><th style="width:160px;">TOTAL</th></tr>
                        </thead>
                        <tbody>
                            @foreach($promotionRows as $i => [$key,$label])
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="hrm[officer_promotion][{{ $key }}]" class="ni hrm-officer-promotion-input" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('hrm.officer_promotion.'.$key) }}"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td></td>
                                <td><strong>TOTAL</strong></td>
                                <td><input type="number" id="hrm-officer-promotion-grand-total" class="ni" readonly placeholder="0"></td>
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

    <!-- TAB 14: Upgrading and Conversion -->
    <div class="tab-panel" id="tab-upgrading-conversion">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-exchange-alt"></i></div>
                    14. Upgrading and Conversion
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th>S/N</th><th>RANK</th><th style="width:160px;">TOTAL</th><th></th></tr>
                        </thead>
                        <tbody id="hrmUpgradingConversionBody">
                            <tr class="data-row">
                                <td>1</td>
                                <td><input type="text" name="hrm[upgrading_conversion][0][rank]" class="ni" placeholder="Rank"></td>
                                <td><input type="number" name="hrm[upgrading_conversion][0][total]" class="ni hrm-upgrading-conversion-total" min="0" placeholder="0" value="{{ old('hrm.upgrading_conversion.0.total') }}"></td>
                                <td><button type="button" class="btn-nis btn-ghost btn-sm hrm-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td><input type="number" id="hrm-upgrading-conversion-grand-total" class="ni" readonly placeholder="0"></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="hrmAddUpgradingConversionRow"><i class="fas fa-plus"></i> Add Row</button>
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

    <!-- TAB 15: Upgrading -->
    <div class="tab-panel" id="tab-upgrading">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-arrow-up"></i></div>
                    15. Upgrading
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th style="width:50px;">S/N</th><th>RANK</th><th style="width:160px;">TOTAL</th></tr>
                        </thead>
                        <tbody>
                            @php $upgrading = [['ii','Inspector of Immigration [II]'],['aii','Assistant Inspector of Immigration [AII]'],['ia1','Immigration Assistant [IA 1]'],['ia2','Immigration Assistant [IA 2]'],['ia3','Immigration Assistant [IA 3]']]; @endphp
                            @foreach($upgrading as $i => [$key,$label])
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="hrm[upgrading][{{ $key }}]" class="ni hrm-upgrading-input" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('hrm.upgrading.'.$key) }}"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td></td>
                                <td><strong>TOTAL</strong></td>
                                <td><input type="number" id="hrm-upgrading-grand-total" class="ni" readonly placeholder="0"></td>
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

    <!-- TAB 16: Promotion/Eligibility -->
    <div class="tab-panel" id="tab-promotion-eligibility">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-clipboard-check"></i></div>
                    16. Promotion/Eligibility
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th style="width:50px;">S/N</th><th>PROMOTION ELIGIBILITY</th><th style="width:160px;">TOTAL</th></tr>
                        </thead>
                        <tbody>
                            @foreach($promotionRows as $i => [$key,$label])
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="hrm[promotion_eligibility][{{ $key }}]" class="ni hrm-promotion-eligibility-input" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('hrm.promotion_eligibility.'.$key) }}"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td></td>
                                <td><strong>TOTAL</strong></td>
                                <td><input type="number" id="hrm-promotion-eligibility-grand-total" class="ni" readonly placeholder="0"></td>
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

    <!-- TAB 17: Permission to Study -->
    <div class="tab-panel" id="tab-permission-study">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-book-reader"></i></div>
                    17. Permission to Study
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th style="width:50px;">S/N</th><th>APPLICATIONS</th><th style="width:160px;">NUMBER</th></tr>
                        </thead>
                        <tbody>
                            @php $study = [['received','Applications Received'],['approved','Applications Approved'],['rejected','Applications Rejected'],['pending','Applications Pending']]; @endphp
                            @foreach($study as $i => [$key,$label])
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $label }}</td>
                                <td><input type="number" name="hrm[study][{{ $key }}]" class="ni hrm-study-input" data-row="{{ $key }}" min="0" placeholder="0" value="{{ old('hrm.study.'.$key) }}"></td>
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

    <!-- TAB 18: Pension -->
    <div class="tab-panel" id="tab-pension">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-hand-holding-usd"></i></div>
                    18. Pension
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th>S/N</th><th>RANK</th><th style="width:100px;">DEATH</th><th style="width:100px;">MANDATORY RETIREMENT</th><th style="width:100px;">VOLUNTARY RETIREMENT</th><th style="width:100px;">RESIGNATION</th><th style="width:100px;">TOTAL</th><th></th></tr>
                        </thead>
                        <tbody id="hrmPensionBody">
                            <tr class="data-row">
                                <td>1</td>
                                <td><input type="text" name="hrm[pension][0][rank]" class="ni" placeholder="Rank"></td>
                                <td><input type="number" name="hrm[pension][0][death]" class="ni hrm-pension-col" data-col="death" min="0" placeholder="0" value="{{ old('hrm.pension.0.death') }}"></td>
                                <td><input type="number" name="hrm[pension][0][mandatory_retirement]" class="ni hrm-pension-col" data-col="mandatory_retirement" min="0" placeholder="0" value="{{ old('hrm.pension.0.mandatory_retirement') }}"></td>
                                <td><input type="number" name="hrm[pension][0][voluntary_retirement]" class="ni hrm-pension-col" data-col="voluntary_retirement" min="0" placeholder="0" value="{{ old('hrm.pension.0.voluntary_retirement') }}"></td>
                                <td><input type="number" name="hrm[pension][0][resignation]" class="ni hrm-pension-col" data-col="resignation" min="0" placeholder="0" value="{{ old('hrm.pension.0.resignation') }}"></td>
                                <td><input type="number" id="hrm-pension-row-total-0" class="ni" readonly placeholder="0"></td>
                                <td><button type="button" class="btn-nis btn-ghost btn-sm hrm-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td><input type="number" id="hrm-pension-grand-death" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-pension-grand-mandatory_retirement" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-pension-grand-voluntary_retirement" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-pension-grand-resignation" class="ni" readonly placeholder="0"></td>
                                <td><input type="number" id="hrm-pension-grand-total" class="ni" readonly placeholder="0"></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="hrmAddPensionRow"><i class="fas fa-plus"></i> Add Row</button>
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

    <!-- TAB 19: Discipline and Award -->
    <div class="tab-panel" id="tab-discipline">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-gavel"></i></div>
                    19. Discipline and Award
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">S/N</th>
                                <th>CASES HANDLED</th>
                                @foreach($disciplineRankCols as $col)<th style="width:60px;">{{ $col }}</th>@endforeach
                                <th style="width:80px;">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $sn = 1; @endphp
                            @foreach($disciplineRows as $rowKey => $rowLabel)
                            <tr>
                                <td>{{ $sn++ }}</td>
                                <td>{{ $rowLabel }}</td>
                                @foreach($disciplineRankCols as $col)
                                <td><input type="number" name="hrm[discipline][{{ $rowKey }}][{{ $col }}]" class="ni hrm-discipline-cell" data-row="{{ $rowKey }}" data-col="{{ $col }}" min="0" placeholder="0" value="{{ old('hrm.discipline.'.$rowKey.'.'.$col) }}"></td>
                                @endforeach
                                <td><input type="number" id="hrm-discipline-row-total-{{ $rowKey }}" class="ni" readonly placeholder="0"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td></td>
                                <td><strong>TOTAL</strong></td>
                                @foreach($disciplineRankCols as $col)
                                <td><input type="number" id="hrm-discipline-col-total-{{ $col }}" class="ni" readonly placeholder="0"></td>
                                @endforeach
                                <td><input type="number" id="hrm-discipline-grand-total" class="ni" readonly placeholder="0"></td>
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

    <!-- TAB 20: NIMCOS -->
    <div class="tab-panel" id="tab-nimcos">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-credit-card"></i></div>
                    20. NIMCOS
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th style="width:50px;">S/N</th><th>ACTIVITIES</th><th style="width:180px;">NUMBER/AMOUNT</th></tr>
                        </thead>
                        <tbody>
                            @php $nimcos = [['total_membership','Total Membership'],['registrations','No. of registrations for the Period'],['withdrawals','No. of officers that withdrew from the period'],['credit_facility','Credit facility granted'],['conventional_loan','No. of officers that benefited from the conventional loan'],['soft_loan','No. of officers that benefited from the soft loan'],['special_projects','Special Projects']]; @endphp
                            @foreach($nimcos as $i => [$key,$label])
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $label }}</td>
                                <td><input type="text" name="hrm[nimcos][{{ $key }}]" class="ni hrm-nimcos-input" data-row="{{ $key }}" placeholder="0" value="{{ old('hrm.nimcos.'.$key) }}"></td>
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

    <!-- TAB 21: GENERAL REPORT -->
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
    function valN(el) { return parseFloat(el?.value || 0) || 0; }
    function set(id, v) { const el = document.getElementById(id); if (el) el.value = v; }

    /* Section 1: Cadre */
    function recomputeCadre() {
        let mT = 0, fT = 0;
        document.querySelectorAll('.hrm-cadre-m').forEach(m => {
            const row = m.dataset.row;
            const mV = val(m), fV = val(document.querySelector(`.hrm-cadre-f[data-row="${row}"]`));
            set(`hrm-cadre-total-${row}`, mV + fV);
            mT += mV; fT += fV;
        });
        set('hrm-cadre-grand-male', mT);
        set('hrm-cadre-grand-female', fT);
        set('hrm-cadre-grand-total', mT + fT);
    }

    /* Section 2: Rank */
    function recomputeRank() {
        let mT = 0, fT = 0;
        document.querySelectorAll('.hrm-rank-m').forEach(m => {
            const row = m.dataset.row;
            const mV = val(m), fV = val(document.querySelector(`.hrm-rank-f[data-row="${row}"]`));
            set(`hrm-rank-total-${row}`, mV + fV);
            mT += mV; fT += fV;
        });
        set('hrm-rank-grand-male', mT);
        set('hrm-rank-grand-female', fT);
        set('hrm-rank-grand-total', mT + fT);
    }

    /* Section 3: Zones */
    function recomputeZones() {
        const cols = ['comptroller','superintendent','inspectorate','assistant'];
        const zones = ['A','B','C','D','E','F','G','H'];
        let grand = {};
        cols.forEach(c => grand[c] = 0);
        let grandTotal = 0;
        zones.forEach(z => {
            let zTotals = {};
            cols.forEach(c => zTotals[c] = 0);
            document.querySelectorAll(`.hrm-zone-comptroller[data-zone="${z}"], .hrm-zone-superintendent[data-zone="${z}"], .hrm-zone-inspectorate[data-zone="${z}"], .hrm-zone-assistant[data-zone="${z}"]`).forEach(inp => {
                const cmd = inp.dataset.command;
                const col = inp.classList.contains('hrm-zone-comptroller') ? 'comptroller'
                    : inp.classList.contains('hrm-zone-superintendent') ? 'superintendent'
                    : inp.classList.contains('hrm-zone-inspectorate') ? 'inspectorate' : 'assistant';
                zTotals[col] += val(inp);
            });
            let zRowTotal = 0;
            document.querySelectorAll(`[id^="hrm-zone-total-${z}-"]`).forEach(el => {
                const cmd = el.id.replace(`hrm-zone-total-${z}-`, '');
                const total = cols.reduce((s, c) => s + val(document.querySelector(`.hrm-zone-${c}[data-zone="${z}"][data-command="${cmd}"]`)), 0);
                set(el.id, total);
                zRowTotal += total;
            });
            cols.forEach(c => {
                set(`hrm-zone-subtotal-${z}-${c}`, zTotals[c]);
                grand[c] += zTotals[c];
            });
            set(`hrm-zone-subtotal-${z}-total`, zRowTotal);
            grandTotal += zRowTotal;
        });
        cols.forEach(c => set(`hrm-zone-grand-${c}`, grand[c]));
        set('hrm-zone-grand-total', grandTotal);
    }

    /* Sections 4-6: Gender */
    function recomputeGender(prefix, mClass, fClass) {
        let mT = 0, fT = 0;
        document.querySelectorAll(mClass).forEach(m => {
            const row = m.dataset.row;
            const f = document.querySelector(`${fClass}[data-row="${row}"]`);
            const mV = val(m), fV = val(f);
            set(`hrm-${prefix}-total-${row}`, mV + fV);
            mT += mV; fT += fV;
        });
        set(`hrm-${prefix}-grand-male`, mT);
        set(`hrm-${prefix}-grand-female`, fT);
        set(`hrm-${prefix}-grand-total`, mT + fT);
    }

    /* Section 7: Training */
    function recomputeTraining() {
        let total = 0;
        document.querySelectorAll('.hrm-training-participants').forEach(el => total += val(el));
        set('hrm-training-grand-total', total);
    }
    function addTrainingRow() {
        const tbody = document.getElementById('hrmTrainingBody');
        const rows = tbody.querySelectorAll('.data-row');
        const idx = rows.length;
        const tr = document.createElement('tr'); tr.className = 'data-row';
        tr.innerHTML = `
            <td></td>
            <td><input type="text" name="hrm[training][${idx}][zone]" class="ni" placeholder="Zone"></td>
            <td><input type="text" name="hrm[training][${idx}][command]" class="ni" placeholder="Command"></td>
            <td><input type="text" name="hrm[training][${idx}][description]" class="ni" placeholder="Description"></td>
            <td><input type="text" name="hrm[training][${idx}][location]" class="ni" placeholder="Location"></td>
            <td><input type="number" name="hrm[training][${idx}][participants]" class="ni hrm-training-participants" min="0" placeholder="0"></td>
            <td><input type="text" name="hrm[training][${idx}][duration]" class="ni" placeholder="e.g. 3 days"></td>
            <td><button type="button" class="btn-nis btn-ghost btn-sm hrm-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>
        `;
        tbody.insertBefore(tr, tbody.lastElementChild);
        renumberAndRecompute(tbody);
    }

    /* Sections 8-9: Records / Registry */
    function recomputeSingleCol(inputClass, grandId) {
        let total = 0;
        document.querySelectorAll(inputClass).forEach(el => total += val(el));
        set(grandId, total);
    }

    /* Sections 10-12: APU / Recruitment / Career */
    function recomputeDynamicTotal(inputClass, grandId) {
        let total = 0;
        document.querySelectorAll(inputClass).forEach(el => total += val(el));
        set(grandId, total);
    }
    function addDynamicRow(bodyId, prefix, cols, totalClass) {
        const tbody = document.getElementById(bodyId);
        const rows = tbody.querySelectorAll('.data-row');
        const idx = rows.length;
        const tr = document.createElement('tr'); tr.className = 'data-row';
        let html = `<td></td>`;
        cols.forEach(c => {
            if (c === 'rank') html += `<td><input type="text" name="${prefix}[${idx}][rank]" class="ni" placeholder="Rank"></td>`;
            else if (c === 'total') html += `<td><input type="number" name="${prefix}[${idx}][total]" class="ni ${totalClass}" min="0" placeholder="0"></td>`;
        });
        html += `<td><button type="button" class="btn-nis btn-ghost btn-sm hrm-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>`;
        tr.innerHTML = html;
        tbody.insertBefore(tr, tbody.lastElementChild);
        renumberAndRecompute(tbody);
    }

    /* Sections 13/16: Promotion matrices */
    function recomputePromotion(inputClass, grandId) {
        let total = 0;
        document.querySelectorAll(inputClass).forEach(el => total += val(el));
        set(grandId, total);
    }

    /* Section 15: Upgrading */
    function recomputeUpgrading() {
        let total = 0;
        document.querySelectorAll('.hrm-upgrading-input').forEach(el => total += val(el));
        set('hrm-upgrading-grand-total', total);
    }

    /* Section 18: Pension */
    function recomputePension() {
        const cols = ['death','mandatory_retirement','voluntary_retirement','resignation'];
        let colTotals = {}; cols.forEach(c => colTotals[c] = 0);
        let grandTotal = 0;
        document.querySelectorAll('#hrmPensionBody .data-row').forEach((row, idx) => {
            let rowTotal = 0;
            cols.forEach(c => {
                const v = val(row.querySelector(`[name="hrm[pension][${idx}][${c}]"]`));
                rowTotal += v; colTotals[c] += v;
            });
            set(`hrm-pension-row-total-${idx}`, rowTotal);
            grandTotal += rowTotal;
        });
        cols.forEach(c => set(`hrm-pension-grand-${c}`, colTotals[c]));
        set('hrm-pension-grand-total', grandTotal);
    }
    function addPensionRow() {
        const tbody = document.getElementById('hrmPensionBody');
        const rows = tbody.querySelectorAll('.data-row');
        const idx = rows.length;
        const tr = document.createElement('tr'); tr.className = 'data-row';
        tr.innerHTML = `
            <td></td>
            <td><input type="text" name="hrm[pension][${idx}][rank]" class="ni" placeholder="Rank"></td>
            <td><input type="number" name="hrm[pension][${idx}][death]" class="ni hrm-pension-col" data-col="death" min="0" placeholder="0"></td>
            <td><input type="number" name="hrm[pension][${idx}][mandatory_retirement]" class="ni hrm-pension-col" data-col="mandatory_retirement" min="0" placeholder="0"></td>
            <td><input type="number" name="hrm[pension][${idx}][voluntary_retirement]" class="ni hrm-pension-col" data-col="voluntary_retirement" min="0" placeholder="0"></td>
            <td><input type="number" name="hrm[pension][${idx}][resignation]" class="ni hrm-pension-col" data-col="resignation" min="0" placeholder="0"></td>
            <td><input type="number" id="hrm-pension-row-total-${idx}" class="ni" readonly placeholder="0"></td>
            <td><button type="button" class="btn-nis btn-ghost btn-sm hrm-remove-row" style="color:var(--color-danger);padding:2px 6px;"><i class="fas fa-times"></i></button></td>
        `;
        tbody.insertBefore(tr, tbody.lastElementChild);
        renumberAndRecompute(tbody);
    }

    /* Section 19: Discipline */
    function recomputeDiscipline() {
        const cols = ['acg','cis','dci','aci','csi','si','dsi','asi1','asi2','ii','aii','cia','sia','ia1','ia2','ia3'];
        let colTotals = {}; cols.forEach(c => colTotals[c] = 0);
        let grandTotal = 0;
        document.querySelectorAll('.hrm-discipline-cell').forEach(cell => {
            const c = cell.dataset.col;
            const v = val(cell);
            colTotals[c] += v;
        });
        document.querySelectorAll('.hrm-discipline-cell').forEach(cell => {
            const row = cell.dataset.row;
            let rowTotal = 0;
            document.querySelectorAll(`.hrm-discipline-cell[data-row="${row}"]`).forEach(c => rowTotal += val(c));
            set(`hrm-discipline-row-total-${row}`, rowTotal);
        });
        cols.forEach(c => {
            set(`hrm-discipline-col-total-${c}`, colTotals[c]);
            grandTotal += colTotals[c];
        });
        set('hrm-discipline-grand-total', grandTotal);
    }

    /* Row numbering and recompute after add/remove */
    function renumberAndRecompute(tbody) {
        tbody.querySelectorAll('.data-row').forEach((row, i) => {
            row.querySelector('td:first-child').textContent = i + 1;
            row.querySelectorAll('input, select').forEach(el => {
                const name = el.name.replace(/\[\d+\]/, `[${i}]`);
                el.name = name;
                if (el.id && el.id.match(/hrm-pension-row-total-\d+/)) el.id = `hrm-pension-row-total-${i}`;
            });
        });
        recomputeAll();
    }

    /* Add/remove row wiring */
    document.getElementById('hrmAddTrainingRow')?.addEventListener('click', addTrainingRow);
    document.getElementById('hrmAddApuRow')?.addEventListener('click', () => addDynamicRow('hrmApuBody', 'hrm[apu]', ['rank','total'], 'hrm-apu-total'));
    document.getElementById('hrmAddRecruitmentRow')?.addEventListener('click', () => addDynamicRow('hrmRecruitmentBody', 'hrm[recruitment]', ['rank','total'], 'hrm-recruitment-total'));
    document.getElementById('hrmAddCareerRow')?.addEventListener('click', () => addDynamicRow('hrmCareerBody', 'hrm[career]', ['rank','total'], 'hrm-career-total'));
    document.getElementById('hrmAddUpgradingConversionRow')?.addEventListener('click', () => addDynamicRow('hrmUpgradingConversionBody', 'hrm[upgrading_conversion]', ['rank','total'], 'hrm-upgrading-conversion-total'));
    document.getElementById('hrmAddPensionRow')?.addEventListener('click', addPensionRow);

    document.addEventListener('click', e => {
        const btn = e.target.closest('.hrm-remove-row');
        if (!btn) return;
        const row = btn.closest('tr');
        const tbody = row.closest('tbody');
        if (tbody.querySelectorAll('.data-row').length <= 1) { alert('At least one row is required.'); return; }
        row.remove();
        renumberAndRecompute(tbody);
    });

    /* Master recompute */
    function recomputeAll() {
        recomputeCadre();
        recomputeRank();
        recomputeZones();
        recomputeGender('gender-cadre', '.hrm-gender-cadre-m', '.hrm-gender-cadre-f');
        recomputeGender('gender-zone', '.hrm-gender-zone-m', '.hrm-gender-zone-f');
        recomputeGender('gender-rank', '.hrm-gender-rank-m', '.hrm-gender-rank-f');
        recomputeTraining();
        recomputeSingleCol('.hrm-records-input', 'hrm-records-grand-total');
        recomputeSingleCol('.hrm-registry-input', 'hrm-registry-grand-total');
        recomputeDynamicTotal('.hrm-apu-total', 'hrm-apu-grand-total');
        recomputeDynamicTotal('.hrm-recruitment-total', 'hrm-recruitment-grand-total');
        recomputeDynamicTotal('.hrm-career-total', 'hrm-career-grand-total');
        recomputePromotion('.hrm-officer-promotion-input', 'hrm-officer-promotion-grand-total');
        recomputeDynamicTotal('.hrm-upgrading-conversion-total', 'hrm-upgrading-conversion-grand-total');
        recomputeUpgrading();
        recomputePromotion('.hrm-promotion-eligibility-input', 'hrm-promotion-eligibility-grand-total');
        recomputePension();
        recomputeDiscipline();
        buildPreview();
    }

    /* Section 21: General Report */
    function addReformRow() {
        const tbody = document.getElementById('reforms-body');
        const nextIndex = tbody.children.length;
        const sn = nextIndex + 1;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="reform-sn">${sn}</td>
            <td><input type="text" name="reforms_innovations[${nextIndex}][title]" class="ni"></td>
            <td><input type="date" name="reforms_innovations[${nextIndex}][date]" class="ni"></td>
        `;
        tbody.appendChild(tr);
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
        document.querySelectorAll('.hrm-cadre-m').forEach(m => {
            const row = m.dataset.row;
            const label = m.closest('tr').querySelector('td').textContent;
            cadreRows.push([
                label,
                getValById(`hrm-cadre-total-${row}`),
                `M: ${m.value || '0'}, F: ${document.querySelector(`.hrm-cadre-f[data-row="${row}"]`)?.value || '0'}`
            ]);
        });
        cadreRows.push(['<strong>Grand Total</strong>', getValById('hrm-cadre-grand-total'), `M: ${getValById('hrm-cadre-grand-male')}, F: ${getValById('hrm-cadre-grand-female')}`]);
        html += previewSectionTitle(1, 'Service Personnel Strength by Cadre');
        html += previewTable(cadreRows, ['Cadre', 'Total', 'Breakdown']);

        /* 2. Rank */
        let rankRows = [];
        document.querySelectorAll('.hrm-rank-m').forEach(m => {
            const row = m.dataset.row;
            const label = m.closest('tr').querySelector('td').textContent;
            rankRows.push([
                label,
                getValById(`hrm-rank-total-${row}`),
                `M: ${m.value || '0'}, F: ${document.querySelector(`.hrm-rank-f[data-row="${row}"]`)?.value || '0'}`
            ]);
        });
        rankRows.push(['<strong>Grand Total</strong>', getValById('hrm-rank-grand-total'), `M: ${getValById('hrm-rank-grand-male')}, F: ${getValById('hrm-rank-grand-female')}`]);
        html += previewSectionTitle(2, 'Personnel Strength by Rank');
        html += previewTable(rankRows, ['Rank', 'Total', 'Breakdown']);

        /* 3. Zones */
        let zoneRows = [];
        document.querySelectorAll('#tab-zones .nis-table tbody tr').forEach(tr => {
            const cells = Array.from(tr.querySelectorAll('td'));
            if (cells.length < 7) return;
            const zone = cells[0].textContent.trim();
            const command = cells[1].textContent.trim();
            const vals = cells.slice(2, 6).map(c => c.querySelector('input')?.value || '0');
            const total = cells[6].querySelector('input')?.value || '0';
            if (zone && command) zoneRows.push([zone, command, vals[0], vals[1], vals[2], vals[3], total]);
        });
        html += previewSectionTitle(3, 'Personnel Strength by Zones and Command');
        html += previewTable(zoneRows, ['Zone', 'Command', 'Compt.', 'Sup.', 'Insp.', 'Asst.', 'Total']);
        html += previewTable([
            ['<strong>Grand Total</strong>', getValById('hrm-zone-grand-comptroller'), getValById('hrm-zone-grand-superintendent'), getValById('hrm-zone-grand-inspectorate'), getValById('hrm-zone-grand-assistant'), getValById('hrm-zone-grand-total')]
        ], ['', 'Compt.', 'Sup.', 'Insp.', 'Asst.', 'Total']);

        /* 4. Gender by Cadre */
        let gcRows = [];
        document.querySelectorAll('.hrm-gender-cadre-m').forEach(m => {
            const row = m.dataset.row;
            const label = m.closest('tr').querySelector('td').textContent;
            gcRows.push([label, getValById(`hrm-gender-cadre-total-${row}`), `M: ${m.value || '0'}, F: ${document.querySelector(`.hrm-gender-cadre-f[data-row="${row}"]`)?.value || '0'}`]);
        });
        gcRows.push(['<strong>Grand Total</strong>', getValById('hrm-gender-cadre-grand-total'), `M: ${getValById('hrm-gender-cadre-grand-male')}, F: ${getValById('hrm-gender-cadre-grand-female')}`]);
        html += previewSectionTitle(4, 'Gender Distribution by Cadre');
        html += `<p style="font-size:.82rem;color:var(--gray-600);margin-bottom:8px;">Command: <strong>${getVal('hrm[gender_cadre][command]') || '—'}</strong></p>`;
        html += previewTable(gcRows, ['Cadre', 'Total', 'Breakdown']);

        /* 5. Gender by Zone */
        let gzRows = [];
        document.querySelectorAll('.hrm-gender-zone-m').forEach(m => {
            const row = m.dataset.row;
            const label = m.closest('tr').querySelector('td').textContent;
            gzRows.push([label, getValById(`hrm-gender-zone-total-${row}`), `M: ${m.value || '0'}, F: ${document.querySelector(`.hrm-gender-zone-f[data-row="${row}"]`)?.value || '0'}`]);
        });
        gzRows.push(['<strong>Grand Total</strong>', getValById('hrm-gender-zone-grand-total'), `M: ${getValById('hrm-gender-zone-grand-male')}, F: ${getValById('hrm-gender-zone-grand-female')}`]);
        html += previewSectionTitle(5, 'Gender Distribution by Zone');
        html += `<p style="font-size:.82rem;color:var(--gray-600);margin-bottom:8px;">Zone: <strong>${getVal('hrm[gender_zone][zone]') || '—'}</strong></p>`;
        html += previewTable(gzRows, ['Cadre', 'Total', 'Breakdown']);

        /* 6. Gender by Rank */
        let grRows = [];
        document.querySelectorAll('.hrm-gender-rank-m').forEach(m => {
            const row = m.dataset.row;
            const label = m.closest('tr').querySelector('td').textContent;
            grRows.push([label, getValById(`hrm-gender-rank-total-${row}`), `M: ${m.value || '0'}, F: ${document.querySelector(`.hrm-gender-rank-f[data-row="${row}"]`)?.value || '0'}`]);
        });
        grRows.push(['<strong>Grand Total</strong>', getValById('hrm-gender-rank-grand-total'), `M: ${getValById('hrm-gender-rank-grand-male')}, F: ${getValById('hrm-gender-rank-grand-female')}`]);
        html += previewSectionTitle(6, 'Gender Distribution by Rank');
        html += previewTable(grRows, ['Rank', 'Total', 'Breakdown']);

        /* 7. Training */
        let trainRows = [];
        document.querySelectorAll('#hrmTrainingBody .data-row').forEach(row => {
            const inputs = row.querySelectorAll('input');
            const vals = Array.from(inputs).map(i => i.value || '—');
            if (vals.some(v => v !== '—')) trainRows.push(vals);
        });
        html += previewSectionTitle(7, 'Training and Staff Development');
        html += previewTable(trainRows, ['Zone', 'Command', 'Description', 'Location', 'Participants', 'Duration']);
        html += previewTable([['<strong>Total Participants</strong>', getValById('hrm-training-grand-total')]], ['', 'Total']);

        /* 8. Records */
        let recordsRows = [];
        document.querySelectorAll('.hrm-records-input').forEach(el => {
            recordsRows.push([el.closest('tr').querySelector('td:nth-child(2)').textContent, el.value || '0']);
        });
        html += previewSectionTitle(8, 'Records Unit (Statistics)');
        html += previewTable(recordsRows, ['Record', 'Number']);
        html += previewTable([['<strong>Total</strong>', getValById('hrm-records-grand-total')]], ['', 'Total']);

        /* 9. Registry */
        let registryRows = [];
        document.querySelectorAll('.hrm-registry-input').forEach(el => {
            registryRows.push([el.closest('tr').querySelector('td:nth-child(2)').textContent, el.value || '0']);
        });
        html += previewSectionTitle(9, 'Registry Activities (Statistics)');
        html += previewTable(registryRows, ['Activity', 'Number']);
        html += previewTable([['<strong>Total</strong>', getValById('hrm-registry-grand-total')]], ['', 'Total']);

        /* 10-12. Dynamic tables */
        function dynamicRows(bodySelector, cols) {
            let rows = [];
            document.querySelectorAll(bodySelector).forEach(row => {
                const inputs = row.querySelectorAll('input');
                const vals = Array.from(inputs).map(i => i.value || '—');
                if (vals.some(v => v !== '—')) rows.push(vals);
            });
            return rows;
        }
        html += previewSectionTitle(10, 'Appointment, Promotion and Upgrading (APU)');
        html += previewTable(dynamicRows('#hrmApuBody .data-row'), ['Rank', 'Total']);
        html += previewTable([['<strong>Total</strong>', getValById('hrm-apu-grand-total')]], ['', 'Total']);

        html += previewSectionTitle(11, 'Recruitment (if any)');
        html += previewTable(dynamicRows('#hrmRecruitmentBody .data-row'), ['Rank', 'Total']);
        html += previewTable([['<strong>Total</strong>', getValById('hrm-recruitment-grand-total')]], ['', 'Total']);

        html += previewSectionTitle(12, 'Career Progression');
        html += previewTable(dynamicRows('#hrmCareerBody .data-row'), ['Rank', 'Total']);
        html += previewTable([['<strong>Total</strong>', getValById('hrm-career-grand-total')]], ['', 'Total']);

        /* 13. Officer Promotion */
        let opRows = [];
        document.querySelectorAll('.hrm-officer-promotion-input').forEach(el => {
            opRows.push([el.closest('tr').querySelector('td:nth-child(2)').textContent, el.value || '0']);
        });
        html += previewSectionTitle(13, 'Officer Promotion (Stats)');
        html += previewTable(opRows, ['Promotion', 'Total']);
        html += previewTable([['<strong>Total</strong>', getValById('hrm-officer-promotion-grand-total')]], ['', 'Total']);

        /* 14. Upgrading and Conversion */
        html += previewSectionTitle(14, 'Upgrading and Conversion');
        html += previewTable(dynamicRows('#hrmUpgradingConversionBody .data-row'), ['Rank', 'Total']);
        html += previewTable([['<strong>Total</strong>', getValById('hrm-upgrading-conversion-grand-total')]], ['', 'Total']);

        /* 15. Upgrading */
        let upRows = [];
        document.querySelectorAll('.hrm-upgrading-input').forEach(el => {
            upRows.push([el.closest('tr').querySelector('td:nth-child(2)').textContent, el.value || '0']);
        });
        html += previewSectionTitle(15, 'Upgrading');
        html += previewTable(upRows, ['Rank', 'Total']);
        html += previewTable([['<strong>Total</strong>', getValById('hrm-upgrading-grand-total')]], ['', 'Total']);

        /* 16. Promotion Eligibility */
        let peRows = [];
        document.querySelectorAll('.hrm-promotion-eligibility-input').forEach(el => {
            peRows.push([el.closest('tr').querySelector('td:nth-child(2)').textContent, el.value || '0']);
        });
        html += previewSectionTitle(16, 'Promotion/Eligibility');
        html += previewTable(peRows, ['Eligibility', 'Total']);
        html += previewTable([['<strong>Total</strong>', getValById('hrm-promotion-eligibility-grand-total')]], ['', 'Total']);

        /* 17. Permission to Study */
        let studyRows = [];
        document.querySelectorAll('.hrm-study-input').forEach(el => {
            studyRows.push([el.closest('tr').querySelector('td:nth-child(2)').textContent, el.value || '0']);
        });
        html += previewSectionTitle(17, 'Permission to Study');
        html += previewTable(studyRows, ['Application', 'Number']);

        /* 18. Pension */
        let pensionRows = [];
        document.querySelectorAll('#hrmPensionBody .data-row').forEach(row => {
            const inputs = row.querySelectorAll('input');
            const rank = inputs[0]?.value || '—';
            const death = inputs[1]?.value || '0';
            const mand = inputs[2]?.value || '0';
            const vol = inputs[3]?.value || '0';
            const res = inputs[4]?.value || '0';
            const total = row.querySelector('[id^="hrm-pension-row-total-"]')?.value || '0';
            if (rank !== '—' || [death, mand, vol, res].some(v => v !== '0')) {
                pensionRows.push([rank, death, mand, vol, res, total]);
            }
        });
        html += previewSectionTitle(18, 'Pension');
        html += previewTable(pensionRows, ['Rank', 'Death', 'Mandatory', 'Voluntary', 'Resignation', 'Total']);
        html += previewTable([[
            '<strong>Grand Total</strong>',
            getValById('hrm-pension-grand-death'),
            getValById('hrm-pension-grand-mandatory_retirement'),
            getValById('hrm-pension-grand-voluntary_retirement'),
            getValById('hrm-pension-grand-resignation'),
            getValById('hrm-pension-grand-total')
        ]], ['', 'Death', 'Mandatory', 'Voluntary', 'Resignation', 'Total']);

        /* 19. Discipline */
        let discRows = [];
        document.querySelectorAll('#tab-discipline .nis-table tbody tr').forEach(tr => {
            const cells = Array.from(tr.querySelectorAll('td'));
            if (cells.length < 19 || tr.classList.contains('total-row')) return;
            const label = cells[1]?.textContent.trim() || '—';
            const vals = cells.slice(2, 18).map(c => c.querySelector('input')?.value || '0');
            const total = cells[18]?.querySelector('input')?.value || '0';
            discRows.push([label, ...vals, total]);
        });
        html += previewSectionTitle(19, 'Discipline and Award');
        html += previewTable(discRows, ['Case', 'ACG', 'CIS', 'DCI', 'ACI', 'CSI', 'SI', 'DSI', 'ASI1', 'ASI2', 'II', 'AII', 'CIA', 'SIA', 'IA1', 'IA2', 'IA3', 'Total']);
        html += previewTable([[
            '<strong>Grand Total</strong>',
            ...['acg','cis','dci','aci','csi','si','dsi','asi1','asi2','ii','aii','cia','sia','ia1','ia2','ia3'].map(c => getValById(`hrm-discipline-col-total-${c}`)),
            getValById('hrm-discipline-grand-total')
        ]], ['', 'ACG', 'CIS', 'DCI', 'ACI', 'CSI', 'SI', 'DSI', 'ASI1', 'ASI2', 'II', 'AII', 'CIA', 'SIA', 'IA1', 'IA2', 'IA3', 'Total']);

        /* 20. NIMCOS */
        let nimcosRows = [];
        document.querySelectorAll('.hrm-nimcos-input').forEach(el => {
            nimcosRows.push([el.closest('tr').querySelector('td:nth-child(2)').textContent, el.value || '—']);
        });
        html += previewSectionTitle(20, 'NIMCOS');
        html += previewTable(nimcosRows, ['Activity', 'Number/Amount']);

        // html += `<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:var(--radius-md);padding:12px 16px;margin-top:14px;font-size:.84rem;color:#1e3a8a;">
        //     <i class="fas fa-info-circle" style="margin-right:6px;"></i>
        //     Please review the details above. If everything is correct, click <strong>Submit Return</strong>. Otherwise, use <strong>Back to Edit</strong> to make corrections.
        // </div>`;

        /* 21. General Report Preview Section for other report and documents preview */
        // Building the preview must not mutate the form. Calling addDocumentRow()
        // here added a new upload field every time any value changed.
        const escapePreviewText = value => String(value ?? '').replace(/[&<>"']/g, character => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        }[character]));
        html += previewSectionTitle(21, 'General Reports and Supporting Documents');
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
                html += `<p style="font-size:.82rem;color:var(--gray-800);margin-top:4px;white-space:pre-wrap;">${escapePreviewText(value)}</p>`;
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
                const fileName = escapePreviewText(input.files[0].name);
                html += `<li style="font-size:.82rem;color:var(--gray-800);">Document ${idx + 1}: ${fileName}</li>`;
            });
            html += `</ul>`;
        }


        container.innerHTML = html;
    }

    document.getElementById('passportAddReformRow')?.addEventListener('click', addReformRow);
    document.getElementById('passportAddDocumentRow')?.addEventListener('click', addDocumentRow);

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
