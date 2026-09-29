@extends('user.directorates._layout')

@section('directorate-tabs', '1')

@section('directorate-sections')

<div class="hrm-tabs-wrap">
    <div class="entry-tabs-wrap" style="margin-bottom:0;">
        <div class="entry-tabs" id="entryTabs">
            @php $tabs = [
                ['staff-strength','fas fa-users','1. Staff Strength'],
                ['staff-development','fas fa-graduation-cap','2. Staff Development'],
                ['complaints','fas fa-comment-dots','3. Complaints'],
                ['cases','fas fa-gavel','4. Cases'],
                ['satisfaction','fas fa-smile','5. Customer Satisfaction'],
                ['monitoring','fas fa-clipboard-check','6. Monitoring &amp; Evaluation'],
                ['attachments','fas fa-paperclip','7. Attachments'],
                ['general-report','fas fa-file-alt','8. General Report'],
                ['preview','fas fa-eye','9. Preview'],
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

    {{-- TAB 1: Staff Strength --}}
    <div class="tab-panel active" id="tab-staff-strength">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-users"></i></div>
                    1. Staff Strength <small>(Current Nominal Roll to be Attached)</small>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th style="width:120px;">Male</th>
                                <th style="width:120px;">Female</th>
                                <th style="width:120px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $cadres = [
                                'Compt. Cadre',
                                'Superintendent Cadre',
                                'Inspectorate Cadre',
                                'Assistant Cadre',
                            ];
                            @endphp
                            @foreach($cadres as $index=>$cadre)
                            <tr>
                                <td>{{ $cadre }}</td>
                                <td>
                                    <input type="number" min="0" class="ni staff-male" data-row="{{ $index }}"
                                        name="staff_strength[{{$index}}][male]">
                                </td>
                                <td>
                                    <input type="number" min="0" class="ni staff-female" data-row="{{ $index }}"
                                        name="staff_strength[{{$index}}][female]">
                                </td>
                                <td>
                                    <input type="number" readonly class="ni staff-total" id="staff-total-{{$index}}"
                                        name="staff_strength[{{$index}}][total]">
                                </td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><input readonly id="maleTotal" class="ni"></td>
                                <td><input readonly id="femaleTotal" class="ni"></td>
                                <td><input readonly id="grandTotal" class="ni"></td>
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

    {{-- TAB 2: Staff Development --}}
    <div class="tab-panel" id="tab-staff-development">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-graduation-cap"></i></div>
                    2. Staff Development (Internal/External)
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th width="70">S/N</th>
                                <th>Title of Workshop / Seminar</th>
                                <th>Location</th>
                                <th width="180">No. of Participants</th>
                                <th width="170">Date</th>
                            </tr>
                        </thead>
                        <tbody id="staffDevelopmentBody">
                            <tr class="data-row">
                                <td>1</td>
                                <td><input class="ni" type="text" name="staff_development[0][title]"></td>
                                <td><input class="ni" type="text" name="staff_development[0][location]"></td>
                                <td><input class="ni sc-staff-dev-participants" type="number" min="0" name="staff_development[0][participants]"></td>
                                <td><input class="ni" type="date" name="staff_development[0][date]"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" data-target="staffDevelopmentBody"
                    data-prefix="staff_development" data-cols='["title","location","participants","date"]'>
                    <i class="fas fa-plus"></i> Add Row
                </button>

                {{-- 2b. STAFF DEVELOPMENT (EXTERNAL) - MYSTERY SHOPPING --}}
                <div class="nis-subsection" style="margin-top:20px;">
                    <div class="nis-subsection-title"><i class="fas fa-user-secret"></i> 2b. Staff Development (External) &mdash; Activities: Mystery Shopping</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Theme</th>
                                    <th width="170">No. of Participants</th>
                                    <th width="160">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $mysteryShoppingLocations = [
                                    "Nnamdi Azikiwe Int'l Airport",
                                    "Mallam Aminu Kano Int'l Airport",
                                    "Murtala Muhammed Int'l Airport",
                                    "Akanu Ibiam Int'l Airport",
                                    "Port Harcourt Int'l Airport",
                                    'Lagos Port Complex (Apapa and Tin Can Island)',
                                    'Onne Port Complex',
                                    'Delta Ports (Warri)',
                                    'Lagos Marine and Seaport Command',
                                ];
                                @endphp
                                @foreach($mysteryShoppingLocations as $index=>$location)
                                <tr>
                                    <td>{{ $location }}</td>
                                    <td><input class="ni" type="text" name="mystery_shopping[{{$index}}][theme]"></td>
                                    <td><input class="ni sc-mystery-participants" type="number" min="0" name="mystery_shopping[{{$index}}][participants]"></td>
                                    <td><input class="ni" type="date" name="mystery_shopping[{{$index}}][date]"></td>
                                </tr>
                                @endforeach
                                <tr class="total-row">
                                    <td colspan="2"><strong>TOTAL</strong></td>
                                    <td><input readonly id="sc-mystery-total" class="ni"></td>
                                    <td></td>
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

    {{-- TAB 3: Complaint Management and Channels --}}
    <div class="tab-panel" id="tab-complaints">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);"><i class="fas fa-comment-dots"></i></div>
                    3. Complaint Management and Channels
                </div>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-bottom:20px;">
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Received</label>
                        <input type="number" class="ni" name="complaints_received" min="0" value="{{ old('complaints_received') }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Resolved</label>
                        <input type="number" class="ni" name="complaints_resolved" min="0" value="{{ old('complaints_resolved') }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Pending</label>
                        <input type="number" class="ni" name="complaints_pending" min="0" value="{{ old('complaints_pending') }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Number Resolved Within 10 Days</label>
                        <input type="number" class="ni" name="complaints_resolved_within_10_days" min="0" value="{{ old('complaints_resolved_within_10_days') }}">
                    </div>
                </div>

                {{-- 3B. INFLUX OF COMPLAINTS BY CHANNEL --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-inbox"></i> 3B. Influx of Complaints by Channel</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th width="70">S/N</th>
                                    <th>Channel</th>
                                    <th width="150">Written</th>
                                    <th width="150">Phone/Calls</th>
                                    <th width="150">Email/Chat</th>
                                </tr>
                            </thead>
                            <tbody id="complaintChannelsBody">
                                <tr class="data-row">
                                    <td>1</td>
                                    <td><input class="ni" type="text" name="complaint_channels[0][channel]"></td>
                                    <td><input class="ni" type="number" min="0" name="complaint_channels[0][written]"></td>
                                    <td><input class="ni" type="number" min="0" name="complaint_channels[0][phone_calls]"></td>
                                    <td><input class="ni" type="number" min="0" name="complaint_channels[0][email_chat]"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="add-row-btn" data-target="complaintChannelsBody"
                        data-prefix="complaint_channels" data-cols='["channel","written","phone_calls","email_chat"]'>
                        <i class="fas fa-plus"></i> Add Row
                    </button>
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

    {{-- TAB 4: Cases --}}
    <div class="tab-panel" id="tab-cases">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-gavel"></i></div>
                    4. Cases
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="scCasesTable">
                        <thead>
                            <tr>
                                <th style="min-width:170px;">Case Type</th>
                                @php $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; @endphp
                                @foreach($months as $month)
                                <th>{{ $month }}</th>
                                @endforeach
                                <th>Total</th>
                                <th>Per (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $caseTypes = [
                                'Recruitment', 'Passport Issue', 'Passport Enquiry', 'Contactless',
                                'Expired Passport', 'Visa Issues', 'E-visa', 'E-CERPAC',
                                'Online Payment Issue', 'Document Fraud', 'Tip-Off', 'Mis-Dial',
                                'Border Issues', 'Appreciation', 'Resolved', 'Follow-up',
                            ];
                            @endphp
                            @foreach($caseTypes as $r=>$case)
                            <tr>
                                <td><strong>{{ $case }}</strong></td>
                                @foreach($months as $c=>$month)
                                <td>
                                    <input type="number" min="0" value="0" class="ni sc-case-input" data-row="{{ $r }}"
                                        data-col="{{ $c }}" name="cases[{{ $r }}][{{ strtolower($month) }}]">
                                </td>
                                @endforeach
                                <td><input type="number" class="ni sc-row-total" id="sc-row-total-{{ $r }}" readonly></td>
                                <td><input type="text" class="ni sc-row-pct" id="sc-row-pct-{{ $r }}" readonly></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>Total</strong></td>
                                @foreach($months as $c=>$month)
                                <td><input class="ni sc-col-total" id="sc-col-total-{{ $c }}" readonly></td>
                                @endforeach
                                <td><input class="ni" id="sc-grand-total" readonly></td>
                                <td><input class="ni" id="sc-grand-pct" readonly></td>
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

    {{-- TAB 5: Customer Satisfaction Survey Results --}}
    <div class="tab-panel" id="tab-satisfaction">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-smile"></i></div>
                    5. Customer Satisfaction Survey Results
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th>Evaluated Category</th>
                                <th width="150">Affirmative (%)</th>
                                <th width="150">Negative (%)</th>
                                <th width="150">Total (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $satisfactionItems = [
                                'Availability of clear directional signage and information',
                                'Conducive waiting rooms and reception halls for applicants',
                                'Availability and active execution of redress mechanisms',
                                'Availability of clean and adequate public conveniences',
                                'Smart, clean, and professional appearance of officers',
                                'General quality of administrative services rendered',
                                'Polite, courteous, and helpful attitude of personnel',
                                'Proactive service window monitoring and complaint tracking',
                            ];
                            @endphp
                            @foreach($satisfactionItems as $index=>$item)
                            <tr>
                                <td>{{ $item }}</td>
                                <td><input type="number" min="0" max="100" step="0.01" class="ni sc-satisfaction-affirmative" data-row="{{ $index }}" name="satisfaction[{{$index}}][affirmative]"></td>
                                <td><input type="number" min="0" max="100" step="0.01" class="ni sc-satisfaction-negative" data-row="{{ $index }}" name="satisfaction[{{$index}}][negative]"></td>
                                <td><input type="text" readonly class="ni sc-satisfaction-total" id="sc-satisfaction-total-{{$index}}"></td>
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

    {{-- TAB 6: Monitoring and Evaluation to Select Service Windows --}}
    <div class="tab-panel" id="tab-monitoring">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-clipboard-check"></i></div>
                    6. Monitoring and Evaluation to Select Service Windows
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th>Location / Office</th>
                                <th width="180">No. of Participants</th>
                                <th width="160">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $serviceWindows = [
                                'Abia Passport Office, Umuahia', 'Adamawa Passport Office, Yola', 'Akwa Ibom Passport Office, Uyo',
                                'Anambra Passport Office, Awka', 'Bauchi Passport Office, Bauchi', 'Bayelsa Passport Office, Yenagoa',
                                'Benue Passport Office, Makurdi', 'Borno Passport Office, Maiduguri', 'Cross River Passport Office, Calabar',
                                'Delta Passport Office, Asaba', 'Ebonyi Passport Office, Abakaliki', 'Edo Passport Office, Benin City',
                                'Ekiti Passport Office, Ado-Ekiti', 'Enugu Passport Office, Enugu', 'Gombe Passport Office, Gombe',
                                'Imo Passport Office, Owerri', 'Jigawa Passport Office, Dutse', 'Kaduna Passport Office, Kaduna',
                                'Kano Passport Office, Kano', 'Katsina Passport Office, Katsina', 'Kebbi Passport Office, Birnin Kebbi',
                                'Kogi Passport Office, Lokoja', 'Kwara Passport Office, Ilorin', 'Lagos (Ikoyi) Passport Office',
                                'Lagos (Festac) Passport Office', 'Lagos (Alausa) Passport Office', 'Nasarawa Passport Office, Lafia',
                                'Niger Passport Office, Minna', 'Ogun Passport Office, Abeokuta', 'Ondo Passport Office, Akure',
                                'Osun (Osogbo) Passport Office', 'Oyo (Ibadan) Passport Office', 'Plateau Passport Office, Jos',
                                'Rivers Passport Office, Port Harcourt', 'Sokoto Passport Office, Sokoto', 'Taraba Passport Office, Jalingo',
                                'Yobe Passport Office, Damaturu', 'Zamfara Passport Office, Gusau',
                            ];
                            @endphp
                            @foreach($serviceWindows as $index=>$office)
                            <tr>
                                <td>{{ $office }}</td>
                                <td><input type="number" min="0" class="ni" name="monitoring[{{$index}}][participants]"></td>
                                <td><input type="date" class="ni" name="monitoring[{{$index}}][date]"></td>
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

    {{-- TAB 7: Attachments --}}
    <div class="tab-panel" id="tab-attachments">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#fef9c3;color:#a16207;"><i class="fas fa-paperclip"></i></div>
                    7. Supporting Documents &amp; Attachments
                </div>
            </div>
            <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Supporting Documents (PDF, XLS, XLSX, PNG, JPG) &mdash; incl. Nominal Roll</label>
                    <input type="file" class="ni" name="supporting_documents[]" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg" multiple>
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Attachments (PDF, DOC, DOCX, JPG, PNG)</label>
                    <input type="file" class="ni" name="attachments[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" multiple>
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

    {{-- TAB 8: General Report --}}
    <div class="tab-panel" id="tab-general-report">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-file-alt"></i></div>
                    8. General Report &amp; Summary
                </div>
            </div>
            <div class="card-body">
                <div class="fg">
                    <label>Executive Summary / General Remarks</label>
                    <textarea class="ni" name="general_remarks" rows="8" placeholder="Provide a detailed summary of SERVICOM Unit activities and achievements for the period...">{{ old('general_remarks') }}</textarea>
                </div>
                <div class="fg" style="margin-top:16px;">
                    <label>Challenges Encountered</label>
                    <textarea class="ni" name="challenges" rows="5" placeholder="List any obstacles faced during service delivery monitoring and complaint resolution...">{{ old('challenges') }}</textarea>
                </div>
                <div class="fg" style="margin-top:16px;">
                    <label>Recommendations / Future Action Plan</label>
                    <textarea class="ni" name="recommendations" rows="5" placeholder="Suggest improvements or outline planned activities for the next period...">{{ old('recommendations') }}</textarea>
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

    {{-- TAB 9: Preview --}}
    <div class="tab-panel" id="tab-preview">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-eye"></i></div>
                    9. Review and Submit
                </div>
            </div>
            <div class="card-body">
                <div class="preview-notice" style="background:#fffbeb; border:1px solid #fef3c7; padding:12px; border-radius:6px; color:#92400e; margin-bottom:16px; font-size:.9rem;">
                    <i class="fas fa-exclamation-triangle"></i> Please review all information carefully. Once submitted, the return will move to the Unit Desk Admin for approval.
                </div>
                <div id="formPreviewContent">
                    <p style="text-align:center; color:var(--gray-500); padding:20px;">Please use the Previous button to review each section before submitting.</p>
                </div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="submit" class="btn-nis btn-primary-nis"><i class="fas fa-paper-plane"></i> Submit Report</button>
        </div>
    </div>

</div>

{{-- SERVICOM-specific computations (scoped to sc- prefixed hooks so they never touch other unit pages) --}}
<script>
(function () {
    function sum(selector) {
        var total = 0;
        document.querySelectorAll(selector).forEach(function (el) { total += Number(el.value) || 0; });
        return total;
    }

    /* Mystery shopping participants total */
    function calcMysteryTotal() {
        var el = document.getElementById('sc-mystery-total');
        if (el) el.value = sum('.sc-mystery-participants');
    }

    /* Cases matrix: row totals, column totals, grand total and per-row percentage */
    function calcCasesMatrix() {
        var rowCount = document.querySelectorAll('.sc-row-total').length;
        var colCount = 12;
        if (!rowCount) return;

        var rowTotals = [];
        for (var r = 0; r < rowCount; r++) {
            var rowTotal = 0;
            for (var c = 0; c < colCount; c++) {
                var input = document.querySelector('.sc-case-input[data-row="' + r + '"][data-col="' + c + '"]');
                rowTotal += Number(input && input.value) || 0;
            }
            rowTotals.push(rowTotal);
            var rowTotalEl = document.getElementById('sc-row-total-' + r);
            if (rowTotalEl) rowTotalEl.value = rowTotal;
        }

        var grandTotal = rowTotals.reduce(function (a, b) { return a + b; }, 0);

        for (var rr = 0; rr < rowCount; rr++) {
            var pctEl = document.getElementById('sc-row-pct-' + rr);
            if (pctEl) pctEl.value = grandTotal > 0 ? ((rowTotals[rr] / grandTotal) * 100).toFixed(1) + '%' : '0%';
        }

        for (var cc = 0; cc < colCount; cc++) {
            var colTotal = 0;
            for (var rc = 0; rc < rowCount; rc++) {
                var colInput = document.querySelector('.sc-case-input[data-row="' + rc + '"][data-col="' + cc + '"]');
                colTotal += Number(colInput && colInput.value) || 0;
            }
            var colTotalEl = document.getElementById('sc-col-total-' + cc);
            if (colTotalEl) colTotalEl.value = colTotal;
        }

        var grandTotalEl = document.getElementById('sc-grand-total');
        if (grandTotalEl) grandTotalEl.value = grandTotal;
        var grandPctEl = document.getElementById('sc-grand-pct');
        if (grandPctEl) grandPctEl.value = grandTotal > 0 ? '100%' : '0%';
    }

    /* Customer satisfaction: total = affirmative + negative */
    function calcSatisfactionRow(row) {
        var aff = Number(document.querySelector('.sc-satisfaction-affirmative[data-row="' + row + '"]')?.value) || 0;
        var neg = Number(document.querySelector('.sc-satisfaction-negative[data-row="' + row + '"]')?.value) || 0;
        var totalEl = document.getElementById('sc-satisfaction-total-' + row);
        if (totalEl) totalEl.value = (aff + neg).toFixed(1) + '%';
    }

    function calcAllSatisfactionRows() {
        document.querySelectorAll('.sc-satisfaction-affirmative').forEach(function (el) { calcSatisfactionRow(el.dataset.row); });
    }

    function recalcAll() {
        calcMysteryTotal();
        calcCasesMatrix();
        calcAllSatisfactionRows();
    }

    /* Bound to the form (not just individual inputs) so recompute also runs
       after the edit-prefill script sets values via JS and dispatches a
       single bubbled 'input' event on the form itself. */
    var form = document.querySelector('main form');
    (form || document).addEventListener('input', recalcAll);

    recalcAll();
})();
</script>

@endsection
