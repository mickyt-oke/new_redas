{{-- ===========================================================
    SECTION 2 : E-MIGRANT CENTRE
=========================================================== --}}

<section
    id="emigrantCard"
    class="visa-card">

    <!-- Header -->

    <button
        type="button"
        id="emigrantToggle"
        class="visa-accordion-header">

        <div class="visa-section-title">

            <div class="visa-section-icon">

                <i class="fas fa-globe-africa"></i>

            </div>

            <div style="text-align: left;">

                <h5>e-Migrant Centre</h5>

                <small>

                    Annual Migrant Statistics by Nationality

                </small>

            </div>

        </div>

        <div style="display:flex;align-items:center;gap:15px;">

            <span
                id="emigrantStatus"
                class="section-status status-progress">

                In Progress

            </span>

            <i class="fas fa-chevron-down accordion-icon"></i>

        </div>

    </button>

    <!-- Body -->

    <div class="visa-card-body">

        <div class="table-toolbar" style="display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:14px;flex-wrap:wrap;">
            <div class="table-toolbar-left">
                <strong style="color:var(--gray-700);font-size:.9rem;">e-Migrant Records</strong>
            </div>
            <div class="table-toolbar-right">
                <button
                    type="button"
                    id="addNationality"
                    class="btn-nis btn-primary-nis">
                    <i class="fas fa-plus"></i> + Add Row
                </button>
            </div>
        </div>

        <div class="table-responsive">

            <table class="visa-table">

                <thead>

                <tr>

                    <th style="width:60px;">S/N</th>
                    <th>Center</th>

                    <th>Nationality</th>

                    <th>Region</th>

                    <th>Regular</th>

                    <th>Irregular</th>

                    <th>Male</th>

                    <th>Female</th>

                    <th>Employed</th>

                    <th>Self Employed</th>

                    <th>Student</th>

                    <th>Spouse</th>

                    <th>Dependant</th>

                    <th>Total</th>

                    <th width="60"></th>

                </tr>

                </thead>

                <tbody id="emigrantBody">

                <tr>
                    <td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">1</td>
                    <td>
                        <input
                            type="text"
                            name="emigrant[0][center]"
                            class="ni"
                            placeholder="Enter Center">
                    </td>

                    <td>
                        <input
                            type="text"
                            name="emigrant[0][nationality]"
                            class="ni"
                            placeholder="Enter Nationality">
                    </td>

                    <td>
                        <input
                            type="text"
                            name="emigrant[0][region]"
                            class="ni"
                            placeholder="Enter Region">
                    </td>

                    <td>

                        <input
                            type="number"
                            value="0"
                            min="0"
                            name="emigrant[0][regular]"
                            class="ni em-number regular">

                    </td>

                    <td>

                        <input
                            type="number"
                            value="0"
                            min="0"
                            name="emigrant[0][irregular]"
                            class="ni em-number irregular">

                    </td>

                    <td>

                        <input
                            type="number"
                            value="0"
                            min="0"
                            name="emigrant[0][male]"
                            class="ni em-number male">

                    </td>

                    <td>

                        <input
                            type="number"
                            value="0"
                            min="0"
                            name="emigrant[0][female]"
                            class="ni em-number female">

                    </td>

                    <td>

                        <input
                            type="number"
                            value="0"
                            min="0"
                            name="emigrant[0][employed]"
                            class="ni em-number">

                    </td>

                    <td>

                        <input
                            type="number"
                            value="0"
                            min="0"
                            name="emigrant[0][self_employed]"
                            class="ni em-number">

                    </td>

                    <td>

                        <input
                            type="number"
                            value="0"
                            min="0"
                            name="emigrant[0][student]"
                            class="ni em-number">

                    </td>

                    <td>

                        <input
                            type="number"
                            value="0"
                            min="0"
                            name="emigrant[0][spouse]"
                            class="ni em-number">

                    </td>

                    <td>

                        <input
                            type="number"
                            value="0"
                            min="0"
                            name="emigrant[0][dependant]"
                            class="ni em-number">

                    </td>

                    <td>

                        <input
                            readonly
                            class="ni total-input emigrant-total"
                            value="0">

                    </td>

                    <td>

                        <button
                            type="button"
                            class="btn-nis btn-danger remove-row">

                            <i class="fas fa-trash"></i>

                        </button>

                    </td>

                </tr>

                </tbody>

                <tfoot>

                <tr class="visa-total-row">

                    <th colspan="4">
                        TOTAL
                    </th>

                    <th>
                        <input readonly id="emigrantRegularTotal" class="ni total-input" value="0">
                    </th>

                    <th>
                        <input readonly id="emigrantIrregularTotal" class="ni total-input" value="0">
                    </th>

                    <th>
                        <input readonly id="emigrantMaleTotal" class="ni total-input" value="0">
                    </th>

                    <th>
                        <input readonly id="emigrantFemaleTotal" class="ni total-input" value="0">
                    </th>

                    <th>
                        <input readonly id="emigrantEmployedTotal" class="ni total-input" value="0">
                    </th>

                    <th>
                        <input readonly id="emigrantSelfEmployedTotal" class="ni total-input" value="0">
                    </th>

                    <th>
                        <input readonly id="emigrantStudentTotal" class="ni total-input" value="0">
                    </th>

                    <th>
                        <input readonly id="emigrantSpouseTotal" class="ni total-input" value="0">
                    </th>

                    <th>
                        <input readonly id="emigrantDependantTotal" class="ni total-input" value="0">
                    </th>

                    <th>

                        <input
                            readonly
                            id="overallMigrants"
                            class="ni total-input"
                            value="0">

                    </th>

                    <th></th>

                </tr>

                </tfoot>

            </table>

        </div>

        <!-- Footer -->

        <div class="section-footer">

            <div class="section-summary">

                Overall Migrants :

                <strong id="emigrantSummary">

                    0

                </strong>

            </div>

            <div class="section-buttons">

                <button
                    type="button"
                    id="resetEMigrant"
                    class="btn-nis btn-ghost">

                    <i class="fas fa-rotate-left"></i>

                    Reset

                </button>

                <button
                    type="button"
                    id="saveEMigrant"
                    class="btn-nis btn-primary-nis">

                    <i class="fas fa-check-circle"></i>

                    Save Section

                </button>

            </div>

        </div>

    </div>

</section>