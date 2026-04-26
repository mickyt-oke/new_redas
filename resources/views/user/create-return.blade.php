@include('partials.header')

    <!-- Page Header -->
    <div class="redas-content" style="padding-bottom:0;">
        <div class="page-header" style="margin-bottom:16px;">
            <div>
                <h1 class="page-title">Submit Operational Return</h1>
                <p class="page-subtitle">Complete all applicable sections and submit to your supervisor for review.</p>
            </div>
            <!-- Workflow indicator -->
            <div class="workflow-path" id="workflowBadge">
                <span class="workflow-step you"><i class="fas fa-user-edit" style="font-size:.68rem;"></i> You</span>
                <span class="workflow-arrow"><i class="fas fa-arrow-right"></i></span>
                <span class="workflow-step" id="nextStep"><i class="fas fa-user-tie" style="font-size:.68rem;"></i> Zonal Coordinator</span>
                <span class="workflow-arrow"><i class="fas fa-arrow-right"></i></span>
                <span class="workflow-step" style="color:var(--gray-400);"><i class="fas fa-user-shield" style="font-size:.68rem;"></i> HQ Admin</span>
            </div>
        </div>
    </div>

    <!-- ═══ TAB NAVIGATION ═══ -->
    <div class="entry-tabs-wrap">
        <div class="entry-tabs" id="entryTabs">
            @php
            $tabs = [
                ['hrm',         'fas fa-users',          'HRM'],
                ['border',      'fas fa-border-all',     'Border Mgt'],
                ['finance',     'fas fa-coins',          'Finance & Accounts'],
                ['ic',          'fas fa-search',         'Investigation'],
                ['prs',         'fas fa-id-card',        'PRS'],
                ['migration',   'fas fa-globe-africa',   'Migration'],
                ['ict',         'fas fa-laptop',         'ICT'],
                ['works',       'fas fa-hard-hat',       'Works & Logistics'],
                ['visa',        'fas fa-stamp',          'Visa & Residency'],
                ['passport',    'fas fa-passport',       'Passport & Travel'],
            ];
            @endphp
            @foreach($tabs as $i => [$id, $icon, $label])
            <button class="entry-tab {{ $i === 0 ? 'active' : '' }}" data-tab="{{ $id }}">
                <span class="tab-dot"></span>
                <i class="{{ $icon }}" style="font-size:.78rem;"></i>
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    <!-- ═══ FORM ═══ -->
    <form id="returnForm" method="POST" action="{{ url('/user/returns') }}" enctype="multipart/form-data">
        @csrf

        <!-- ── HEADER (always visible) ── -->
        <div class="redas-content" style="padding-bottom:0;padding-top:16px;">
            <div class="nis-section">
                <div class="nis-section-head">
                    <span class="sec-num">A</span>
                    Nigeria Immigration Service — Reporting Template Header
                </div>
                <div class="nis-section-body">
                    <div class="form-grid-4" style="align-items:end;">
                        <div class="fg">
                            <label>Command / Formation</label>
                            <select name="command_name" id="commandName" class="ni ni-select" required>
                                <option value="">Select Command</option>
                                <optgroup label="HQ Directorates">
                                    <option value="HRM Directorate">HRM Directorate</option>
                                    <option value="Finance & Accounts">Finance &amp; Accounts</option>
                                    <option value="Border Management">Border Management</option>
                                    <option value="Migration Directorate">Migration Directorate</option>
                                    <option value="POTD Directorate">POTD Directorate</option>
                                    <option value="Visa & Residency">Visa &amp; Residency</option>
                                    <option value="PRS Directorate">PRS Directorate</option>
                                    <option value="Investigation & Compliance">Investigation &amp; Compliance</option>
                                    <option value="ICT Directorate">ICT Directorate</option>
                                    <option value="Works & Logistics">Works &amp; Logistics</option>
                                </optgroup>
                                <optgroup label="Zones">
                                    <option value="Zone A Lagos">Zone A — Lagos</option>
                                    <option value="Zone B Kaduna">Zone B — Kaduna</option>
                                    <option value="Zone C Bauchi">Zone C — Bauchi</option>
                                    <option value="Zone D Niger">Zone D — Niger</option>
                                    <option value="Zone E Imo">Zone E — Imo</option>
                                    <option value="Zone F Oyo">Zone F — Ibadan</option>
                                    <option value="Zone G Edo">Zone G — Edo</option>
                                    <option value="Zone H Benue">Zone H — Benue</option>
                                </optgroup>
                                <optgroup label="States">
                                    @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara','FCT'] as $s)
                                    <option value="{{ $s }} State">{{ $s }} State</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Special Commands">
                                    <option value="MMIA Lagos">MMIA Lagos</option>
                                    <option value="NAIA Abuja">NAIA Abuja</option>
                                    <option value="MAKIA Kano">MAKIA Kano</option>
                                    <option value="PHIA Port Harcourt">PHIA Port Harcourt</option>
                                    <option value="Seme Border Command">Seme Border Command</option>
                                    <option value="Idiroko Border Command">Idiroko Border Command</option>
                                    <option value="Lagos Passport Command">Lagos Passport Command</option>
                                    <option value="NIS HQ Abuja">NIS HQ Abuja</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="fg">
                            <label>Return Period</label>
                            <input type="month" name="period" class="ni" required value="{{ now()->format('Y-m') }}">
                        </div>
                        <div class="fg">
                            <label>Return Type</label>
                            <select name="return_type" class="ni ni-select" required>
                                <option value="monthly">Monthly Return</option>
                                <option value="quarterly">Quarterly Return</option>
                                <option value="biannual">Bi-Annual Return</option>
                                <option value="annual">Annual Return</option>
                                <option value="special">Special Report</option>
                            </select>
                        </div>
                        <div class="fg">
                            <label>Reporting Officer</label>
                            <input type="text" name="reporting_officer" class="ni" value="{{ auth()->user()->name }}" readonly>
                        </div>
                    </div>
                    <input type="hidden" name="workflow_path" id="workflowPath" value="zonal">
                    <input type="hidden" name="status" value="pending">
                </div>
            </div>
        </div>

        <!-- ═══ TAB PANELS ═══ -->
        <div class="redas-content" style="padding-top:8px;">

            <!-- ══ TAB: HRM ══ -->
            <div class="tab-panel active" id="tab-hrm">

                <!-- Staff Strength -->
                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§1</span> Staff Strength <small style="font-weight:400;opacity:.8;">(Current nominal roll to be attached)</small></div>
                    <div class="nis-section-body">
                        <table class="nis-table">
                            <thead><tr><th>Cadre</th><th style="width:160px;">Number</th></tr></thead>
                            <tbody>
                                @foreach(['Comptroller Cadre','Superintendent Cadre','Inspectorate Cadre','Assistant Cadre'] as $cadre)
                                <tr><td>{{ $cadre }}</td><td><input type="number" name="staff[{{ Str::slug($cadre) }}]" class="ni staff-input" min="0" placeholder="0"></td></tr>
                                @endforeach
                                <tr class="total-row"><td><strong>TOTAL</strong></td><td><input type="number" id="staffTotal" class="ni" readonly placeholder="0"></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Staff Development -->
                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§3</span> Staff Development — Training &amp; Workshops</div>
                    <div class="nis-section-body">
                        <div class="table-responsive">
                            <table class="nis-table">
                                <thead><tr><th>S/N</th><th>Workshop / Seminar</th><th>Location</th><th>Cost (₦)</th><th>Participants</th><th>Duration</th></tr></thead>
                                <tbody id="staffDevBody">
                                    <tr class="data-row">
                                        <td>1</td>
                                        <td><input type="text" name="staff_dev[0][title]" class="ni" placeholder="Workshop name"></td>
                                        <td><input type="text" name="staff_dev[0][location]" class="ni" placeholder="Location"></td>
                                        <td><input type="number" name="staff_dev[0][cost]" class="ni" placeholder="0.00"></td>
                                        <td><input type="number" name="staff_dev[0][participants]" class="ni" placeholder="0"></td>
                                        <td><input type="text" name="staff_dev[0][duration]" class="ni" placeholder="e.g. 3 days"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="add-row-btn" data-target="staffDevBody" data-prefix="staff_dev" data-cols='["title","location","cost","participants","duration"]'>
                            <i class="fas fa-plus"></i> Add Row
                        </button>
                    </div>
                </div>

                <!-- Promotions & Transfers -->
                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§HR</span> Promotions, Transfers &amp; Discipline</div>
                    <div class="nis-section-body">
                        <div class="form-grid-3">
                            <div class="fg"><label>Officers Promoted</label><input type="number" name="hrm[promoted]" class="ni" placeholder="0" min="0"></div>
                            <div class="fg"><label>Officers Transferred In</label><input type="number" name="hrm[transferred_in]" class="ni" placeholder="0" min="0"></div>
                            <div class="fg"><label>Officers Transferred Out</label><input type="number" name="hrm[transferred_out]" class="ni" placeholder="0" min="0"></div>
                            <div class="fg"><label>Officers Retired</label><input type="number" name="hrm[retired]" class="ni" placeholder="0" min="0"></div>
                            <div class="fg"><label>Officers Dismissed</label><input type="number" name="hrm[dismissed]" class="ni" placeholder="0" min="0"></div>
                            <div class="fg"><label>Officers on Course/Training</label><input type="number" name="hrm[on_course]" class="ni" placeholder="0" min="0"></div>
                        </div>
                        <div class="fg" style="margin-top:4px;"><label>Remarks</label><textarea name="hrm[remarks]" class="ni" rows="2" placeholder="Additional HR remarks..."></textarea></div>
                    </div>
                </div>
            </div>

            <!-- ══ TAB: BORDER MANAGEMENT ══ -->
            <div class="tab-panel" id="tab-border">

                <!-- Arms/Armoury -->
                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§2A</span> Arms / Armoury Returns</div>
                    <div class="nis-section-body">
                        <div class="table-responsive">
                            <table class="nis-table">
                                <thead><tr><th>S/N</th><th>Type</th><th>Condition</th><th>Number</th><th>Subtotal</th></tr></thead>
                                <tbody>
                                    @php
                                    $arms = ['G3 Rifle','AR70 Rifle','AK47 Rifle','Galil Rifle','LAR Rifle','SMG','Pistol Baretta','DICON Pistol','Stone Pistol','Others'];
                                    @endphp
                                    @foreach($arms as $i => $arm)
                                    <tr>
                                        <td rowspan="2">{{ $i+1 }}</td>
                                        <td rowspan="2">{{ $arm }}</td>
                                        <td>Serviceable</td>
                                        <td><input type="number" name="arms[{{ $i }}][serviceable]" class="ni arms-svc" data-group="{{ $i }}" min="0" placeholder="0"></td>
                                        <td rowspan="2"><input type="number" class="ni arms-sub" id="arms-sub-{{ $i }}" readonly placeholder="0"></td>
                                    </tr>
                                    <tr>
                                        <td>Unserviceable</td>
                                        <td><input type="number" name="arms[{{ $i }}][unserviceable]" class="ni arms-unsvc" data-group="{{ $i }}" min="0" placeholder="0"></td>
                                    </tr>
                                    @endforeach
                                    <tr class="total-row"><td colspan="3"><strong>GRAND TOTAL</strong></td><td></td><td><input type="number" id="armsGrandTotal" class="ni" readonly placeholder="0"></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Ammunition -->
                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§2B</span> Ammunition Returns</div>
                    <div class="nis-section-body">
                        <div class="table-responsive">
                            <table class="nis-table">
                                <thead><tr><th>S/N</th><th>Type</th><th>No. of Rounds</th><th>Total</th><th>No. Used</th><th>Balance C/F</th></tr></thead>
                                <tbody id="ammoBody">
                                    <tr class="data-row"><td>1</td><td><input type="text" name="ammo[0][type]" class="ni"></td><td><input type="number" name="ammo[0][rounds]" class="ni" min="0"></td><td><input type="number" name="ammo[0][total]" class="ni" min="0"></td><td><input type="number" name="ammo[0][used]" class="ni" min="0"></td><td><input type="number" name="ammo[0][balance]" class="ni" readonly></td></tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="add-row-btn" data-target="ammoBody" data-prefix="ammo" data-cols='["type","rounds","total","used","balance"]'><i class="fas fa-plus"></i> Add Row</button>
                    </div>
                </div>

                <!-- Border Statistics -->
                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§BDR</span> Border Movement Statistics</div>
                    <div class="nis-section-body">
                        <div class="form-grid-4">
                            <div class="fg"><label>Arrivals (Nigerians)</label><input type="number" name="border[arrivals_ng]" class="ni" placeholder="0" min="0"></div>
                            <div class="fg"><label>Arrivals (Foreigners)</label><input type="number" name="border[arrivals_fg]" class="ni" placeholder="0" min="0"></div>
                            <div class="fg"><label>Departures (Nigerians)</label><input type="number" name="border[departures_ng]" class="ni" placeholder="0" min="0"></div>
                            <div class="fg"><label>Departures (Foreigners)</label><input type="number" name="border[departures_fg]" class="ni" placeholder="0" min="0"></div>
                            <div class="fg"><label>Refused Entry</label><input type="number" name="border[refused_entry]" class="ni" placeholder="0" min="0"></div>
                            <div class="fg"><label>Refused Departure</label><input type="number" name="border[refused_departure]" class="ni" placeholder="0" min="0"></div>
                            <div class="fg"><label>Stowaways</label><input type="number" name="border[stowaways]" class="ni" placeholder="0" min="0"></div>
                            <div class="fg"><label>Apprehensions</label><input type="number" name="border[apprehensions]" class="ni" placeholder="0" min="0"></div>
                        </div>
                    </div>
                </div>

                <!-- Deportation/Repatriation -->
                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§17</span> Deportation / Repatriation / Refused Entry</div>
                    <div class="nis-section-body">
                        <div class="nis-subsection">
                            <div class="nis-subsection-title"><i class="fas fa-plane-arrival"></i> Nigerians Deported from Abroad</div>
                            <table class="nis-table">
                                <thead><tr><th>S/N</th><th>State of Origin</th><th>Male</th><th>Female</th><th>Total</th></tr></thead>
                                <tbody id="deportNgBody">
                                    <tr class="data-row"><td>1</td><td><input type="text" name="deport_ng[0][state]" class="ni"></td><td><input type="number" name="deport_ng[0][male]" class="ni deport-m" data-row="deport_ng_0" min="0"></td><td><input type="number" name="deport_ng[0][female]" class="ni deport-f" data-row="deport_ng_0" min="0"></td><td><input type="number" name="deport_ng[0][total]" class="ni deport-t" id="deport_ng_0" readonly></td></tr>
                                </tbody>
                            </table>
                            <button type="button" class="add-row-btn" data-target="deportNgBody" data-prefix="deport_ng" data-cols='["state","male","female","total"]'><i class="fas fa-plus"></i> Add Row</button>
                        </div>
                        <div class="nis-subsection">
                            <div class="nis-subsection-title"><i class="fas fa-plane-departure"></i> Migrants Deported from Nigeria</div>
                            <table class="nis-table">
                                <thead><tr><th>S/N</th><th>Nationality</th><th>Male</th><th>Female</th><th>Total</th></tr></thead>
                                <tbody id="deportFgBody">
                                    <tr class="data-row"><td>1</td><td><input type="text" name="deport_fg[0][nationality]" class="ni"></td><td><input type="number" name="deport_fg[0][male]" class="ni" min="0"></td><td><input type="number" name="deport_fg[0][female]" class="ni" min="0"></td><td><input type="number" name="deport_fg[0][total]" class="ni" readonly></td></tr>
                                </tbody>
                            </table>
                            <button type="button" class="add-row-btn" data-target="deportFgBody" data-prefix="deport_fg" data-cols='["nationality","male","female","total"]'><i class="fas fa-plus"></i> Add Row</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══ TAB: FINANCE & ACCOUNTS ══ -->
            <div class="tab-panel" id="tab-finance">
                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§2C</span> Store Returns</div>
                    <div class="nis-section-body">
                        <div class="table-responsive">
                            <table class="nis-table">
                                <thead><tr><th>S/N</th><th>Item</th><th>Bal B/F</th><th>Qty Received</th><th>Total</th><th>Qty Issued</th><th>Balance</th></tr></thead>
                                <tbody id="storeBody">
                                    <tr class="data-row"><td>1</td><td><input type="text" name="store[0][item]" class="ni"></td><td><input type="number" name="store[0][bf]" class="ni store-bf" min="0"></td><td><input type="number" name="store[0][rx]" class="ni store-rx" min="0"></td><td><input type="number" name="store[0][total]" class="ni" readonly></td><td><input type="number" name="store[0][issued]" class="ni store-iss" min="0"></td><td><input type="number" name="store[0][balance]" class="ni" readonly></td></tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="add-row-btn" data-target="storeBody" data-prefix="store" data-cols='["item","bf","rx","total","issued","balance"]'><i class="fas fa-plus"></i> Add Row</button>
                    </div>
                </div>

                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§FIN</span> Revenue Summary</div>
                    <div class="nis-section-body">
                        <div class="form-grid-2">
                            <div>
                                <div class="nis-subsection-title" style="margin-bottom:8px;"><i class="fas fa-money-bill-wave"></i> Revenue Generated</div>
                                <table class="nis-table">
                                    <thead><tr><th>Source</th><th>Amount (₦)</th></tr></thead>
                                    <tbody>
                                        @foreach(['Passport Fees','Visa Fees','CERPAC Fees','Residence Permit','Work Permit','Other Revenue'] as $src)
                                        <tr><td>{{ $src }}</td><td><input type="number" name="revenue[{{ Str::slug($src) }}]" class="ni rev-input" min="0" placeholder="0.00"></td></tr>
                                        @endforeach
                                        <tr class="total-row"><td><strong>TOTAL</strong></td><td><input type="number" id="revTotal" class="ni" readonly placeholder="0.00"></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <div class="nis-subsection-title" style="margin-bottom:8px;"><i class="fas fa-file-invoice"></i> Expenditure</div>
                                <div class="fg"><label>Personnel Cost (₦)</label><input type="number" name="expenditure[personnel]" class="ni" placeholder="0.00" min="0"></div>
                                <div class="fg"><label>Overhead (₦)</label><input type="number" name="expenditure[overhead]" class="ni" placeholder="0.00" min="0"></div>
                                <div class="fg"><label>Capital (₦)</label><input type="number" name="expenditure[capital]" class="ni" placeholder="0.00" min="0"></div>
                                <div class="fg"><label>Remittance to Treasury (₦)</label><input type="number" name="expenditure[remittance]" class="ni" placeholder="0.00" min="0"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══ TAB: INVESTIGATION & COMPLIANCE ══ -->
            <div class="tab-panel" id="tab-ic">
                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§15</span> Investigation &amp; Compliance</div>
                    <div class="nis-section-body">
                        <table class="nis-table">
                            <thead><tr><th>S/N</th><th>Activity</th><th>Number</th><th>Remark</th></tr></thead>
                            <tbody>
                                @foreach([
                                    'Cases involving Companies',
                                    'Cases involving Expatriates',
                                    'Cases involving Officers',
                                    'Arrests Made',
                                    'Prosecutions Initiated',
                                    'Convictions Secured',
                                    'Cases Pending',
                                    'Cases Closed',
                                ] as $i => $act)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $act }}</td>
                                    <td><input type="number" name="ic[{{ $i }}][number]" class="ni" min="0" placeholder="0"></td>
                                    <td><input type="text" name="ic[{{ $i }}][remark]" class="ni" placeholder="Remark"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§16</span> Refugee / Asylum Seekers</div>
                    <div class="nis-section-body">
                        <table class="nis-table">
                            <thead><tr><th>S/N</th><th>Activity</th><th>Number</th><th>Remark</th></tr></thead>
                            <tbody>
                                @foreach([
                                    'Asylum Applications Received',
                                    'Applications Approved',
                                    'Applications Rejected',
                                    'Cases Pending',
                                    'Refugees Resettled',
                                    'Refugees Repatriated',
                                ] as $i => $act)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $act }}</td>
                                    <td><input type="number" name="refugee[{{ $i }}][number]" class="ni" min="0" placeholder="0"></td>
                                    <td><input type="text" name="refugee[{{ $i }}][remark]" class="ni" placeholder="Remark"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ══ TAB: PRS ══ -->
            <div class="tab-panel" id="tab-prs">
                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§4</span> Passport Administration Activities</div>
                    <div class="nis-section-body">
                        <table class="nis-table">
                            <thead><tr><th>S/N</th><th>Type of Case</th><th>Number of Cases</th></tr></thead>
                            <tbody>
                                @foreach(['Fresh','Re-issue','Marriage','Lost','Damaged','Mutilated'] as $i => $t)
                                <tr><td>{{ $i+1 }}</td><td>{{ $t }}</td><td><input type="number" name="passport_admin[{{ $i }}]" class="ni ppt-admin-input" min="0" placeholder="0"></td></tr>
                                @endforeach
                                <tr class="total-row"><td></td><td><strong>Total</strong></td><td><input type="number" id="pptAdminTotal" class="ni" readonly placeholder="0"></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§5</span> Change of Data Activities <span style="font-weight:400;opacity:.8;">(HQ Only)</span></div>
                    <div class="nis-section-body">
                        <table class="nis-table">
                            <thead><tr><th>S/N</th><th>Type</th><th>Number</th></tr></thead>
                            <tbody>
                                @foreach(['Change of Date of Birth','Change of Place of Birth','Change of Signature','Change of Name(s)','Others'] as $i => $t)
                                <tr><td>{{ $i+1 }}</td><td>{{ $t }}</td><td><input type="number" name="cod[{{ $i }}]" class="ni" min="0" placeholder="0"></td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ══ TAB: MIGRATION ══ -->
            <div class="tab-panel" id="tab-migration">
                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§12</span> Migration / Anti-Human Trafficking &amp; Child Labour</div>
                    <div class="nis-section-body">
                        <table class="nis-table">
                            <thead><tr><th>S/N</th><th>Activity</th><th>Number</th><th>Status / Remark</th></tr></thead>
                            <tbody>
                                @foreach([
                                    'Migrants Intercepted','Smugglers Arrested','HT&CL Victims Rescued',
                                    'Human Traffickers Apprehended','HT&CL Victims Referred to NAPTIP',
                                    'HT&CL Traffickers Referred to NAPTIP','HT&CL Victims Re-United','Victims/Migrants Repatriated',
                                ] as $i => $act)
                                <tr>
                                    <td>{{ $i+1 }}</td><td>{{ $act }}</td>
                                    <td><input type="number" name="anti_ht[{{ $i }}][number]" class="ni" min="0" placeholder="0"></td>
                                    <td><input type="text" name="anti_ht[{{ $i }}][remark]" class="ni" placeholder="Status or remark"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§13</span> Foreigners in Nigeria</div>
                    <div class="nis-section-body">
                        <div class="nis-subsection">
                            <div class="nis-subsection-title"><i class="fas fa-briefcase"></i> Foreigners Employed in Nigeria</div>
                            <table class="nis-table">
                                <thead><tr><th>S/N</th><th>Name</th><th>Gender</th><th>Nationality</th><th>Dependants</th><th>Position</th><th>Industry</th></tr></thead>
                                <tbody id="foreignEmpBody">
                                    <tr class="data-row"><td>1</td><td><input type="text" name="foreign_emp[0][name]" class="ni"></td><td><select name="foreign_emp[0][gender]" class="ni ni-select"><option>M</option><option>F</option></select></td><td><input type="text" name="foreign_emp[0][nationality]" class="ni"></td><td><input type="number" name="foreign_emp[0][dependants]" class="ni" min="0"></td><td><input type="text" name="foreign_emp[0][position]" class="ni"></td><td><input type="text" name="foreign_emp[0][industry]" class="ni"></td></tr>
                                </tbody>
                            </table>
                            <button type="button" class="add-row-btn" data-target="foreignEmpBody" data-prefix="foreign_emp" data-cols='["name","gender","nationality","dependants","position","industry"]'><i class="fas fa-plus"></i> Add Row</button>
                        </div>
                        <div class="nis-subsection">
                            <div class="nis-subsection-title"><i class="fas fa-id-badge"></i> Work Permits Issued</div>
                            <table class="nis-table">
                                <thead><tr><th>S/N</th><th>Name</th><th>Gender</th><th>Nationality</th><th>Permit Type</th><th>Duration</th></tr></thead>
                                <tbody id="workPermitBody">
                                    <tr class="data-row"><td>1</td><td><input type="text" name="work_permit[0][name]" class="ni"></td><td><select name="work_permit[0][gender]" class="ni ni-select"><option>M</option><option>F</option></select></td><td><input type="text" name="work_permit[0][nationality]" class="ni"></td><td><input type="text" name="work_permit[0][permit_type]" class="ni"></td><td><input type="text" name="work_permit[0][duration]" class="ni"></td></tr>
                                </tbody>
                            </table>
                            <button type="button" class="add-row-btn" data-target="workPermitBody" data-prefix="work_permit" data-cols='["name","gender","nationality","permit_type","duration"]'><i class="fas fa-plus"></i> Add Row</button>
                        </div>
                    </div>
                </div>

                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§18</span> Migrant E-Registration</div>
                    <div class="nis-section-body">
                        <div class="table-responsive">
                            <table class="nis-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2">S/N</th><th rowspan="2">Nationality</th>
                                        <th colspan="3">Sex</th>
                                        <th colspan="7">Migrant Status</th>
                                    </tr>
                                    <tr>
                                        <th>M</th><th>F</th><th>Total</th>
                                        <th>Employed</th><th>Student</th><th>Self-Emp</th><th>Spouse</th><th>Dependant</th><th>Regular</th><th>Irregular</th>
                                    </tr>
                                </thead>
                                <tbody id="eregBody">
                                    <tr class="data-row"><td>1</td><td><input type="text" name="ereg[0][nationality]" class="ni" style="min-width:90px;"></td><td><input type="number" name="ereg[0][m]" class="ni ereg-m" data-grp="0" min="0" style="width:50px;"></td><td><input type="number" name="ereg[0][f]" class="ni ereg-f" data-grp="0" min="0" style="width:50px;"></td><td><input type="number" name="ereg[0][total]" class="ni ereg-tot" id="ereg-tot-0" readonly style="width:55px;"></td><td><input type="number" name="ereg[0][employed]" class="ni" min="0" style="width:55px;"></td><td><input type="number" name="ereg[0][student]" class="ni" min="0" style="width:55px;"></td><td><input type="number" name="ereg[0][self_emp]" class="ni" min="0" style="width:55px;"></td><td><input type="number" name="ereg[0][spouse]" class="ni" min="0" style="width:55px;"></td><td><input type="number" name="ereg[0][dependant]" class="ni" min="0" style="width:55px;"></td><td><input type="number" name="ereg[0][regular]" class="ni" min="0" style="width:55px;"></td><td><input type="number" name="ereg[0][irregular]" class="ni" min="0" style="width:55px;"></td></tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="add-row-btn" data-target="eregBody" data-prefix="ereg" data-cols='["nationality","m","f","total","employed","student","self_emp","spouse","dependant","regular","irregular"]'><i class="fas fa-plus"></i> Add Row</button>
                    </div>
                </div>
            </div>

            <!-- ══ TAB: ICT ══ -->
            <div class="tab-panel" id="tab-ict">
                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§ICT</span> ICT Assets &amp; Infrastructure</div>
                    <div class="nis-section-body">
                        <div class="form-grid-3">
                            @foreach(['Desktop Computers','Laptop Computers','Servers','Printers','UPS Units','CCTV Cameras','Network Switches','Routers','Biometric Devices','Others'] as $i => $item)
                            <div class="fg">
                                <label>{{ $item }}</label>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                                    <input type="number" name="ict_assets[{{ $i }}][functional]" class="ni" placeholder="Functional" min="0">
                                    <input type="number" name="ict_assets[{{ $i }}][faulty]" class="ni" placeholder="Faulty" min="0">
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§SYS</span> Connectivity &amp; System Status</div>
                    <div class="nis-section-body">
                        <div class="form-grid-2">
                            <div class="fg"><label>Internet Connectivity Status</label><select name="ict[connectivity]" class="ni ni-select"><option>Available — Stable</option><option>Available — Unstable</option><option>Unavailable</option></select></div>
                            <div class="fg"><label>BIMS System Status</label><select name="ict[bims]" class="ni ni-select"><option>Operational</option><option>Degraded</option><option>Down</option></select></div>
                            <div class="fg"><label>PISCES System Status</label><select name="ict[pisces]" class="ni ni-select"><option>Operational</option><option>Degraded</option><option>Down</option></select></div>
                            <div class="fg"><label>Power Supply (hrs/day)</label><input type="number" name="ict[power_hrs]" class="ni" placeholder="e.g. 18" min="0" max="24"></div>
                        </div>
                        <div class="fg"><label>ICT Challenges / Incidents</label><textarea name="ict[challenges]" class="ni" rows="3" placeholder="Describe any ICT issues, outages, or incidents during this period..."></textarea></div>
                        <div class="fg"><label>ICT Projects / Initiatives</label><textarea name="ict[projects]" class="ni" rows="2" placeholder="Ongoing or completed ICT projects..."></textarea></div>
                    </div>
                </div>
            </div>

            <!-- ══ TAB: WORKS & LOGISTICS ══ -->
            <div class="tab-panel" id="tab-works">
                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§2E</span> Projects</div>
                    <div class="nis-section-body">
                        <div class="table-responsive">
                            <table class="nis-table">
                                <thead><tr><th>S/N</th><th>Description</th><th>Year</th><th>Location</th><th>Contractor</th><th>Status</th><th>% Complete</th></tr></thead>
                                <tbody id="projectsBody">
                                    <tr class="data-row"><td>1</td><td><input type="text" name="projects[0][desc]" class="ni"></td><td><input type="text" name="projects[0][year]" class="ni" style="width:70px;"></td><td><input type="text" name="projects[0][location]" class="ni"></td><td><input type="text" name="projects[0][contractor]" class="ni"></td><td><select name="projects[0][status]" class="ni ni-select"><option>Not Started</option><option>In Progress</option><option>Completed</option><option>Abandoned</option></select></td><td><input type="number" name="projects[0][completion]" class="ni project-pct" min="0" max="100" placeholder="0"></td></tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="add-row-btn" data-target="projectsBody" data-prefix="projects" data-cols='["desc","year","location","contractor","status","completion"]'><i class="fas fa-plus"></i> Add Row</button>
                    </div>
                </div>

                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§2D</span> Vehicle Returns</div>
                    <div class="nis-section-body">
                        <div class="table-responsive">
                            <table class="nis-table">
                                <thead><tr><th>S/N</th><th>Type</th><th>Engine / Chassis No.</th><th>Vehicle No.</th><th>Condition</th></tr></thead>
                                <tbody id="vehicleBody">
                                    <tr class="data-row"><td>1</td><td><input type="text" name="vehicles[0][type]" class="ni"></td><td><input type="text" name="vehicles[0][id]" class="ni"></td><td><input type="text" name="vehicles[0][reg]" class="ni"></td><td><select name="vehicles[0][condition]" class="ni ni-select"><option>Serviceable</option><option>Unserviceable</option><option>Under Repair</option></select></td></tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="add-row-btn" data-target="vehicleBody" data-prefix="vehicles" data-cols='["type","id","reg","condition"]'><i class="fas fa-plus"></i> Add Row</button>
                    </div>
                </div>
            </div>

            <!-- ══ TAB: VISA & RESIDENCY ══ -->
            <div class="tab-panel" id="tab-visa">
                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§8</span> Visa &amp; Residency Activities</div>
                    <div class="nis-section-body">
                        <table class="nis-table">
                            <thead><tr><th>Type</th><th>Number</th><th>Amount Generated (₦)</th></tr></thead>
                            <tbody>
                                @foreach([
                                    'Residence Card','V/Pass Ext (ECOWAS)','CERPAC (African)',
                                    'CERPAC (Non-African)','V/Pass Ext (Non-ECOWAS)','PUR','ECOWAS Registration',
                                ] as $i => $t)
                                <tr>
                                    <td>{{ $t }}</td>
                                    <td><input type="number" name="visa_res[{{ $i }}][number]" class="ni" min="0" placeholder="0"></td>
                                    <td><input type="number" name="visa_res[{{ $i }}][amount]" class="ni" min="0" placeholder="0.00" {{ $t === 'ECOWAS Registration' ? 'readonly value="0"' : '' }}></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§9</span> Free Trade Zone</div>
                    <div class="nis-section-body">
                        <table class="nis-table">
                            <thead><tr><th>Activity</th><th>Number</th></tr></thead>
                            <tbody>
                                @foreach(['Regularization','Renewal','COE','Re-Designation'] as $i => $act)
                                <tr><td>{{ $act }}</td><td><input type="number" name="ftz[{{ $i }}]" class="ni ftz-input" min="0" placeholder="0"></td></tr>
                                @endforeach
                                <tr class="total-row"><td><strong>TOTAL</strong></td><td><input type="number" id="ftzTotal" class="ni" readonly></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§10</span> Quota Management</div>
                    <div class="nis-section-body">
                        <table class="nis-table">
                            <thead><tr><th>Activity</th><th>Number</th></tr></thead>
                            <tbody>
                                @foreach([
                                    'Companies that Opened File','Quota Placements — ROS',
                                    'Quota Placements — Renewal','Quota Placements — COE','Quota Placements — Re-designation',
                                ] as $i => $act)
                                <tr><td>{{ $act }}</td><td><input type="number" name="quota[{{ $i }}]" class="ni" min="0" placeholder="0"></td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ══ TAB: PASSPORT & TRAVEL DOCUMENTS ══ -->
            <div class="tab-panel" id="tab-passport">
                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§6</span> Passport Revenue</div>
                    <div class="nis-section-body">
                        <table class="nis-table">
                            <thead><tr><th>e-Passport Type</th><th>No. Issued</th><th>Amount Generated (₦)</th></tr></thead>
                            <tbody>
                                @foreach([
                                    '32-Pages (5 Years)','64-Pages (5 Years)',
                                    'Enhanced 32-Pages (5 Years)','Enhanced 64-Pages (5 Years)','Enhanced 64-Pages (10 Years)',
                                    'ECOWAS Travel Certificate',
                                ] as $i => $t)
                                <tr>
                                    <td>{{ $t }}</td>
                                    <td><input type="number" name="ppt_rev[{{ $i }}][issued]" class="ni" min="0" placeholder="0"></td>
                                    <td><input type="number" name="ppt_rev[{{ $i }}][amount]" class="ni ppt-rev-amount" min="0" placeholder="0.00"></td>
                                </tr>
                                @endforeach
                                <tr class="total-row"><td><strong>TOTAL</strong></td><td><input type="number" id="pptRevIssuedTotal" class="ni" readonly></td><td><input type="number" id="pptRevAmtTotal" class="ni" readonly></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="nis-section">
                    <div class="nis-section-head"><span class="sec-num">§7</span> Passport Stock Returns</div>
                    <div class="nis-section-body">
                        <div class="table-responsive">
                            <table class="nis-table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>32-Pg (5Y)</th><th>64-Pg (5Y)</th>
                                        <th>Enh 32-Pg (5Y)</th><th>Enh 64-Pg (5Y)</th><th>Enh 64-Pg (10Y)</th><th>ETC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(['Balance B/F','Collected from HQ','Issued','Voided/Damaged'] as $ri => $row)
                                    <tr>
                                        <td>{{ $row }}</td>
                                        @foreach(range(0,5) as $ci)
                                        <td><input type="number" name="ppt_stock[{{ $ri }}][{{ $ci }}]" class="ni stock-r{{ $ri }}-c{{ $ci }}" min="0" placeholder="0"></td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                    <tr class="total-row">
                                        <td><strong>Stock Balance</strong></td>
                                        @foreach(range(0,5) as $ci)
                                        <td><input type="number" name="ppt_stock[4][{{ $ci }}]" class="ni stock-bal-{{ $ci }}" readonly placeholder="0"></td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p style="font-size:.74rem;color:var(--gray-400);margin-top:8px;"><i class="fas fa-info-circle"></i> Stock Balance = B/F + Collected − Issued − Voided</p>
                    </div>
                </div>
            </div>

            <!-- ══ GENERAL REPORT (always shown below tabs) ══ -->
            <div class="nis-section" style="margin-top:8px;" id="generalReport">
                <div class="nis-section-head"><span class="sec-num">§19</span> General Report</div>
                <div class="nis-section-body">
                    <div class="form-grid-2">
                        <div class="fg"><label>Security Report</label><textarea name="general[security]" class="ni" rows="4" placeholder="Security situation and incidents during the reporting period..."></textarea></div>
                        <div class="fg"><label>Other Reports</label><textarea name="general[other]" class="ni" rows="4" placeholder="Any other relevant operational reports..."></textarea></div>
                        <div class="fg"><label>Challenges</label><textarea name="general[challenges]" class="ni" rows="4" placeholder="Challenges encountered during the reporting period..."></textarea></div>
                        <div class="fg"><label>Recommendations / Way Forward</label><textarea name="general[recommendations]" class="ni" rows="4" placeholder="Recommended actions and way forward..."></textarea></div>
                    </div>
                    <div class="fg" style="margin-top:4px;"><label>Conclusion</label><textarea name="general[conclusion]" class="ni" rows="3" placeholder="Concluding remarks..."></textarea></div>

                    <!-- Attachments -->
                    <div class="fg" style="margin-top:8px;">
                        <label><i class="fas fa-paperclip"></i> Attachments <span style="font-weight:400;color:var(--gray-400);text-transform:none;">(PDFs, images, Word — max 10MB each)</span></label>
                        <div class="attach-zone" id="attachZone">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <div style="font-size:.85rem;color:var(--gray-500);margin-bottom:4px;">Drag &amp; drop files here or click to browse</div>
                            <div style="font-size:.74rem;color:var(--gray-400);">Nominal roll, pictures, supporting documents</div>
                            <input type="file" name="attachments[]" id="attachInput" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display:none;">
                        </div>
                        <div class="attach-list" id="attachList"></div>
                    </div>

                    <!-- Signature -->
                    <div style="background:var(--gray-50);border:1px solid var(--gray-200);border-radius:var(--radius-md);padding:16px;margin-top:8px;">
                        <div class="nis-subsection-title" style="border:none;margin-bottom:12px;"><i class="fas fa-pen-nib"></i> Reporting Officer Declaration</div>
                        <div class="form-grid-3">
                            <div class="fg"><label>Full Name</label><input type="text" name="sig_name" class="ni" value="{{ auth()->user()->name }}" required></div>
                            <div class="fg"><label>Rank</label><input type="text" name="sig_rank" class="ni" placeholder="e.g. DCI" required></div>
                            <div class="fg"><label>GSM Number</label><input type="tel" name="sig_gsm" class="ni" placeholder="+234 8XX XXX XXXX" required></div>
                        </div>
                        <div class="fg" style="margin-top:4px;">
                            <label>Date</label>
                            <input type="date" name="sig_date" class="ni" value="{{ now()->format('Y-m-d') }}" style="max-width:200px;" required>
                        </div>
                        <p style="font-size:.76rem;color:var(--gray-500);margin-top:8px;margin-bottom:0;"><i class="fas fa-lock" style="color:var(--nis-600);"></i> By submitting this return, I certify that the information provided is accurate and complete to the best of my knowledge, in compliance with NIS reporting standards.</p>
                    </div>
                </div>
            </div>

        </div><!-- /redas-content -->

        <!-- ═══ STICKY ACTION BAR ═══ -->
        <div class="entry-action-bar" id="actionBar">
            <div class="action-bar-left">
                <div class="autosave-indicator" id="autosaveIndicator">
                    <i class="fas fa-circle-check"></i> <span id="autosaveText">All changes saved</span>
                </div>
                <span style="color:var(--gray-300);">|</span>
                <span id="progressText" style="font-size:.74rem;color:var(--gray-500);">0 / 10 sections filled</span>
            </div>
            <div class="action-bar-right">
                <button type="button" class="btn-nis btn-ghost btn-sm" id="clearFormBtn">
                    <i class="fas fa-eraser"></i> Clear
                </button>
                <button type="button" class="btn-nis btn-outline-nis btn-sm" id="saveDraftBtn">
                    <i class="fas fa-save"></i> Save Draft
                </button>
                <button type="button" class="btn-nis btn-ghost btn-sm" id="previewBtn" style="background:var(--gold-50);border-color:var(--gold-400);color:var(--gold-600);">
                    <i class="fas fa-eye"></i> Preview
                </button>
                <button type="submit" class="btn-nis btn-primary-nis btn-sm" id="submitBtn">
                    <i class="fas fa-paper-plane"></i> Submit Return
                </button>
            </div>
        </div>

    </form>
