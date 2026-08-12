@include('partials.header')

<div class="redas-content" style="padding-bottom:0;">

    {{-- ================= PAGE HEADER ================= --}}
    <div class="page-header" style="margin-bottom:16px;">
        <div>
            <h1 class="page-title">
                ANTI-CORRUPTION AND TRANSPARENCY UNIT (ACTU) REPORTING TEMPLATE
            </h1>

            <p class="page-subtitle">
                Complete all applicable Anti-Corruption & Transparency Unit
                operational returns before submission.
            </p>
        </div>

        <div class="workflow-path">

            <span class="workflow-step you">
                <i class="fas fa-user-edit"></i>
                You
            </span>

            <span class="workflow-arrow">
                <i class="fas fa-arrow-right"></i>
            </span>

            <span class="workflow-step">
                <i class="fas fa-user-tie"></i>
                Unit Head
            </span>

            <span class="workflow-arrow">
                <i class="fas fa-arrow-right"></i>
            </span>

            <span class="workflow-step">
                <i class="fas fa-user-shield"></i>
                HQ ACTU
            </span>

        </div>
    </div>

</div>

<form method="POST" action="{{ url('/user/returns') }}" enctype="multipart/form-data" id="actuReturnForm">

    @csrf

    <input type="hidden" name="cgis" value="ACTU">
    <input type="hidden" name="status" value="pending">

    <div class="redas-content" style="padding-top:10px;">

        <div class="nis-section">

            <div class="nis-section-head">
                Anti-Corruption & Transparency Unit Return
            </div>

            <div class="nis-section-body">

                <div class="form-grid-4">

                    <div class="fg">

                        <label>Period of Return</label>

                        <input type="month" class="ni" name="period" required value="{{ now()->format('Y-m') }}">

                    </div>

                    <div class="fg">

                        <label>Return Type</label>

                        <select name="return_type" class="ni ni-select">

                            <option>Monthly Return</option>

                            <option>Quarterly Return</option>

                            <option>Annual Return</option>

                            <option>Special Return</option>

                        </select>

                    </div>

                    <div class="fg">

                        <label>Reporting Officer</label>

                        <input type="text" class="ni" value="{{ auth()->user()->name }}" readonly>

                    </div>

                </div>

            </div>

        </div>


        <div class="nis-section">

            <div class="nis-section-head">

                <span class="sec-num">1</span>

                Staff Strength

                <small>
                    (Current Nominal Roll to be Attached)
                </small>

            </div>

            <div class="nis-section-body">

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

                                <td>

                                    <strong>TOTAL</strong>

                                </td>

                                <td>

                                    <input readonly id="maleTotal" class="ni">

                                </td>

                                <td>

                                    <input readonly id="femaleTotal" class="ni">

                                </td>

                                <td>

                                    <input readonly id="grandTotal" class="ni">

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        <div class="nis-section">

            <div class="nis-section-head">

                <span class="sec-num">2</span>

                Staff Development (External)

            </div>

            <div class="nis-section-body">

                <div class="table-responsive">

                    <table class="nis-table">

                        <thead>

                            <tr>

                                <th width="70">S/N</th>

                                <th>Title of Workshop / Seminar</th>

                                <th>Location</th>

                                <th width="180">

                                    No. of Participants

                                </th>

                                <th width="170">

                                    Date

                                </th>

                            </tr>

                        </thead>

                        <tbody id="staffDevelopmentBody">

                            <tr class="data-row">

                                <td>1</td>

                                <td>

                                    <input class="ni" type="text" name="staff_development[0][title]">

                                </td>

                                <td>

                                    <input class="ni" type="text" name="staff_development[0][location]">

                                </td>

                                <td>

                                    <input class="ni" type="number" min="0" name="staff_development[0][participants]">

                                </td>

                                <td>

                                    <input class="ni" type="date" name="staff_development[0][date]">

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

                <button type="button" class="add-row-btn" data-target="staffDevelopmentBody"
                    data-prefix="staff_development" data-cols='["title","location","participants","date"]'>

                    <i class="fas fa-plus"></i>

                    Add Row

                </button>

            </div>

        </div>


        {{-- SECTION 3 - ACTIVITIES --}}

        <div class="nis-section">

            <div class="nis-section-head">
                <span class="sec-num">3</span>
                Activities
            </div>

            <div class="nis-section-body">

                {{-- 3A AIRPORT SENSITIZATION --}}

                <div class="nis-subsection">

                    <div class="nis-subsection-title">
                        <i class="fas fa-plane"></i>
                        3A. Airport Sensitization
                    </div>

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

                                    <td>
                                        <input class="ni" type="text" name="airport_sensitization[0][location]">
                                    </td>

                                    <td>
                                        <input class="ni" type="text" name="airport_sensitization[0][theme]">
                                    </td>

                                    <td>
                                        <input class="ni" type="number" min="0"
                                            name="airport_sensitization[0][participants]">
                                    </td>

                                    <td>
                                        <input class="ni" type="date" name="airport_sensitization[0][date]">
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                    <button type="button" class="add-row-btn" data-target="airportBody"
                        data-prefix="airport_sensitization" data-cols='["location","theme","participants","date"]'>
                        <i class="fas fa-plus"></i>
                        Add Row
                    </button>

                </div>

                {{-- 3B TRAINING SCHOOL --}}

                <div class="nis-subsection">

                    <div class="nis-subsection-title">
                        <i class="fas fa-school"></i>
                        3B. Training School
                    </div>

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

                                    <td>
                                        <input class="ni" type="text" name="training_school[0][scope]">
                                    </td>

                                    <td>
                                        <input class="ni" type="text" name="training_school[0][location]">
                                    </td>

                                    <td>
                                        <input class="ni" type="text" name="training_school[0][theme]">
                                    </td>

                                    <td>
                                        <input class="ni" type="number" min="0" name="training_school[0][participants]">
                                    </td>

                                    <td>
                                        <input class="ni" type="date" name="training_school[0][date]">
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                    <button type="button" class="add-row-btn" data-target="trainingSchoolBody"
                        data-prefix="training_school" data-cols='["scope","location","theme","participants","date"]'>
                        <i class="fas fa-plus"></i>
                        Add Row
                    </button>

                </div>

                {{-- 3C ANNUAL NATIONWIDE SENSITIZATION --}}

                <div class="nis-subsection">

                    <div class="nis-subsection-title">
                        <i class="fas fa-users"></i>
                        3C. Annual Nationwide Anti-Corruption Sensitization
                    </div>

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

                                    <td>

                                        <input class="ni" type="number" name="annual_sensitization[0][year]">

                                    </td>

                                    <td>

                                        <input class="ni" type="text" name="annual_sensitization[0][theme]">

                                    </td>

                                    <td>

                                        <input class="ni" type="text" name="annual_sensitization[0][location]">

                                    </td>

                                    <td>

                                        <input class="ni" type="date" name="annual_sensitization[0][date]">

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                    <button type="button" class="add-row-btn" data-target="annualBody"
                        data-prefix="annual_sensitization" data-cols='["year","theme","location","date"]'>
                        <i class="fas fa-plus"></i>
                        Add Row
                    </button>

                </div>

                {{-- 3D ACAN --}}

                <div class="nis-subsection">

                    <div class="nis-subsection-title">
                        <i class="fas fa-graduation-cap"></i>
                        3D. Anti-Corruption Academy of Nigeria (ACAN) Step-down Training
                    </div>

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

                                    <td>
                                        <input class="ni" type="text" name="acan[0][scope]">
                                    </td>

                                    <td>
                                        <input class="ni" type="text" name="acan[0][theme]">
                                    </td>

                                    <td>
                                        <input class="ni" type="number" min="0" name="acan[0][participants]">
                                    </td>

                                    <td>
                                        <input class="ni" type="date" name="acan[0][date]">
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                    <button type="button" class="add-row-btn" data-target="acanBody" data-prefix="acan"
                        data-cols='["scope","theme","participants","date"]'>
                        <i class="fas fa-plus"></i>
                        Add Row
                    </button>

                </div>

                {{-- 3E EICS --}}

                <div class="nis-subsection">

                    <div class="nis-subsection-title">
                        <i class="fas fa-chart-line"></i>
                        3E. Ethics & Integrity Compliance Scorecard (EICS) Exercise
                    </div>

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

                                    <td>
                                        <input class="ni" type="number" name="eics[0][year]">
                                    </td>

                                    <td>
                                        <input class="ni" type="text" name="eics[0][rating]">
                                    </td>

                                    <td>
                                        <input class="ni" type="text" name="eics[0][score]">
                                    </td>

                                    <td>
                                        <input class="ni" type="text" name="eics[0][ranking]">
                                    </td>

                                    <td>
                                        <input class="ni" type="text" name="eics[0][remarks]">
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                    <button type="button" class="add-row-btn" data-target="eicsBody" data-prefix="eics"
                        data-cols='["year","rating","score","ranking","remarks"]'>
                        <i class="fas fa-plus"></i>
                        Add Row
                    </button>

                </div>

            </div>

        </div>

        {{-- SECTION 4 - CASES --}}

        <div class="nis-section">

            <div class="nis-section-head">
                <span class="sec-num">4</span>
                CASES
            </div>

            <div class="nis-section-body">

                <div class="table-responsive">

                    <table class="nis-table" id="casesTable">

                        <thead>

                            <tr>

                                <th style="min-width:180px;">
                                    Case Type
                                </th>

                                @php
                                $months = [
                                'Jan','Feb','Mar','Apr',
                                'May','Jun','Jul','Aug',
                                'Sep','Oct','Nov','Dec'
                                ];
                                @endphp

                                @foreach($months as $month)
                                <th>{{ $month }}</th>
                                @endforeach

                                <th>Total</th>

                            </tr>

                        </thead>

                        <tbody>

                            @php

                            $caseTypes = [

                            'Recruitment',

                            'Passport Issues',

                            'Visa Issues',

                            'Others'

                            ];

                            @endphp

                            @foreach($caseTypes as $r=>$case)

                            <tr>

                                <td>
                                    <strong>{{ $case }}</strong>
                                </td>

                                @foreach($months as $c=>$month)

                                <td>

                                    <input type="number" min="0" value="0" class="ni case-input" data-row="{{ $r }}"
                                        data-col="{{ $c }}" name="cases[{{ $r }}][{{ strtolower($month) }}]">

                                </td>

                                @endforeach

                                <td>

                                    <input type="number" class="ni row-total" id="row-total-{{ $r }}" readonly>

                                </td>

                            </tr>

                            @endforeach

                            {{-- Monthly Totals --}}

                            <tr class="total-row">

                                <td>

                                    <strong>Total</strong>

                                </td>

                                @foreach($months as $c=>$month)

                                <td>

                                    <input class="ni col-total" id="col-total-{{ $c }}" readonly>

                                </td>

                                @endforeach

                                <td>

                                    <input class="ni" id="grand-total" readonly>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        {{-- SECTION 5 - ATTACHMENTS --}}

        <div class="nis-section">

            <div class="nis-section-head">
                <span class="sec-num">5</span>
                Supporting Documents
            </div>

            <div class="nis-section-body">

                <div class="fg">

                    <label>

                        <i class="fas fa-paperclip"></i>

                        Attach Supporting Documents

                    </label>

                    <div class="attach-zone" id="attachZone">

                        <i class="fas fa-cloud-upload-alt"></i>

                        <div style="font-size:.85rem;margin-bottom:4px;">

                            Drag & Drop files here

                        </div>

                        <div style="font-size:.75rem;color:var(--gray-500);">

                            Nominal Roll • Attendance Register • Workshop Reports • Pictures • Circulars • Other
                            Evidence

                        </div>

                        <input type="file" id="attachInput" name="attachments[]" multiple
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display:none;">

                    </div>

                    <div id="attachList" class="attach-list"></div>

                </div>

            </div>

        </div>


        {{-- SECTION 6 - REPORTING OFFICER --}}

        <div class="nis-section">

            <div class="nis-section-head">

                <span class="sec-num">6</span>

                Reporting Officer Declaration

            </div>

            <div class="nis-section-body">

                <div class="form-grid-3">

                    <div class="fg">

                        <label>Reporting Officer</label>

                        <input class="ni" type="text" name="reporting_officer" value="{{ auth()->user()->name }}"
                            readonly>

                    </div>

                    <div class="fg">

                        <label>Rank</label>

                        <input class="ni" type="text" name="rank" required>

                    </div>

                    <div class="fg">

                        <label>Service Number</label>

                        <input class="ni" type="text" name="service_number">

                    </div>

                    <div class="fg">

                        <label>Phone Number</label>

                        <input class="ni" type="text" name="phone">

                    </div>

                    <div class="fg">

                        <label>Email</label>

                        <input class="ni" type="email" name="email" value="{{ auth()->user()->email ?? '' }}">

                    </div>

                    <div class="fg">

                        <label>Date</label>

                        <input class="ni" type="date" name="submitted_at" value="{{ now()->format('Y-m-d') }}">

                    </div>

                </div>

                <div class="fg" style="margin-top:10px;">

                    <label>Declaration</label>

                    <textarea class="ni" rows="3"
                        readonly>I hereby certify that the information supplied in this report is true and correct to the best of my knowledge and in accordance with the reporting requirements of the Anti-Corruption and Transparency Unit (ACTU).</textarea>

                </div>

            </div>

        </div>

        {{-- SECTION 7 - ACTION BUTTONS --}}
        <div class="entry-action-bar">

            <div class="action-bar-left">

                <div class="autosave-indicator">

                    <i class="fas fa-circle-check"></i>

                    All changes saved

                </div>

            </div>

            <div class="action-bar-right">

                <button type="button" class="btn-nis btn-ghost" id="clearFormBtn">

                    <i class="fas fa-eraser"></i>

                    Clear

                </button>

                <button type="button" class="btn-nis btn-outline-nis" id="saveDraftBtn">

                    <i class="fas fa-save"></i>

                    Save Draft

                </button>

                <button type="submit" class="btn-nis btn-primary-nis">

                    <i class="fas fa-paper-plane"></i>

                    Submit ACTU Return

                </button>

            </div>

        </div>

    </div>

</form>


<!-- #region -->
@include('partials.footer')
