{{-- Shared Passport directorate return-form sections.
     Included by the standalone directorate page (user.directorates.passport)
     and by the combined state return form (user.states._return-layout).
     Tab ids and field names are namespaced (passport-*) so all ten
     directorate forms can coexist on one page. The surrounding layout
     provides the <form>, CSRF, period, return_type, reporting_officer,
     data_consent and (on the combined page) a shared attachments[] upload;
     do not nest another form or file upload here. --}}
<div class="hrm-tabs-wrap">
    <div class="entry-tabs-wrap" style="margin-bottom:0;">
        <div class="entry-tabs" id="passportEntryTabs">
            @php $tabs = [
                ['passport-meta','fas fa-info-circle','1. Personnel Strength & Development'],
                ['passport-admin','fas fa-id-card','2. Passport Admin'],
                ['passport-exec','fas fa-map-marker-alt','3. Executive Summary'],
                ['passport-foreign','fas fa-globe','4. Foreign Missions'],
                ['passport-reports','fas fa-file-alt','5. General Report'],
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

<!-- TAB 1: META & STAFF -->
<div class="tab-panel active" id="tab-passport-meta">
            <div class="redas-card" style="margin-bottom:16px;">
                <div class="card-head"><div class="card-head-title">1. STAFF STRENGTH (Current nominal roll)</div></div>
                <div class="card-body no-pad" style="overflow-x:auto;">
                    <table class="redas-table">
                        <thead>
                            <tr>
                                <th>Cadre</th>
                                <th>MALE</th>
                                <th>FEMALE</th>
                                <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="font-weight:600;">Deputy Comptroller - General</td>
                                <td><input type="number" name="passport[staff_strength][0][male]" class="ni calc-male" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][0][female]" class="ni calc-female" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][0][total]" class="ni calc-row-total" placeholder="0" readonly style="background:var(--gray-50);"></td>
                            </tr>
                            <tr>
                                <td style="font-weight:600;">Assistant Comptroller - General</td>
                                <td><input type="number" name="passport[staff_strength][1][male]" class="ni calc-male" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][1][female]" class="ni calc-female" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][1][total]" class="ni calc-row-total" placeholder="0" readonly style="background:var(--gray-50);"></td>
                            </tr>
                            <tr>
                                <td style="font-weight:600;">Comptroller of Immigration</td>
                                <td><input type="number" name="passport[staff_strength][2][male]" class="ni calc-male" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][2][female]" class="ni calc-female" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][2][total]" class="ni calc-row-total" placeholder="0" readonly style="background:var(--gray-50);"></td>
                            </tr>
                            <tr>
                                <td style="font-weight:600;">Deputy Comptroller of Immigration</td>
                                <td><input type="number" name="passport[staff_strength][3][male]" class="ni calc-male" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][3][female]" class="ni calc-female" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][3][total]" class="ni calc-row-total" placeholder="0" readonly style="background:var(--gray-50);"></td>
                            </tr>
                            <tr>
                                <td style="font-weight:600;">Assistant Comptroller of Immigration</td>
                                <td><input type="number" name="passport[staff_strength][4][male]" class="ni calc-male" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][4][female]" class="ni calc-female" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][4][total]" class="ni calc-row-total" placeholder="0" readonly style="background:var(--gray-50);"></td>
                            </tr>
                            <tr>
                                <td style="font-weight:600;">Chief Superintendent of Immigration</td>
                                <td><input type="number" name="passport[staff_strength][5][male]" class="ni calc-male" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][5][female]" class="ni calc-female" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][5][total]" class="ni calc-row-total" placeholder="0" readonly style="background:var(--gray-50);"></td>
                            </tr>
                            <tr>
                                <td style="font-weight:600;">Superintendent of Immigration</td>
                                <td><input type="number" name="passport[staff_strength][6][male]" class="ni calc-male" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][6][female]" class="ni calc-female" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][6][total]" class="ni calc-row-total" placeholder="0" readonly style="background:var(--gray-50);"></td>
                            </tr>
                            <tr>
                                <td style="font-weight:600;">Deputy Superintendent of Immigration</td>
                                <td><input type="number" name="passport[staff_strength][7][male]" class="ni calc-male" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][7][female]" class="ni calc-female" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][7][total]" class="ni calc-row-total" placeholder="0" readonly style="background:var(--gray-50);"></td>
                            </tr>
                            <tr>
                                <td style="font-weight:600;">Assistant Superintendent I</td>
                                <td><input type="number" name="passport[staff_strength][8][male]" class="ni calc-male" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][8][female]" class="ni calc-female" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][8][total]" class="ni calc-row-total" placeholder="0" readonly style="background:var(--gray-50);"></td>
                            </tr>
                            <tr>
                                <td style="font-weight:600;">Assistant Superintendent II</td>
                                <td><input type="number" name="passport[staff_strength][9][male]" class="ni calc-male" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][9][female]" class="ni calc-female" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][9][total]" class="ni calc-row-total" placeholder="0" readonly style="background:var(--gray-50);"></td>
                            </tr>
                            <tr>
                                <td style="font-weight:600;">Inspector of Immigration</td>
                                <td><input type="number" name="passport[staff_strength][10][male]" class="ni calc-male" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][10][female]" class="ni calc-female" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][10][total]" class="ni calc-row-total" placeholder="0" readonly style="background:var(--gray-50);"></td>
                            </tr>
                            <tr>
                                <td style="font-weight:600;">Assistant Inspector of Immigration</td>
                                <td><input type="number" name="passport[staff_strength][11][male]" class="ni calc-male" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][11][female]" class="ni calc-female" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][11][total]" class="ni calc-row-total" placeholder="0" readonly style="background:var(--gray-50);"></td>
                            </tr>
                            <tr>
                                <td style="font-weight:600;">Immigration Assistant I</td>
                                <td><input type="number" name="passport[staff_strength][12][male]" class="ni calc-male" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][12][female]" class="ni calc-female" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][12][total]" class="ni calc-row-total" placeholder="0" readonly style="background:var(--gray-50);"></td>
                            </tr>
                            <tr>
                                <td style="font-weight:600;">Immigration Assistant II</td>
                                <td><input type="number" name="passport[staff_strength][13][male]" class="ni calc-male" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][13][female]" class="ni calc-female" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][13][total]" class="ni calc-row-total" placeholder="0" readonly style="background:var(--gray-50);"></td>
                            </tr>
                            <tr>
                                <td style="font-weight:600;">Immigration Assistant III</td>
                                <td><input type="number" name="passport[staff_strength][14][male]" class="ni calc-male" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][14][female]" class="ni calc-female" placeholder="0"></td>
                                <td><input type="number" name="passport[staff_strength][14][total]" class="ni calc-row-total" placeholder="0" readonly style="background:var(--gray-50);"></td>
                            </tr>
                            <tr style="background:var(--gray-100);font-weight:bold;">
                                <td style="font-weight:700;">TOTAL STAFF STRENGTH</td>
                                <td><input type="number" id="total-male" name="passport[staff_strength][15][male]" class="ni" placeholder="0" readonly style="background:transparent;border:none;font-weight:bold;"></td>
                                <td><input type="number" id="total-female" name="passport[staff_strength][15][female]" class="ni" placeholder="0" readonly style="background:transparent;border:none;font-weight:bold;"></td>
                                <td><input type="number" id="total-all" name="passport[staff_strength][15][total]" class="ni" placeholder="0" readonly style="background:transparent;border:none;font-weight:bold;color:var(--nis-600);"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="redas-card" style="margin-bottom:16px;">
                <div class="card-head" style="display:flex; justify-content:space-between; align-items:center;">
                    <div class="card-head-title">2. STAFF DEVELOPMENT (Training/Workshops)</div>
                    <button type="button" class="btn-nis btn-ghost btn-sm" id="passportAddStaffDevRow">
                        <i class="fas fa-plus"></i> Add More
                    </button>
                </div>
                <div class="card-body no-pad" style="overflow-x:auto;">
                    <table class="redas-table" id="staff-development-table">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>TITLE OF WORKSHOP/SEMINAR</th>
                                <th>LOCATION</th>
                                <th>COST IMPLICATION</th>
                                <th>NO. OF PARTICIPANTS</th>
                                <th>DURATION</th>
                            </tr>
                        </thead>
                        <tbody id="staff-development-body">
                            <tr>
                                <td class="row-index">1</td>
                                <td><input type="text" name="passport[staff_development][0][title]" class="ni"></td>
                                <td><input type="text" name="passport[staff_development][0][location]" class="ni"></td>
                                <td><input type="text" name="passport[staff_development][0][cost]" class="ni"></td>
                                <td><input type="number" name="passport[staff_development][0][participants]" class="ni"></td>
                                <td><input type="text" name="passport[staff_development][0][duration]" class="ni"></td>
                            </tr>
                            <tr>
                                <td class="row-index">2</td>
                                <td><input type="text" name="passport[staff_development][1][title]" class="ni"></td>
                                <td><input type="text" name="passport[staff_development][1][location]" class="ni"></td>
                                <td><input type="text" name="passport[staff_development][1][cost]" class="ni"></td>
                                <td><input type="number" name="passport[staff_development][1][participants]" class="ni"></td>
                                <td><input type="text" name="passport[staff_development][1][duration]" class="ni"></td>
                            </tr>
                            <tr>
                                <td class="row-index">3</td>
                                <td><input type="text" name="passport[staff_development][2][title]" class="ni"></td>
                                <td><input type="text" name="passport[staff_development][2][location]" class="ni"></td>
                                <td><input type="text" name="passport[staff_development][2][cost]" class="ni"></td>
                                <td><input type="number" name="passport[staff_development][2][participants]" class="ni"></td>
                                <td><input type="text" name="passport[staff_development][2][duration]" class="ni"></td>
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

