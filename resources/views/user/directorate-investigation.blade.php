@php
    // Defensive defaults so this view also works if the controller doesn't
    // pass every variable the generic directorate view expects.
    $slug        = $slug ?? 'investigation-compliance';
    $directorate = $directorate ?? [
        'name' => 'Investigation and Compliance',
        'icon' => 'fas fa-user-shield',
    ];
    $allDirectorates = $allDirectorates ?? [];

    // Canonical Zone / Command structure shared by the Breach of Immigration
    // Law, DFU Activities and Suspect Index tables (as laid out in the
    // directorate's return template).
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
@endphp
@include('partials.header')

<main class="redas-content">

    <div class="page-header" style="margin-bottom:18px;">
        <div>
            <h1 class="page-title" style="margin-bottom:4px;">
                <i class="{{ $directorate['icon'] }}" style="margin-right:8px;"></i>
                {{ $directorate['name'] }} Return
            </h1>
            <p class="page-subtitle">Monthly return &mdash; Investigation &amp; Compliance Directorate reporting template.</p>
        </div>
        @if(auth()->user()?->role === 'directorate')
        <a href="{{ route('user.directorate.home') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to Directorates
        </a>
        @else
        <a href="{{ route('user.dashboard') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        @endif
    </div>

    @if (session('status'))
        <div style="background:#ecfdf5;border:1px solid #86efac;color:#166534;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
            <i class="fas fa-check-circle" style="margin-right:6px;"></i>{{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
            <i class="fas fa-triangle-exclamation" style="margin-right:6px;"></i>
            Please correct the {{ $errors->count() }} error(s) below before submitting.
        </div>
    @endif

    {{-- ============ STICKY SECTION NAVIGATION ============ --}}
    <div style="position:sticky;top:var(--topbar-height);z-index:95;background:#fff;border-bottom:2px solid var(--gray-100);box-shadow:0 2px 8px rgba(0,0,0,.04);margin-bottom:16px;border-radius:var(--radius-md);overflow:hidden;">
        <div style="display:flex;overflow-x:auto;scrollbar-width:none;gap:0;padding:0 4px;">
            <div style="display:flex;gap:0;padding:6px 0;">
                @php
                    $sections = [
                        ['id' => 'section-meta', 'label' => 'Report Meta', 'icon' => 'fa-file-signature'],
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
                    ];
                @endphp
                @foreach($sections as $sec)
                    <a href="#{{ $sec['id'] }}"
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

    @if(count($allDirectorates))
    <div class="redas-card" style="margin-bottom:16px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);">
                    <i class="fas fa-sitemap"></i>
                </div>
                Directorate Overview
            </div>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:10px;">
                @foreach($allDirectorates as $dirSlug => $dir)
                    <a href="{{ route('user.directorates.show', $dirSlug) }}"
                       class="btn-nis {{ $slug === $dirSlug ? 'btn-primary-nis' : 'btn-ghost' }}"
                       style="justify-content:flex-start;">
                        <i class="{{ $dir['icon'] }}"></i>
                        {{ $dir['name'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('user.directorates.store', $slug) }}" id="investigation-compliance-form">
        @csrf

        {{-- ============ REPORT META ============ --}}
        <div id="section-meta" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:var(--gold-100);color:var(--gold-600);">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    Monthly Return
                </div>
            </div>
            <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Report Period</label>
                    <input type="month" class="ni" name="report_period" required value="{{ old('report_period', now()->format('Y-m')) }}">
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Reporting Officer</label>
                    <input type="text" class="ni" name="reporting_officer" required value="{{ old('reporting_officer', auth()->user()->name) }}">
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Directorate</label>
                    <input type="text" class="ni" value="{{ $directorate['name'] }}" readonly>
                </div>
            </div>
        </div>

        {{-- ============ 1. STAFF STRENGTH ============ --}}
        @php
            $ranks = [
                'dcg'   => 'Deputy Comptroller - General',
                'acg'   => 'Assistant Comptroller - General',
                'ci'    => 'Comptroller of Immigration',
                'dci'   => 'Deputy Comptroller of Immigration',
                'aci'   => 'Assistant Comptroller of Immigration',
                'csi'   => 'Chief Superintendent of Immigration',
                'si'    => 'Superintendent of Immigration',
                'dsi'   => 'Deputy Superintendent of Immigration',
                'asi'   => 'Assistant Superintendent I & II',
                'iaii'  => 'Inspector and Assistant Inspector of Immigration',
                'ia123' => 'Immigration Assistant I, II & III',
            ];
        @endphp
        <div id="section-1" class="redas-card" style="margin-bottom:14px;">
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
                            @foreach($ranks as $rankKey => $rankLabel)
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
        <div id="section-2" class="redas-card" style="margin-bottom:14px;">
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
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="breach-tbody">
                            <!-- Rows are added dynamically via "Add Row" button -->
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="add-breach-row"><i class="fas fa-plus"></i> Add Row</button>
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
        <div id="section-3" class="redas-card" style="margin-bottom:14px;">
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
                        <tbody id="dfu-tbody">
                            <!-- Rows are added dynamically via "Add Row" button -->
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="add-dfu-row"><i class="fas fa-plus"></i> Add Row</button>
                <p style="font-size:.75rem;color:var(--gray-500);margin-top:8px;">
                    <i class="fas fa-circle-info"></i> Scroll horizontally to see all DFU activity columns.
                </p>
            </div>
        </div>

        {{-- ============ 4. DOFIT (DOCUMENT FRAUD INVESTIGATION TEAM) ============ --}}
        <div class="redas-card" style="margin-bottom:14px;">
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
        <div class="redas-card" style="margin-bottom:14px;">
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
        <div class="redas-card" style="margin-bottom:14px;">
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
        <div class="redas-card" style="margin-bottom:14px;">
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
        <div class="redas-card" style="margin-bottom:14px;">
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
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="suspect-tbody">
                            <!-- Rows are added dynamically via "Add Row" button -->
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="add-suspect-row"><i class="fas fa-plus"></i> Add Row</button>
            </div>
        </div>

        {{-- ============ 9. SCREENING CENTRE ACTIVITIES ============ --}}
        <div class="redas-card" style="margin-bottom:14px;">
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
        <div class="redas-card" style="margin-bottom:14px;">
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

        {{-- ============ SUBMIT ============ --}}
        <div class="redas-card">
            <div class="card-body" style="display:flex;justify-content:flex-end;gap:10px;">
                <a href="{{ route('user.dashboard') }}" class="btn-nis btn-ghost">Cancel</a>
                <button type="submit" class="btn-nis btn-primary-nis">
                    <i class="fas fa-paper-plane"></i> Submit {{ $directorate['name'] }} Return
                </button>
            </div>
        </div>
    </form>
</main>

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
    document.querySelectorAll('#breach-table tbody tr, #dofit-table tbody tr, #surveillance-table tbody tr, #citizenship-table tbody tr, #screening-table tbody tr, #dr-table tbody tr, #suspect-table tbody tr').forEach(bindRemove);

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

addBtn('add-breach-row', function () {
        addDynamicRow('breach-tbody', 'breach', function (prefix, idx) {
            const sn = parseInt(document.querySelectorAll('#breach-tbody tr').length) + 1;
            return '' +
                '<td>' + sn + '</td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][zone]" placeholder="Zone"></td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][command]" placeholder="Command"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][companies]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][expatriates]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][officers]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][nigerians]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni row-total" readonly tabindex="-1" name="' + prefix + '[' + idx + '][total]" placeholder="0"></td>' +
                '<td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>';
        });
    });

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

    addBtn('add-dfu-row', function () {
        addDynamicRow('dfu-tbody', 'dfu', function (prefix, idx) {
            const sn = parseInt(document.querySelectorAll('#dfu-tbody tr').length) + 1;
            return '' +
                '<td>' + sn + '</td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][zone]" placeholder="Zone"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][dfit_team]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][inv_exam]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][retrieval]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][nigerians]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][border_fwd]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][legal_unit]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][attestation]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][authentication]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][cgis]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][shq_damaged]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][shq_change]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][phone_forensic]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][non_collection]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][airports]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni row-total" readonly tabindex="-1" name="' + prefix + '[' + idx + '][total]" placeholder="0"></td>' +
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

    addBtn('add-suspect-row', function () {
        addDynamicRow('suspect-tbody', 'suspect', function (prefix, idx) {
            const sn = parseInt(document.querySelectorAll('#suspect-tbody tr').length) + 1;
            return '' +
                '<td>' + sn + '</td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][zone]" placeholder="Zone"></td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][command]" placeholder="Command"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][watch_persons]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][stop_persons]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][watch_passports]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][vacation_stop]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][persons_index]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input" name="' + prefix + '[' + idx + '][searches]" placeholder="0"></td>' +
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
    const form = document.getElementById('investigation-compliance-form');
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
})();
</script>

@include('partials.footer')

