<div class="visa-card" id="generalCard" style="margin-bottom: 24px; border: 1px solid var(--gray-200); border-radius: var(--visa-card-radius); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
    <button type="button" class="visa-accordion-header" id="generalToggle">
        <div class="visa-section-title">
            <div class="visa-section-icon" style="background:#FEF3C7; color:#B45309; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px;">
                <i class="fas fa-file-alt"></i>
            </div>
            <div style="text-align: left;">
                <h5 style="font-size: 1.05rem; font-weight: 600; color: var(--gray-800); margin: 0;">General Report Information</h5>
                <small style="color: var(--gray-500); font-size: 0.8rem;">Basic identification and metadata for the annual report</small>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px;">
            <span id="generalStatus" class="section-status status-complete">
                Complete
            </span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <div class="visa-card-body" style="padding: 24px;">
        <div class="report-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
            <!-- Reporting Year -->
            <div class="report-group">
                <label class="report-label" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">
                    Reporting Year
                </label>
                <select
                    name="report_year"
                    id="report_year"
                    class="ni"
                    required
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 0.9rem; transition: var(--visa-transition); background-color: #fff;">
                    @for($year=2026; $year>=2023; $year--)
                        <option
                            value="{{ $year }}"
                            {{ old('report_year',$period)==$year?'selected':'' }}>
                            {{ $year }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Report Number -->
            <div class="report-group">
                <label class="report-label" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">
                    Report Number
                </label>
                <input
                    type="text"
                    class="ni"
                    readonly
                    value="VR-{{ date('Y') }}-000001"
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-200); border-radius: 8px; font-size: 0.9rem; background-color: var(--gray-50); color: var(--gray-500); cursor: not-allowed; font-weight: 500;">
            </div>

            <!-- Directorate -->
            <div class="report-group">
                <label class="report-label" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">
                    Directorate
                </label>
                <input
                    type="text"
                    class="ni"
                    readonly
                    value="Visa & Residence Directorate"
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-200); border-radius: 8px; font-size: 0.9rem; background-color: var(--gray-50); color: var(--gray-500); cursor: not-allowed; font-weight: 500;">
            </div>

            <!-- Officer -->
            <div class="report-group">
                <label class="report-label" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">
                    Reporting Officer
                </label>
                <input
                    type="text"
                    class="ni"
                    name="reporting_officer"
                    readonly
                    value="{{ auth()->user()->name }}"
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-200); border-radius: 8px; font-size: 0.9rem; background-color: var(--gray-50); color: var(--gray-500); cursor: not-allowed; font-weight: 500;">
            </div>

            <!-- Service Number -->
            <div class="report-group">
                <label class="report-label" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">
                    NIS No
                </label>
                <input
                    type="text"
                    class="ni"
                    name="nis_no"
                    readonly
                    value="{{ auth()->user()->service_number }}"
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-200); border-radius: 8px; font-size: 0.9rem; background-color: var(--gray-50); color: var(--gray-500); cursor: not-allowed; font-weight: 500;">
            </div>



            <!-- Date -->
            <div class="report-group">
                <label class="report-label" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">
                    Report Date
                </label>
                <input
                    type="date"
                    class="ni"
                    name="report_date"
                    value="{{ date('Y-m-d') }}"
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 0.9rem; transition: var(--visa-transition); background-color: #fff;">
            </div>


        </div>

        <hr style="border: 0; border-top: 1px solid var(--gray-200); margin: 24px 0;">

        <div class="report-group">
            <label class="report-label" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">
                Remarks
            </label>
            <textarea
                rows="4"
                class="ni"
                name="remarks"
                placeholder="Enter any remarks about this annual report..."
                style="width: 100%; padding: 12px 14px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 0.9rem; transition: var(--visa-transition); background-color: #fff; resize: vertical; min-height: 100px;">{{ old('remarks', $application->comments ?? '') }}</textarea>
        </div>
    </div>
</div>