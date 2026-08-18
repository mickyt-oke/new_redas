@extends('user.directorates._layout')

{{-- This view renders its own Declaration & Consent card (section 11), so the
     shared form body skips its declaration card to avoid a duplicate consent field. --}}
@section('directorate-declaration', '1')

@section('directorate-sections')

@php
    $zoneMap = [
        'SHQ'    => ['label' => 'SHQ', 'commands' => []],
        'ZONE_A' => ['label' => 'Zone A', 'commands' => ['LASC', 'SEME', 'IDIROKO', 'LSPMC', 'LAPC', 'MMIA', 'OGSC']],
        'ZONE_B' => ['label' => 'Zone B', 'commands' => ['KDSC', 'KNSC', 'ITSK', 'MAKIA', 'KTSC', 'JIBIYA', 'SOSC', 'ICSC', 'ILLELA', 'ZMSC', 'JGSC']],
        'ZONE_C' => ['label' => 'Zone C', 'commands' => ['BASC', 'YBSC', 'BOSC', 'ADSC', 'GOSC', 'PLSC']],
        'ZONE_D' => ['label' => 'Zone D', 'commands' => ['FCT', 'NAIA', 'KWSC', 'NGSC', 'KBSC']],
        'ZONE_E' => ['label' => 'Zone E', 'commands' => ['ABSC', 'AKSC', 'CRSC', 'MFUM', 'EBSC', 'IMSC', 'NITSOL', 'RVSC', 'NITSA', 'RVMC']],
        'ZONE_F' => ['label' => 'Zone F', 'commands' => ['OYSC', 'OSSC', 'ONSC', 'EKSC']],
        'ZONE_G' => ['label' => 'Zone G', 'commands' => ['ANSC', 'BYSC', 'DLSC', 'ENSC', 'EDSC']],
        'ZONE_H' => ['label' => 'Zone H', 'commands' => ['BNSC', 'KGSC', 'TRSC', 'PLSC', 'NASC']],
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

@endphp

<div style="position:sticky;top:var(--topbar-height);z-index:95;background:#fff;border-bottom:2px solid var(--gray-100);box-shadow:0 2px 8px rgba(0,0,0,.04);margin-bottom:16px;border-radius:var(--radius-md);overflow:hidden;">
        <div class="investigation-section-nav" style="display:flex;overflow-x:auto;scrollbar-width:none;gap:0;padding:0 4px;">
            <div style="display:flex;gap:0;padding:6px 0;">
                @php
                    $sections = [
                        ['id' => 'section-1', 'label' => '1. Staff Strength', 'icon' => 'fa-users'],
                        ['id' => 'section-2', 'label' => '2. Breach of Law', 'icon' => 'fa-gavel'],
                        ['id' => 'section-3', 'label' => '3. DFU Activities', 'icon' => 'fa-file-invoice'],
                        ['id' => 'section-4', 'label' => '4. DOFIT', 'icon' => 'fa-passport'],
                        ['id' => 'section-5', 'label' => '5. Surveillance', 'icon' => 'fa-binoculars'],
                        ['id' => 'section-6', 'label' => '6. Interpol', 'icon' => 'fa-globe'],
                        ['id' => 'section-7', 'label' => '7. Citizenship', 'icon' => 'fa-id-card'],
                        ['id' => 'section-8', 'label' => '8. Suspect Index', 'icon' => 'fa-search'],
                        ['id' => 'section-9', 'label' => '9. Screening', 'icon' => 'fa-building'],
                        ['id' => 'section-10', 'label' => '10. D&R', 'icon' => 'fa-plane-departure'],
                        ['id' => 'section-11', 'label' => '11. Declaration', 'icon' => 'fa-shield-alt'],
                    ];
                @endphp
                @foreach($sections as $sec)
                    <a href="#{{ $sec['id'] }}"
                       data-section="{{ $sec['id'] }}"
                       style="display:flex;align-items:center;gap:6px;padding:10px 14px;font-size:.75rem;font-weight:600;color:var(--gray-600);white-space:nowrap;text-decoration:none;border-bottom:3px solid transparent;transition:all .15s;border-radius:var(--radius-sm);"
                       onmouseover="this.style.background='var(--nis-50)';this.style.color='var(--nis-700)'"
                       onmouseout="this.style.background='';this.style.color='var(--gray-600)'">
                        <i class="fas {{ $sec['icon'] }}" style="font-size:.75rem;color:var(--nis-500);"></i>
                        <span>{{ $sec['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- The shared layout provides the <form>; do not nest another one here. --}}
        {{-- ============ 1. STAFF STRENGTH ============ --}}
        <div id="section-1" class="redas-card investigation-step active" data-page="1" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-users"></i>
                    </div>
                    1. Staff Strength
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="staff-strength-table">
                        <thead>
                            <tr>
                                <th style="min-width:260px;">Rank</th>
                                <th style="width:110px;">Male</th>
                                <th style="width:110px;">Female</th>
                                <th style="width:110px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rankRows as [$rankKey, $rankLabel])
                                <tr>
                                    <td>{{ $rankLabel }}</td>
                                    <td>
                                        <input type="number" min="0" class="ni qty-input staff-male"
                                               name="staff_strength[{{ $rankKey }}][male]"
                                               value="{{ old('staff_strength.'.$rankKey.'.male') }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="ni qty-input staff-female"
                                               name="staff_strength[{{ $rankKey }}][female]"
                                               value="{{ old('staff_strength.'.$rankKey.'.female') }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="ni row-total" readonly tabindex="-1"
                                               name="staff_strength[{{ $rankKey }}][total]"
                                               value="{{ old('staff_strength.'.$rankKey.'.total') }}" placeholder="0">
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td id="staff-total-male"><strong>0</strong></td>
                                <td id="staff-total-female"><strong>0</strong></td>
                                <td id="staff-total-all"><strong>0</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

{{-- ============ 2. BREACH OF IMMIGRATION LAW ============ --}}
        <div id="section-2" class="redas-card investigation-step" data-page="2" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-gavel"></i>
                    </div>
                    2. Breach of Immigration Law
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="breach-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">S/N</th>
                                <th style="min-width:160px;">SHQ / Zones</th>
                                <th style="min-width:100px;">Command</th>
                                <th>Cases involving Companies</th>
                                <th>Cases involving Expatriates</th>
                                <th>Cases involving Officers</th>
                                <th>Cases Involving Nigerians</th>
                                <th style="width:100px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $sn = 0; @endphp
                            {{-- SHQ row --}}
                            <tr class="zone-row">
                                <td>{{ ++$sn }}</td>
                                <td><strong>SHQ</strong></td>
                                <td></td>
                                @foreach(['companies', 'expatriates', 'officers', 'nigerians'] as $col)
                                    <td>
                                        <input type="number" min="0" class="ni qty-input"
                                               name="breach[SHQ][_shq][{{ $col }}]"
                                               value="{{ old('breach.SHQ._shq.'.$col) }}" placeholder="0">
                                    </td>
                                @endforeach
                                <td>
                                    <input type="number" min="0" class="ni row-total" readonly tabindex="-1"
                                           name="breach[SHQ][_shq][total]"
                                           value="{{ old('breach.SHQ._shq.total') }}" placeholder="0">
                                </td>
                            </tr>
                            @foreach($zoneMap as $zoneKey => $zone)
                                @continue($zoneKey === 'SHQ')
                                <tr class="zone-row">
                                    <td>{{ ++$sn }}</td>
                                    <td><strong>{{ $zone['label'] }}</strong></td>
                                    <td></td>
                                    @foreach(['companies', 'expatriates', 'officers', 'nigerians'] as $col)
                                        <td>
                                            <input type="number" min="0" class="ni qty-input"
                                                   name="breach[{{ $zoneKey }}][_zone][{{ $col }}]"
                                                   value="{{ old('breach.'.$zoneKey.'._zone.'.$col) }}" placeholder="0">
                                        </td>
                                    @endforeach
                                    <td>
                                        <input type="number" min="0" class="ni row-total" readonly tabindex="-1"
                                               name="breach[{{ $zoneKey }}][_zone][total]"
                                               value="{{ old('breach.'.$zoneKey.'._zone.total') }}" placeholder="0">
                                    </td>
                                </tr>
                                @foreach($zone['commands'] as $cmd)
                                    <tr>
                                        <td>{{ ++$sn }}</td>
                                        <td></td>
                                        <td style="padding-left:22px;">{{ $cmd }}</td>
                                        @foreach(['companies', 'expatriates', 'officers', 'nigerians'] as $col)
                                            <td>
                                                <input type="number" min="0" class="ni qty-input"
                                                       name="breach[{{ $zoneKey }}][{{ $cmd }}][{{ $col }}]"
                                                       value="{{ old('breach.'.$zoneKey.'.'.$cmd.'.'.$col) }}" placeholder="0">
                                            </td>
                                        @endforeach
                                        <td>
                                            <input type="number" min="0" class="ni row-total" readonly tabindex="-1"
                                                   name="breach[{{ $zoneKey }}][{{ $cmd }}][total]"
                                                   value="{{ old('breach.'.$zoneKey.'.'.$cmd.'.total') }}" placeholder="0">
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============ 3. DFU (DOCUMENT FRAUD UNIT) ACTIVITIES ============ --}}
        @php
            $dfuCols = [
                'dfit_team'       => 'Document Fraud Investigation Team',
                'inv_exam'        => 'Investigation Examination',
                'retrieval'       => 'Retrieval / Released Passport',
                'nigerians'       => 'Cases Involving Nigerians',
                'border_fwd'      => 'Cases Forwarded from Border Points',
                'legal_unit'      => 'Legal Unit (Passports)',
                'attestation'     => 'Attestation / Breeder Documents',
                'authentication'  => 'Authentication (Passport)',
                'cgis'            => 'CGIS (Passports)',
                'shq_damaged'     => 'SHQ Damaged Passport',
                'shq_change'      => 'SHQ Change of Data Request (Breeder)',
                'phone_forensic'  => 'Phone Forensic',
                'non_collection'  => 'Non Collection (Passport)',
                'airports'        => 'International Airports (Passports)',
            ];
        @endphp
        <div id="section-3" class="redas-card investigation-step" data-page="3" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    3. DFU Activities (Document Fraud Unit)
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="dfu-table" style="min-width:1900px;">
                        <thead>
                            <tr>
                                <th style="width:40px;">S/N</th>
                                <th style="min-width:120px;">Zone / CMD</th>
                                @foreach($dfuCols as $colKey => $colLabel)
                                    <th title="{{ $colLabel }}">{{ $colLabel }}</th>
                                @endforeach
                                <th style="width:100px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $sn = 0; @endphp
                            {{-- SHQ row --}}
                            <tr class="zone-row">
                                <td>{{ ++$sn }}</td>
                                <td><strong>SHQ</strong></td>
                                @foreach($dfuCols as $colKey => $colLabel)
                                    <td>
                                        <input type="number" min="0" class="ni qty-input"
                                               name="dfu[SHQ][_shq][{{ $colKey }}]"
                                               value="{{ old('dfu.SHQ._shq.'.$colKey) }}" placeholder="0">
                                    </td>
                                @endforeach
                                <td>
                                    <input type="number" min="0" class="ni row-total" readonly tabindex="-1"
                                           name="dfu[SHQ][_shq][total]"
                                           value="{{ old('dfu.SHQ._shq.total') }}" placeholder="0">
                                </td>
                            </tr>
                            @foreach($zoneMap as $zoneKey => $zone)
                                @continue($zoneKey === 'SHQ')
                                <tr class="zone-row">
                                    <td>{{ ++$sn }}</td>
                                    <td><strong>{{ $zone['label'] }}</strong></td>
                                    @foreach($dfuCols as $colKey => $colLabel)
                                        <td>
                                            <input type="number" min="0" class="ni qty-input"
                                                   name="dfu[{{ $zoneKey }}][_zone][{{ $colKey }}]"
                                                   value="{{ old('dfu.'.$zoneKey.'._zone.'.$colKey) }}" placeholder="0">
                                        </td>
                                    @endforeach
                                    <td>
                                        <input type="number" min="0" class="ni row-total" readonly tabindex="-1"
                                               name="dfu[{{ $zoneKey }}][_zone][total]"
                                               value="{{ old('dfu.'.$zoneKey.'._zone.total') }}" placeholder="0">
                                    </td>
                                </tr>
                                @foreach($zone['commands'] as $cmd)
                                    <tr>
                                        <td>{{ ++$sn }}</td>
                                        <td style="padding-left:22px;">{{ $cmd }}</td>
                                        @foreach($dfuCols as $colKey => $colLabel)
                                            <td>
                                                <input type="number" min="0" class="ni qty-input"
                                                       name="dfu[{{ $zoneKey }}][{{ $cmd }}][{{ $colKey }}]"
                                                       value="{{ old('dfu.'.$zoneKey.'.'.$cmd.'.'.$colKey) }}" placeholder="0">
                                            </td>
                                        @endforeach
                                        <td>
                                            <input type="number" min="0" class="ni row-total" readonly tabindex="-1"
                                                   name="dfu[{{ $zoneKey }}][{{ $cmd }}][total]"
                                                   value="{{ old('dfu.'.$zoneKey.'.'.$cmd.'.total') }}" placeholder="0">
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p style="font-size:.75rem;color:var(--gray-500);margin-top:8px;">
                    <i class="fas fa-circle-info"></i> Scroll horizontally to see all DFU activity columns.
                </p>
            </div>
        </div>

        {{-- ============ 4. DOFIT (DOCUMENT FRAUD INVESTIGATION TEAM) ============ --}}
        <div id="section-4" class="redas-card investigation-step" data-page="4" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-passport"></i>
                    </div>
                    4. DOFIT &mdash; Document Fraud Investigation Team
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="dofit-table">
                        <thead>
                            <tr>
                                <th style="min-width:140px;">SHQ / Command</th>
                                <th style="min-width:200px;">Embassies / High Commissions</th>
                                <th style="width:120px;">No. of Passports Referred</th>
                                <th style="min-width:180px;">Reason(s)</th>
                                <th style="width:120px;">No. Returned to Holders</th>
                                <th style="width:120px;">No. Retained for Investigation</th>
                                <th style="min-width:160px;">Remark</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="dofit-tbody">
                            @forelse(old('dofit', []) as $i => $row)
                                <tr>
                                    <td><input type="text" class="ni" name="dofit[{{ $i }}][command]" value="{{ $row['command'] ?? '' }}" placeholder="Command"></td>
                                    <td><input type="text" class="ni" name="dofit[{{ $i }}][embassy]" value="{{ $row['embassy'] ?? '' }}" placeholder="Embassy / High Commission"></td>
                                    <td><input type="number" min="0" class="ni" name="dofit[{{ $i }}][no_referred]" value="{{ $row['no_referred'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="text" class="ni" name="dofit[{{ $i }}][reason]" value="{{ $row['reason'] ?? '' }}" placeholder="Reason"></td>
                                    <td><input type="number" min="0" class="ni" name="dofit[{{ $i }}][no_returned]" value="{{ $row['no_returned'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni" name="dofit[{{ $i }}][no_retained]" value="{{ $row['no_retained'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="text" class="ni" name="dofit[{{ $i }}][remark]" value="{{ $row['remark'] ?? '' }}" placeholder="Remark"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @empty
                                <tr>
                                    <td><input type="text" class="ni" name="dofit[0][command]" placeholder="Command"></td>
                                    <td><input type="text" class="ni" name="dofit[0][embassy]" placeholder="Embassy / High Commission"></td>
                                    <td><input type="number" min="0" class="ni" name="dofit[0][no_referred]" placeholder="0"></td>
                                    <td><input type="text" class="ni" name="dofit[0][reason]" placeholder="Reason"></td>
                                    <td><input type="number" min="0" class="ni" name="dofit[0][no_returned]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni" name="dofit[0][no_retained]" placeholder="0"></td>
                                    <td><input type="text" class="ni" name="dofit[0][remark]" placeholder="Remark"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="add-dofit-row"><i class="fas fa-plus"></i> Add Row</button>
            </div>
        </div>

        {{-- ============ 5. SURVEILLANCE, INTELLIGENCE & RISK ANALYSIS ============ --}}
        <div id="section-5" class="redas-card investigation-step" data-page="5" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-binoculars"></i>
                    </div>
                    5. Surveillance, Intelligence &amp; Risk Analysis Activities
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="surveillance-table">
                        <thead>
                            <tr>
                                <th style="min-width:140px;">SHQ</th>
                                <th style="min-width:280px;">Activities</th>
                                <th style="width:120px;">Number</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="surveillance-tbody">
                            @forelse(old('surveillance', []) as $i => $row)
                                <tr>
                                    <td><input type="text" class="ni" name="surveillance[{{ $i }}][shq]" value="{{ $row['shq'] ?? '' }}" placeholder="SHQ"></td>
                                    <td><input type="text" class="ni" name="surveillance[{{ $i }}][activity]" value="{{ $row['activity'] ?? '' }}" placeholder="Activity"></td>
                                    <td><input type="number" min="0" class="ni" name="surveillance[{{ $i }}][number]" value="{{ $row['number'] ?? '' }}" placeholder="0"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @empty
                                <tr>
                                    <td><input type="text" class="ni" name="surveillance[0][shq]" placeholder="SHQ"></td>
                                    <td><input type="text" class="ni" name="surveillance[0][activity]" placeholder="Activity"></td>
                                    <td><input type="number" min="0" class="ni" name="surveillance[0][number]" placeholder="0"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="add-surveillance-row"><i class="fas fa-plus"></i> Add Row</button>
            </div>
        </div>

        {{-- ============ 6. INTELLIGENCE / INTERPOL ============ --}}
        <div id="section-6" class="redas-card investigation-step" data-page="6" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-globe"></i>
                    </div>
                    6. Intelligence / Interpol
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="interpol-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">S/N</th>
                                <th style="min-width:120px;">Zones</th>
                                <th style="min-width:280px;">Activities</th>
                                <th style="width:120px;">Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $sn = 0; @endphp
                            @foreach($zoneMap as $zoneKey => $zone)
                                @continue($zoneKey === 'SHQ')
                                <tr>
                                    <td>{{ ++$sn }}</td>
                                    <td><strong>{{ $zone['label'] }}</strong></td>
                                    <td>
                                        <input type="text" class="ni"
                                               name="interpol[{{ $zoneKey }}][activity]"
                                               value="{{ old('interpol.'.$zoneKey.'.activity') }}" placeholder="Activity">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="ni"
                                               name="interpol[{{ $zoneKey }}][number]"
                                               value="{{ old('interpol.'.$zoneKey.'.number') }}" placeholder="0">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============ 7. CITIZENSHIP ACTIVITIES ============ --}}
        <div id="section-7" class="redas-card investigation-step" data-page="7" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-id-card"></i>
                    </div>
                    7. Citizenship Activities
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="citizenship-table">
                        <thead>
                            <tr>
                                <th style="min-width:140px;">SHQ / Command</th>
                                <th style="width:110px;">B/F from 2024</th>
                                <th style="width:110px;">Received This Year</th>
                                <th style="width:110px;">Treated &amp; Forwarded to HMOI</th>
                                <th style="width:100px;">Rejected</th>
                                <th style="width:110px;">KIV (Awaiting Docs)</th>
                                <th style="width:110px;">Cumulative for Period</th>
                                <th style="width:100px;">Total</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="citizenship-tbody">
                            @forelse(old('citizenship', []) as $i => $row)
                                <tr>
                                    <td><input type="text" class="ni" name="citizenship[{{ $i }}][command]" value="{{ $row['command'] ?? '' }}" placeholder="Command"></td>
                                    <td><input type="number" min="0" class="ni qty-input" name="citizenship[{{ $i }}][brought_forward]" value="{{ $row['brought_forward'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input" name="citizenship[{{ $i }}][received]" value="{{ $row['received'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni" name="citizenship[{{ $i }}][treated]" value="{{ $row['treated'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni" name="citizenship[{{ $i }}][rejected]" value="{{ $row['rejected'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni" name="citizenship[{{ $i }}][kiv]" value="{{ $row['kiv'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni" name="citizenship[{{ $i }}][cumulative]" value="{{ $row['cumulative'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni row-total" readonly tabindex="-1" name="citizenship[{{ $i }}][total]" value="{{ $row['total'] ?? '' }}" placeholder="0"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @empty
                                <tr>
                                    <td><input type="text" class="ni" name="citizenship[0][command]" placeholder="Command"></td>
                                    <td><input type="number" min="0" class="ni qty-input" name="citizenship[0][brought_forward]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input" name="citizenship[0][received]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni" name="citizenship[0][treated]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni" name="citizenship[0][rejected]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni" name="citizenship[0][kiv]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni" name="citizenship[0][cumulative]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni row-total" readonly tabindex="-1" name="citizenship[0][total]" placeholder="0"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="add-citizenship-row"><i class="fas fa-plus"></i> Add Row</button>
                <p style="font-size:.75rem;color:var(--gray-500);margin-top:8px;">
                    <i class="fas fa-circle-info"></i> Total is auto-calculated from Brought Forward + Received.
                </p>
            </div>
        </div>

        {{-- ============ 8. SUSPECT INDEX ============ --}}
        @php
            $suspectCols = [
                'watch_persons'    => 'No. of Watch Listed Persons',
                'stop_persons'     => 'No. of Stop Listed Persons',
                'watch_passports'  => 'No. of Watch Listed Passports',
                'vacation_stop'    => 'No. of Vacation of Stop List Order',
                'persons_index'    => 'No. of Persons in Suspect Index',
                'searches'         => 'Searches',
            ];
        @endphp
        <div id="section-8" class="redas-card investigation-step" data-page="8" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-search"></i>
                    </div>
                    8. Suspect Index
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
<table class="nis-table" id="suspect-table" style="min-width:1200px;">
                        <thead>
                            <tr>
                                <th style="width:40px;">S/N</th>
                                <th style="min-width:160px;">SHQ / Zone</th>
                                <th style="min-width:100px;">Command</th>
                                @foreach($suspectCols as $colKey => $colLabel)
                                    <th title="{{ $colLabel }}">{{ $colLabel }}</th>
                                @endforeach
                                <th style="width:100px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $sn = 0; @endphp
                            {{-- SHQ row --}}
                            <tr class="zone-row">
                                <td>{{ ++$sn }}</td>
                                <td><strong>SHQ</strong></td>
                                <td></td>
                                @foreach($suspectCols as $colKey => $colLabel)
                                    <td>
                                        <input type="number" min="0" class="ni qty-input"
                                               name="suspect[SHQ][_shq][{{ $colKey }}]"
                                               value="{{ old('suspect.SHQ._shq.'.$colKey) }}" placeholder="0">
                                    </td>
                                @endforeach
                                <td>
                                    <input type="number" min="0" class="ni row-total" readonly tabindex="-1"
                                           name="suspect[SHQ][_shq][total]"
                                           value="{{ old('suspect.SHQ._shq.total') }}" placeholder="0">
                                </td>
                            </tr>
                            @foreach($zoneMap as $zoneKey => $zone)
                                @continue($zoneKey === 'SHQ')
                                <tr class="zone-row">
                                    <td>{{ ++$sn }}</td>
                                    <td><strong>{{ $zone['label'] }}</strong></td>
                                    <td></td>
                                    @foreach($suspectCols as $colKey => $colLabel)
                                        <td>
                                            <input type="number" min="0" class="ni qty-input"
                                                   name="suspect[{{ $zoneKey }}][_zone][{{ $colKey }}]"
                                                   value="{{ old('suspect.'.$zoneKey.'._zone.'.$colKey) }}" placeholder="0">
                                        </td>
                                    @endforeach
                                    <td>
                                        <input type="number" min="0" class="ni row-total" readonly tabindex="-1"
                                               name="suspect[{{ $zoneKey }}][_zone][total]"
                                               value="{{ old('suspect.'.$zoneKey.'._zone.total') }}" placeholder="0">
                                    </td>
                                </tr>
                                @foreach($zone['commands'] as $cmd)
                                    <tr>
                                        <td>{{ ++$sn }}</td>
                                        <td></td>
                                        <td style="padding-left:22px;">{{ $cmd }}</td>
                                        @foreach($suspectCols as $colKey => $colLabel)
                                            <td>
                                                <input type="number" min="0" class="ni qty-input"
                                                       name="suspect[{{ $zoneKey }}][{{ $cmd }}][{{ $colKey }}]"
                                                       value="{{ old('suspect.'.$zoneKey.'.'.$cmd.'.'.$colKey) }}" placeholder="0">
                                            </td>
                                        @endforeach
                                        <td>
                                            <input type="number" min="0" class="ni row-total" readonly tabindex="-1"
                                                   name="suspect[{{ $zoneKey }}][{{ $cmd }}][total]"
                                                   value="{{ old('suspect.'.$zoneKey.'.'.$cmd.'.total') }}" placeholder="0">
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============ 9. SCREENING CENTRE ACTIVITIES ============ --}}
        <div id="section-9" class="redas-card investigation-step" data-page="9" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-building"></i>
                    </div>
                    9. Screening Centre Activities (Detainees and their Nationality)
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="screening-table">
                        <thead>
                            <tr>
                                <th style="width:60px;">S/NO.</th>
                                <th style="min-width:220px;">Nationality</th>
                                <th style="width:140px;">No. of Persons</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="screening-tbody">
                            @forelse(old('screening', []) as $i => $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><input type="text" class="ni" name="screening[{{ $i }}][nationality]" value="{{ $row['nationality'] ?? '' }}" placeholder="Nationality"></td>
                                    <td><input type="number" min="0" class="ni" name="screening[{{ $i }}][persons]" value="{{ $row['persons'] ?? '' }}" placeholder="0"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @empty
                                @for($i = 0; $i < 5; $i++)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><input type="text" class="ni" name="screening[{{ $i }}][nationality]" placeholder="Nationality"></td>
                                    <td><input type="number" min="0" class="ni" name="screening[{{ $i }}][persons]" placeholder="0"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                                @endfor
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="add-screening-row"><i class="fas fa-plus"></i> Add Row</button>
            </div>
        </div>

        {{-- ============ 10. DEPORTATION AND REPATRIATION (D&R) ============ --}}
        <div id="section-10" class="redas-card investigation-step" data-page="10" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-plane-departure"></i>
                    </div>
                    10. Deportation and Repatriation (D&amp;R)
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="dr-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">S/N</th>
                                <th style="width:120px;">No. of Persons</th>
                                <th style="min-width:180px;">Nationality</th>
                                <th style="width:110px;">Gender</th>
                                <th style="width:110px;">Court Ordered</th>
                                <th style="width:110px;">HOI Ordered</th>
                                <th style="width:110px;">Service Ordered</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="dr-tbody">
                            @forelse(old('deportation', []) as $i => $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><input type="number" min="0" class="ni" name="deportation[{{ $i }}][no_persons]" value="{{ $row['no_persons'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="text" class="ni" name="deportation[{{ $i }}][nationality]" value="{{ $row['nationality'] ?? '' }}" placeholder="Nationality"></td>
                                    <td>
                                        <select class="ni ni-select" name="deportation[{{ $i }}][gender]">
                                            <option value="">Select</option>
                                            <option value="Male" {{ ($row['gender'] ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ ($row['gender'] ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </td>
                                    <td style="text-align:center;"><input type="checkbox" name="deportation[{{ $i }}][court_ordered]" value="1" {{ !empty($row['court_ordered']) ? 'checked' : '' }}></td>
                                    <td style="text-align:center;"><input type="checkbox" name="deportation[{{ $i }}][hoi_ordered]" value="1" {{ !empty($row['hoi_ordered']) ? 'checked' : '' }}></td>
                                    <td style="text-align:center;"><input type="checkbox" name="deportation[{{ $i }}][service_ordered]" value="1" {{ !empty($row['service_ordered']) ? 'checked' : '' }}></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @empty
                                <tr>
                                    <td>1</td>
                                    <td><input type="number" min="0" class="ni" name="deportation[0][no_persons]" placeholder="0"></td>
                                    <td><input type="text" class="ni" name="deportation[0][nationality]" placeholder="Nationality"></td>
                                    <td>
                                        <select class="ni ni-select" name="deportation[0][gender]">
                                            <option value="">Select</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </td>
                                    <td style="text-align:center;"><input type="checkbox" name="deportation[0][court_ordered]" value="1"></td>
                                    <td style="text-align:center;"><input type="checkbox" name="deportation[0][hoi_ordered]" value="1"></td>
                                    <td style="text-align:center;"><input type="checkbox" name="deportation[0][service_ordered]" value="1"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="add-dr-row"><i class="fas fa-plus"></i> Add Row</button>
            </div>
        </div>

        {{-- ============ DECLARATION & CONSENT ============ --}}
        <div id="section-11" class="redas-card investigation-step" data-page="11" style="margin-bottom:14px;border-left:4px solid #1d4ed8;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    Declaration &amp; Consent
                </div>
            </div>
            <div class="card-body">
                <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;font-size:.84rem;color:var(--gray-700);">
                    <input type="checkbox" name="data_consent" value="1" required style="accent-color:var(--nis-600);margin-top:2px;">
                    <span>
                        I confirm that the information provided is accurate, limited to what is necessary for official NIS reporting,
                        and that I have authority to submit it. I understand that this data will be processed and retained in accordance with
                        the <a href="{{ route('privacy') }}" target="_blank" style="color:#1d4ed8;text-decoration:underline;">Privacy Policy</a>.
                    </span>
                </label>
            </div>
        </div>

        {{-- ============ SUBMIT ============ --}}
        <div class="redas-card">
            <div class="card-body" style="display:flex;align-items:center;justify-content:flex-end;gap:10px;flex-wrap:wrap;">
                <a href="{{ route('user.dashboard') }}" class="btn-nis btn-ghost">Cancel</a>
                <button type="submit" class="btn-nis btn-primary-nis">
                    <i class="fas fa-paper-plane"></i> Submit {{ $directorateName ?? 'Directorate' }} Return
                </button>
            </div>
        </div>

<script>
(function () {
    'use strict';

    /* ---------- Row total auto-calculation ---------- */
    function recalcRow(tr) {
        if (!tr) return;
        const totalField = tr.querySelector('.row-total');
        if (!totalField) return;
        let sum = 0;
        tr.querySelectorAll('.qty-input').forEach(function (input) {
            sum += parseFloat(input.value) || 0;
        });
        totalField.value = sum;
    }

    document.querySelectorAll('.qty-input').forEach(function (input) {
        input.addEventListener('input', function () {
            recalcRow(input.closest('tr'));
        });
    });

    /* ---------- Staff Strength grand total ---------- */
    function recalcStaffTotals() {
        let male = 0, female = 0;
        document.querySelectorAll('.staff-male').forEach(function (i) { male += parseFloat(i.value) || 0; });
        document.querySelectorAll('.staff-female').forEach(function (i) { female += parseFloat(i.value) || 0; });
        const mEl = document.getElementById('staff-total-male');
        const fEl = document.getElementById('staff-total-female');
        const aEl = document.getElementById('staff-total-all');
        if (mEl) mEl.textContent = male;
        if (fEl) fEl.textContent = female;
        if (aEl) aEl.textContent = male + female;
    }
    document.querySelectorAll('.staff-male, .staff-female').forEach(function (input) {
        input.addEventListener('input', recalcStaffTotals);
    });
    recalcStaffTotals();

    /* ---------- Remove-row buttons (existing rows) ---------- */
    function bindRemove(tr) {
        const btn = tr.querySelector('.remove-row-btn');
        if (btn && !btn.dataset.bound) {
            btn.dataset.bound = '1';
            btn.addEventListener('click', function () { tr.remove(); });
        }
    }
    document.querySelectorAll('#dofit-table tbody tr, #surveillance-table tbody tr, #citizenship-table tbody tr, #screening-table tbody tr, #dr-table tbody tr').forEach(bindRemove);

    /* ---------- Generic dynamic-row adder ---------- */
    let rowCounters = {};
    function addDynamicRow(tbodyId, prefix, buildRowHtml) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        rowCounters[tbodyId] = (rowCounters[tbodyId] || tbody.querySelectorAll('tr').length) + 1;
        const idx = 'n' + Date.now() + '_' + rowCounters[tbodyId];
        const tr = document.createElement('tr');
        tr.innerHTML = buildRowHtml(prefix, idx);
        tbody.appendChild(tr);
        bindRemove(tr);
        const firstInput = tr.querySelector('input, select');
        if (firstInput) firstInput.focus();
        tr.querySelectorAll('.qty-input').forEach(function (input) {
            input.addEventListener('input', function () { recalcRow(input.closest('tr')); });
        });
    }

    const addBtn = function (id, handler) {
        const btn = document.getElementById(id);
        if (btn) btn.addEventListener('click', handler);
    };

    addBtn('add-dofit-row', function () {
        addDynamicRow('dofit-tbody', 'dofit', function (prefix, idx) {
            return '' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][command]" placeholder="Command"></td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][embassy]" placeholder="Embassy / High Commission"></td>' +
                '<td><input type="number" min="0" class="ni" name="' + prefix + '[' + idx + '][no_referred]" placeholder="0"></td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][reason]" placeholder="Reason"></td>' +
                '<td><input type="number" min="0" class="ni" name="' + prefix + '[' + idx + '][no_returned]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni" name="' + prefix + '[' + idx + '][no_retained]" placeholder="0"></td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][remark]" placeholder="Remark"></td>' +
                '<td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>';
        });
    });

    addBtn('add-surveillance-row', function () {
        addDynamicRow('surveillance-tbody', 'surveillance', function (prefix, idx) {
            return '' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][shq]" placeholder="SHQ"></td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][activity]" placeholder="Activity"></td>' +
                '<td><input type="number" min="0" class="ni" name="' + prefix + '[' + idx + '][number]" placeholder="0"></td>' +
                '<td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>';
        });
    });

    addBtn('add-citizenship-row', function () {
        addDynamicRow('citizenship-tbody', 'citizenship', function (prefix, idx) {
            return '' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][command]" placeholder="Command"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][brought_forward]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][received]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni" name="' + prefix + '[' + idx + '][treated]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni" name="' + prefix + '[' + idx + '][rejected]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni" name="' + prefix + '[' + idx + '][kiv]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni" name="' + prefix + '[' + idx + '][cumulative]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni row-total" readonly tabindex="-1" name="' + prefix + '[' + idx + '][total]" placeholder="0"></td>' +
                '<td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>';
        });
    });

    addBtn('add-screening-row', function () {
        addDynamicRow('screening-tbody', 'screening', function (prefix, idx) {
            return '' +
                '<td>' + (parseInt(document.querySelectorAll('#screening-tbody tr').length) + 1) + '</td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][nationality]" placeholder="Nationality"></td>' +
                '<td><input type="number" min="0" class="ni" name="' + prefix + '[' + idx + '][persons]" placeholder="0"></td>' +
                '<td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>';
        });
    });

    addBtn('add-dr-row', function () {
        addDynamicRow('dr-tbody', 'deportation', function (prefix, idx) {
            return '' +
                '<td>' + (parseInt(document.querySelectorAll('#dr-tbody tr').length) + 1) + '</td>' +
                '<td><input type="number" min="0" class="ni" name="' + prefix + '[' + idx + '][no_persons]" placeholder="0"></td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][nationality]" placeholder="Nationality"></td>' +
                '<td><select class="ni ni-select" name="' + prefix + '[' + idx + '][gender]"><option value="">Select</option><option value="Male">Male</option><option value="Female">Female</option></select></td>' +
                '<td style="text-align:center;"><input type="checkbox" name="' + prefix + '[' + idx + '][court_ordered]" value="1"></td>' +
                '<td style="text-align:center;"><input type="checkbox" name="' + prefix + '[' + idx + '][hoi_ordered]" value="1"></td>' +
                '<td style="text-align:center;"><input type="checkbox" name="' + prefix + '[' + idx + '][service_ordered]" value="1"></td>' +
                '<td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>';
        });
    });

    /* ---------- Basic client-side confirmation before submit ---------- */
    const form = document.querySelector('main form') || document.querySelector('form[action*="directorates"]');
    if (form) {
        form.addEventListener('submit', function (e) {
            const period = form.querySelector('[name="report_period"]');
            if (period && !period.value) {
                e.preventDefault();
                period.focus();
                alert('Please select the report period before submitting.');
            }
        });
    }

    /* ---------- Section page navigation ---------- */
    const steps = Array.from(document.querySelectorAll('.investigation-step'));
    const prevBtn = document.getElementById('investigation-prev-btn');
    const nextBtn = document.getElementById('investigation-next-btn');
    const pageLabel = document.getElementById('investigation-page-label');
    let currentPage = 1;
    const lastPage = steps.length;

    const navLinks = Array.from(document.querySelectorAll('.investigation-section-nav a'));

    function updateStepNavigation() {
        steps.forEach(function (step) {
            const page = Number(step.dataset.page || 0);
            step.classList.toggle('active', page === currentPage);
        });
        navLinks.forEach(function (link) {
            const sectionId = link.dataset.section;
            const page = Number(sectionId?.replace('section-', '') || 0);
            link.classList.toggle('active', page === currentPage);
        });
        if (pageLabel) {
            pageLabel.textContent = 'Page ' + currentPage + ' of ' + lastPage;
        }
        if (prevBtn) {
            prevBtn.disabled = currentPage === 1;
        }
        if (nextBtn) {
            if (currentPage === lastPage) {
                nextBtn.innerHTML = 'Finish <i class="fas fa-check"></i>';
            } else {
                nextBtn.innerHTML = 'Next <i class="fas fa-chevron-right"></i>';
            }
        }
    }

    function goToPage(page) {
        currentPage = Math.min(Math.max(1, page), lastPage);
        updateStepNavigation();
        const section = document.querySelector('.investigation-step.active');
        if (section) {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            goToPage(currentPage - 1);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            if (currentPage === lastPage) {
                if (form && window.confirm('Are you sure you want to submit this return?\n\nPlease verify all information before continuing.')) {
                    if (form.requestSubmit) form.requestSubmit();
                    else form.submit();
                }
                return;
            }
            goToPage(currentPage + 1);
        });
    }

    navLinks.forEach(function (link) {
        const page = Number(link.dataset.section?.replace('section-', '') || 0);
        if (!page) {
            return;
        }
        link.addEventListener('click', function (e) {
            e.preventDefault();
            goToPage(page);
        });
    });

    updateStepNavigation();
})();
</script>

@endsection
