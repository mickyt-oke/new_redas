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

<!-- TAB 5: GENERAL REPORT -->
<div class="tab-panel" id="tab-passport-reports">

            <div class="redas-card" style="margin-bottom:16px;">
                <div class="card-head" style="display:flex; justify-content:space-between; align-items:center;">
                    <div class="card-head-title">REFORMS AND INNOVATION INITIATED IN THE PERIOD UNDER REVIEW</div>
                    <button type="button" class="btn-nis btn-ghost btn-sm" id="passportAddReformRow">
                        <i class="fas fa-plus"></i> Add Reform
                    </button>
                </div>
                <div class="card-body no-pad" style="overflow-x:auto;">
                    <table class="redas-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th>REFORMS/INNOVATIONS</th>
                                <th style="width:250px;">DATE OF IMPLEMENTATION</th>
                            </tr>
                        </thead>
                        <tbody id="reforms-body">
                            <tr>
                                <td class="reform-sn">1</td>
                                <td><input type="text" name="passport[reforms_innovations][0][title]" class="ni"></td>
                                <td><input type="date" name="passport[reforms_innovations][0][date]" class="ni"></td>
                            </tr>
                            <tr>
                                <td class="reform-sn">2</td>
                                <td><input type="text" name="passport[reforms_innovations][1][title]" class="ni"></td>
                                <td><input type="date" name="passport[reforms_innovations][1][date]" class="ni"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="redas-card" style="margin-bottom:16px;">
                <div class="card-head"><div class="card-head-title">GENERAL REPORT</div></div>
                <div class="card-body">
                    <div style="margin-bottom:12px;">
                        <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">i. Other Reports</label>
                        <textarea name="passport[general_report][other_reports]" class="ni" rows="3">{{ old('passport.general_report.other_reports') }}</textarea>
                    </div>
                    <div style="margin-bottom:12px;">
                        <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">ii. Challenges</label>
                        <textarea name="passport[general_report][challenges]" class="ni" rows="3">{{ old('passport.general_report.challenges') }}</textarea>
                    </div>
                    <div style="margin-bottom:12px;">
                        <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">iii. Recommendations / Way Forward</label>
                        <textarea name="passport[general_report][recommendations]" class="ni" rows="3">{{ old('passport.general_report.recommendations') }}</textarea>
                    </div>
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">iv. Conclusion</label>
                        <textarea name="passport[general_report][conclusion]" class="ni" rows="3">{{ old('passport.general_report.conclusion') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Supporting documents upload lives in the standalone wrapper
                 (user.directorates.passport); the combined state form uses its
                 shared attachments[] input instead. --}}

            {{-- <div class="redas-card" style="margin-bottom:14px;">
                <div class="card-head"><div class="card-head-title">REPORTER</div></div>
                <div class="card-body">
                    <div class="form-grid-2">
                        <div>
                            <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Rank</label>
                            <input type="text" name="passport[rank]" class="ni" value="{{ old('passport.rank') }}">
                        </div>
                        <div>
                            <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Phone Number</label>
                            <input type="text" name="passport[gsm_number]" class="ni" value="{{ old('passport.gsm_number') }}">
                        </div>
                    </div>
                </div>
            </div> --}}
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next: Preview <i class="fas fa-arrow-right"></i></button>
        </div>
        </div>


