@include('partials.header')

<div class="redas-content" style="padding-bottom:0;">

    {{-- PAGE HEADER --}}
    <div class="page-header" style="margin-bottom:16px;">
        <div>
            <h1 class="page-title">
                PROVOST/SECURITY UNIT REPORTING TEMPLATE
            </h1>

            <p class="page-subtitle">
                Complete all applicable Provost/Security Unit
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
                HQ PROVOST/SECURITY
            </span>

        </div>
    </div>

</div>

<form method="POST" action="{{ url('/user/returns') }}" enctype="multipart/form-data" id="provostSecurityReturnForm">

    @csrf

    <input type="hidden" name="cgis" value="PROVOST/SECURITY">
    <input type="hidden" name="status" value="pending">

    <div class="redas-content" style="padding-top:10px;">

        <div class="nis-section">

            <div class="nis-section-head">
                Provost/Security Unit Return
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


{{-- Introduction --}}

<div class="nis-section">

    <div class="nis-section-head">
        <span class="sec-num">1</span>
        Introduction
    </div>

    <div class="nis-section-body">

        <div class="fg">
            <label>Introduction</label>

            <textarea
                name="introduction"
                class="ni"
                rows="6"
                placeholder="Provide a brief overview of the activities of the Provost/Security Unit during the reporting period..."
            ></textarea>

            <small style="color:var(--gray-500);">
                Summarize the major operations, security situation, achievements and any significant observations during the reporting period.
            </small>
        </div>

    </div>

</div>




{{-- SECTION 2 - UNITS UNDER PROVOST/SECURITY --}}

<div class="nis-section">

    <div class="nis-section-head">
        <span class="sec-num">2</span>
        Units Under Provost / Security
    </div>

    <div class="nis-section-body">

        <div class="table-responsive">

            <table class="nis-table">

                <thead>

                <tr>

                    <th style="width:80px;">S/N</th>
                    <th>Sub Unit</th>
                    <th style="width:70px;">Action</th>

                </tr>

                </thead>

                <tbody id="provostUnitsBody">

                    <tr class="data-row">

                        <td>1</td>

                        <td>

                            <input
                                type="text"
                                class="ni provost-unit-name"
                                name="provost_units[0][name]"
                                placeholder="Enter Sub Unit Name"
                            >

                        </td>

                        <td>

                            <button
                                type="button"
                                class="remove-row-btn removeProvostUnit"
                                title="Remove Sub Unit"
                            >
                                <i class="fas fa-trash"></i>
                            </button>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        <button
            type="button"
            class="add-row-btn"
            id="addProvostUnit"
        >
            <i class="fas fa-plus"></i>

            Add Sub Unit

        </button>

    </div>

</div>



{{-- SECTION 3 - TOTAL STAFF STRENGTH --}}

<div class="nis-section">

    <div class="nis-section-head">
        <span class="sec-num">3</span>
        Total Staff Strength
    </div>

    <div class="nis-section-body">

        <div class="table-responsive">

            <table class="nis-table">

                <thead>

                    <tr>

                        <th style="width:80px;">S/N</th>

                        <th>Sub Unit</th>

                        <th style="width:180px;">Strength</th>

                    </tr>

                </thead>

                <tbody id="staffStrengthBody">

                    <!-- Populated automatically from Section 2 -->

                </tbody>

                <tfoot>

                    <tr class="total-row">

                        <td colspan="2">
                            <strong>TOTAL STAFF STRENGTH</strong>
                        </td>

                        <td>

                            <input
                                type="number"
                                readonly
                                class="ni"
                                id="staffStrengthGrandTotal"
                                value="0"
                            >

                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>

        <small style="color:var(--gray-500);">
            This section is automatically generated from the Sub Units entered above.
        </small>

    </div>

</div>


{{-- SECTION 4A - FIREARMS (PROVOST) --}}

