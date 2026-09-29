{{-- Investigation & Intelligence directorate return sections.
     Included by user.directorates.investigation (standalone page) and by the
     combined state return form inside #dir-investigation. All element ids are
     prefixed with `investigation-` so they stay unique on the combined page. --}}

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
                        ['id' => 'investigation-section-1', 'label' => '1. Staff Strength', 'icon' => 'fa-users'],
                        ['id' => 'investigation-section-2', 'label' => '2. Breach of Law', 'icon' => 'fa-gavel'],
                        ['id' => 'investigation-section-3', 'label' => '3. DFU Activities', 'icon' => 'fa-file-invoice'],
                        ['id' => 'investigation-section-4', 'label' => '4. DOFIT', 'icon' => 'fa-passport'],
                        ['id' => 'investigation-section-5', 'label' => '5. Surveillance', 'icon' => 'fa-binoculars'],
                        ['id' => 'investigation-section-6', 'label' => '6. Interpol', 'icon' => 'fa-globe'],
                        ['id' => 'investigation-section-7', 'label' => '7. Citizenship', 'icon' => 'fa-id-card'],
                        ['id' => 'investigation-section-8', 'label' => '8. Suspect Index', 'icon' => 'fa-search'],
                        ['id' => 'investigation-section-9', 'label' => '9. Screening', 'icon' => 'fa-building'],
                        ['id' => 'investigation-section-10', 'label' => '10. D&R', 'icon' => 'fa-plane-departure'],
                        ['id' => 'investigation-section-11', 'label' => '11. Declaration', 'icon' => 'fa-shield-alt'],
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
        <div id="investigation-section-1" class="redas-card investigation-step active" data-page="1" style="margin-bottom:14px;">
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
                    <table class="nis-table" id="investigation-staff-strength-table">
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
                                               name="investigation[staff_strength][{{ $rankKey }}][male]"
                                               value="{{ old('investigation.staff_strength.'.$rankKey.'.male') }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="ni qty-input staff-female"
                                               name="investigation[staff_strength][{{ $rankKey }}][female]"
                                               value="{{ old('investigation.staff_strength.'.$rankKey.'.female') }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="ni row-total" readonly tabindex="-1"
                                               name="investigation[staff_strength][{{ $rankKey }}][total]"
                                               value="{{ old('investigation.staff_strength.'.$rankKey.'.total') }}" placeholder="0">
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td id="investigation-staff-total-male"><strong>0</strong></td>
                                <td id="investigation-staff-total-female"><strong>0</strong></td>
                                <td id="investigation-staff-total-all"><strong>0</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

{{-- ============ 2. BREACH OF IMMIGRATION LAW ============ --}}
        <div id="investigation-section-2" class="redas-card investigation-step" data-page="2" style="margin-bottom:14px;">
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
                    <table class="nis-table" id="investigation-breach-table">
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
        <div id="investigation-section-3" class="redas-card investigation-step" data-page="3" style="margin-bottom:14px;">
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
                    <table class="nis-table" id="investigation-dfu-table" style="min-width:1900px;">
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
        <div id="investigation-section-4" class="redas-card investigation-step" data-page="4" style="margin-bottom:14px;">
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
                    <table class="nis-table" id="investigation-dofit-table">
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
                        <tbody id="investigation-dofit-tbody">
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
                <button type="button" class="add-row-btn" id="investigation-add-dofit-row" data-row-target="investigation-dofit-tbody"><i class="fas fa-plus"></i> Add Row</button>
            </div>
        </div>

        {{-- ============ 5. SURVEILLANCE, INTELLIGENCE & RISK ANALYSIS ============ --}}
        <div id="investigation-section-5" class="redas-card investigation-step" data-page="5" style="margin-bottom:14px;">
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
                    <table class="nis-table" id="investigation-surveillance-table">
                        <thead>
                            <tr>
                                <th style="min-width:140px;">SHQ</th>
                                <th style="min-width:280px;">Activities</th>
                                <th style="width:120px;">Number</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="investigation-surveillance-tbody">
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
                <button type="button" class="add-row-btn" id="investigation-add-surveillance-row" data-row-target="investigation-surveillance-tbody"><i class="fas fa-plus"></i> Add Row</button>
            </div>
        </div>

        {{-- ============ 6. INTELLIGENCE / INTERPOL ============ --}}
        <div id="investigation-section-6" class="redas-card investigation-step" data-page="6" style="margin-bottom:14px;">
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
                    <table class="nis-table" id="investigation-interpol-table">
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
        <div id="investigation-section-7" class="redas-card investigation-step" data-page="7" style="margin-bottom:14px;">
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
                    <table class="nis-table" id="investigation-citizenship-table">
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
                        <tbody id="investigation-citizenship-tbody">
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
                <button type="button" class="add-row-btn" id="investigation-add-citizenship-row" data-row-target="investigation-citizenship-tbody"><i class="fas fa-plus"></i> Add Row</button>
                <p style="font-size:.75rem;color:var(--gray-500);margin-top:8px;">
                    <i class="fas fa-circle-info"></i> Total is auto-calculated from Brought Forward + Received.
                </p>
            </div>
        </div>