<!-- TAB 2: PASSPORT ADMIN -->
<div class="tab-panel" id="tab-passport-admin">
            <div class="grid-2">
                <!-- A. STANDARD PASSPORT -->
                <div class="redas-card" style="margin-bottom:16px;">
                    <div class="card-head"><div class="card-head-title">A. STANDARD PASSPORT</div></div>
                    <div class="card-body no-pad">
                        <table class="redas-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>32 Pages Number</th>
                                    <th>64 Pages Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-weight:600;">Fresh</td>
                                    <td><input type="number" name="passport[standard_passport][0][32p]" class="ni calc-passport-input calc-std-32p"></td>
                                    <td><input type="number" name="passport[standard_passport][0][64p]" class="ni calc-passport-input calc-std-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Re-issue</td>
                                    <td><input type="number" name="passport[standard_passport][1][32p]" class="ni calc-passport-input calc-std-32p"></td>
                                    <td><input type="number" name="passport[standard_passport][1][64p]" class="ni calc-passport-input calc-std-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Lost</td>
                                    <td><input type="number" name="passport[standard_passport][2][32p]" class="ni calc-passport-input calc-std-32p"></td>
                                    <td><input type="number" name="passport[standard_passport][2][64p]" class="ni calc-passport-input calc-std-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Change of Data</td>
                                    <td><input type="number" name="passport[standard_passport][3][32p]" class="ni calc-passport-input calc-std-32p"></td>
                                    <td><input type="number" name="passport[standard_passport][3][64p]" class="ni calc-passport-input calc-std-64p"></td>
                                </tr>
                                <tr style="background:var(--gray-100);font-weight:bold;">
                                    <td style="font-weight:700;">Total</td>
                                    <td><input type="number" name="passport[standard_passport][4][32p]" class="ni std-32p-total" readonly style="background:transparent;border:none;font-weight:bold;"></td>
                                    <td><input type="number" name="passport[standard_passport][4][64p]" class="ni std-64p-total" readonly style="background:transparent;border:none;font-weight:bold;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- B. OFFICIAL PASSPORT -->
                <div class="redas-card" style="margin-bottom:16px;">
                    <div class="card-head"><div class="card-head-title">B. OFFICIAL PASSPORT</div></div>
                    <div class="card-body no-pad">
                        <table class="redas-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>32 Pages Number</th>
                                    <th>64 Pages Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-weight:600;">Fresh</td>
                                    <td><input type="number" name="passport[official_passport][0][32p]" class="ni calc-passport-input calc-official-32p"></td>
                                    <td><input type="number" name="passport[official_passport][0][64p]" class="ni calc-passport-input calc-official-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Re-issue</td>
                                    <td><input type="number" name="passport[official_passport][1][32p]" class="ni calc-passport-input calc-official-32p"></td>
                                    <td><input type="number" name="passport[official_passport][1][64p]" class="ni calc-passport-input calc-official-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Lost</td>
                                    <td><input type="number" name="passport[official_passport][2][32p]" class="ni calc-passport-input calc-official-32p"></td>
                                    <td><input type="number" name="passport[official_passport][2][64p]" class="ni calc-passport-input calc-official-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Change of Data</td>
                                    <td><input type="number" name="passport[official_passport][3][32p]" class="ni calc-passport-input calc-official-32p"></td>
                                    <td><input type="number" name="passport[official_passport][3][64p]" class="ni calc-passport-input calc-official-64p"></td>
                                </tr>
                                <tr style="background:var(--gray-100);font-weight:bold;">
                                    <td style="font-weight:700;">Total</td>
                                    <td><input type="number" name="passport[official_passport][4][32p]" class="ni official-32p-total" readonly style="background:transparent;border:none;font-weight:bold;"></td>
                                    <td><input type="number" name="passport[official_passport][4][64p]" class="ni official-64p-total" readonly style="background:transparent;border:none;font-weight:bold;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- C. DIPLOMATIC PASSPORT -->
                <div class="redas-card" style="margin-bottom:16px;">
                    <div class="card-head"><div class="card-head-title">C. DIPLOMATIC PASSPORT</div></div>
                    <div class="card-body no-pad">
                        <table class="redas-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>32 Pages Number</th>
                                    <th>64 Pages Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-weight:600;">Fresh</td>
                                    <td><input type="number" name="passport[diplomatic_passport][0][32p]" class="ni calc-passport-input calc-diplo-32p"></td>
                                    <td><input type="number" name="passport[diplomatic_passport][0][64p]" class="ni calc-passport-input calc-diplo-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Re-issue</td>
                                    <td><input type="number" name="passport[diplomatic_passport][1][32p]" class="ni calc-passport-input calc-diplo-32p"></td>
                                    <td><input type="number" name="passport[diplomatic_passport][1][64p]" class="ni calc-passport-input calc-diplo-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Lost</td>
                                    <td><input type="number" name="passport[diplomatic_passport][2][32p]" class="ni calc-passport-input calc-diplo-32p"></td>
                                    <td><input type="number" name="passport[diplomatic_passport][2][64p]" class="ni calc-passport-input calc-diplo-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Change of Data</td>
                                    <td><input type="number" name="passport[diplomatic_passport][3][32p]" class="ni calc-passport-input calc-diplo-32p"></td>
                                    <td><input type="number" name="passport[diplomatic_passport][3][64p]" class="ni calc-passport-input calc-diplo-64p"></td>
                                </tr>
                                <tr style="background:var(--gray-100);font-weight:bold;">
                                    <td style="font-weight:700;">Total</td>
                                    <td><input type="number" name="passport[diplomatic_passport][4][32p]" class="ni diplo-32p-total" readonly style="background:transparent;border:none;font-weight:bold;"></td>
                                    <td><input type="number" name="passport[diplomatic_passport][4][64p]" class="ni diplo-64p-total" readonly style="background:transparent;border:none;font-weight:bold;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- D. CONVENTION TRAVEL CERTIFICATE -->
                <div class="redas-card" style="margin-bottom:16px;">
                    <div class="card-head"><div class="card-head-title">D. CONVENTION TRAVEL CERTIFICATE</div></div>
                    <div class="card-body no-pad">
                        <table class="redas-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>32 Pages Number</th>
                                    <th>64 Pages Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-weight:600;">Fresh</td>
                                    <td><input type="number" name="passport[convention_travel_certificate][0][32p]" class="ni calc-passport-input calc-ctc-32p"></td>
                                    <td><input type="number" name="passport[convention_travel_certificate][0][64p]" class="ni calc-passport-input calc-ctc-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Re-issue</td>
                                    <td><input type="number" name="passport[convention_travel_certificate][1][32p]" class="ni calc-passport-input calc-ctc-32p"></td>
                                    <td><input type="number" name="passport[convention_travel_certificate][1][64p]" class="ni calc-passport-input calc-ctc-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Lost</td>
                                    <td><input type="number" name="passport[convention_travel_certificate][2][32p]" class="ni calc-passport-input calc-ctc-32p"></td>
                                    <td><input type="number" name="passport[convention_travel_certificate][2][64p]" class="ni calc-passport-input calc-ctc-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Change of Data</td>
                                    <td><input type="number" name="passport[convention_travel_certificate][3][32p]" class="ni calc-passport-input calc-ctc-32p"></td>
                                    <td><input type="number" name="passport[convention_travel_certificate][3][64p]" class="ni calc-passport-input calc-ctc-64p"></td>
                                </tr>
                                <tr style="background:var(--gray-100);font-weight:bold;">
                                    <td style="font-weight:700;">Total</td>
                                    <td><input type="number" name="passport[convention_travel_certificate][4][32p]" class="ni ctc-32p-total" readonly style="background:transparent;border:none;font-weight:bold;"></td>
                                    <td><input type="number" name="passport[convention_travel_certificate][4][64p]" class="ni ctc-64p-total" readonly style="background:transparent;border:none;font-weight:bold;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- E. SINGLE TRAVEL EMERGENCY PASSPORT (STEP) -->
                <div class="redas-card" style="margin-bottom:16px;">
                    <div class="card-head"><div class="card-head-title">E. SINGLE TRAVEL EMERGENCY PASSPORT (STEP)</div></div>
                    <div class="card-body no-pad">
                        <table class="redas-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>32 Pages Number</th>
                                    <th>64 Pages Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-weight:600;">Fresh</td>
                                    <td><input type="number" name="passport[single_travel_emergency_passport][0][32p]" class="ni calc-passport-input calc-step-32p"></td>
                                    <td><input type="number" name="passport[single_travel_emergency_passport][0][64p]" class="ni calc-passport-input calc-step-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Re-issue</td>
                                    <td><input type="number" name="passport[single_travel_emergency_passport][1][32p]" class="ni calc-passport-input calc-step-32p"></td>
                                    <td><input type="number" name="passport[single_travel_emergency_passport][1][64p]" class="ni calc-passport-input calc-step-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Lost</td>
                                    <td><input type="number" name="passport[single_travel_emergency_passport][2][32p]" class="ni calc-passport-input calc-step-32p"></td>
                                    <td><input type="number" name="passport[single_travel_emergency_passport][2][64p]" class="ni calc-passport-input calc-step-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Change of Data</td>
                                    <td><input type="number" name="passport[single_travel_emergency_passport][3][32p]" class="ni calc-passport-input calc-step-32p"></td>
                                    <td><input type="number" name="passport[single_travel_emergency_passport][3][64p]" class="ni calc-passport-input calc-step-64p"></td>
                                </tr>
                                <tr style="background:var(--gray-100);font-weight:bold;">
                                    <td style="font-weight:700;">Total</td>
                                    <td><input type="number" name="passport[single_travel_emergency_passport][4][32p]" class="ni step-32p-total" readonly style="background:transparent;border:none;font-weight:bold;"></td>
                                    <td><input type="number" name="passport[single_travel_emergency_passport][4][64p]" class="ni step-64p-total" readonly style="background:transparent;border:none;font-weight:bold;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- F. REFUGEE TRAVEL DOCUMENT (RTD) -->
                <div class="redas-card" style="margin-bottom:16px;">
                    <div class="card-head"><div class="card-head-title">F. REFUGEE TRAVEL DOCUMENT (RTD)</div></div>
                    <div class="card-body no-pad">
                        <table class="redas-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>32 Pages Number</th>
                                    <th>64 Pages Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-weight:600;">Fresh</td>
                                    <td><input type="number" name="passport[refugee_travel_document][0][32p]" class="ni calc-passport-input calc-rtd-32p"></td>
                                    <td><input type="number" name="passport[refugee_travel_document][0][64p]" class="ni calc-passport-input calc-rtd-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Re-issue</td>
                                    <td><input type="number" name="passport[refugee_travel_document][1][32p]" class="ni calc-passport-input calc-rtd-32p"></td>
                                    <td><input type="number" name="passport[refugee_travel_document][1][64p]" class="ni calc-passport-input calc-rtd-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Lost</td>
                                    <td><input type="number" name="passport[refugee_travel_document][2][32p]" class="ni calc-passport-input calc-rtd-32p"></td>
                                    <td><input type="number" name="passport[refugee_travel_document][2][64p]" class="ni calc-passport-input calc-rtd-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Change of Data</td>
                                    <td><input type="number" name="passport[refugee_travel_document][3][32p]" class="ni calc-passport-input calc-rtd-32p"></td>
                                    <td><input type="number" name="passport[refugee_travel_document][3][64p]" class="ni calc-passport-input calc-rtd-64p"></td>
                                </tr>
                                <tr style="background:var(--gray-100);font-weight:bold;">
                                    <td style="font-weight:700;">Total</td>
                                    <td><input type="number" name="passport[refugee_travel_document][4][32p]" class="ni rtd-32p-total" readonly style="background:transparent;border:none;font-weight:bold;"></td>
                                    <td><input type="number" name="passport[refugee_travel_document][4][64p]" class="ni rtd-64p-total" readonly style="background:transparent;border:none;font-weight:bold;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- G. ECOWAS NATIONAL BIOMETRIC IDENTITY CARD -->
                <div class="redas-card" style="margin-bottom:16px;">
                    <div class="card-head"><div class="card-head-title">G. ECOWAS NATIONAL BIOMETRIC IDENTITY CARD</div></div>
                    <div class="card-body no-pad">
                        <table class="redas-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>32 Pages Number</th>
                                    <th>64 Pages Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-weight:600;">Fresh</td>
                                    <td><input type="number" name="passport[ecowas_enbic][0][32p]" class="ni calc-passport-input calc-enbic-32p"></td>
                                    <td><input type="number" name="passport[ecowas_enbic][0][64p]" class="ni calc-passport-input calc-enbic-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Re-issue</td>
                                    <td><input type="number" name="passport[ecowas_enbic][1][32p]" class="ni calc-passport-input calc-enbic-32p"></td>
                                    <td><input type="number" name="passport[ecowas_enbic][1][64p]" class="ni calc-passport-input calc-enbic-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Lost</td>
                                    <td><input type="number" name="passport[ecowas_enbic][2][32p]" class="ni calc-passport-input calc-enbic-32p"></td>
                                    <td><input type="number" name="passport[ecowas_enbic][2][64p]" class="ni calc-passport-input calc-enbic-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Change of Data</td>
                                    <td><input type="number" name="passport[ecowas_enbic][3][32p]" class="ni calc-passport-input calc-enbic-32p"></td>
                                    <td><input type="number" name="passport[ecowas_enbic][3][64p]" class="ni calc-passport-input calc-enbic-64p"></td>
                                </tr>
                                <tr style="background:var(--gray-100);font-weight:bold;">
                                    <td style="font-weight:700;">Total</td>
                                    <td><input type="number" name="passport[ecowas_enbic][4][32p]" class="ni enbic-32p-total" readonly style="background:transparent;border:none;font-weight:bold;"></td>
                                    <td><input type="number" name="passport[ecowas_enbic][4][64p]" class="ni enbic-64p-total" readonly style="background:transparent;border:none;font-weight:bold;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- H. DIGITAL TRAVEL CERTIFICATE -->
                <div class="redas-card" style="margin-bottom:16px;">
                    <div class="card-head"><div class="card-head-title">H. DIGITAL TRAVEL CERTIFICATE</div></div>
                    <div class="card-body no-pad">
                        <table class="redas-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>32 Pages Number</th>
                                    <th>64 Pages Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-weight:600;">Fresh</td>
                                    <td><input type="number" name="passport[digital_travel_certificate][0][32p]" class="ni calc-passport-input calc-dtc-32p"></td>
                                    <td><input type="number" name="passport[digital_travel_certificate][0][64p]" class="ni calc-passport-input calc-dtc-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Re-issue</td>
                                    <td><input type="number" name="passport[digital_travel_certificate][1][32p]" class="ni calc-passport-input calc-dtc-32p"></td>
                                    <td><input type="number" name="passport[digital_travel_certificate][1][64p]" class="ni calc-passport-input calc-dtc-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Lost</td>
                                    <td><input type="number" name="passport[digital_travel_certificate][2][32p]" class="ni calc-passport-input calc-dtc-32p"></td>
                                    <td><input type="number" name="passport[digital_travel_certificate][2][64p]" class="ni calc-passport-input calc-dtc-64p"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:600;">Change of Data</td>
                                    <td><input type="number" name="passport[digital_travel_certificate][3][32p]" class="ni calc-passport-input calc-dtc-32p"></td>
                                    <td><input type="number" name="passport[digital_travel_certificate][3][64p]" class="ni calc-passport-input calc-dtc-64p"></td>
                                </tr>
                                <tr style="background:var(--gray-100);font-weight:bold;">
                                    <td style="font-weight:700;">Total</td>
                                    <td><input type="number" name="passport[digital_travel_certificate][4][32p]" class="ni dtc-32p-total" readonly style="background:transparent;border:none;font-weight:bold;"></td>
                                    <td><input type="number" name="passport[digital_travel_certificate][4][64p]" class="ni dtc-64p-total" readonly style="background:transparent;border:none;font-weight:bold;"></td>
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