<div class="nis-section">

    <div class="nis-section-head">
        <span class="sec-num">4A</span>
        Firearms: Provost / Security
    </div>

    <div class="nis-section-body">

        <div class="table-responsive">

            <table class="nis-table">

                <thead>

                    <tr>
                        <th style="width:70px;">S/N</th>
                        <th>Type</th>
                        <th style="width:180px;">Ammunition</th>
                        <th style="width:90px;">Action</th>
                    </tr>

                </thead>

                <tbody id="provostFirearmsBody">

                <tr class="data-row">

                    <td>1</td>

                    <td>
                        <input
                            type="text"
                            class="ni firearm-type"
                            name="provost_firearms[0][type]"
                        >
                    </td>

                    <td>
                        <input
                            type="number"
                            min="0"
                            class="ni firearm-ammo"
                            name="provost_firearms[0][ammunition]"
                        >
                    </td>

                    <td>

                        <button
                            type="button"
                            class="btn-icon btn-danger remove-firearm-row"
                        >
                            <i class="fas fa-trash"></i>
                        </button>

                    </td>

                </tr>

                </tbody>

                <tfoot>

                    <tr class="total-row">

                        <td colspan="2">
                            <strong>Total Firearm Types</strong>
                        </td>

                        <td>

                            <input
                                readonly
                                id="provostFirearmCount"
                                class="ni"
                                value="1"
                            >

                        </td>

                    </tr>

                    <tr class="total-row">

                        <td colspan="2">
                            <strong>Total Ammunition</strong>
                        </td>

                        <td>

                            <input
                                readonly
                                id="provostAmmoTotal"
                                class="ni"
                                value="0"
                            >

                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>

        <button
            type="button"
            class="add-row-btn"
            data-body="provostFirearmsBody"
            data-prefix="provost_firearms"
        >
            <i class="fas fa-plus"></i>
            Add Firearm
        </button>

    </div>

</div>




<div class="nis-section">

    <div class="nis-section-head">
        <span class="sec-num">4B</span>
        Firearms: RRS
    </div>

    <div class="nis-section-body">

        <div class="table-responsive">

            <table class="nis-table">

                <thead>

                <tr>
                    <th style="width:70px;">S/N</th>
                    <th>Type</th>
                    <th style="width:180px;">Ammunition</th>
                    <th style="width:90px;">Action</th>
                </tr>

                </thead>

                <tbody id="rrsFirearmsBody">

                    <tr class="data-row">

                        <td>1</td>

                        <td>
                            <input
                                type="text"
                                class="ni firearm-type"
                                name="provost_firearms[0][type]"
                            >
                        </td>

                        <td>
                            <input
                                type="number"
                                min="0"
                                class="ni firearm-ammo"
                                name="provost_firearms[0][ammunition]"
                            >
                        </td>

                        <td>

                            <button
                                type="button"
                                class="btn-icon btn-danger remove-firearm-row"
                            >
                                <i class="fas fa-trash"></i>
                            </button>

                        </td>

                    </tr>

                </tbody>

                <tfoot>

                    <tr class="total-row">
                        <td colspan="2"><strong>Total Firearm Types</strong></td>
                        <td><input readonly id="rrsFirearmCount" class="ni" value="1"></td>
                    </tr>

                    <tr class="total-row">
                        <td colspan="2"><strong>Total Ammunition</strong></td>
                        <td><input readonly id="rrsAmmoTotal" class="ni" value="0"></td>
                    </tr>

                </tfoot>

            </table>

        </div>

        <button
            type="button"
            class="add-row-btn"
            data-body="rrsFirearmsBody"
            data-prefix="rrs_firearms"
        >
            <i class="fas fa-plus"></i>
            Add Firearm
        </button>

    </div>

</div>




<div class="nis-section">

    <div class="nis-section-head">
        <span class="sec-num">4C</span>
        Firearms: JTF
    </div>

    <div class="nis-section-body">

        <div class="table-responsive">

            <table class="nis-table">

                <thead>

                <tr>
                    <th style="width:70px;">S/N</th>
                    <th>Type</th>
                    <th style="width:180px;">Ammunition</th>
                    <th style="width:90px;">Action</th>
                </tr>

                </thead>

                <tbody id="jtfFirearmsBody">

                    <tr class="data-row">

                        <td>1</td>

                        <td>
                            <input
                                type="text"
                                class="ni firearm-type"
                                name="provost_firearms[0][type]"
                            >
                        </td>

                        <td>
                            <input
                                type="number"
                                min="0"
                                class="ni firearm-ammo"
                                name="provost_firearms[0][ammunition]"
                            >
                        </td>

                        <td>

                            <button
                                type="button"
                                class="btn-icon btn-danger remove-firearm-row"
                            >
                                <i class="fas fa-trash"></i>
                            </button>

                        </td>

                    </tr>

                </tbody>

                <tfoot>

                    <tr class="total-row">
                        <td colspan="2"><strong>Total Firearm Types</strong></td>
                        <td><input readonly id="jtfFirearmCount" class="ni" value="1"></td>
                    </tr>

                    <tr class="total-row">
                        <td colspan="2"><strong>Total Ammunition</strong></td>
                        <td><input readonly id="jtfAmmoTotal" class="ni" value="0"></td>
                    </tr>

                </tfoot>

            </table>

        </div>

        <button
            type="button"
            class="add-row-btn"
            data-body="jtfFirearmsBody"
            data-prefix="jtf_firearms"
        >
            <i class="fas fa-plus"></i>
            Add Firearm
        </button>

    </div>

