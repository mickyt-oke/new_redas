@extends('user.directorates._layout')

@section('directorate-tabs', '1')

@section('directorate-sections')

<div class="hrm-tabs-wrap">
    <div class="entry-tabs-wrap" style="margin-bottom:0;">
        <div class="entry-tabs" id="entryTabs">
            @php $tabs = [
                ['staff-strength','fas fa-users','1. Staff Strength'],
                ['staff-development','fas fa-graduation-cap','2. Staff Development'],
                ['activities','fas fa-tasks','3. Activities'],
                ['cases','fas fa-gavel','4. Cases'],
                ['attachments','fas fa-paperclip','5. Attachments'],
                ['general-report','fas fa-file-alt','6. General Report'],
                ['preview','fas fa-eye','7. Preview'],
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
                                <th>Cadre</th>
                                <th style="width:120px;">Male</th>
                                <th style="width:120px;">Female</th>
                                <th style="width:120px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $cadres = [
                                'Comptroller Cadre',
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
                    2. Staff Development (External)
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
                                <td><input class="ni" type="number" min="0" name="staff_development[0][participants]"></td>
                                <td><input class="ni" type="date" name="staff_development[0][date]"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" data-target="staffDevelopmentBody"
                    data-prefix="staff_development" data-cols='["title","location","participants","date"]'>
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

    {{-- TAB 3: Activities --}}
    <div class="tab-panel" id="tab-activities">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-tasks"></i></div>
                    3. Activities
                </div>
            </div>
            <div class="card-body">
                {{-- 3A AIRPORT SENSITIZATION --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-plane"></i> 3A. Airport Sensitization</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th width="70">S/N</th>
                                    <th>Location / Airport</th>
                                    <th>Theme</th>
                                    <th width="180">Participants</th>
                                    <th width="170">Date</th>
                                </tr>
                            </thead>
                            <tbody id="airportBody">
                                <tr class="data-row">
                                    <td>1</td>
                                    <td><input class="ni" type="text" name="airport_sensitization[0][location]"></td>
                                    <td><input class="ni" type="text" name="airport_sensitization[0][theme]"></td>
                                    <td><input class="ni" type="number" min="0" name="airport_sensitization[0][participants]"></td>
                                    <td><input class="ni" type="date" name="airport_sensitization[0][date]"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="add-row-btn" data-target="airportBody"
                        data-prefix="airport_sensitization" data-cols='["location","theme","participants","date"]'>
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                </div>

                {{-- 3B TRAINING SCHOOL --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-school"></i> 3B. Training School</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th width="70">S/N</th>
                                    <th>Scope of Activity</th>
                                    <th>Location</th>
                                    <th>Theme</th>
                                    <th width="170">Participants</th>
                                    <th width="170">Date</th>
                                </tr>
                            </thead>
                            <tbody id="trainingSchoolBody">
                                <tr class="data-row">
                                    <td>1</td>
                                    <td><input class="ni" type="text" name="training_school[0][scope]"></td>
                                    <td><input class="ni" type="text" name="training_school[0][location]"></td>
                                    <td><input class="ni" type="text" name="training_school[0][theme]"></td>
                                    <td><input class="ni" type="number" min="0" name="training_school[0][participants]"></td>
                                    <td><input class="ni" type="date" name="training_school[0][date]"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="add-row-btn" data-target="trainingSchoolBody"
                        data-prefix="training_school" data-cols='["scope","location","theme","participants","date"]'>
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                </div>

                {{-- 3C ANNUAL NATIONWIDE SENSITIZATION --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-users"></i> 3C. Annual Nationwide Anti-Corruption Sensitization</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th width="70">S/N</th>
                                    <th width="150">Year</th>
                                    <th>Theme</th>
                                    <th>Location</th>
                                    <th width="170">Date</th>
                                </tr>
                            </thead>
                            <tbody id="annualBody">
                                <tr class="data-row">
                                    <td>1</td>
                                    <td><input class="ni" type="number" name="annual_sensitization[0][year]"></td>
                                    <td><input class="ni" type="text" name="annual_sensitization[0][theme]"></td>
                                    <td><input class="ni" type="text" name="annual_sensitization[0][location]"></td>
                                    <td><input class="ni" type="date" name="annual_sensitization[0][date]"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="add-row-btn" data-target="annualBody"
                        data-prefix="annual_sensitization" data-cols='["year","theme","location","date"]'>
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                </div>

                {{-- 3D ACAN --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-graduation-cap"></i> 3D. Anti-Corruption Academy of Nigeria (ACAN) Step-down Training</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th width="70">S/N</th>
                                    <th>Scope of Activity</th>
                                    <th>Theme</th>
                                    <th width="170">Participants</th>
                                    <th width="170">Date</th>
                                </tr>
                            </thead>
                            <tbody id="acanBody">
                                <tr class="data-row">
                                    <td>1</td>
                                    <td><input class="ni" type="text" name="acan[0][scope]"></td>
                                    <td><input class="ni" type="text" name="acan[0][theme]"></td>
                                    <td><input class="ni" type="number" min="0" name="acan[0][participants]"></td>
                                    <td><input class="ni" type="date" name="acan[0][date]"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="add-row-btn" data-target="acanBody" data-prefix="acan"
                        data-cols='["scope","theme","participants","date"]'>
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                </div>

                {{-- 3E EICS --}}
                <div class="nis-subsection">
                    <div class="nis-subsection-title"><i class="fas fa-chart-line"></i> 3E. Ethics & Integrity Compliance Scorecard (EICS) Exercise</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th width="70">S/N</th>
                                    <th width="150">Year in View</th>
                                    <th width="150">Rating</th>
                                    <th width="130">Score</th>
                                    <th width="130">Ranking</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody id="eicsBody">
                                <tr class="data-row">
                                    <td>1</td>
                                    <td><input class="ni" type="number" name="eics[0][year]"></td>
                                    <td><input class="ni" type="text" name="eics[0][rating]"></td>
                                    <td><input class="ni" type="text" name="eics[0][score]"></td>
                                    <td><input class="ni" type="text" name="eics[0][ranking]"></td>
                                    <td><input class="ni" type="text" name="eics[0][remarks]"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="add-row-btn" data-target="eicsBody" data-prefix="eics"
                        data-cols='["year","rating","score","ranking","remarks"]'>
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
                    4. CASES
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="casesTable">
                        <thead>
                            <tr>
                                <th style="min-width:180px;">Case Type</th>
                                @php
                                $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                                @endphp
                                @foreach($months as $month)
                                <th>{{ $month }}</th>
                                @endforeach
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $caseTypes = ['Recruitment','Passport Issues','Visa Issues','Others'];
                            @endphp
                            @foreach($caseTypes as $r=>$case)
                            <tr>
                                <td><strong>{{ $case }}</strong></td>
                                @foreach($months as $c=>$month)
                                <td>
                                    <input type="number" min="0" value="0" class="ni case-input" data-row="{{ $r }}"
                                        data-col="{{ $c }}" name="cases[{{ $r }}][{{ strtolower($month) }}]">
                                </td>
                                @endforeach
                                <td><input type="number" class="ni row-total" id="row-total-{{ $r }}" readonly></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>Total</strong></td>
                                @foreach($months as $c=>$month)
                                <td><input class="ni col-total" id="col-total-{{ $c }}" readonly></td>
                                @endforeach
                                <td><input class="ni" id="grand-total" readonly></td>
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

    {{-- TAB 5: Attachments --}}
    <div class="tab-panel" id="tab-attachments">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-paperclip"></i></div>
                    5. Supporting Documents
                </div>
            </div>
            <div class="card-body">
                <div class="fg">
                    <label><i class="fas fa-paperclip"></i> Attach Supporting Documents</label>
                    <div class="attach-zone" id="attachZone">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <div style="font-size:.85rem;margin-bottom:4px;">Drag & Drop files here</div>
                        <div style="font-size:.75rem;color:var(--gray-500);">
                            Nominal Roll • Attendance Register • Workshop Reports • Pictures • Circulars • Other Evidence
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

    {{-- TAB 6: General Report --}}
    <div class="tab-panel" id="tab-general-report">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-file-alt"></i></div>
                    6. General Report & Summary
                </div>
            </div>
            <div class="card-body">
                <div class="fg">
                    <label>Executive Summary / General Remarks</label>
                    <textarea class="ni" name="general_remarks" rows="8" placeholder="Provide a detailed summary of ACTU activities and achievements for the period..."></textarea>
                </div>
                <div class="fg" style="margin-top:16px;">
                    <label>Challenges Encountered</label>
                    <textarea class="ni" name="challenges" rows="5" placeholder="List any obstacles faced during the implementation of anti-corruption measures..."></textarea>
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

    {{-- TAB 7: Preview --}}
    <div class="tab-panel" id="tab-preview">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-eye"></i></div>
                    7. Review and Submit
                </div>
            </div>
            <div class="card-body">
                <div class="preview-notice" style="background:#fffbeb; border:1px solid #fef3c7; padding:12px; border-radius:6px; color:#92400e; margin-bottom:16px; font-size:.9rem;">
                    <i class="fas fa-exclamation-triangle"></i> Please review all information carefully. Once submitted, the report will move to the Unit Head for approval.
                </div>
                <div id="formPreviewContent">
                    <p style="text-align:center; color:var(--gray-500); padding:20px;">Generating preview... please wait.</p>
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

@endsection
