@extends('user.directorates._layout')

@section('directorate-tabs', '1')

@section('directorate-preview', '1')

@section('directorate-sections')

<div class="hrm-tabs-wrap">
    <div class="entry-tabs-wrap" style="margin-bottom:0;">
        <div class="entry-tabs" id="entryTabs">
            @php $tabs = [
                ['personnel-strength','fas fa-users','1. Personnel Strength'],
                ['kpis','fas fa-chart-line','2. Key Performance Indicators'],
                ['enrolment-operations','fas fa-passport','3. Passport Enrolment Ops'],
                ['specialised-systems','fas fa-server','4. Vetting & Specialised Systems'],
                ['innovation','fas fa-lightbulb','5. Innovation & Digital Initiatives'],
                ['investigation','fas fa-magnifying-glass','6. Investigation & Compliance'],
                ['complaints','fas fa-comment-dots','7. Complaints & Applicant Service'],
                ['training','fas fa-graduation-cap','8. Training & Capacity Building'],
                ['challenges','fas fa-triangle-exclamation','9. Challenges & Mitigation'],
                ['outlook','fas fa-binoculars','10. Outlook & Recommendations'],
                ['attachments','fas fa-paperclip','11. Attachments'],
                ['general-report','fas fa-file-alt','12. General Report'],
                ['preview','fas fa-eye','13. Preview'],
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


<div class="tab-content" id="epmsTabContent">

    {{-- TAB 1: Personnel Strength --}}
    <div class="tab-panel epms-preview-src active" id="tab-personnel-strength">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-users"></i></div>
                    1. Personnel Strength
                </div>
            </div>
            <div class="card-body">
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-id-badge"></i> 1.1 &mdash; Personnel Profile of the Innovation/EPMS Unit</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th style="min-width:160px;">Category</th>
                                    <th style="width:130px;">Comptroller</th>
                                    <th style="width:130px;">Superintendent</th>
                                    <th style="width:130px;">Inspectorate</th>
                                    <th style="width:130px;">Assistant</th>
                                    <th style="width:120px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $personnelRows = [
                                    'staff_strength' => 'Staff Strength',
                                    'postings_in' => 'Postings In',
                                    'postings_out' => 'Postings Out',
                                    'promotions' => 'Promotions',
                                    'retirements_exits' => 'Retirements/Exits',
                                    'strength_at_31_december' => 'Strength at 31 December',
                                ];
                                $cadres = ['comptroller', 'superintendent', 'inspectorate', 'assistant'];
                                @endphp
                                @foreach($personnelRows as $key => $label)
                                <tr>
                                    <td>{{ $label }}</td>
                                    @foreach($cadres as $cadre)
                                    <td>
                                        <input type="number" min="0" class="ni epms-pers" data-row="{{ $key }}"
                                            name="personnel[{{ $key }}][{{ $cadre }}]">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="number" readonly class="ni" id="epms-pers-total-{{ $key }}"
                                            name="personnel[{{ $key }}][total]">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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

    {{-- TAB 2: Key Performance Indicators --}}
    <div class="tab-panel epms-preview-src" id="tab-kpis">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-chart-line"></i></div>
                    2. Key Performance Indicators
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="min-width:240px;">Indicator</th>
                                <th style="width:130px;">Current Year</th>
                                <th style="width:130px;">Previous Year</th>
                                <th style="width:110px;">% Change</th>
                                <th style="width:200px;">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $kpiRows = [
                                'total_support_applications' => 'Total support provided for passport applications received',
                                'avg_turnaround_days' => 'Average turnaround time for support (days)',
                                'systems_uptime' => 'Systems uptime (%) based on number of working days in the year',
                                'support_domestic' => 'Support provided to Passport offices (domestic)',
                                'support_diaspora' => 'Support provided to Passport offices (diaspora)',
                                'fraud_cases_detected' => 'Fraud/irregularity cases detected',
                                'complaints_received_resolved' => 'Complaints received / resolved',
                            ];
                            @endphp
                            @foreach($kpiRows as $key => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td><input type="text" class="ni" name="kpi[{{ $key }}][current]"></td>
                                <td><input type="text" class="ni" name="kpi[{{ $key }}][previous]"></td>
                                <td><input type="text" class="ni" name="kpi[{{ $key }}][change]"></td>
                                <td><input type="text" class="ni" name="kpi[{{ $key }}][remarks]"></td>
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

    {{-- TAB 3: Passport Enrolment Operations --}}
    <div class="tab-panel epms-preview-src" id="tab-enrolment-operations">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-passport"></i></div>
                    3. Passport Enrolment Operations
                </div>
            </div>
            <div class="card-body">

                {{-- 3.1 Vetting Operations --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-user-check"></i> 3.1 Vetting Operations</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th style="min-width:160px;">TYPE</th>
                                    <th style="width:170px;">COD Cases Received</th>
                                    <th style="width:170px;">COD Cases Vetted</th>
                                    <th style="width:130px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $vettingRows = [
                                    'change_of_data' => 'Change of Data',
                                    'lost_case' => 'Lost Case',
                                    'other' => 'Other',
                                ];
                                @endphp
                                @foreach($vettingRows as $key => $label)
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td><input type="number" min="0" class="ni epms-vet" data-row="{{ $key }}" name="vetting_operations[{{ $key }}][cod_received]"></td>
                                    <td><input type="number" min="0" class="ni epms-vet" data-row="{{ $key }}" name="vetting_operations[{{ $key }}][cod_vetted]"></td>
                                    <td><input type="number" readonly class="ni" id="epms-vet-total-{{ $key }}" name="vetting_operations[{{ $key }}][total]"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 3.2 Change of Data Operations by Location --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-location-dot"></i> 3.2 Change of Data Operations by Location</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th width="60">S/N</th>
                                    <th>Centre (HQ Annex / Special Centre / PPT Front Office)</th>
                                    <th style="width:170px;">COD Cases Received</th>
                                    <th style="width:170px;">COD Cases Treated</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $codCentres = [
                                    'state_house' => 'Passport Front Office, State House, Abuja',
                                    'ministry_of_foreign_affairs' => 'Passport Front Office, Ministry of Foreign Affairs, Abuja',
                                    'ministry_of_interior' => 'Passport Front Office, Ministry of Interior, Abuja',
                                    'nass' => 'Passport Front Office, NASS, Abuja',
                                    'nnpc' => 'Passport Front Office, NNPC, Abuja',
                                ];
                                @endphp
                                @foreach($codCentres as $key => $label)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $label }}</td>
                                    <td><input type="number" min="0" class="ni epms-codloc-received" name="cod_locations[{{ $key }}][received]"></td>
                                    <td><input type="number" min="0" class="ni epms-codloc-treated" name="cod_locations[{{ $key }}][treated]"></td>
                                </tr>
                                @endforeach
                                <tr class="total-row">
                                    <td colspan="2"><strong>TOTAL</strong></td>
                                    <td><input readonly class="ni" id="epmsCodlocTotalReceived"></td>
                                    <td><input readonly class="ni" id="epmsCodlocTotalTreated"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Table 3.3 COD Operations by Passport Offices --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-building"></i> Table 3.3 &mdash; COD Operations by Passport Offices</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th style="min-width:200px;">State Command</th>
                                    <th style="width:160px;">COD Cases Received</th>
                                    <th style="width:160px;">COD Cases Treated</th>
                                    <th style="width:130px;">Pending</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $stateCommands = [
                                    'abia' => 'Abia',
                                    'adamawa' => 'Adamawa',
                                    'akwa_ibom' => 'Akwa Ibom',
                                    'anambra' => 'Anambra',
                                    'bauchi' => 'Bauchi',
                                    'bayelsa' => 'Bayelsa',
                                    'benue' => 'Benue',
                                    'borno' => 'Borno',
                                    'cross_river' => 'Cross River',
                                    'delta_asaba' => 'Delta — Asaba',
                                    'warri' => 'Warri',
                                    'ebonyi' => 'Ebonyi',
                                    'edo' => 'Edo — Auchi, Benin City',
                                    'ekiti' => 'Ekiti',
                                    'enugu' => 'Enugu',
                                    'gombe' => 'Gombe',
                                    'imo' => 'Imo',
                                    'jigawa' => 'Jigawa',
                                    'kaduna' => 'Kaduna — Zaria, Kaduna',
                                    'kano' => 'Kano — Farm Center, Dawakin Kudu',
                                    'katsina' => 'Katsina — Daura, Katsina',
                                    'kebbi' => 'Kebbi',
                                    'kogi' => 'Kogi',
                                    'kwara' => 'Kwara',
                                    'lagos' => 'Lagos — Ikoyi, Festac, Alausa, Alimosho, Ikorodu',
                                    'nasarawa' => 'Nasarawa',
                                    'niger' => 'Niger',
                                    'ogun' => 'Ogun — Sagamu, Abeokuta',
                                    'ondo' => 'Ondo — Ile-Oluji, Akure',
                                    'osun' => 'Osun — Osogbo, Ilesha',
                                    'oyo' => 'Oyo — Oyo Town, Ibadan',
                                    'plateau' => 'Plateau',
                                    'rivers' => 'Rivers',
                                    'sokoto' => 'Sokoto',
                                    'taraba' => 'Taraba',
                                    'yobe' => 'Yobe',
                                    'zamfara' => 'Zamfara',
                                    'fct_abuja' => 'FCT Abuja',
                                ];
                                @endphp
                                @foreach($stateCommands as $key => $label)
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td><input type="number" min="0" class="ni epms-state-received" name="cod_state_commands[{{ $key }}][received]"></td>
                                    <td><input type="number" min="0" class="ni epms-state-treated" name="cod_state_commands[{{ $key }}][treated]"></td>
                                    <td><input type="number" min="0" class="ni epms-state-pending" name="cod_state_commands[{{ $key }}][pending]"></td>
                                </tr>
                                @endforeach
                                <tr class="total-row">
                                    <td><strong>TOTAL</strong></td>
                                    <td><input readonly class="ni" id="epmsStateTotalReceived"></td>
                                    <td><input readonly class="ni" id="epmsStateTotalTreated"></td>
                                    <td><input readonly class="ni" id="epmsStateTotalPending"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Table 3.4 Technical Support to Attaches at Foreign Missions --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-globe"></i> Table 3.4 &mdash; Technical Support Services provided to Attach&eacute;s at Foreign Missions</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th width="60">S/N</th>
                                    <th>Mission / Post</th>
                                    <th style="width:140px;">Applications</th>
                                    <th style="width:140px;">Enrolled</th>
                                    <th style="width:140px;">Passports Issued</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $missionRegions = [
                                    'AFRICA' => [
                                        [1, "Embassy of Nigeria, Abidjan, Côte d'Ivoire"],
                                        [2, 'Nigeria High Commission, Accra, Ghana'],
                                        [3, 'Embassy of Nigeria, Addis Ababa, Ethiopia'],
                                        [4, 'Embassy of Nigeria, Algiers, Algeria'],
                                        [5, 'Embassy of Nigeria, Bamako, Mali'],
                                        [13, 'Embassy of Nigeria, Cairo, Egypt'],
                                        [15, 'Embassy of Nigeria, Cotonou, Benin'],
                                        [16, 'Embassy of Nigeria, Dakar, Senegal'],
                                        [17, 'Nigeria High Commission, Dar es Salaam, Tanzania'],
                                        [18, 'Nigeria Consulate, Douala, Cameroon'],
                                        [19, 'Nigeria High Commission, Freetown, Sierra Leone'],
                                        [22, 'Nigeria Consulate, Johannesburg, South Africa'],
                                        [24, 'Nigeria High Commission, Kampala, Uganda'],
                                        [25, 'Embassy of Nigeria, Khartoum, Sudan'],
                                        [28, 'Embassy of Nigeria, Libreville, Gabon'],
                                        [34, 'Embassy of Nigeria, Maputo, Mozambique'],
                                        [35, 'Embassy of Nigeria, Monrovia, Liberia'],
                                        [36, 'Nigeria High Commission, Nairobi, Kenya'],
                                        [39, 'Embassy of Nigeria, Ouagadougou, Burkina Faso'],
                                        [40, 'Nigeria High Commission, Pretoria, South Africa'],
                                        [41, 'Embassy of Nigeria, Rabat, Morocco'],
                                        [42, 'Embassy of Nigeria, Tripoli, Libya'],
                                    ],
                                    'AMERICA' => [
                                        [46, 'Nigeria Consulate, Atlanta, USA'],
                                        [47, 'Embassy of Nigeria, Brasília, Brazil'],
                                        [50, 'Nigeria High Commission, Kingston, Jamaica'],
                                        [51, 'Nigeria Consulate, New York, USA'],
                                        [52, 'Nigeria High Commission, Ottawa, Canada'],
                                        [54, 'Embassy of Nigeria, Washington DC, USA'],
                                    ],
                                    'ASIA' => [
                                        [55, 'Embassy of Nigeria, Abu Dhabi, UAE'],
                                        [56, 'Embassy of Nigeria, Bangkok, Thailand'],
                                        [57, 'Embassy of Nigeria, Beijing, China'],
                                        [58, 'Embassy of Nigeria, Beirut, Lebanon'],
                                        [59, 'Nigeria High Commission, Canberra, Australia'],
                                        [61, 'Embassy of Nigeria, Doha, Qatar'],
                                        [62, 'Nigeria Consulate, Dubai, UAE'],
                                        [63, 'Nigeria Consulate, Guangzhou, China'],
                                        [64, 'Nigeria High Commission, Islamabad, Pakistan'],
                                        [65, 'Embassy of Nigeria, Jakarta, Indonesia'],
                                        [66, 'Nigeria Consulate, Jeddah, Saudi Arabia'],
                                        [67, 'Nigeria High Commission, Kuala Lumpur, Malaysia'],
                                        [69, 'Embassy of Nigeria, Manila, Philippines'],
                                        [70, 'Nigeria High Commission, New Delhi, India'],
                                        [72, 'Embassy of Nigeria, Seoul, South Korea'],
                                        [74, 'Embassy of Nigeria, Tel Aviv, Israel'],
                                        [75, 'Embassy of Nigeria, Tokyo, Japan'],
                                    ],
                                    'EUROPE' => [
                                        [76, 'Embassy of Nigeria, Ankara, Turkey'],
                                        [77, 'Embassy of Nigeria, Athens, Greece'],
                                        [79, 'Embassy of Nigeria, Berlin, Germany'],
                                        [80, 'Embassy of Nigeria, Bern, Switzerland'],
                                        [81, 'Embassy of Nigeria, Brussels, Belgium'],
                                        [82, 'Embassy of Nigeria, Bucharest, Romania'],
                                        [83, 'Embassy of Nigeria, Budapest, Hungary'],
                                        [84, 'Embassy of Nigeria, Dublin, Ireland'],
                                        [85, 'Permanent Mission of Nigeria to the United Nations, Geneva, Switzerland'],
                                        [86, 'Embassy of Nigeria, The Hague, Netherlands'],
                                        [87, 'Embassy of Nigeria, Kyiv, Ukraine'],
                                        [89, 'Nigeria High Commission, London, United Kingdom'],
                                        [90, 'Embassy of Nigeria, Madrid, Spain'],
                                        [91, 'Embassy of Nigeria, Moscow, Russia'],
                                        [92, 'Embassy of Nigeria, Paris, France'],
                                        [93, 'Embassy of Nigeria, Rome, Italy'],
                                        [94, 'Embassy of Nigeria, Stockholm, Sweden'],
                                        [96, 'Embassy of Nigeria, Vienna, Austria'],
                                        [97, 'Embassy of Nigeria, Warsaw, Poland'],
                                        [98, 'Embassy of Nigeria, Frankfurt, Germany'],
                                    ],
                                ];
                                $missionIndex = 0;
                                @endphp
                                @foreach($missionRegions as $region => $missions)
                                <tr>
                                    <td colspan="5" style="text-align:center;background:#f8fafc;"><strong>{{ $region }}</strong></td>
                                </tr>
                                @foreach($missions as [$sn, $mission])
                                <tr>
                                    <td>{{ $sn }}</td>
                                    <td>
                                        {{ $mission }}
                                        <input type="hidden" name="mission_support[{{ $missionIndex }}][mission]" value="{{ $mission }}">
                                    </td>
                                    <td><input type="number" min="0" class="ni epms-mission-app" name="mission_support[{{ $missionIndex }}][applications]"></td>
                                    <td><input type="number" min="0" class="ni epms-mission-enr" name="mission_support[{{ $missionIndex }}][enrolled]"></td>
                                    <td><input type="number" min="0" class="ni epms-mission-iss" name="mission_support[{{ $missionIndex }}][passports_issued]"></td>
                                </tr>
                                @php $missionIndex++; @endphp
                                @endforeach
                                @endforeach
                                <tr class="total-row">
                                    <td colspan="2"><strong>TOTAL</strong></td>
                                    <td><input readonly class="ni" id="epmsMissionTotalApp"></td>
                                    <td><input readonly class="ni" id="epmsMissionTotalEnr"></td>
                                    <td><input readonly class="ni" id="epmsMissionTotalIss"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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

    {{-- TAB 4: Vetting, Change-of-Data, Verification and Specialised Systems Operations --}}
    <div class="tab-panel epms-preview-src" id="tab-specialised-systems">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-server"></i></div>
                    4. Vetting, Change-of-Data, Verification &amp; Specialised Systems Operations
                </div>
            </div>
            <div class="card-body">

                {{-- 4.1 Vetting Operation summary --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-clipboard-check"></i> 4.1 Vetting Operations Summary</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th style="min-width:140px;">Vetting Operation</th>
                                    <th>COD Cases Received</th>
                                    <th>COD Cases Vetted</th>
                                    <th>Lost Cases Received</th>
                                    <th>Lost Cases Vetted</th>
                                    <th style="width:110px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $specialisedRows = [
                                    'change_of_data' => 'Change of Data',
                                    'lost_case' => 'Lost Case',
                                    'other' => 'Other',
                                ];
                                @endphp
                                @foreach($specialisedRows as $key => $label)
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td><input type="number" min="0" class="ni epms-sv" data-row="{{ $key }}" name="specialised_vetting[{{ $key }}][cod_received]"></td>
                                    <td><input type="number" min="0" class="ni epms-sv" data-row="{{ $key }}" name="specialised_vetting[{{ $key }}][cod_vetted]"></td>
                                    <td><input type="number" min="0" class="ni epms-sv" data-row="{{ $key }}" name="specialised_vetting[{{ $key }}][lost_received]"></td>
                                    <td><input type="number" min="0" class="ni epms-sv" data-row="{{ $key }}" name="specialised_vetting[{{ $key }}][lost_vetted]"></td>
                                    <td><input type="number" readonly class="ni" id="epms-sv-total-{{ $key }}" name="specialised_vetting[{{ $key }}][total]"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 4.2 Change-of-Data Processing --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-database"></i> 4.2 Change-of-Data Processing</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th style="width:180px;">Month</th>
                                    <th>Requests Received</th>
                                    <th>Approved</th>
                                    <th>Declined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="month" class="ni" name="cod_processing[month]"></td>
                                    <td><input type="number" min="0" class="ni" name="cod_processing[received]"></td>
                                    <td><input type="number" min="0" class="ni" name="cod_processing[approved]"></td>
                                    <td><input type="number" min="0" class="ni" name="cod_processing[declined]"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 4.3 Passport Verification Requests --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-check-double"></i> 4.3 Passport Verification Requests</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th style="width:180px;">Month</th>
                                    <th>Requests Received</th>
                                    <th>Requests Treated</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="month" class="ni" name="verification_requests[month]"></td>
                                    <td><input type="number" min="0" class="ni" name="verification_requests[received]"></td>
                                    <td><input type="number" min="0" class="ni" name="verification_requests[treated]"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 4.4 On-boarding / De-boarding of Personnel (PAMS / DVS / EPMS) --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-user-plus"></i> 4.4 On-boarding / De-boarding of Personnel into the Automated Passport Application Management System (PAMS) / Document Verification System (DVS), and the Electronic Passport Management System</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th width="60">S/N</th>
                                    <th>NIS Formation / Nigeria&rsquo;s Mission</th>
                                    <th style="width:160px;">Number On-boarded</th>
                                    <th style="width:160px;">Number De-boarded</th>
                                </tr>
                            </thead>
                            <tbody id="pamsOnboardingBody">
                                @for($i = 0; $i < 2; $i++)
                                <tr class="data-row">
                                    <td>{{ $i + 1 }}</td>
                                    <td><input type="text" class="ni" name="pams_onboarding[{{ $i }}][formation]"></td>
                                    <td><input type="number" min="0" class="ni" name="pams_onboarding[{{ $i }}][onboarded]"></td>
                                    <td><input type="number" min="0" class="ni" name="pams_onboarding[{{ $i }}][deboarded]"></td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="add-row-btn" data-target="pamsOnboardingBody"
                        data-prefix="pams_onboarding" data-cols='["formation","onboarded","deboarded"]'>
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                    <div class="table-responsive" style="max-width:560px;margin-top:12px;">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th>Indicator</th>
                                    <th style="width:140px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Personnel active as at current month</td>
                                    <td><input type="number" min="0" class="ni" name="pams_summary[personnel_active]"></td>
                                </tr>
                                <tr>
                                    <td>Personnel on-boarded</td>
                                    <td><input type="number" min="0" class="ni" name="pams_summary[personnel_onboarded]"></td>
                                </tr>
                                <tr>
                                    <td>Personnel de-boarded</td>
                                    <td><input type="number" min="0" class="ni" name="pams_summary[personnel_deboarded]"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 4.5 Innovation/EPMS Technical Support for Personnel on VCMS --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-headset"></i> 4.5 Innovation/EPMS Technical Support for Personnel on Visa Case Management System (VCMS)</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th width="60">S/N</th>
                                    <th>Nigeria&rsquo;s Mission</th>
                                    <th style="width:160px;">Number On-boarded</th>
                                    <th style="width:160px;">Number De-boarded</th>
                                </tr>
                            </thead>
                            <tbody id="vcmsOnboardingBody">
                                @for($i = 0; $i < 2; $i++)
                                <tr class="data-row">
                                    <td>{{ $i + 1 }}</td>
                                    <td><input type="text" class="ni" name="vcms_onboarding[{{ $i }}][mission]"></td>
                                    <td><input type="number" min="0" class="ni" name="vcms_onboarding[{{ $i }}][onboarded]"></td>
                                    <td><input type="number" min="0" class="ni" name="vcms_onboarding[{{ $i }}][deboarded]"></td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="add-row-btn" data-target="vcmsOnboardingBody"
                        data-prefix="vcms_onboarding" data-cols='["mission","onboarded","deboarded"]'>
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                    <div style="margin:14px 0 6px;font-size:.84rem;font-weight:700;">Table 4.5 &mdash; VCMS Access Summary</div>
                    <div class="table-responsive" style="max-width:560px;">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th>Indicator</th>
                                    <th style="width:140px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Attach&eacute;s active on VCMS</td>
                                    <td><input type="number" min="0" class="ni" name="vcms_summary[attaches_active]"></td>
                                </tr>
                                <tr>
                                    <td>Attach&eacute;s on-boarded</td>
                                    <td><input type="number" min="0" class="ni" name="vcms_summary[attaches_onboarded]"></td>
                                </tr>
                                <tr>
                                    <td>Attach&eacute;s de-boarded</td>
                                    <td><input type="number" min="0" class="ni" name="vcms_summary[attaches_deboarded]"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 4.5 MAMS Support --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-calendar-check"></i> 4.5 Mission Appointment Management System (MAMS)</div>
                    <p style="font-size:.8rem;color:var(--gray-500);font-style:italic;margin:0 0 8px;">Innovation/EPMS Support provided for MAMS Operationalisation and resolution of challenges</p>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th style="width:180px;">Month</th>
                                    <th>Support Requests Received</th>
                                    <th>Resolved</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="month" class="ni" name="mams_support[month]"></td>
                                    <td><input type="number" min="0" class="ni" name="mams_support[received]"></td>
                                    <td><input type="number" min="0" class="ni" name="mams_support[resolved]"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 4.6 Contactless Passport Platform --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-wifi"></i> 4.6 Technical Support Services &mdash; Contactless Passport Platform</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th style="width:180px;">Month</th>
                                    <th>Support Requests Received</th>
                                    <th>Resolved</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="month" class="ni" name="contactless_support[month]"></td>
                                    <td><input type="number" min="0" class="ni" name="contactless_support[received]"></td>
                                    <td><input type="number" min="0" class="ni" name="contactless_support[resolved]"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 4.7 Passport Interventions at Nigeria's Missions --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-hand-holding"></i> 4.7 Passport Interventions at Nigeria&rsquo;s Missions</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th style="width:180px;">Month</th>
                                    <th>Mission</th>
                                    <th style="width:200px;">Applicants Supported</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="month" class="ni" name="passport_interventions[month]"></td>
                                    <td><input type="text" class="ni" name="passport_interventions[mission]"></td>
                                    <td><input type="number" min="0" class="ni" name="passport_interventions[applicants]"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 4.8 Technical Support & Capacity Building at Missions --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-screwdriver-wrench"></i> 4.8 Technical Support Services and Capacity Building at Nigeria&rsquo;s Missions on upgrades to the Electronic Passport Management System and equipment</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th width="60">S/N</th>
                                    <th>Mission / Post</th>
                                    <th>Type of Support (Technical)</th>
                                </tr>
                            </thead>
                            <tbody id="capacitySupportBody">
                                @for($i = 0; $i < 2; $i++)
                                <tr class="data-row">
                                    <td>{{ $i + 1 }}</td>
                                    <td><input type="text" class="ni" name="capacity_support[{{ $i }}][mission]"></td>
                                    <td><input type="text" class="ni" name="capacity_support[{{ $i }}][support_type]"></td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="add-row-btn" data-target="capacitySupportBody"
                        data-prefix="capacity_support" data-cols='["mission","support_type"]'>
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                    <p style="font-size:.8rem;color:var(--gray-500);font-style:italic;margin:8px 0 0;">Includes remote and on-site technical support rendered to Missions, and training delivered on EPMS and related systems.</p>
                </div>

                {{-- 4.10 Narrative --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-align-left"></i> 4.10 Narrative on Vetting, Verification and Specialised Systems Operations</div>
                    <div class="fg">
                        <textarea class="ni" name="narrative_specialised" rows="5" placeholder="Remarks on volumes and trends, processing bottlenecks, system enhancements deployed, technical support and intervention outcomes, mobile enrolment deployments, and coordination with Missions and partner agencies."></textarea>
                    </div>
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

    {{-- TAB 5: Innovation and Digital Initiatives --}}
    <div class="tab-panel epms-preview-src" id="tab-innovation">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-lightbulb"></i></div>
                    5. Innovation and Digital Initiatives
                </div>
            </div>
            <div class="card-body">
                <p style="font-size:.8rem;color:var(--gray-500);font-style:italic;margin:0 0 10px;">Records of every innovation project handled by the Service/ NIS Personnel with oversight of Innovation/EPMS Unit during the year.</p>
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-rocket"></i> Table 5.1 &mdash; Innovation Initiative Log</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th width="60">S/N</th>
                                    <th>Initiative</th>
                                    <th>Description / Objective</th>
                                    <th style="width:180px;">Status (Concept / Pilot / Live)</th>
                                    <th style="width:150px;">Date Deployed</th>
                                </tr>
                            </thead>
                            <tbody id="innovationLogBody">
                                @for($i = 0; $i < 2; $i++)
                                <tr class="data-row">
                                    <td>{{ $i + 1 }}</td>
                                    <td><input type="text" class="ni" name="innovation_log[{{ $i }}][initiative]"></td>
                                    <td><input type="text" class="ni" name="innovation_log[{{ $i }}][description]"></td>
                                    <td><input type="text" class="ni" name="innovation_log[{{ $i }}][status]" placeholder="Concept / Pilot / Live"></td>
                                    <td><input type="date" class="ni" name="innovation_log[{{ $i }}][date_deployed]"></td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="add-row-btn" data-target="innovationLogBody"
                        data-prefix="innovation_log" data-cols='["initiative","description","status","date_deployed"]'>
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                </div>
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-align-left"></i> 5.2 Narrative on Innovation Outcomes</div>
                    <div class="fg">
                        <textarea class="ni" name="narrative_innovation" rows="5" placeholder="Describe the outcomes and impact of innovation initiatives recorded above..."></textarea>
                    </div>
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

    {{-- TAB 6: Investigation and Compliance Report --}}
    <div class="tab-panel epms-preview-src" id="tab-investigation">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-magnifying-glass"></i></div>
                    6. Investigation and Compliance Report
                </div>
            </div>
            <div class="card-body">
                <div class="nis-subsection">
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th style="width:160px;">Month</th>
                                    <th>Impersonation / Identity Fraud Cases</th>
                                    <th>Forged Documents Detected</th>
                                    <th>Cases Referred for Prosecution</th>
                                    <th>Other cases</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="month" class="ni" name="investigation_compliance[month]"></td>
                                    <td><input type="number" min="0" class="ni" name="investigation_compliance[impersonation_cases]"></td>
                                    <td><input type="number" min="0" class="ni" name="investigation_compliance[forged_documents]"></td>
                                    <td><input type="number" min="0" class="ni" name="investigation_compliance[referred_prosecution]"></td>
                                    <td><input type="number" min="0" class="ni" name="investigation_compliance[other_cases]"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-align-left"></i> 6.2 Narrative on Investigation &amp; Compliance Support</div>
                    <div class="fg">
                        <textarea class="ni" name="narrative_investigation" rows="5" placeholder="Summarise investigation and compliance support provided during the period..."></textarea>
                    </div>
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

    {{-- TAB 7: Complaints and Applicant Service --}}
    <div class="tab-panel epms-preview-src" id="tab-complaints">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-comment-dots"></i></div>
                    7. Complaints and Applicant Service
                </div>
            </div>
            <div class="card-body">
                <div class="nis-subsection">
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th style="min-width:200px;">Complaint Category</th>
                                    <th style="width:120px;">Received</th>
                                    <th style="width:120px;">Resolved</th>
                                    <th style="width:120px;">Pending</th>
                                    <th style="width:170px;">Avg. Resolution Time (Days)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $complaintRows = [
                                    'delayed_processing' => 'Delayed processing / issuance',
                                    'payment_double_debit' => 'Payment / double-debit issues',
                                    'data_errors_on_passport' => 'Data errors on passport',
                                    'portal_booking_difficulties' => 'Portal / booking difficulties',
                                    'personnel_conduct' => 'Personnel conduct',
                                    'other' => 'Other',
                                ];
                                @endphp
                                @foreach($complaintRows as $key => $label)
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td><input type="number" min="0" class="ni epms-comp-received" name="complaints[{{ $key }}][received]"></td>
                                    <td><input type="number" min="0" class="ni epms-comp-resolved" name="complaints[{{ $key }}][resolved]"></td>
                                    <td><input type="number" min="0" class="ni epms-comp-pending" name="complaints[{{ $key }}][pending]"></td>
                                    <td><input type="number" min="0" step="0.1" class="ni epms-comp-avg" name="complaints[{{ $key }}][avg_days]"></td>
                                </tr>
                                @endforeach
                                <tr class="total-row">
                                    <td><strong>TOTAL</strong></td>
                                    <td><input readonly class="ni" id="epmsCompTotalReceived"></td>
                                    <td><input readonly class="ni" id="epmsCompTotalResolved"></td>
                                    <td><input readonly class="ni" id="epmsCompTotalPending"></td>
                                    <td><input readonly class="ni" id="epmsCompTotalAvg"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-align-left"></i> 7.2 Narrative on Service Improvements for Applicants</div>
                    <div class="fg">
                        <textarea class="ni" name="narrative_service_improvements" rows="5" placeholder="Describe service improvements implemented for applicants during the period..."></textarea>
                    </div>
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

    {{-- TAB 8: Training and Capacity Building --}}
    <div class="tab-panel epms-preview-src" id="tab-training">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-graduation-cap"></i></div>
                    8. Training and Capacity Building
                </div>
            </div>
            <div class="card-body">
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-chalkboard-user"></i> Table 8.1 &mdash; Training Undertaken During the Year</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th width="60">S/N</th>
                                    <th>Course / Programme</th>
                                    <th>Provider / Venue</th>
                                    <th style="width:150px;">Date(s)</th>
                                    <th style="width:150px;">No. Of Personnel Trained</th>
                                    <th style="width:180px;">Remark(s)</th>
                                </tr>
                            </thead>
                            <tbody id="epmsTrainingBody">
                                @for($i = 0; $i < 2; $i++)
                                <tr class="data-row">
                                    <td>{{ $i + 1 }}</td>
                                    <td><input type="text" class="ni" name="training[{{ $i }}][course]"></td>
                                    <td><input type="text" class="ni" name="training[{{ $i }}][provider]"></td>
                                    <td><input type="text" class="ni" name="training[{{ $i }}][dates]"></td>
                                    <td><input type="number" min="0" class="ni" name="training[{{ $i }}][personnel_trained]"></td>
                                    <td><input type="text" class="ni" name="training[{{ $i }}][remarks]"></td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="add-row-btn" data-target="epmsTrainingBody"
                        data-prefix="training" data-cols='["course","provider","dates","personnel_trained","remarks"]'>
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                </div>
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-align-left"></i> 8.2 Skills Gaps and Training Needs Identified</div>
                    <div class="fg">
                        <textarea class="ni" name="skills_gaps" rows="5" placeholder="List skills gaps and training needs identified during the period..."></textarea>
                    </div>
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

    {{-- TAB 9: Challenges and Mitigation Measures --}}
    <div class="tab-panel epms-preview-src" id="tab-challenges">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-triangle-exclamation"></i></div>
                    9. Challenges and Mitigation Measures
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th width="60">S/N</th>
                                <th>Challenge</th>
                                <th>Impact on Operations</th>
                                <th>Mitigation Taken</th>
                                <th>Support Required</th>
                            </tr>
                        </thead>
                        <tbody id="epmsChallengesBody">
                            @for($i = 0; $i < 2; $i++)
                            <tr class="data-row">
                                <td>{{ $i + 1 }}</td>
                                <td><input type="text" class="ni" name="challenges_mitigation[{{ $i }}][challenge]"></td>
                                <td><input type="text" class="ni" name="challenges_mitigation[{{ $i }}][impact]"></td>
                                <td><input type="text" class="ni" name="challenges_mitigation[{{ $i }}][mitigation]"></td>
                                <td><input type="text" class="ni" name="challenges_mitigation[{{ $i }}][support_required]"></td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" data-target="epmsChallengesBody"
                    data-prefix="challenges_mitigation" data-cols='["challenge","impact","mitigation","support_required"]'>
                    <i class="fas fa-plus"></i> Add Row
                </button>
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

    {{-- TAB 10: Outlook, Projections and Recommendations --}}
    <div class="tab-panel epms-preview-src" id="tab-outlook">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-binoculars"></i></div>
                    10. Outlook, Projections and Recommendations
                </div>
            </div>
            <div class="card-body">
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-chart-simple"></i> 10.1 Projections</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th style="min-width:200px;">Indicator</th>
                                    <th>Current Year Actual</th>
                                    <th>Next Year Projection</th>
                                    <th>Basis of Projection</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $projectionRows = [
                                    'innovations' => 'Innovations',
                                    'electronic_passport_management_system' => 'Electronic Passport Management System',
                                    'others' => 'Others',
                                ];
                                @endphp
                                @foreach($projectionRows as $key => $label)
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td><input type="text" class="ni" name="projections[{{ $key }}][current_actual]"></td>
                                    <td><input type="text" class="ni" name="projections[{{ $key }}][next_projection]"></td>
                                    <td><input type="text" class="ni" name="projections[{{ $key }}][basis]"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-list-check"></i> 10.2 Planned Initiatives</div>
                    <div class="fg">
                        <textarea class="ni" name="planned_initiatives" rows="5" placeholder="Outline initiatives planned for the next period..."></textarea>
                    </div>
                </div>
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-thumbs-up"></i> 10.3 Recommendations</div>
                    <div class="fg">
                        <textarea class="ni" name="outlook_recommendations" rows="5" placeholder="Provide recommendations arising from this reporting period..."></textarea>
                    </div>
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

    {{-- TAB 11: Attachments --}}
    <div class="tab-panel" id="tab-attachments">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-paperclip"></i></div>
                    11. Supporting Documents
                </div>
            </div>
            <div class="card-body">
                <div class="fg" style="margin-bottom:16px;">
                    <label><i class="fas fa-paperclip"></i> Supporting Documents (PDF, XLS, XLSX, PNG, JPG)</label>
                    <input type="file" class="ni" id="epmsSupportingDocs" name="supporting_documents[]" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg" multiple>
                </div>
                <div class="fg">
                    <label><i class="fas fa-paperclip"></i> Other Attachments</label>
                    <div class="attach-zone" id="attachZone">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <div style="font-size:.85rem;margin-bottom:4px;">Drag &amp; Drop files here</div>
                        <div style="font-size:.75rem;color:var(--gray-500);">
                            Nominal Roll • Training Reports • Support Logs • Pictures • Circulars • Other Evidence
                        </div>
                        <input type="file" id="attachInput" name="attachments[]" multiple
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display:none;">
                    </div>
                    <div id="attachmentList" style="margin-top:12px;"></div>
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

    {{-- TAB 12: General Report --}}
    <div class="tab-panel epms-preview-src" id="tab-general-report">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-file-alt"></i></div>
                    12. General Report &amp; Summary
                </div>
            </div>
            <div class="card-body">
                <div class="fg">
                    <label>Executive Summary / General Remarks</label>
                    <textarea class="ni" name="general_remarks" rows="8" placeholder="Provide a detailed summary of Innovation/EPMS Unit activities and achievements for the period..."></textarea>
                </div>
                <div class="fg" style="margin-top:16px;">
                    <label>Challenges Encountered</label>
                    <textarea class="ni" name="challenges" rows="5" placeholder="List any obstacles faced during the implementation of EPMS support and innovation initiatives..."></textarea>
                </div>
                <div class="fg" style="margin-top:16px;">
                    <label>Recommendations / Future Action Plan</label>
                    <textarea class="ni" name="recommendations" rows="5" placeholder="Suggest improvements or outline planned activities for the next period..."></textarea>
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

    {{-- TAB 13: Preview --}}
    <div class="tab-panel" id="tab-preview">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#f0fdf4;color:#15803d;"><i class="fas fa-eye"></i></div>
                    13. Review and Submit
                </div>
            </div>
            <div class="card-body">
                <div class="preview-notice" style="background:#fffbeb; border:1px solid #fef3c7; padding:12px; border-radius:6px; color:#92400e; margin-bottom:16px; font-size:.9rem;">
                    <i class="fas fa-exclamation-triangle"></i> Please review all information carefully. Once submitted, the report will move to the Unit Head for approval.
                </div>
                <div id="epmsPreviewContent">
                    <p style="text-align:center; color:var(--gray-500); padding:20px;">Open this tab to generate a preview of your entries before submission.</p>
                </div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-submit-return-btn"><i class="fas fa-paper-plane"></i> Submit Report</button>
        </div>
    </div>

</div>

{{-- EPMS form logic: auto totals + full-report preview. Scoped with epms- prefixes
     so it never collides with the shared ACTU/Provost scripts in the footer. --}}
<script>
(function () {
    function num(el) { return Number(el && el.value) || 0; }
    function sumClass(cls) {
        var total = 0;
        document.querySelectorAll('.' + cls).forEach(function (el) { total += num(el); });
        return total;
    }
    function setVal(id, value) {
        var el = document.getElementById(id);
        if (el) el.value = value;
    }

    /* 1. Personnel Strength — row totals across the four cadres */
    function recalcPersonnel() {
        var rows = {};
        document.querySelectorAll('.epms-pers').forEach(function (el) {
            rows[el.dataset.row] = (rows[el.dataset.row] || 0) + num(el);
        });
        Object.keys(rows).forEach(function (key) { setVal('epms-pers-total-' + key, rows[key]); });
    }

    /* 3.1 Vetting Operations — row totals */
    function recalcVetting() {
        var rows = {};
        document.querySelectorAll('.epms-vet').forEach(function (el) {
            rows[el.dataset.row] = (rows[el.dataset.row] || 0) + num(el);
        });
        Object.keys(rows).forEach(function (key) { setVal('epms-vet-total-' + key, rows[key]); });
    }

    /* 4.1 Specialised vetting — row totals */
    function recalcSpecialised() {
        var rows = {};
        document.querySelectorAll('.epms-sv').forEach(function (el) {
            rows[el.dataset.row] = (rows[el.dataset.row] || 0) + num(el);
        });
        Object.keys(rows).forEach(function (key) { setVal('epms-sv-total-' + key, rows[key]); });
    }

    /* Column totals for the fixed tables with a TOTAL row */
    function recalcColumnTotals() {
        setVal('epmsCodlocTotalReceived', sumClass('epms-codloc-received'));
        setVal('epmsCodlocTotalTreated', sumClass('epms-codloc-treated'));
        setVal('epmsStateTotalReceived', sumClass('epms-state-received'));
        setVal('epmsStateTotalTreated', sumClass('epms-state-treated'));
        setVal('epmsStateTotalPending', sumClass('epms-state-pending'));
        setVal('epmsMissionTotalApp', sumClass('epms-mission-app'));
        setVal('epmsMissionTotalEnr', sumClass('epms-mission-enr'));
        setVal('epmsMissionTotalIss', sumClass('epms-mission-iss'));
        setVal('epmsCompTotalReceived', sumClass('epms-comp-received'));
        setVal('epmsCompTotalResolved', sumClass('epms-comp-resolved'));
        setVal('epmsCompTotalPending', sumClass('epms-comp-pending'));

        /* Complaints: average resolution time across the entered categories */
        var avgInputs = document.querySelectorAll('.epms-comp-avg');
        var sum = 0, count = 0;
        avgInputs.forEach(function (el) {
            if (el.value !== '') { sum += Number(el.value) || 0; count++; }
        });
        setVal('epmsCompTotalAvg', count ? (Math.round((sum / count) * 10) / 10) : '');
    }

    function recalcAll() {
        recalcPersonnel();
        recalcVetting();
        recalcSpecialised();
        recalcColumnTotals();
    }

    document.addEventListener('input', function (e) {
        var t = e.target;
        if (!t || !t.classList) return;
        /* A bulk input event on the form itself means a draft or a saved
           submission was just restored — recompute every total. */
        if (t.tagName === 'FORM') { recalcAll(); return; }
        if (t.classList.contains('epms-pers')) recalcPersonnel();
        else if (t.classList.contains('epms-vet')) recalcVetting();
        else if (t.classList.contains('epms-sv')) recalcSpecialised();
        if (/(epms-codloc|epms-state|epms-mission|epms-comp)/.test(t.className)) recalcColumnTotals();
    });

    /* ── Full-report preview (hooked into the shared layout via
       window.buildDirectoratePreview, invoked when the Preview tab opens) ── */
    function esc(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function fieldVal(name) {
        var f = document.querySelector('[name="' + name + '"]');
        return f && f.value ? f.value : '—';
    }
    /* Clone a form block and flatten every input into plain text so the
       preview mirrors the official reporting-template tables. */
    function flattenNode(node) {
        var clone = node.cloneNode(true);
        clone.querySelectorAll('button, .add-row-btn, .hrm-actions, input[type="file"], input[type="hidden"]').forEach(function (el) {
            el.remove();
        });
        Array.from(clone.querySelectorAll('input, textarea, select')).forEach(function (field) {
            var value;
            if (field.tagName === 'SELECT') {
                value = field.options[field.selectedIndex] ? field.options[field.selectedIndex].text : '';
            } else if (field.type === 'checkbox' || field.type === 'radio') {
                value = field.checked ? 'Yes' : 'No';
            } else {
                value = field.value;
            }
            var span = document.createElement(field.tagName === 'TEXTAREA' ? 'div' : 'span');
            if (field.tagName === 'TEXTAREA') span.style.whiteSpace = 'pre-wrap';
            span.textContent = value !== '' ? value : '—';
            field.replaceWith(span);
        });
        return clone;
    }

    window.buildDirectoratePreview = function () {
        var container = document.getElementById('epmsPreviewContent');
        if (!container) return;

        var html = '<div style="text-align:center;margin-bottom:20px;padding-bottom:14px;border-bottom:2px solid var(--nis-700);">'
            + '<img src="{{ asset('nis.png') }}" alt="NIS" style="height:52px;margin-bottom:8px;" onerror="this.style.display=\'none\'"><br>'
            + '<strong style="font-size:1rem;color:var(--nis-800);">NIGERIA IMMIGRATION SERVICE</strong><br>'
            + '<strong style="font-size:.9rem;color:var(--nis-700);">INNOVATION / ELECTRONIC PASSPORT MANAGEMENT SYSTEM (EPMS) UNIT</strong><br>'
            + '<span style="font-size:.82rem;color:var(--gray-600);">Monthly Return &mdash; ' + esc(fieldVal('report_period')) + '</span><br>'
            + '<span style="font-size:.82rem;color:var(--gray-600);">Reporting Officer: ' + esc(fieldVal('reporting_officer')) + '</span>'
            + '</div>';

        document.querySelectorAll('.tab-panel.epms-preview-src').forEach(function (panel) {
            panel.querySelectorAll('.redas-card').forEach(function (card) {
                var title = card.querySelector('.card-head-title');
                var body = card.querySelector('.card-body');
                if (!body) return;
                html += '<div class="hrm-preview-section-title">'
                    + esc(title ? title.textContent.trim().replace(/\s+/g, ' ') : 'Section')
                    + '</div>';
                html += '<div class="epms-preview-block" style="margin-bottom:10px;">'
                    + flattenNode(body).innerHTML
                    + '</div>';
            });
        });

        /* Attachments summary */
        var files = [];
        ['epmsSupportingDocs', 'attachInput'].forEach(function (id) {
            var input = document.getElementById(id);
            if (input && input.files) {
                Array.from(input.files).forEach(function (f) { files.push(f.name); });
            }
        });
        if (files.length) {
            html += '<div class="hrm-preview-section-title">Attachments</div>'
                + '<table class="hrm-preview-table"><thead><tr><th>#</th><th>File</th></tr></thead><tbody>'
                + files.map(function (name, i) { return '<tr><td>' + (i + 1) + '</td><td>' + esc(name) + '</td></tr>'; }).join('')
                + '</tbody></table>';
        }

        container.innerHTML = html;
    };

    recalcAll();
})();
</script>

@endsection