</div>





<!-- ================================= -->
<!-- SECTION 5 - AREAS OF RESPONSIBILITY -->
<!-- ================================= -->

<div class="nis-section">

    <div class="nis-section-head">
        <span class="sec-num">5</span>
        Areas of Responsibility
    </div>

    <div class="nis-section-body">

        <div class="table-responsive">

            <table class="nis-table">

                <thead>

                    <tr>
                        <th style="width:70px;">S/N</th>
                        <th>Area of Responsibility</th>
                        <th style="width:90px;">Action</th>
                    </tr>

                </thead>

                <tbody id="responsibilityBody">

                    <tr class="data-row">

                        <td>1</td>

                        <td>
                            <input
                                type="text"
                                class="ni"
                                name="responsibilities[0][description]"
                                placeholder="Enter Area of Responsibility"
                            >
                        </td>

                        <td>

                            <button
                                type="button"
                                class="btn-icon btn-danger remove-simple-row"
                            >
                                <i class="fas fa-trash"></i>
                            </button>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        <button
            type="button"
            class="add-row-btn"
            data-body="responsibilityBody"
            data-prefix="responsibilities"
        >
            <i class="fas fa-plus"></i>
            Add Area
        </button>

    </div>

</div>



{{-- SECTION 6 - ACTIVITIES --}}

<div class="nis-section">

    <div class="nis-section-head">
        <span class="sec-num">6</span>
        Activities Carried Out
    </div>

    <div class="nis-section-body">

        <div class="table-responsive">

            <table class="nis-table">

                <thead>

                    <tr>
                        <th style="width:70px;">S/N</th>
                        <th>Activity</th>
                        <th style="width:90px;">Action</th>
                    </tr>

                </thead>

                <tbody id="activitiesBody">

                    <tr class="data-row">

                        <td>1</td>

                        <td>
                            <input
                                type="text"
                                class="ni"
                                name="activities[0][description]"
                                placeholder="Enter Activity"
                            >
                        </td>

                        <td>

                            <button
                                type="button"
                                class="btn-icon btn-danger remove-simple-row"
                            >
                                <i class="fas fa-trash"></i>
                            </button>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        <button
            type="button"
            class="add-row-btn"
            data-body="activitiesBody"
            data-prefix="activities"
        >
            <i class="fas fa-plus"></i>
            Add Activity
        </button>

    </div>

</div>


{{-- SECTION 7 - CHALLENGES --}}

<div class="nis-section">

    <div class="nis-section-head">
        <span class="sec-num">7</span>
        Challenges
    </div>

    <div class="nis-section-body">

        <div class="table-responsive">

            <table class="nis-table">

                <thead>

                    <tr>
                        <th style="width:70px;">S/N</th>
                        <th>Challenge</th>
                        <th style="width:90px;">Action</th>
                    </tr>

                </thead>

                <tbody id="challengesBody">

                    <tr class="data-row">

                        <td>1</td>

                        <td>
                            <input
                                type="text"
                                class="ni"
                                name="challenges[0][description]"
                                placeholder="Enter Challenge"
                            >
                        </td>

                        <td>

                            <button
                                type="button"
                                class="btn-icon btn-danger remove-simple-row"
                            >
                                <i class="fas fa-trash"></i>
                            </button>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        <button
            type="button"
            class="add-row-btn"
            data-body="challengesBody"
            data-prefix="challenges"
        >
            <i class="fas fa-plus"></i>
            Add Challenge
        </button>

    </div>

</div>




   <!-- ================================ -->
<!-- SECTION 8 - CONCLUSION -->
<!-- ================================ -->

<div class="nis-section">

    <div class="nis-section-head">
        <span class="sec-num">8</span>
        Conclusion
    </div>

    <div class="nis-section-body">

        <div class="fg">

            <label>Conclusion</label>

            <textarea
                name="conclusion"
                class="ni"
                rows="6"
                placeholder="Provide concluding remarks..."
            ></textarea>

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

                            Nominal Roll • Attendance Register • Pictures • Circulars • Other
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
                        readonly>I hereby certify that the information supplied in this report is true and correct to the best of my knowledge and in accordance with the reporting requirements of the Provost/Security Unit.</textarea>

                </div>

            </div>

        </div>


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

                    Submit Provost/Security Return

                </button>

            </div>

        </div>

    </div>

</form>


@include('partials.footer')
