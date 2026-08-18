@extends('user.directorates._layout')

{{-- This view renders its own tab bar and its own Review & Submit tab,
     so the shared layout skips both. --}}
@section('directorate-tabs', '1')
@section('directorate-preview', '1')

@section('directorate-sections')

@php
        $ranks = [
            'Deputy Comptroller - General',
            'Assistant Comptroller - General',
            'Comptroller of Immigration',
            'Deputy Comptroller of Immigration',
            'Assistant Comptroller of Immigration',
            'Chief Superintendent of Immigration',
            'Superintendent of Immigration',
            'Deputy Superintendent of Immigration',
            'Assistant Superintendent I',
            'Assistant Superintendent II',
            'Inspector of Immigration',
            'Assistant Inspector of Immigration',
            'Immigration Assistant I',
            'Immigration Assistant II',
            'Immigration Assistant III'
        ];

        $commands = [
            'ZONE A' => ['LASC', 'SEBC', 'IDBC', 'LSPMC', 'LAPC', 'LABPC', 'MMIA', 'OGSC'],
            'ZONE B' => ['KDSC', 'KNSC', 'ITSK', 'MAKIA', 'KTSC', 'JIBIYA', 'SOSC', 'ICSC', 'ILBC', 'ZMSC', 'JGSC'],
            'ZONE C' => ['BASC', 'YBSC', 'BOSC', 'ADSC', 'GMSC', 'PLSC'],
            'ZONE D' => ['FCTC', 'NAIA', 'KWSC', 'NGSC', 'KBSC'],
            'ZONE E' => ['ABSC', 'AKSC', 'CRSC', 'MFBC', 'EBSC', 'IMSC', 'NITSOL', 'RVSC', 'NITSA', 'RVMC'],
            'ZONE F' => ['OYSC', 'OSSC', 'ODSC', 'EKSC'],
            'ZONE G' => ['ANSC', 'BYSC', 'DTSC', 'ENSC', 'EDSC'],
            'ZONE H' => ['BNSC', 'KGSC', 'TRSC', 'NASC']
        ];

        $mouCountries = [
            'ecowas' => ['Benin', 'Burkina Faso', 'Cabo Verde', "Cote d'Ivoire", 'Gambia', 'Ghana', 'Guinea', 'Guinea-Bissau', 'Liberia', 'Mali', 'Niger', 'Nigeria', 'Senegal', 'Sierra Leone', 'Togo'],
            'non_ecowas' => ['Algeria', 'Angola', 'Botswana', 'Burundi', 'Cameroon', 'Cape Verde', 'Central African Republic', 'Chad', 'Comoros', 'Congo', 'Dem. Rep. Congo', 'Djibouti', 'Egypt', 'Equatorial Guinea', 'Eritrea', 'Eswatini', 'Ethiopia', 'Gabon', 'Kenya', 'Lesotho', 'Libya', 'Madagascar', 'Malawi', 'Mauritania', 'Mauritius', 'Morocco', 'Mozambique', 'Namibia', 'Rwanda', 'Sao Tome and Principe', 'Seychelles', 'Somalia', 'South Africa', 'South Sudan', 'Sudan', 'Tanzania', 'Tunisia', 'Uganda', 'Zambia', 'Zimbabwe'],
            'asia' => ['Afghanistan', 'Armenia', 'Azerbaijan', 'Bahrain', 'Bangladesh', 'Bhutan', 'Brunei', 'Cambodia', 'China', 'Georgia', 'India', 'Indonesia', 'Iran', 'Iraq', 'Israel', 'Japan', 'Jordan', 'Kazakhstan', 'Kuwait', 'Kyrgyzstan', 'Laos', 'Lebanon', 'Malaysia', 'Maldives', 'Mongolia', 'Myanmar', 'Nepal', 'North Korea', 'Oman', 'Pakistan', 'Philippines', 'Qatar', 'Saudi Arabia', 'Singapore', 'South Korea', 'Sri Lanka', 'Syria', 'Taiwan', 'Tajikistan', 'Thailand', 'Timor-Leste', 'Turkey', 'Turkmenistan', 'UAE', 'Uzbekistan', 'Vietnam', 'Yemen'],
            'europe' => ['Albania', 'Andorra', 'Austria', 'Belarus', 'Belgium', 'Bosnia and Herzegovina', 'Bulgaria', 'Croatia', 'Cyprus', 'Czech Republic', 'Denmark', 'Estonia', 'Finland', 'France', 'Germany', 'Greece', 'Hungary', 'Iceland', 'Ireland', 'Italy', 'Kosovo', 'Latvia', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Malta', 'Moldova', 'Monaco', 'Montenegro', 'Netherlands', 'North Macedonia', 'Norway', 'Poland', 'Portugal', 'Romania', 'Russia', 'San Marino', 'Serbia', 'Slovakia', 'Slovenia', 'Spain', 'Sweden', 'Switzerland', 'Ukraine', 'United Kingdom', 'Vatican City'],
            'north_am' => ['Antigua and Barbuda', 'Bahamas', 'Barbados', 'Belize', 'Canada', 'Costa Rica', 'Cuba', 'Dominica', 'Dominican Republic', 'El Salvador', 'Grenada', 'Guatemala', 'Haiti', 'Honduras', 'Jamaica', 'Mexico', 'Nicaragua', 'Panama', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 'Trinidad and Tobago', 'USA'],
            'south_am' => ['Argentina', 'Bolivia', 'Brazil', 'Chile', 'Colombia', 'Ecuador', 'Guyana', 'Paraguay', 'Peru', 'Suriname', 'Uruguay', 'Venezuela'],
            'australia' => ['Australia', 'Fiji', 'Kiribati', 'Marshall Islands', 'Micronesia', 'Nauru', 'New Zealand', 'Palau', 'Papua New Guinea', 'Samoa', 'Solomon Islands', 'Tonga', 'Tuvalu', 'Vanuatu']
        ];
        @endphp

 <!-- ═══ MIGRATION TABS ═══ -->
    <div class="entry-tabs-wrap" style="position:static;margin-bottom:16px;box-shadow:none;border-radius:var(--radius-md);border:1px solid var(--gray-200);max-width:100%;width:100%;">
        <div class="entry-tabs" id="migrationFormTabs" style="border-bottom:none; overflow-x: auto !important; display: flex !important; flex-wrap: nowrap !important; width: 100% !important; max-width: 100% !important; -webkit-overflow-scrolling: touch !important; touch-action: pan-x !important;">
            <button class="entry-tab active" data-m-tab="staff" style="flex-shrink:0 !important; display:flex !important; align-items:center !important;">
                <i class="fas fa-users" style="font-size:.78rem;"></i> Personnel Strength
            </button>
            <button class="entry-tab" data-m-tab="som" style="flex-shrink:0 !important; display:flex !important; align-items:center !important;">
                <i class="fas fa-shield-halved" style="font-size:.78rem;"></i> Smuggling of Migrants
            </button>
            <button class="entry-tab" data-m-tab="tip" style="flex-shrink:0 !important; display:flex !important; align-items:center !important;">
                <i class="fas fa-person-circle-exclamation" style="font-size:.78rem;"></i> Trafficking in Persons
            </button>
            <button class="entry-tab" data-m-tab="refugees" style="flex-shrink:0 !important; display:flex !important; align-items:center !important;">
                <i class="fas fa-hands-holding" style="font-size:.78rem;"></i> Refugees &amp; Asylum Seekers
            </button>
            <button class="entry-tab" data-m-tab="mous" style="flex-shrink:0 !important; display:flex !important; align-items:center !important;">
                <i class="fas fa-file-contract" style="font-size:.78rem;"></i> MoUs with Countries
            </button>
            <button class="entry-tab" data-m-tab="reports" style="flex-shrink:0 !important; display:flex !important; align-items:center !important;">
                <i class="fas fa-file-alt" style="font-size:.78rem;"></i> General Report
            </button>
            <button class="entry-tab" data-m-tab="review" style="color:var(--color-primary);font-weight:700;flex-shrink:0 !important; display:flex !important; align-items:center !important;">
                <i class="fas fa-check-double" style="font-size:.78rem;"></i> Review & Submit
            </button>
        </div>
    </div>

    {{-- The shared layout provides the <form>; do not nest another one here. --}}

        <!-- ═══ TAB 1: STAFF STRENGTH ═══ -->
        <div class="m-tab-content active" id="tab-staff">
            <div class="redas-card">
                <div class="card-head">
                    <div class="card-head-title">Personnel Strength Rank-by-Rank Breakdown</div>
                </div>
                <div class="card-body no-pad" style="overflow-x:auto;">
                    <table class="redas-table" style="min-width:600px;">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th style="width:180px;">Male</th>
                                <th style="width:180px;">Female</th>
                                <th style="width:180px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ranks as $rank)
                            @php $slugRank = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $rank)); @endphp
                            <tr>
                                <td><strong>{{ $rank }}</strong></td>
                                <td>
                                    <input type="number" min="0" class="ni staff-male-input"
                                        name="staff_strength[{{ $slugRank }}][male]"
                                        id="male-{{ $slugRank }}" value="0" placeholder="0" style="padding:6px 10px;">
                                </td>
                                <td>
                                    <input type="number" min="0" class="ni staff-female-input"
                                        name="staff_strength[{{ $slugRank }}][female]"
                                        id="female-{{ $slugRank }}" value="0" placeholder="0" style="padding:6px 10px;">
                                </td>
                                <td>
                                    <input type="number" class="ni staff-total-output"
                                        name="staff_strength[{{ $slugRank }}][total]"
                                        id="total-{{ $slugRank }}" value="0" readonly style="background:var(--gray-50);padding:6px 10px;font-weight:700;">
                                </td>
                            </tr>
                            @endforeach
                            <tr style="font-weight:800;">
                                <td>TOTAL PERSONNEL STRENGTH</td>
                                <td id="staff-male-total">0</td>
                                <td id="staff-female-total">0</td>
                                <td id="staff-grand-total">0</td>
                            </tr>
                        </tbody>
                    </table>
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

        <!-- ═══ TAB 2: SMUGGLING OF MIGRANTS (SOM) ═══ -->
        <div class="m-tab-content" id="tab-som" style="display:none;">
            <div class="redas-card">
                <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;padding:12px 20px;border-bottom:1px solid var(--gray-200);">
                    <div class="card-head-title">Anti-Smuggling of Migrants Returns (ANTI-SOM)</div>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <select id="som-zone-selector" class="ni ni-select" style="padding:4px 10px;font-size:0.8rem;width:150px;height:34px;">
                            <option value="">Add Zone...</option>
                            <option value="ZONE B">Zone B</option>
                            <option value="ZONE C">Zone C</option>
                            <option value="ZONE D">Zone D</option>
                            <option value="ZONE E">Zone E</option>
                            <option value="ZONE F">Zone F</option>
                            <option value="ZONE G">Zone G</option>
                            <option value="ZONE H">Zone H</option>
                        </select>
                        <button type="button" class="btn-nis btn-sm btn-primary-nis" onclick="addSomZone()" style="padding:6px 12px;font-size:0.8rem;">
                            <i class="fas fa-plus"></i> Add Zone Rows
                        </button>
                    </div>
                </div>
                <div style="background:var(--gray-50);padding:8px 20px;font-size:0.75rem;font-weight:700;color:var(--gray-500);display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <span>ACTIVE ZONES IN FORM:</span>
                    <div id="som-active-list" style="display:flex;gap:4px;">
                        <span class="status-badge badge-approved" id="som-badge-zone_a">Zone A</span>
                    </div>
                </div>
                <div class="card-body no-pad" style="overflow-x:auto;">
                    <table class="redas-table" style="min-width:1250px;font-size:0.8rem;">
                        <thead>
                            <tr>
                                <th style="width:110px;text-align:center;">Zone</th>
                                <th style="width:110px;">Command</th>
                                <th>Migrants Intercepted</th>
                                <th>Smugglers Arrested</th>
                                <th>Cases Investigation</th>
                                <th>Cases Prosecution</th>
                                <th>No. Convicted</th>
                                <th>Reunited w/ Family</th>
                                <th>Referral Agencies</th>
                                <th>Nigerians Repatriated</th>
                                <th>Foreigners Repatriated</th>
                                <th style="width:150px;min-width:150px;">Total</th>
                            </tr>
                        </thead>
                        @foreach($commands as $zone => $zoneCommands)
                        @php $zoneId = strtolower(str_replace(' ', '_', $zone)); @endphp
                        <tbody class="som-zone-group {{ $zone === 'ZONE A' ? '' : 'd-none' }}" id="som-zone-{{ $zoneId }}">
                            @foreach($zoneCommands as $commandName)
                            <tr class="som-row" @if($loop->first) style="border-top:3px solid var(--nis-600) !important;" @endif>
                                @if($loop->first)
                                <td rowspan="{{ count($zoneCommands) }}" style="vertical-align:middle;font-weight:800;background:var(--gray-50);text-align:center;border-right:1px solid var(--gray-200);">
                                    {{ $zone }}
                                </td>
                                @endif
                                <td><strong>{{ $commandName }}</strong></td>
                                @foreach([
                                    'migrants_intercepted',
                                    'smugglers_arrested',
                                    'cases_investigation',
                                    'cases_prosecution',
                                    'smugglers_convicted',
                                    'reunited_family',
                                    'referrals',
                                    'repatriated_nigerians',
                                    'repatriated_foreigners'
                                ] as $fieldKey)
                                <td>
                                    <input type="number" min="0" class="ni som-field-input"
                                        name="smuggling_of_migrants[{{ $commandName }}][{{ $fieldKey }}]"
                                        data-command="{{ $commandName }}" data-field="{{ $fieldKey }}" value="0" placeholder="0" style="padding:4px 6px;font-size:0.8rem;text-align:center;">
                                </td>
                                @endforeach
                                <td>
                                    <input type="number" class="ni som-row-total"
                                        name="smuggling_of_migrants[{{ $commandName }}][total]"
                                        id="som-total-{{ $commandName }}" value="0" readonly style="background:var(--gray-50);padding:4px 6px;font-size:0.8rem;font-weight:700;text-align:center;min-width:130px;">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

        <!-- ═══ TAB 3: TRAFFICKING IN PERSONS (TIP) ═══ -->
        <div class="m-tab-content" id="tab-tip" style="display:none;">
            <div class="redas-card">
                <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;padding:12px 20px;border-bottom:1px solid var(--gray-200);">
                    <div class="card-head-title">Trafficking In Persons Returns (TIP)</div>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <select id="tip-zone-selector" class="ni ni-select" style="padding:4px 10px;font-size:0.8rem;width:150px;height:34px;">
                            <option value="">Add Zone...</option>
                            <option value="ZONE B">Zone B</option>
                            <option value="ZONE C">Zone C</option>
                            <option value="ZONE D">Zone D</option>
                            <option value="ZONE E">Zone E</option>
                            <option value="ZONE F">Zone F</option>
                            <option value="ZONE G">Zone G</option>
                            <option value="ZONE H">Zone H</option>
                        </select>
                        <button type="button" class="btn-nis btn-sm btn-primary-nis" onclick="addTipZone()" style="padding:6px 12px;font-size:0.8rem;">
                            <i class="fas fa-plus"></i> Add Zone Rows
                        </button>
                    </div>
                </div>
                <div style="background:var(--gray-50);padding:8px 20px;font-size:0.75rem;font-weight:700;color:var(--gray-500);display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <span>ACTIVE ZONES IN FORM:</span>
                    <div id="tip-active-list" style="display:flex;gap:4px;">
                        <span class="status-badge badge-approved" id="tip-badge-zone_a">Zone A</span>
                    </div>
                </div>
                <div class="card-body no-pad" style="overflow-x:auto;">
                    <table class="redas-table" style="min-width:1150px;font-size:0.8rem;">
                        <thead>
                            <tr>
                                <th style="width:110px;text-align:center;">Zone</th>
                                <th style="width:110px;">Command</th>
                                <th>TIP Victims Rescued</th>
                                <th>Traffickers Arrested</th>
                                <th>Victims referred NAPTIP</th>
                                <th>Traffickers referred NAPTIP</th>
                                <th>Victims Reunited</th>
                                <th>CL Victims Apprehended NAPTIP</th>
                                <th>Victims Repatriated NAPTIP</th>
                                <th style="width:150px;min-width:150px;">Total</th>
                            </tr>
                        </thead>
                        @foreach($commands as $zone => $zoneCommands)
                        @php $zoneId = strtolower(str_replace(' ', '_', $zone)); @endphp
                        <tbody class="tip-zone-group {{ $zone === 'ZONE A' ? '' : 'd-none' }}" id="tip-zone-{{ $zoneId }}">
                            @foreach($zoneCommands as $commandName)
                            <tr class="tip-row" @if($loop->first) style="border-top:3px solid var(--nis-600) !important;" @endif>
                                @if($loop->first)
                                <td rowspan="{{ count($zoneCommands) }}" style="vertical-align:middle;font-weight:800;background:var(--gray-50);text-align:center;border-right:1px solid var(--gray-200);">
                                    {{ $zone }}
                                </td>
                                @endif
                                <td><strong>{{ $commandName }}</strong></td>
                                @foreach([
                                    'victims_rescued',
                                    'traffickers_arrested',
                                    'victims_referred_naptip',
                                    'traffickers_referred_naptip',
                                    'victims_reunited',
                                    'cl_victims_naptip',
                                    'victims_repatriated'
                                ] as $fieldKey)
                                <td>
                                    <input type="number" min="0" class="ni tip-field-input"
                                        name="trafficking_in_persons[{{ $commandName }}][{{ $fieldKey }}]"
                                        data-command="{{ $commandName }}" data-field="{{ $fieldKey }}" value="0" placeholder="0" style="padding:4px 6px;font-size:0.8rem;text-align:center;">
                                </td>
                                @endforeach
                                <td>
                                    <input type="number" class="ni tip-row-total"
                                        name="trafficking_in_persons[{{ $commandName }}][total]"
                                        id="tip-total-{{ $commandName }}" value="0" readonly style="background:var(--gray-50);padding:4px 6px;font-size:0.8rem;font-weight:700;text-align:center;min-width:130px;">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

        <!-- ═══ TAB 4: REFUGEES & ASYLUM SEEKERS ═══ -->
        <div class="m-tab-content" id="tab-refugees" style="display:none;">
            <!-- Refugee Applications Card -->
            <div class="redas-card" style="margin-bottom:16px;">
                <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                    <div class="card-head-title">Refugee Applications by State</div>
                    <button type="button" class="btn-nis btn-ghost btn-sm" onclick="addRefugeeRow()">
                        <i class="fas fa-plus"></i> Add State Row
                    </button>
                </div>
                <div class="card-body no-pad" style="overflow-x:auto;">
                    <table class="redas-table" id="refugeesTable" style="min-width:600px;">
                        <thead>
                            <tr>
                                <th>State</th>
                                <th style="width:160px;">Applications</th>
                                <th style="width:160px;">Approved</th>
                                <th style="width:160px;">Rejected</th>
                                <th style="width:140px;">Total</th>
                                <th style="width:60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="refugeesBody">
                            <tr class="refugee-row">
                                <td>
                                    <select name="refugees_asylum_seekers[refugees][0][state]" class="ni ni-select" required style="padding:6px 10px;">
                                        <option value="">Select State</option>
                                        @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara','FCT'] as $s)
                                        <option value="{{ $s }}">{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" min="0" class="ni refugee-apps" name="refugees_asylum_seekers[refugees][0][applications]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
                                </td>
                                <td>
                                    <input type="number" min="0" class="ni refugee-approved" name="refugees_asylum_seekers[refugees][0][approved]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
                                </td>
                                <td>
                                    <input type="number" min="0" class="ni refugee-rejected" name="refugees_asylum_seekers[refugees][0][rejected]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
                                </td>
                                <td>
                                    <input type="number" class="ni refugee-total" name="refugees_asylum_seekers[refugees][0][total]" value="0" readonly style="background:var(--gray-50);padding:6px 10px;text-align:center;font-weight:700;">
                                </td>
                                <td>
                                    <button type="button" class="btn-nis btn-ghost btn-sm" onclick="removeRow(this)" style="color:var(--color-danger);"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Asylum Seekers Applications Card -->
            <div class="redas-card">
                <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                    <div class="card-head-title">Asylum Seekers Applications by State</div>
                    <button type="button" class="btn-nis btn-ghost btn-sm" onclick="addAsylumRow()">
                        <i class="fas fa-plus"></i> Add State Row
                    </button>
                </div>
                <div class="card-body no-pad" style="overflow-x:auto;">
                    <table class="redas-table" id="asylumTable" style="min-width:600px;">
                        <thead>
                            <tr>
                                <th>State</th>
                                <th style="width:160px;">Applications</th>
                                <th style="width:160px;">Approved</th>
                                <th style="width:160px;">Rejected</th>
                                <th style="width:140px;">Total</th>
                                <th style="width:60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="asylumBody">
                            <tr class="asylum-row">
                                <td>
                                    <select name="refugees_asylum_seekers[asylum_seekers][0][state]" class="ni ni-select" required style="padding:6px 10px;">
                                        <option value="">Select State</option>
                                        @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara','FCT'] as $s)
                                        <option value="{{ $s }}">{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" min="0" class="ni asylum-apps" name="refugees_asylum_seekers[asylum_seekers][0][applications]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
                                </td>
                                <td>
                                    <input type="number" min="0" class="ni asylum-approved" name="refugees_asylum_seekers[asylum_seekers][0][approved]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
                                </td>
                                <td>
                                    <input type="number" min="0" class="ni asylum-rejected" name="refugees_asylum_seekers[asylum_seekers][0][rejected]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
                                </td>
                                <td>
                                    <input type="number" class="ni asylum-total" name="refugees_asylum_seekers[asylum_seekers][0][total]" value="0" readonly style="background:var(--gray-50);padding:6px 10px;text-align:center;font-weight:700;">
                                </td>
                                <td>
                                    <button type="button" class="btn-nis btn-ghost btn-sm" onclick="removeRow(this)" style="color:var(--color-danger);"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ═══ TAB 5: MoUs WITH COUNTRIES ═══ -->
        <div class="m-tab-content" id="tab-mous" style="display:none;">
            @foreach([
                'ecowas'      => 'ECOWAS Countries',
                'non_ecowas'  => 'Non-ECOWAS African Countries',
                'asia'        => 'Asia',
                'australia'   => 'Australia',
                'europe'      => 'Europe',
                'north_am'    => 'North America',
                'south_am'    => 'South America'
            ] as $key => $title)
            <div class="redas-card" style="margin-bottom:14px;">
                <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                    <div class="card-head-title">Memorandum of Understanding with Countries: {{ $title }}</div>
                    <button type="button" class="btn-nis btn-ghost btn-sm" onclick="addMouRow('{{ $key }}')">
                        <i class="fas fa-plus"></i> Add Country Row
                    </button>
                </div>
                <div class="card-body no-pad" style="overflow-x:auto;">
                    <table class="redas-table" id="mouTable-{{ $key }}" style="min-width:600px;">
                        <thead>
                            <tr>
                                <th style="width:280px;">Countries</th>
                                <th>Description / Year</th>
                                <th style="width:60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="mouBody-{{ $key }}">
                            <tr class="mou-row-{{ $key }}">
                                <td>
                                    <select name="mou_countries[{{ $key }}][0][country]" class="ni ni-select" required style="padding:6px 10px;">
                                        <option value="">Select Country</option>
                                        @foreach($mouCountries[$key] as $country)
                                        <option value="{{ $country }}">{{ $country }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="ni" name="mou_countries[{{ $key }}][0][description]" placeholder="e.g. Visa Waiver Agreement (2024)" required style="padding:6px 10px;">
                                </td>
                                <td>
                                    <button type="button" class="btn-nis btn-ghost btn-sm" onclick="removeRow(this)" style="color:var(--color-danger);"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>

        <!-- TAB: GENERAL REPORT -->
        <div class="m-tab-content" id="tab-reports" style="display:none;">
            <div class="redas-card" style="margin-bottom:16px;">
                <div class="card-head"><div class="card-head-title">CHALLENGES &amp; WAY FORWARD</div></div>
                <div class="card-body">
                    <div class="auth-form-group" style="margin-bottom:12px;">
                        <label class="form-label-nis">Major Challenges Faced</label>
                        <textarea name="general_report[challenges]" class="ni" rows="4" placeholder="Describe any migration challenges..."></textarea>
                    </div>
                    <div class="auth-form-group">
                        <label class="form-label-nis">Recommendations / Way Forward</label>
                        <textarea name="general_report[recommendations]" class="ni" rows="4" placeholder="Propose solutions or recommendations..."></textarea>
                    </div>
                </div>
            </div>

            <div class="redas-card" style="margin-bottom:16px;">
                <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                    <div class="card-head-title">SUPPORTING DOCUMENTS</div>
                    <button type="button" class="btn-nis btn-ghost btn-sm" onclick="addDocumentInput()" style="padding:4px 10px;font-size:0.8rem;">
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

            {{-- <div class="redas-card" style="margin-bottom:16px;">
                <div class="card-head"><div class="card-head-title">REPORTER</div></div>
                <div class="card-body">
                    <div class="form-grid-2">
                        <div class="auth-form-group">
                            <label class="form-label-nis">Full Name of the Reporting Officer</label>
                            <input type="text" name="reporting_officer" class="ni" required value="{{ old('reporting_officer', auth()->user()->name) }}">
                        </div>
                        <div class="auth-form-group">
                            <label class="form-label-nis">Rank</label>
                            <input type="text" name="rank" class="ni">
                        </div>
                        <div class="auth-form-group">
                            <label class="form-label-nis">Phone Number</label>
                            <input type="text" name="gsm_number" class="ni">
                        </div>
                        <div class="auth-form-group">
                            <label class="form-label-nis">NIS Number</label>
                            <input type="text" name="reporting_officer_nis" class="ni" value="{{ old('reporting_officer_nis', preg_replace('/[^0-9]/', '', auth()->user()->service_number)) }}">
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>

        <!-- TAB: REVIEW & SUBMIT -->
        <div class="m-tab-content" id="tab-review" style="display:none;">
            <div style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;border-radius:12px;padding:16px;margin-bottom:16px;">
                <i class="fas fa-exclamation-triangle" style="margin-right:8px;color:#d97706;"></i>
                <strong>Review Your Submission:</strong> Please carefully review all the data you have entered below. Once you are sure everything is correct, click the Submit button at the bottom.
            </div>

            <div id="review-snapshot-container">
                <!-- Javascript will inject the locked snapshot here -->
            </div>

            {{-- <!--<div style="padding:20px;display:flex;justify-content:flex-end;align-items:center;">
                <button type="submit" class="btn-nis btn-primary" style="padding:12px 24px;font-size:1rem;">
                    <i class="fas fa-paper-plane" style="margin-right:8px;"></i> Submit Return
                </button>
            </div> --}}
        </div>

<script>
    // Predefined lists of countries for MoUs selection
    const countryLists = {
        ecowas: {!! json_encode($mouCountries['ecowas']) !!},
        non_ecowas: {!! json_encode($mouCountries['non_ecowas']) !!},
        asia: {!! json_encode($mouCountries['asia']) !!},
        europe: {!! json_encode($mouCountries['europe']) !!},
        north_am: {!! json_encode($mouCountries['north_am']) !!},
        south_am: {!! json_encode($mouCountries['south_am']) !!},
        australia: {!! json_encode($mouCountries['australia']) !!}
    };

    // Global scopes functions registered on window immediately
    window.addDocumentInput = () => {
        const container = document.getElementById('documents-body');
        const div = document.createElement('div');
        div.className = 'auth-form-group';
        div.style.marginBottom = '12px';
        div.innerHTML = '<input type="file" name="supporting_documents[]" class="ni" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg">';
        container.appendChild(div);
    };

    let refugeeIndex = 1;
    window.addRefugeeRow = () => {
        const body = document.getElementById('refugeesBody');
        const newRow = document.createElement('tr');
        newRow.className = 'refugee-row';
        newRow.innerHTML = `
            <td>
                <select name="refugees_asylum_seekers[refugees][${refugeeIndex}][state]" class="ni ni-select" required style="padding:6px 10px;">
                    <option value="">Select State</option>
                    @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara','FCT'] as $s)
                    <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" min="0" class="ni refugee-apps" name="refugees_asylum_seekers[refugees][${refugeeIndex}][applications]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
            </td>
            <td>
                <input type="number" min="0" class="ni refugee-approved" name="refugees_asylum_seekers[refugees][${refugeeIndex}][approved]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
            </td>
            <td>
                <input type="number" min="0" class="ni refugee-rejected" name="refugees_asylum_seekers[refugees][${refugeeIndex}][rejected]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
            </td>
            <td>
                <input type="number" class="ni refugee-total" name="refugees_asylum_seekers[refugees][${refugeeIndex}][total]" value="0" readonly style="background:var(--gray-50);padding:6px 10px;text-align:center;font-weight:700;">
            </td>
            <td>
                <button type="button" class="btn-nis btn-ghost btn-sm" onclick="removeRow(this)" style="color:var(--color-danger);"><i class="fas fa-trash"></i></button>
            </td>
        `;
        body.appendChild(newRow);

        // Listeners for calculation
        newRow.querySelectorAll('.refugee-apps, .refugee-approved, .refugee-rejected').forEach(input => {
            input.addEventListener('input', () => {
                const valApproved = parseInt(newRow.querySelector('.refugee-approved').value) || 0;
                const valRejected = parseInt(newRow.querySelector('.refugee-rejected').value) || 0;
                newRow.querySelector('.refugee-total').value = valApproved + valRejected;
            });
        });
        refugeeIndex++;
    };

    let asylumIndex = 1;
    window.addAsylumRow = () => {
        const body = document.getElementById('asylumBody');
        const newRow = document.createElement('tr');
        newRow.className = 'asylum-row';
        newRow.innerHTML = `
            <td>
                <select name="refugees_asylum_seekers[asylum_seekers][${asylumIndex}][state]" class="ni ni-select" required style="padding:6px 10px;">
                    <option value="">Select State</option>
                    @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara','FCT'] as $s)
                    <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" min="0" class="ni asylum-apps" name="refugees_asylum_seekers[asylum_seekers][${asylumIndex}][applications]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
            </td>
            <td>
                <input type="number" min="0" class="ni asylum-approved" name="refugees_asylum_seekers[asylum_seekers][${asylumIndex}][approved]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
            </td>
            <td>
                <input type="number" min="0" class="ni asylum-rejected" name="refugees_asylum_seekers[asylum_seekers][${asylumIndex}][rejected]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
            </td>
            <td>
                <input type="number" class="ni asylum-total" name="refugees_asylum_seekers[asylum_seekers][${asylumIndex}][total]" value="0" readonly style="background:var(--gray-50);padding:6px 10px;text-align:center;font-weight:700;">
            </td>
            <td>
                <button type="button" class="btn-nis btn-ghost btn-sm" onclick="removeRow(this)" style="color:var(--color-danger);"><i class="fas fa-trash"></i></button>
            </td>
        `;
        body.appendChild(newRow);

        // Listeners for calculation
        newRow.querySelectorAll('.asylum-apps, .asylum-approved, .asylum-rejected').forEach(input => {
            input.addEventListener('input', () => {
                const valApproved = parseInt(newRow.querySelector('.asylum-approved').value) || 0;
                const valRejected = parseInt(newRow.querySelector('.asylum-rejected').value) || 0;
                newRow.querySelector('.asylum-total').value = valApproved + valRejected;
            });
        });
        asylumIndex++;
    };

    const mouIndices = {};
    window.addMouRow = (key) => {
        if (!mouIndices[key]) {
            mouIndices[key] = 1;
        }
        const body = document.getElementById('mouBody-' + key);
        const newRow = document.createElement('tr');
        newRow.className = 'mou-row-' + key;

        let options = '<option value="">Select Country</option>';
        countryLists[key].forEach(c => {
            options += `<option value="${c}">${c}</option>`;
        });

        newRow.innerHTML = `
            <td>
                <select name="mou_countries[${key}][${mouIndices[key]}][country]" class="ni ni-select" required style="padding:6px 10px;">
                    ${options}
                </select>
            </td>
            <td>
                <input type="text" class="ni" name="mou_countries[${key}][${mouIndices[key]}][description]" placeholder="e.g. Agreement details" required style="padding:6px 10px;">
            </td>
            <td>
                <button type="button" class="btn-nis btn-ghost btn-sm" onclick="removeRow(this)" style="color:var(--color-danger);"><i class="fas fa-trash"></i></button>
            </td>
        `;
        body.appendChild(newRow);
        mouIndices[key]++;
    };

    window.removeRow = (btn) => {
        const row = btn.closest('tr');
        row?.remove();

        // Recalculate totals if it was staff strength
        let totalMale = 0, totalFemale = 0, grandTotal = 0;
        document.querySelectorAll('.staff-male-input').forEach(input => {
            const valMale = parseInt(input.value) || 0;
            const femaleInput = input.closest('tr').querySelector('.staff-female-input');
            const valFemale = parseInt(femaleInput.value) || 0;
            totalMale += valMale;
            totalFemale += valFemale;
            grandTotal += (valMale + valFemale);
        });
        const maleEl = document.getElementById('staff-male-total');
        if (maleEl) maleEl.textContent = totalMale.toLocaleString();
        const femaleEl = document.getElementById('staff-female-total');
        if (femaleEl) femaleEl.textContent = totalFemale.toLocaleString();
        const grandEl = document.getElementById('staff-grand-total');
        if (grandEl) grandEl.textContent = grandTotal.toLocaleString();
    };

    window.addSomZone = () => {
        const selector = document.getElementById('som-zone-selector');
        const zone = selector.value;
        if (!zone) return;

        const zoneId = zone.toLowerCase().replace(' ', '_');
        const tbody = document.getElementById('som-zone-' + zoneId);
        if (tbody) {
            tbody.classList.remove('d-none');

            // Add badge
            const activeList = document.getElementById('som-active-list');
            const badge = document.createElement('span');
            badge.className = 'status-badge badge-approved';
            badge.id = 'som-badge-' + zoneId;
            badge.textContent = zone;
            badge.style.marginLeft = '4px';
            activeList.appendChild(badge);

            // Remove option
            const option = selector.querySelector(`option[value="${zone}"]`);
            option?.remove();
            selector.value = '';

            if (window.REDAS) {
                window.REDAS.showToast(`${zone} added to form.`, 'success');
            } else {
                alert(`${zone} added to form.`);
            }
        }
    };

    window.addTipZone = () => {
        const selector = document.getElementById('tip-zone-selector');
        const zone = selector.value;
        if (!zone) return;

        const zoneId = zone.toLowerCase().replace(' ', '_');
        const tbody = document.getElementById('tip-zone-' + zoneId);
        if (tbody) {
            tbody.classList.remove('d-none');

            // Add badge
            const activeList = document.getElementById('tip-active-list');
            const badge = document.createElement('span');
            badge.className = 'status-badge badge-approved';
            badge.id = 'tip-badge-' + zoneId;
            badge.textContent = zone;
            badge.style.marginLeft = '4px';
            activeList.appendChild(badge);

            // Remove option
            const option = selector.querySelector(`option[value="${zone}"]`);
            option?.remove();
            selector.value = '';

            if (window.REDAS) {
                window.REDAS.showToast(`${zone} added to form.`, 'success');
            } else {
                alert(`${zone} added to form.`);
            }
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        const tabs = document.querySelectorAll('#migrationFormTabs .entry-tab');
        const contents = document.querySelectorAll('.m-tab-content');
        const tabOrder = Array.from(tabs).map(tab => tab.dataset.mTab).filter(Boolean);

        function showTab(target) {
            const tab = document.querySelector('#migrationFormTabs .entry-tab[data-m-tab="' + target + '"]');
            if (!tab) return;

            tabs.forEach(t => t.classList.toggle('active', t === tab));
            contents.forEach(content => {
                const isActive = content.id === 'tab-' + target;
                content.style.display = isActive ? 'block' : 'none';
                content.classList.toggle('active', isActive);
            });

            if (target === 'review') {
                generateReviewSnapshot();
            }

            updateActionButtons();
        }

        function updateActionButtons() {
            const activeTabId = document.querySelector('#migrationFormTabs .entry-tab.active')?.dataset.mTab || 'staff';
            contents.forEach(content => {
                const actions = content.querySelector('.hrm-actions');
                if (!actions) return;

                const prev = actions.querySelector('.hrm-prev-btn');
                const next = actions.querySelector('.hrm-next-btn');
                const contentKey = content.id.replace('tab-', '');
                const isFirst = contentKey === tabOrder[0];
                const isLast = contentKey === tabOrder[tabOrder.length - 1];

                if (prev) {
                    prev.disabled = isFirst;
                    prev.style.opacity = isFirst ? '0.5' : '1';
                    prev.style.cursor = isFirst ? 'not-allowed' : 'pointer';
                }

                if (next) {
                    next.innerHTML = isLast
                        ? '<i class="fas fa-check"></i> Submit'
                        : 'Next <i class="fas fa-arrow-right"></i>';
                    next.dataset.target = isLast ? 'review' : tabOrder[Math.min(tabOrder.indexOf(contentKey) + 1, tabOrder.length - 1)];
                }
            });

            const activeContent = document.getElementById('tab-' + activeTabId);
            if (activeContent) {
                const actionBar = activeContent.querySelector('.hrm-actions');
                if (actionBar && actionBar.querySelector('.hrm-next-btn')) {
                    const nextBtn = actionBar.querySelector('.hrm-next-btn');
                    nextBtn.disabled = false;
                }
            }
        }

        function saveDraft() {
            const form = document.querySelector('form[action*="directorates"]');
            if (!form) return;

            const data = {};
            new FormData(form).forEach((value, key) => {
                data[key] = value;
            });

            const draftKey = 'redas_migration_draft_' + (document.querySelector('[name="report_period"]')?.value || 'default');
            localStorage.setItem(draftKey, JSON.stringify(data));
            if (window.REDAS && typeof window.REDAS.showToast === 'function') {
                window.REDAS.showToast('Draft saved successfully.', 'success');
            } else {
                alert('Draft saved successfully.');
            }
        }

        contents.forEach(content => {
            if (content.querySelector('.hrm-actions')) return;

            const actions = document.createElement('div');
            actions.className = 'hrm-actions';
            actions.innerHTML = `
                <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
                <div class="hrm-actions-center">
                    <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
                </div>
                <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next <i class="fas fa-arrow-right"></i></button>
            `;

            content.appendChild(actions);
        });

        document.querySelectorAll('.hrm-prev-btn').forEach(button => {
            button.addEventListener('click', () => {
                const currentContent = button.closest('.m-tab-content');
                const currentKey = currentContent?.id.replace('tab-', '');
                const currentIndex = tabOrder.indexOf(currentKey);
                if (currentIndex > 0) {
                    showTab(tabOrder[currentIndex - 1]);
                }
            });
        });

        document.querySelectorAll('.hrm-next-btn').forEach(button => {
            button.addEventListener('click', () => {
                const currentContent = button.closest('.m-tab-content');
                const currentKey = currentContent?.id.replace('tab-', '');
                const currentIndex = tabOrder.indexOf(currentKey);

                if (currentIndex === tabOrder.length - 1) {
                    // On the final (Review & Submit) tab the primary button submits the return.
                    const form = document.querySelector('main form') || document.querySelector('form[action*="directorates"]');
                    if (!form) return;
                    // Required fields live on hidden tabs; an invalid control that is
                    // not focusable blocks submission silently, so switch to its tab first.
                    if (!form.checkValidity()) {
                        const invalid = form.querySelector(':invalid');
                        const panel = invalid ? invalid.closest('.m-tab-content') : null;
                        if (panel) showTab(panel.id.replace('tab-', ''));
                        form.reportValidity();
                        return;
                    }
                    if (window.confirm('Are you sure you want to submit this return?\n\nPlease verify all information before continuing.')) {
                        if (form.requestSubmit) form.requestSubmit();
                        else form.submit();
                    }
                    return;
                }

                if (currentIndex >= 0) {
                    showTab(tabOrder[currentIndex + 1]);
                }
            });
        });

        document.querySelectorAll('.hrm-save-draft-btn').forEach(button => {
            button.addEventListener('click', saveDraft);
        });

        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                showTab(tab.dataset.mTab);
            });
        });

        updateActionButtons();

        function generateReviewSnapshot() {
            const container = document.getElementById('review-snapshot-container');
            container.innerHTML = ''; // Clear previous snapshot

            // Grab all redas-cards from all tabs except the review tab
            const sourceTabs = document.querySelectorAll('.m-tab-content:not(#tab-review)');

            sourceTabs.forEach(tab => {
                const cards = tab.querySelectorAll('.redas-card');
                cards.forEach(card => {
                    // Clone the card
                    const clone = card.cloneNode(true);

                    // Remove all buttons (Add More, etc)
                    clone.querySelectorAll('button').forEach(btn => btn.remove());

                    // Strip 'name' and 'id' attributes to prevent submission conflicts
                    // and disable the fields
                    clone.querySelectorAll('input, select, textarea').forEach(input => {
                        // Crucial: Copy the LIVE value from the original form to the clone
                        // because cloneNode does not copy the dynamic value state
                        const originalInput = card.querySelector(`[name="${input.getAttribute('name')}"]`);
                        if(originalInput) {
                            input.value = originalInput.value;
                            if(input.tagName === 'SELECT') {
                                input.innerHTML = `<option>${originalInput.value}</option>`;
                            }
                        }

                        input.removeAttribute('name');
                        input.removeAttribute('id');
                        input.setAttribute('readonly', 'readonly');
                        input.setAttribute('disabled', 'disabled');

                        // Fix styling for disabled inputs to look cleaner
                        input.style.backgroundColor = 'transparent';
                        input.style.border = 'none';
                        input.style.fontWeight = 'bold';
                        input.style.color = 'var(--nis-900)';
                        input.style.padding = '0';
                    });

                    // Add a subtle border to the clone for the review tab
                    clone.style.border = '1px solid var(--nis-200)';
                    clone.style.boxShadow = 'none';

                    container.appendChild(clone);
                });
            });
        }

        // Auto calculation: Staff Strength
        const recalculateStaff = () => {
            let totalMale = 0;
            let totalFemale = 0;
            let grandTotal = 0;

            document.querySelectorAll('.staff-male-input').forEach(input => {
                const femaleInput = input.closest('tr').querySelector('.staff-female-input');
                const totalOutput = input.closest('tr').querySelector('.staff-total-output');

                const valMale = parseInt(input.value) || 0;
                const valFemale = parseInt(femaleInput.value) || 0;
                const valTotal = valMale + valFemale;

                totalOutput.value = valTotal;

                totalMale += valMale;
                totalFemale += valFemale;
                grandTotal += valTotal;
            });

            document.getElementById('staff-male-total').textContent = totalMale.toLocaleString();
            document.getElementById('staff-female-total').textContent = totalFemale.toLocaleString();
            document.getElementById('staff-grand-total').textContent = grandTotal.toLocaleString();
        };

        document.querySelectorAll('.staff-male-input, .staff-female-input').forEach(input => {
            input.addEventListener('input', recalculateStaff);
        });

        // Auto calculation: Smuggling of Migrants (SOM)
        const recalculateSom = (e) => {
            const row = e.target.closest('.som-row');
            let rowTotal = 0;
            row.querySelectorAll('.som-field-input').forEach(input => {
                rowTotal += parseInt(input.value) || 0;
            });
            const output = row.querySelector('.som-row-total');
            if (output) output.value = rowTotal;
        };

        document.querySelectorAll('.som-field-input').forEach(input => {
            input.addEventListener('input', recalculateSom);
        });

        // Auto calculation: Trafficking In Persons (TIP)
        const recalculateTip = (e) => {
            const row = e.target.closest('.tip-row');
            let rowTotal = 0;
            row.querySelectorAll('.tip-field-input').forEach(input => {
                rowTotal += parseInt(input.value) || 0;
            });
            const output = row.querySelector('.tip-row-total');
            if (output) output.value = rowTotal;
        };

        document.querySelectorAll('.tip-field-input').forEach(input => {
            input.addEventListener('input', recalculateTip);
        });

        // Auto calculation: Refugees Initial Row
        document.querySelectorAll('.refugee-row').forEach(row => {
            row.querySelectorAll('.refugee-apps, .refugee-approved, .refugee-rejected').forEach(input => {
                input.addEventListener('input', () => {
                    const valApproved = parseInt(row.querySelector('.refugee-approved').value) || 0;
                    const valRejected = parseInt(row.querySelector('.refugee-rejected').value) || 0;
                    row.querySelector('.refugee-total').value = valApproved + valRejected;
                });
            });
        });

        // Auto calculation: Asylum Seekers Initial Row
        document.querySelectorAll('.asylum-row').forEach(row => {
            row.querySelectorAll('.asylum-apps, .asylum-approved, .asylum-rejected').forEach(input => {
                input.addEventListener('input', () => {
                    const valApproved = parseInt(row.querySelector('.asylum-approved').value) || 0;
                    const valRejected = parseInt(row.querySelector('.asylum-rejected').value) || 0;
                    row.querySelector('.asylum-total').value = valApproved + valRejected;
                });
            });
        });
    });


</script>

@endsection