<!-- TAB 3: EXECUTIVE SUMMARY -->
<div class="tab-panel" id="tab-passport-exec">
            <div class="redas-card">
                <div class="card-head" style="display:flex; justify-content:space-between; align-items:center;">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-building"></i></div>
                        PASSPORT OPERATIONAL RECORDS
                    </div>
                    <button type="button" class="btn-nis btn-ghost btn-sm" id="passportAddExecRow">
                        <i class="fas fa-plus"></i> Add Center
                    </button>
                </div>
                <div class="card-body no-pad" style="overflow-x:auto;">
                    <table class="redas-table" style="min-width:1400px;">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th style="min-width:250px;">PASSPORT PROCESSING CENTRES</th>
                                <th style="min-width:120px;">Total Enrolled</th>
                                <th style="min-width:120px;">Fresh</th>
                                <th style="min-width:120px;">Re-issue</th>
                                <th style="min-width:120px;">Loss Only</th>
                                <th style="min-width:150px;">Change of Data (COD)</th>
                                <th style="min-width:120px;">Male (Adult)</th>
                                <th style="min-width:120px;">Female (Adult)</th>
                                <th style="min-width:120px;">Male (Minor)</th>
                            </tr>
                        </thead>
                        <tbody id="exec-summary-body">
                            <!-- Rows will be injected by JavaScript -->
                        </tbody>
                        <tfoot>
                            <tr style="background:var(--gray-100);font-weight:bold;">
                                <td></td>
                                <td style="font-weight:700;text-align:right;">TOTAL:</td>
                                <td><input type="number" class="ni" id="exec-total-enrolled" readonly style="background:transparent;border:none;font-weight:bold;padding:6px;font-size:0.85rem;"></td>
                                <td><input type="number" class="ni" id="exec-total-fresh" readonly style="background:transparent;border:none;font-weight:bold;padding:6px;font-size:0.85rem;"></td>
                                <td><input type="number" class="ni" id="exec-total-reissue" readonly style="background:transparent;border:none;font-weight:bold;padding:6px;font-size:0.85rem;"></td>
                                <td><input type="number" class="ni" id="exec-total-loss" readonly style="background:transparent;border:none;font-weight:bold;padding:6px;font-size:0.85rem;"></td>
                                <td><input type="number" class="ni" id="exec-total-cod" readonly style="background:transparent;border:none;font-weight:bold;padding:6px;font-size:0.85rem;"></td>
                                <td><input type="number" class="ni" id="exec-total-male-adult" readonly style="background:transparent;border:none;font-weight:bold;padding:6px;font-size:0.85rem;"></td>
                                <td><input type="number" class="ni" id="exec-total-female-adult" readonly style="background:transparent;border:none;font-weight:bold;padding:6px;font-size:0.85rem;"></td>
                                <td><input type="number" class="ni" id="exec-total-male-minor" readonly style="background:transparent;border:none;font-weight:bold;padding:6px;font-size:0.85rem;"></td>
                            </tr>
                        </tfoot>
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

