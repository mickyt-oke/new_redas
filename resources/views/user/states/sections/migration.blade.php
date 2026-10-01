{{-- Migration directorate return sections — reusable partial.
     Included by the standalone wrapper (user.directorates.migration) and by the
     combined state form (user.states._return-layout, inside #dir-migration).
     Provides only the tab bar + data tab panels; the standalone wrapper adds the
     supporting-documents upload card and the Review & Submit tab. --}}

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

<style>
/* The tab buttons intentionally do NOT use the global .entry-tab class (the
   footer handler and the combined page's scoped handler both bind to it);
   mirror its styling here so the bar looks the same on both pages. */
#migrationFormTabs [data-m-tab] { display:flex; align-items:center; gap:7px; padding:14px 18px; font-size:.78rem; font-weight:600; color:var(--gray-500); white-space:nowrap; cursor:pointer; border:none; background:none; border-bottom:3px solid transparent; transition:all .2s; position:relative; }
#migrationFormTabs [data-m-tab]:hover { color:var(--nis-700); background:var(--nis-50); }
#migrationFormTabs [data-m-tab].active { color:var(--nis-700); border-bottom-color:var(--nis-600); background:var(--nis-50); }
</style>

 <!-- ═══ MIGRATION TABS ═══ -->
    <div class="entry-tabs-wrap" style="position:static;margin-bottom:16px;box-shadow:none;border-radius:var(--radius-md);border:1px solid var(--gray-200);max-width:100%;width:100%;">
        <div class="entry-tabs" id="migrationFormTabs" style="border-bottom:none; overflow-x: auto !important; display: flex !important; flex-wrap: nowrap !important; width: 100% !important; max-width: 100% !important; -webkit-overflow-scrolling: touch !important; touch-action: pan-x !important;">
            <button type="button" class="active" data-m-tab="staff" style="flex-shrink:0 !important; display:flex !important; align-items:center !important;">
                <i class="fas fa-users" style="font-size:.78rem;"></i> Personnel Strength
            </button>
            <button type="button" data-m-tab="som" style="flex-shrink:0 !important; display:flex !important; align-items:center !important;">
                <i class="fas fa-shield-halved" style="font-size:.78rem;"></i> Smuggling of Migrants
            </button>
            <button type="button" data-m-tab="tip" style="flex-shrink:0 !important; display:flex !important; align-items:center !important;">
                <i class="fas fa-person-circle-exclamation" style="font-size:.78rem;"></i> Trafficking in Persons
            </button>
            <button type="button" data-m-tab="refugees" style="flex-shrink:0 !important; display:flex !important; align-items:center !important;">
                <i class="fas fa-hands-holding" style="font-size:.78rem;"></i> Refugees &amp; Asylum Seekers
            </button>
            <button type="button" data-m-tab="mous" style="flex-shrink:0 !important; display:flex !important; align-items:center !important;">
                <i class="fas fa-file-contract" style="font-size:.78rem;"></i> MoUs with Countries
            </button>
            <button type="button" data-m-tab="reports" style="flex-shrink:0 !important; display:flex !important; align-items:center !important;">
                <i class="fas fa-file-alt" style="font-size:.78rem;"></i> General Report
            </button>
        </div>
    </div>

    {{-- The surrounding page provides the <form>; do not nest another one here. --}}

        <!-- ═══ TAB 1: STAFF STRENGTH ═══ -->
        <div class="m-tab-content active" id="tab-migration-staff">
            <div class="redas-card">
                <div class="card-head">
                    <div class="card-head-title">Personnel Strength Rank-by-Rank Breakdown</div>
                </div>
                <div class="card-body no-pad" style="overflow-x:auto;">
                @if($stateEmbedded ?? false)
                    <p style="font-size:.82rem;color:var(--gray-500);padding:12px 16px;">
                        <i class="fas fa-circle-info" style="color:var(--nis-500);margin-right:6px;"></i>
                        Staff strength for this directorate is captured once under the <strong>HRM</strong> section of this return.
                    </p>
                @else
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
                                        name="migration[staff_strength][{{ $slugRank }}][male]"
                                        id="migration-male-{{ $slugRank }}" value="0" placeholder="0" style="padding:6px 10px;">
                                </td>
                                <td>
                                    <input type="number" min="0" class="ni staff-female-input"
                                        name="migration[staff_strength][{{ $slugRank }}][female]"
                                        id="migration-female-{{ $slugRank }}" value="0" placeholder="0" style="padding:6px 10px;">
                                </td>
                                <td>
                                    <input type="number" class="ni staff-total-output"
                                        name="migration[staff_strength][{{ $slugRank }}][total]"
                                        id="migration-total-{{ $slugRank }}" value="0" readonly style="background:var(--gray-50);padding:6px 10px;font-weight:700;">
                                </td>
                            </tr>
                            @endforeach
                            <tr style="font-weight:800;">
                                <td>TOTAL PERSONNEL STRENGTH</td>
                                <td id="migration-staff-male-total">0</td>
                                <td id="migration-staff-female-total">0</td>
                                <td id="migration-staff-grand-total">0</td>
                            </tr>
                        </tbody>
                    </table>
                @endif
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
        <div class="m-tab-content" id="tab-migration-som" style="display:none;">
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
                        <button type="button" class="btn-nis btn-sm btn-primary-nis" data-m-action="add-som-zone" style="padding:6px 12px;font-size:0.8rem;">
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
        <div class="m-tab-content" id="tab-migration-tip" style="display:none;">
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
                        <button type="button" class="btn-nis btn-sm btn-primary-nis" data-m-action="add-tip-zone" style="padding:6px 12px;font-size:0.8rem;">
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
        <div class="m-tab-content" id="tab-migration-refugees" style="display:none;">
            <!-- Refugee Applications Card -->
            <div class="redas-card" style="margin-bottom:16px;">
                <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                    <div class="card-head-title">Refugee Applications by State</div>
                    <button type="button" class="btn-nis btn-ghost btn-sm" data-m-action="add-refugee-row">
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
                                    <select name="refugees_asylum_seekers[refugees][0][state]" class="ni ni-select" style="padding:6px 10px;">
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
                                    <button type="button" class="btn-nis btn-ghost btn-sm" data-m-action="remove-row" style="color:var(--color-danger);"><i class="fas fa-trash"></i></button>
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
                    <button type="button" class="btn-nis btn-ghost btn-sm" data-m-action="add-asylum-row">
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
                                    <select name="refugees_asylum_seekers[asylum_seekers][0][state]" class="ni ni-select" style="padding:6px 10px;">
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
                                    <button type="button" class="btn-nis btn-ghost btn-sm" data-m-action="remove-row" style="color:var(--color-danger);"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ═══ TAB 5: MoUs WITH COUNTRIES ═══ -->
        <div class="m-tab-content" id="tab-migration-mous" style="display:none;">
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
                    <button type="button" class="btn-nis btn-ghost btn-sm" data-m-action="add-mou-row" data-m-key="{{ $key }}">
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
                                    <select name="mou_countries[{{ $key }}][0][country]" class="ni ni-select" style="padding:6px 10px;">
                                        <option value="">Select Country</option>
                                        @foreach($mouCountries[$key] as $country)
                                        <option value="{{ $country }}">{{ $country }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="ni" name="mou_countries[{{ $key }}][0][description]" placeholder="e.g. Visa Waiver Agreement (2024)" style="padding:6px 10px;">
                                </td>
                                <td>
                                    <button type="button" class="btn-nis btn-ghost btn-sm" data-m-action="remove-row" style="color:var(--color-danger);"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>

        <!-- TAB: GENERAL REPORT -->
        <div class="m-tab-content" id="tab-migration-reports" style="display:none;">
            <div class="redas-card" style="margin-bottom:16px;">
                <div class="card-head"><div class="card-head-title">CHALLENGES &amp; WAY FORWARD</div></div>
                <div class="card-body">
                    <div class="auth-form-group" style="margin-bottom:12px;">
                        <label class="form-label-nis">Major Challenges Faced</label>
                        <textarea name="migration[general_report][challenges]" class="ni" rows="4" placeholder="Describe any migration challenges..."></textarea>
                    </div>
                    <div class="auth-form-group">
                        <label class="form-label-nis">Recommendations / Way Forward</label>
                        <textarea name="migration[general_report][recommendations]" class="ni" rows="4" placeholder="Propose solutions or recommendations..."></textarea>
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

<script>
(function () {
    'use strict';

    /* Root scoping: on the combined state page this partial lives inside
       #dir-migration; on the standalone page fall back to the document. */
    var MIG_ROOT = document.getElementById('dir-migration') || document;
    var isCombined = !!document.getElementById('dir-migration');

    // Predefined lists of countries for MoUs selection
    var countryLists = {
        ecowas: {!! json_encode($mouCountries['ecowas']) !!},
        non_ecowas: {!! json_encode($mouCountries['non_ecowas']) !!},
        asia: {!! json_encode($mouCountries['asia']) !!},
        europe: {!! json_encode($mouCountries['europe']) !!},
        north_am: {!! json_encode($mouCountries['north_am']) !!},
        south_am: {!! json_encode($mouCountries['south_am']) !!},
        australia: {!! json_encode($mouCountries['australia']) !!}
    };

    var refugeeIndex = 1;
    function addRefugeeRow() {
        const body = document.getElementById('refugeesBody');
        const newRow = document.createElement('tr');
        newRow.className = 'refugee-row';
        newRow.innerHTML = `
            <td>
                <select name="refugees_asylum_seekers[refugees][${refugeeIndex}][state]" class="ni ni-select" style="padding:6px 10px;">
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
                <button type="button" class="btn-nis btn-ghost btn-sm" data-m-action="remove-row" style="color:var(--color-danger);"><i class="fas fa-trash"></i></button>
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
    }

    var asylumIndex = 1;
    function addAsylumRow() {
        const body = document.getElementById('asylumBody');
        const newRow = document.createElement('tr');
        newRow.className = 'asylum-row';
        newRow.innerHTML = `
            <td>
                <select name="refugees_asylum_seekers[asylum_seekers][${asylumIndex}][state]" class="ni ni-select" style="padding:6px 10px;">
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
                <button type="button" class="btn-nis btn-ghost btn-sm" data-m-action="remove-row" style="color:var(--color-danger);"><i class="fas fa-trash"></i></button>
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
    }

    var mouIndices = {};
    function addMouRow(key) {
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
                <select name="mou_countries[${key}][${mouIndices[key]}][country]" class="ni ni-select" style="padding:6px 10px;">
                    ${options}
                </select>
            </td>
            <td>
                <input type="text" class="ni" name="mou_countries[${key}][${mouIndices[key]}][description]" placeholder="e.g. Agreement details" style="padding:6px 10px;">
            </td>
            <td>
                <button type="button" class="btn-nis btn-ghost btn-sm" data-m-action="remove-row" style="color:var(--color-danger);"><i class="fas fa-trash"></i></button>
            </td>
        `;
        body.appendChild(newRow);
        mouIndices[key]++;
    }

    function removeRow(btn) {
        const row = btn.closest('tr');
        row?.remove();

        // Recalculate totals if it was staff strength
        let totalMale = 0, totalFemale = 0, grandTotal = 0;
        MIG_ROOT.querySelectorAll('.staff-male-input').forEach(input => {
            const valMale = parseInt(input.value) || 0;
            const femaleInput = input.closest('tr').querySelector('.staff-female-input');
            const valFemale = parseInt(femaleInput.value) || 0;
            totalMale += valMale;
            totalFemale += valFemale;
            grandTotal += (valMale + valFemale);
        });
        const maleEl = document.getElementById('migration-staff-male-total');
        if (maleEl) maleEl.textContent = totalMale.toLocaleString();
        const femaleEl = document.getElementById('migration-staff-female-total');
        if (femaleEl) femaleEl.textContent = totalFemale.toLocaleString();
        const grandEl = document.getElementById('migration-staff-grand-total');
        if (grandEl) grandEl.textContent = grandTotal.toLocaleString();
    }

    function addSomZone() {
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
    }

    function addTipZone() {
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
    }

    /* Add-row buttons carry data-m-action instead of inline onclick so the
       partial declares no global functions. */
    MIG_ROOT.querySelectorAll('[data-m-action]').forEach(btn => {
        const action = btn.getAttribute('data-m-action');
        if (action === 'add-refugee-row') btn.addEventListener('click', addRefugeeRow);
        else if (action === 'add-asylum-row') btn.addEventListener('click', addAsylumRow);
        else if (action === 'add-mou-row') btn.addEventListener('click', () => addMouRow(btn.getAttribute('data-m-key')));
        else if (action === 'add-som-zone') btn.addEventListener('click', addSomZone);
        else if (action === 'add-tip-zone') btn.addEventListener('click', addTipZone);
    });
    /* Remove buttons also appear in dynamically added rows: delegate. */
    MIG_ROOT.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-m-action="remove-row"]');
        if (btn) removeRow(btn);
    });

    document.addEventListener('DOMContentLoaded', () => {
        const tabs = MIG_ROOT.querySelectorAll('#migrationFormTabs [data-m-tab]');
        const contents = MIG_ROOT.querySelectorAll('.m-tab-content');
        const tabOrder = Array.from(tabs).map(tab => tab.dataset.mTab).filter(Boolean);

        function showTab(target) {
            const tab = MIG_ROOT.querySelector('#migrationFormTabs [data-m-tab="' + target + '"]');
            if (!tab) return;

            tabs.forEach(t => t.classList.toggle('active', t === tab));
            contents.forEach(content => {
                const isActive = content.id === 'tab-migration-' + target;
                content.style.display = isActive ? 'block' : 'none';
                content.classList.toggle('active', isActive);
            });

            // The standalone wrapper registers the review snapshot builder.
            if (target === 'review' && typeof window.migrationBuildReviewSnapshot === 'function') {
                window.migrationBuildReviewSnapshot();
            }

            updateActionButtons();
        }

        function updateActionButtons() {
            const activeTabId = MIG_ROOT.querySelector('#migrationFormTabs [data-m-tab].active')?.dataset.mTab || 'staff';
            contents.forEach(content => {
                const actions = content.querySelector('.hrm-actions');
                if (!actions) return;

                const prev = actions.querySelector('.hrm-prev-btn');
                const next = actions.querySelector('.hrm-next-btn');
                const contentKey = content.id.replace('tab-migration-', '');
                const isFirst = contentKey === tabOrder[0];
                const isLast = contentKey === tabOrder[tabOrder.length - 1];

                if (prev) {
                    prev.disabled = isFirst;
                    prev.style.opacity = isFirst ? '0.5' : '1';
                    prev.style.cursor = isFirst ? 'not-allowed' : 'pointer';
                }

                if (next) {
                    if (isLast && isCombined) {
                        /* The combined state form owns submission; this panel's
                           last tab simply ends. */
                        next.innerHTML = 'Next <i class="fas fa-arrow-right"></i>';
                        next.disabled = true;
                        next.style.opacity = '0.5';
                        next.style.cursor = 'not-allowed';
                    } else {
                        next.disabled = false;
                        next.style.opacity = '1';
                        next.style.cursor = 'pointer';
                        next.innerHTML = isLast
                            ? '<i class="fas fa-check"></i> Submit'
                            : 'Next <i class="fas fa-arrow-right"></i>';
                        next.dataset.target = isLast ? 'review' : tabOrder[Math.min(tabOrder.indexOf(contentKey) + 1, tabOrder.length - 1)];
                    }
                }
            });

            if (!(isCombined && activeTabId === tabOrder[tabOrder.length - 1])) {
                const activeContent = document.getElementById('tab-migration-' + activeTabId);
                if (activeContent) {
                    const actionBar = activeContent.querySelector('.hrm-actions');
                    if (actionBar && actionBar.querySelector('.hrm-next-btn')) {
                        const nextBtn = actionBar.querySelector('.hrm-next-btn');
                        nextBtn.disabled = false;
                    }
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

        MIG_ROOT.querySelectorAll('.hrm-prev-btn').forEach(button => {
            button.addEventListener('click', () => {
                const currentContent = button.closest('.m-tab-content');
                const currentKey = currentContent?.id.replace('tab-migration-', '');
                const currentIndex = tabOrder.indexOf(currentKey);
                if (currentIndex > 0) {
                    showTab(tabOrder[currentIndex - 1]);
                }
            });
        });

        MIG_ROOT.querySelectorAll('.hrm-next-btn').forEach(button => {
            button.addEventListener('click', () => {
                const currentContent = button.closest('.m-tab-content');
                const currentKey = currentContent?.id.replace('tab-migration-', '');
                const currentIndex = tabOrder.indexOf(currentKey);

                if (currentIndex === tabOrder.length - 1) {
                    if (isCombined) return; // the combined state form owns submission
                    // On the final (Review & Submit) tab the primary button submits the return.
                    const form = document.querySelector('main form') || document.querySelector('form[action*="directorates"]');
                    if (!form) return;
                    // Required fields live on hidden tabs; an invalid control that is
                    // not focusable blocks submission silently, so switch to its tab first.
                    if (!form.checkValidity()) {
                        const invalid = form.querySelector(':invalid');
                        const panel = invalid ? invalid.closest('.m-tab-content') : null;
                        if (panel) showTab(panel.id.replace('tab-migration-', ''));
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

        MIG_ROOT.querySelectorAll('.hrm-save-draft-btn').forEach(button => {
            button.addEventListener('click', saveDraft);
        });

        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                showTab(tab.dataset.mTab);
            });
        });

        updateActionButtons();

        // Auto calculation: Staff Strength
        const recalculateStaff = () => {
            let totalMale = 0;
            let totalFemale = 0;
            let grandTotal = 0;

            MIG_ROOT.querySelectorAll('.staff-male-input').forEach(input => {
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

            document.getElementById('migration-staff-male-total').textContent = totalMale.toLocaleString();
            document.getElementById('migration-staff-female-total').textContent = totalFemale.toLocaleString();
            document.getElementById('migration-staff-grand-total').textContent = grandTotal.toLocaleString();
        };

        MIG_ROOT.querySelectorAll('.staff-male-input, .staff-female-input').forEach(input => {
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

        MIG_ROOT.querySelectorAll('.som-field-input').forEach(input => {
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

        MIG_ROOT.querySelectorAll('.tip-field-input').forEach(input => {
            input.addEventListener('input', recalculateTip);
        });

        // Auto calculation: Refugees Initial Row
        MIG_ROOT.querySelectorAll('.refugee-row').forEach(row => {
            row.querySelectorAll('.refugee-apps, .refugee-approved, .refugee-rejected').forEach(input => {
                input.addEventListener('input', () => {
                    const valApproved = parseInt(row.querySelector('.refugee-approved').value) || 0;
                    const valRejected = parseInt(row.querySelector('.refugee-rejected').value) || 0;
                    row.querySelector('.refugee-total').value = valApproved + valRejected;
                });
            });
        });

        // Auto calculation: Asylum Seekers Initial Row
        MIG_ROOT.querySelectorAll('.asylum-row').forEach(row => {
            row.querySelectorAll('.asylum-apps, .asylum-approved, .asylum-rejected').forEach(input => {
                input.addEventListener('input', () => {
                    const valApproved = parseInt(row.querySelector('.asylum-approved').value) || 0;
                    const valRejected = parseInt(row.querySelector('.asylum-rejected').value) || 0;
                    row.querySelector('.asylum-total').value = valApproved + valRejected;
                });
            });
        });
    });
})();
</script>