</div><!-- /redas-main -->

<!-- ═══ PREVIEW MODAL ═══ 
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border-radius:var(--radius-lg);border:none;">
            <div class="modal-header" style="background:var(--nis-700);color:#fff;border:none;padding:14px 20px;">
                <div>
                    <h5 class="modal-title" style="font-weight:700;font-size:.95rem;">Return Preview</h5>
                    <div style="font-size:.74rem;opacity:.8;" id="previewMeta">—</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;max-height:70vh;overflow-y:auto;" id="previewBody">
                <p style="color:var(--gray-400);text-align:center;padding:40px 0;"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Generating preview…</p>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--gray-100);padding:12px 20px;gap:8px;">
                <button class="btn-nis btn-ghost btn-sm" data-bs-dismiss="modal">Close</button>
                <button class="btn-nis btn-outline-nis btn-sm" id="printPreviewBtn"><i class="fas fa-print"></i> Print</button>
                <button class="btn-nis btn-primary-nis btn-sm" id="submitFromPreview"><i class="fas fa-paper-plane"></i> Submit Now</button>
            </div>
        </div>
    </div>
</div> -->

<!-- ═══ CONFIRM SUBMIT MODAL ═══ -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius-lg);border:none;">
            <div class="modal-header" style="background:var(--nis-700);color:#fff;border:none;padding:14px 20px;">
                <h5 class="modal-title" style="font-weight:700;font-size:.95rem;"><i class="fas fa-paper-plane me-2"></i>Confirm Submission</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <p style="color:var(--gray-600);font-size:.875rem;">You are about to submit this return for supervisor review. Once submitted, you cannot edit it unless it is returned/queried.</p>
                <div style="background:var(--nis-50);border:1px solid var(--nis-200);border-radius:var(--radius-md);padding:12px 16px;font-size:.84rem;margin-top:12px;">
                    <div><strong>Command:</strong> <span id="confirmCommand">—</span></div>
                    <div><strong>Period:</strong> <span id="confirmPeriod">—</span></div>
                    <div><strong>Routing to:</strong> <span id="confirmRoute" style="color:var(--nis-700);font-weight:700;">—</span></div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--gray-100);padding:12px 20px;gap:8px;">
                <button class="btn-nis btn-ghost btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button class="btn-nis btn-primary-nis btn-sm" id="confirmSubmitBtn"><i class="fas fa-check"></i> Yes, Submit</button>
            </div>
        </div>
    </div>
</div>

