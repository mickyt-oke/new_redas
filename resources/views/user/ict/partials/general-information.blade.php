<div class="visa-card" id="generalCard" style="margin-bottom: 24px; border: 1px solid var(--gray-200); border-radius: var(--visa-card-radius); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
    <!-- Section Header -->
    <button type="button" id="generalToggle" class="visa-accordion-header">
        <div class="visa-section-title">
            <div class="visa-section-icon" style="background:#FEF3C7; color:#B45309; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px;">
                <i class="fas fa-circle-info"></i>
            </div>
            <div style="text-align: left;">
                <h5 style="font-size: 1.05rem; font-weight: 600; color: var(--gray-800); margin: 0;">General Information</h5>
                <small style="color: var(--gray-500); font-size: 0.8rem;">Reporting period, officers and summary remarks</small>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:15px;">
            <span class="section-status status-complete" id="generalStatus">Complete</span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </div>
    </button>

    <!-- Section Body -->
    <div class="visa-card-body" style="padding: 24px;">
        <div class="report-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
            <!-- Reporting Period (Year) -->
            <div class="report-group">
                <label class="report-label" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px; display: block;">
                    Reporting Period (Year)
                </label>
                <select 
                    name="report_year" 
                    id="report_year" 
                    class="ni ni-select" 
                    required
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 0.9rem; transition: var(--visa-transition); background-color: #fff;">
                    <option value="2026" {{ old('report_year', $period) == 2026 ? 'selected' : '' }}>Year 2026</option>
                    <option value="2025" {{ old('report_year', $period) == 2025 ? 'selected' : '' }}>Year 2025</option>
                    <option value="2024" {{ old('report_year', $period) == 2024 ? 'selected' : '' }}>Year 2024</option>
                    <option value="2023" {{ old('report_year', $period) == 2023 ? 'selected' : '' }}>Year 2023</option>
                </select>
            </div>

            <!-- Report Number -->
            <div class="report-group">
                <label class="report-label" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px; display: block;">
                    Report Number
                </label>
                <input
                    type="text"
                    class="ni"
                    readonly
                    value="IR-{{ date('Y') }}-000001"
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-200); border-radius: 8px; font-size: 0.9rem; background-color: var(--gray-50); color: var(--gray-500); cursor: not-allowed; font-weight: 500;">
            </div>


            <!-- Directorate -->
            <div class="report-group">
                <label class="report-label" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px; display: block;">
                    Directorate
                </label>
                <input
                    type="text"
                    class="ni"
                    readonly
                    value="ICT & Cybersecurity Directorate"
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-200); border-radius: 8px; font-size: 0.9rem; background-color: var(--gray-50); color: var(--gray-500); cursor: not-allowed; font-weight: 500;">
            </div>

            <!-- Reporting Officer -->
            <div class="report-group">
                <label class="report-label" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px; display: block;">
                    Reporting Officer
                </label>
                <input 
                    type="text" 
                    id="reporting_officer" 
                    name="reporting_officer" 
                    class="ni" 
                    readonly
                    required 
                    value="{{ auth()->user()->name }}"
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-200); border-radius: 8px; font-size: 0.9rem; background-color: var(--gray-50); color: var(--gray-500); cursor: not-allowed; font-weight: 500;">
            </div>

            <!-- Service Number -->
            <div class="report-group">
                <label class="report-label" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px; display: block;">
                    NIS No
                </label>
                <input 
                    type="text" 
                    id="service_no" 
                    name="service_no" 
                    class="ni" 
                    readonly
                    required 
                    value="{{ auth()->user()->service_number }}"
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-200); border-radius: 8px; font-size: 0.9rem; background-color: var(--gray-50); color: var(--gray-500); cursor: not-allowed; font-weight: 500;">
            </div>



            <!-- Date -->
            <div class="report-group">
                <label class="report-label" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px; display: block;">
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

        <div style="margin-top: 24px;">
            <label class="report-label" for="remarks" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px; display: block;">
                General Remarks / Comments
            </label>
            <textarea 
                name="remarks" 
                id="remarks" 
                class="ni" 
                rows="3" 
                placeholder="Enter any notable remarks or notes for the supervisor..."
                style="width: 100%; padding: 12px 14px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 0.9rem; transition: var(--visa-transition); background-color: #fff; resize: vertical; min-height: 100px;">{{ old('remarks', $application->comments ?? '') }}</textarea>
        </div>


    </div>
</div>