<!-- TAB 4: FOREIGN MISSIONS -->
<div class="tab-panel" id="tab-passport-foreign">
            <div class="redas-card">
                <div class="card-head" style="display:flex; justify-content:space-between; align-items:center;">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#fce7f3;color:#be185d;"><i class="fas fa-plane-departure"></i></div>
                        Passport Issued in Foreign Missions
                    </div>
                    <button type="button" class="btn-nis btn-ghost btn-sm" id="passportAddMissionRow">
                        <i class="fas fa-plus"></i> Add Mission
                    </button>
                </div>
                <div class="card-body no-pad" style="overflow-x:auto;">
                    <table class="redas-table" style="min-width:1200px;">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th style="min-width:200px;">ISSUING CENTRE</th>
                                @foreach(['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'] as $m)
                                <th style="min-width:70px;">{{ $m }}</th>
                                @endforeach
                                <th style="min-width:80px;">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody id="foreign-missions-body">
                            <!-- Rows will be injected by JavaScript -->
                        </tbody>
                        <tfoot>
                            <tr style="background:var(--gray-100);font-weight:bold;">
                                <td></td>
                                <td style="font-weight:700;text-align:right;">TOTAL:</td>
                                @foreach(['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'] as $m)
                                <td><input type="number" class="ni" id="mission-total-col-{{ $m }}" readonly style="background:transparent;border:none;font-weight:bold;padding:4px;font-size:0.75rem;min-width:60px;"></td>
                                @endforeach
                                <td><input type="number" class="ni" id="mission-total-col-total" readonly style="background:transparent;border:none;font-weight:bold;padding:4px;font-size:0.75rem;min-width:60px;color:var(--nis-600);"></td>
                            </tr>
                        </tfoot>
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