<script>
(function () {
    /* Scope every lookup to this directorate's panel when embedded in the
       combined state form (#dir-passport); on the standalone page that wrapper
       does not exist, so lookups fall back to the whole document (unchanged
       behaviour). */
    var ROOT = document.getElementById('dir-passport') || document;
    function qsa(sel) { return ROOT.querySelectorAll(sel); }
    function qs(sel) { return ROOT.querySelector(sel); }

    const allCenters = @json($processingCenters ?? []);
    const allMissions = @json($foreignMissions ?? []);

    function getSelectedCenters() {
        const selects = qsa('.exec-center-select');
        const selected = [];
        selects.forEach(s => {
            if(s.value) selected.push(s.value);
        });
        return selected;
    }

    function updateCenterDropdowns() {
        const selected = getSelectedCenters();
        const selects = qsa('.exec-center-select');

        selects.forEach(select => {
            const currentValue = select.value;
            // Rebuild options
            select.innerHTML = '<option value="">Select a Center...</option>';
            allCenters.forEach(center => {
                const opt = document.createElement('option');
                opt.value = center;
                opt.textContent = center;
                // Disable if selected elsewhere
                if (selected.includes(center) && currentValue !== center) {
                    opt.disabled = true;
                }
                if (currentValue === center) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });
        });
    }

    function addStaffDevelopmentRow() {
        const tbody = qs('#staff-development-body');
        const nextIndex = tbody.children.length;
        const sn = nextIndex + 1;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="row-index">${sn}</td>
            <td><input type="text" name="passport[staff_development][${nextIndex}][title]" class="ni"></td>
            <td><input type="text" name="passport[staff_development][${nextIndex}][location]" class="ni"></td>
            <td><input type="text" name="passport[staff_development][${nextIndex}][cost]" class="ni"></td>
            <td><input type="number" name="passport[staff_development][${nextIndex}][participants]" class="ni"></td>
            <td><input type="text" name="passport[staff_development][${nextIndex}][duration]" class="ni"></td>
        `;
        tbody.appendChild(tr);
    }

    function getSelectedMissions() {
        const selects = qsa('.foreign-mission-select');
        const selected = [];
        selects.forEach(s => {
            if(s.value) selected.push(s.value);
        });
        return selected;
    }

    function updateMissionDropdowns() {
        const selected = getSelectedMissions();
        const selects = qsa('.foreign-mission-select');

        selects.forEach(select => {
            const currentValue = select.value;
            select.innerHTML = '<option value="">Select a Mission...</option>';
            allMissions.forEach(mission => {
                const opt = document.createElement('option');
                opt.value = mission;
                opt.textContent = mission;
                if (selected.includes(mission) && currentValue !== mission) {
                    opt.disabled = true;
                }
                if (currentValue === mission) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });
        });
    }

    function addForeignMissionRow() {
        const tbody = qs('#foreign-missions-body');
        const nextIndex = tbody.children.length;
        const sn = nextIndex + 1;

        const tr = document.createElement('tr');
        const months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

        let html = `
            <td class="mission-sn">${sn}</td>
            <td>
                <select name="passport[foreign_missions][${nextIndex}][mission]" class="ni foreign-mission-select" style="padding:4px;font-size:0.75rem;min-width:180px;">
                    <option value="">Select a Mission...</option>
                </select>
            </td>
        `;

        months.forEach(m => {
            html += `<td><input type="number" name="passport[foreign_missions][${nextIndex}][${m}]" class="ni calc-mission-month calc-mission-month-${m}" data-idx="${nextIndex}" style="padding:4px;font-size:0.75rem;min-width:60px;"></td>`;
        });

        html += `<td><input type="number" name="passport[foreign_missions][${nextIndex}][total]" id="mission-total-${nextIndex}" class="ni calc-mission-row-total" readonly style="padding:4px;font-size:0.75rem;min-width:60px;background:var(--gray-100);font-weight:bold;"></td>`;

        tr.innerHTML = html;
        tbody.appendChild(tr);

        tr.querySelectorAll('.calc-mission-month').forEach(input => {
            input.addEventListener('input', function() {
                const idx = this.getAttribute('data-idx');
                let rowSum = 0;
                qsa(`.calc-mission-month[data-idx="${idx}"]`).forEach(i => {
                    rowSum += parseInt(i.value) || 0;
                });
                qs(`#mission-total-${idx}`).value = rowSum > 0 ? rowSum : '';
                calculateAllTotals();
            });
        });

        const select = tr.querySelector('.foreign-mission-select');
        select.addEventListener('change', updateMissionDropdowns);
        updateMissionDropdowns();
    }

    function addReformRow() {
        const tbody = qs('#reforms-body');
        const nextIndex = tbody.children.length;
        const sn = nextIndex + 1;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="reform-sn">${sn}</td>
            <td><input type="text" name="passport[reforms_innovations][${nextIndex}][title]" class="ni"></td>
            <td><input type="date" name="passport[reforms_innovations][${nextIndex}][date]" class="ni"></td>
        `;
        tbody.appendChild(tr);
    }

    function addExecutiveSummaryRow() {
        const tbody = qs('#exec-summary-body');
        const nextIndex = tbody.children.length;
        const sn = nextIndex + 1;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="exec-sn">${sn}</td>
            <td>
                <select name="passport[executive_summary][${nextIndex}][center]" class="ni exec-center-select" style="padding:6px;font-size:0.85rem;">
                    <option value="">Select a Center...</option>
                </select>
            </td>
            <td><input type="number" name="passport[executive_summary][${nextIndex}][enrolled]" class="ni calc-exec-enrolled" style="padding:6px;font-size:0.85rem;"></td>
            <td><input type="number" name="passport[executive_summary][${nextIndex}][fresh]" class="ni calc-exec-fresh" style="padding:6px;font-size:0.85rem;"></td>
            <td><input type="number" name="passport[executive_summary][${nextIndex}][reissue]" class="ni calc-exec-reissue" style="padding:6px;font-size:0.85rem;"></td>
            <td><input type="number" name="passport[executive_summary][${nextIndex}][loss]" class="ni calc-exec-loss" style="padding:6px;font-size:0.85rem;"></td>
            <td><input type="number" name="passport[executive_summary][${nextIndex}][cod]" class="ni calc-exec-cod" style="padding:6px;font-size:0.85rem;"></td>
            <td><input type="number" name="passport[executive_summary][${nextIndex}][male_adult]" class="ni calc-exec-male-adult" style="padding:6px;font-size:0.85rem;"></td>
            <td><input type="number" name="passport[executive_summary][${nextIndex}][female_adult]" class="ni calc-exec-female-adult" style="padding:6px;font-size:0.85rem;"></td>
            <td><input type="number" name="passport[executive_summary][${nextIndex}][male_minor]" class="ni calc-exec-male-minor" style="padding:6px;font-size:0.85rem;"></td>
        `;
        tbody.appendChild(tr);

        // Attach event listeners to new inputs
        tr.querySelectorAll('.calc-exec-enrolled, .calc-exec-fresh, .calc-exec-reissue, .calc-exec-loss, .calc-exec-cod, .calc-exec-male-adult, .calc-exec-female-adult, .calc-exec-male-minor').forEach(input => {
            input.addEventListener('input', calculateAllTotals);
        });

        const select = tr.querySelector('.exec-center-select');
        select.addEventListener('change', updateCenterDropdowns);

        // Initialize options for the newly added select
        updateCenterDropdowns();
    }

    function calculateAllTotals() {
        // Staff Totals
        let totalMale = 0, totalFemale = 0, totalAll = 0;
        const males = qsa('.calc-male');
        const females = qsa('.calc-female');
        const rowTotals = qsa('.calc-row-total');
        males.forEach((input, index) => {
            const m = parseInt(input.value) || 0;
            const f = parseInt(females[index].value) || 0;
            const rowT = m + f;
            rowTotals[index].value = rowT > 0 ? rowT : '';
            totalMale += m; totalFemale += f; totalAll += rowT;
        });
        qs('#total-male').value = totalMale;
        qs('#total-female').value = totalFemale;
        qs('#total-all').value = totalAll;

        // Column sums (New Passport Types)
        const calcColSum = (selector, totalSelector) => {
            let sum = 0;
            qsa(selector).forEach(i => sum += parseInt(i.value) || 0);
            const totalEl = qs(totalSelector);
            if(totalEl) totalEl.value = sum > 0 ? sum : '';
        };
        calcColSum('.calc-std-32p', '.std-32p-total');
        calcColSum('.calc-std-64p', '.std-64p-total');
        calcColSum('.calc-official-32p', '.official-32p-total');
        calcColSum('.calc-official-64p', '.official-64p-total');
        calcColSum('.calc-diplo-32p', '.diplo-32p-total');
        calcColSum('.calc-diplo-64p', '.diplo-64p-total');
        calcColSum('.calc-ctc-32p', '.ctc-32p-total');
        calcColSum('.calc-ctc-64p', '.ctc-64p-total');
        calcColSum('.calc-step-32p', '.step-32p-total');
        calcColSum('.calc-step-64p', '.step-64p-total');
        calcColSum('.calc-rtd-32p', '.rtd-32p-total');
        calcColSum('.calc-rtd-64p', '.rtd-64p-total');
        calcColSum('.calc-enbic-32p', '.enbic-32p-total');
        calcColSum('.calc-enbic-64p', '.enbic-64p-total');
        calcColSum('.calc-dtc-32p', '.dtc-32p-total');
        calcColSum('.calc-dtc-64p', '.dtc-64p-total');

        // Exec Summary Totals
        calcColSum('.calc-exec-enrolled', '#exec-total-enrolled');
        calcColSum('.calc-exec-fresh', '#exec-total-fresh');
        calcColSum('.calc-exec-reissue', '#exec-total-reissue');
        calcColSum('.calc-exec-loss', '#exec-total-loss');
        calcColSum('.calc-exec-cod', '#exec-total-cod');
        calcColSum('.calc-exec-male-adult', '#exec-total-male-adult');
        calcColSum('.calc-exec-female-adult', '#exec-total-female-adult');
        calcColSum('.calc-exec-male-minor', '#exec-total-male-minor');

        // Foreign Missions Column Totals
        const months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
        months.forEach(m => {
            calcColSum(`.calc-mission-month-${m}`, `#mission-total-col-${m}`);
        });
        calcColSum('.calc-mission-row-total', '#mission-total-col-total');
    }

    /* Preview: read-only snapshot of every card in the form, rendered into the
       shared layout's preview panel (#hrmPreviewBody). The layout calls this
       whenever the Preview tab is opened. Guarded so the combined state form's
       own preview builder (if any) is not clobbered. */
    window.buildDirectoratePreview = window.buildDirectoratePreview || function () {
        const container = document.getElementById('hrmPreviewBody');
        const form = document.getElementById('dir-passport') || document.querySelector('main form');
        if (!container || !form) return;
        container.innerHTML = '';

        form.querySelectorAll('.redas-card').forEach(card => {
            const clone = card.cloneNode(true);

            // Remove all buttons (Add More, etc)
            clone.querySelectorAll('button').forEach(btn => btn.remove());

            // Strip 'name'/'id' to prevent submission conflicts; show live values read-only
            clone.querySelectorAll('input, select, textarea').forEach(input => {
                const name = input.getAttribute('name');
                const originalInput = name ? card.querySelector(`[name="${name}"]`) : null;
                if (originalInput) {
                    if (input.type === 'file') {
                        input.outerHTML = originalInput.files.length > 0
                            ? `<span style="font-weight:bold;color:var(--nis-600);">${originalInput.files.length} file(s) selected</span>`
                            : `<span style="color:var(--gray-500);">No files selected</span>`;
                        return;
                    }
                    if (input.type === 'checkbox' || input.type === 'radio') {
                        input.outerHTML = `<span style="font-weight:bold;color:var(--nis-600);">${originalInput.checked ? 'Yes' : 'No'}</span>`;
                        return;
                    }
                    input.value = originalInput.value;
                    if (input.tagName === 'SELECT') {
                        input.innerHTML = `<option>${originalInput.value}</option>`;
                    }
                    if (input.tagName === 'TEXTAREA') {
                        input.textContent = originalInput.value;
                    }
                }

                input.removeAttribute('name');
                input.removeAttribute('id');
                input.setAttribute('readonly', 'readonly');
                input.setAttribute('disabled', 'disabled');
                input.style.backgroundColor = 'transparent';
                input.style.border = 'none';
                input.style.fontWeight = 'bold';
                input.style.color = 'var(--nis-900)';
                input.style.padding = '0';
            });

            clone.style.border = '1px solid var(--nis-200)';
            clone.style.boxShadow = 'none';
            clone.style.marginBottom = '14px';

            container.appendChild(clone);
        });
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Attach calculation listeners to staff and column sums
        qsa('.calc-male, .calc-female, .calc-passport-input, .calc-exec-enrolled, .calc-exec-fresh, .calc-exec-reissue, .calc-exec-loss, .calc-exec-cod, .calc-exec-male-adult, .calc-exec-female-adult, .calc-exec-male-minor').forEach(input => {
            input.addEventListener('input', calculateAllTotals);
        });

        // Attach listeners for Foreign Missions Row Totals
        qsa('.calc-mission-month').forEach(input => {
            input.addEventListener('input', function() {
                const idx = this.getAttribute('data-idx');
                let rowSum = 0;
                qsa(`.calc-mission-month[data-idx="${idx}"]`).forEach(i => {
                    rowSum += parseInt(i.value) || 0;
                });
                qs(`#mission-total-${idx}`).value = rowSum > 0 ? rowSum : '';
            });
        });

        /* "Add row" buttons (tab switching and the Preview tab are handled
           globally by partials/footer.blade.php and the shared layout). */
        qs('#passportAddStaffDevRow')?.addEventListener('click', addStaffDevelopmentRow);
        qs('#passportAddExecRow')?.addEventListener('click', addExecutiveSummaryRow);
        qs('#passportAddMissionRow')?.addEventListener('click', addForeignMissionRow);
        qs('#passportAddReformRow')?.addEventListener('click', addReformRow);

        // Initialize executive summary with 3 blank rows
        addExecutiveSummaryRow();
        addExecutiveSummaryRow();
        addExecutiveSummaryRow();

        // Initialize foreign missions with 3 blank rows
        addForeignMissionRow();
        addForeignMissionRow();
        addForeignMissionRow();

        /* Recalculate totals from any restored draft / prefilled values */
        calculateAllTotals();
    });
})();
</script>
