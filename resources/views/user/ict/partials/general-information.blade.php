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
        <div class="report-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
            <div>
                <label class="report-label" for="report_year" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px; display: block;">
                    Reporting Period (Year) <span style="color:var(--color-danger)">*</span>
                </label>
                <select 
                    name="report_year" 
                    id="report_year" 
                    class="ni ni-select" 
                    required
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 0.9rem; transition: var(--visa-transition); background-color: #fff;">
                    <option value="{{ date('Y') }}" selected>Year {{ date('Y') }}</option>
                    <option value="{{ date('Y') - 1 }}">Year {{ date('Y') - 1 }}</option>
                    <option value="{{ date('Y') - 2 }}">Year {{ date('Y') - 2 }}</option>
                </select>
            </div>
            <div>
                <label class="report-label" for="reporting_office" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px; display: block;">
                    Reporting Office <span style="color:var(--color-danger)">*</span>
                </label>
                <input 
                    type="text" 
                    id="reporting_office" 
                    name="reporting_office" 
                    class="ni" 
                    required 
                    value="ICT & Cybersecurity Directorate"
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 0.9rem; transition: var(--visa-transition); background-color: #fff;">
            </div>
            <div>
                <label class="report-label" for="reporting_officer" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px; display: block;">
                    Reporting Officer <span style="color:var(--color-danger)">*</span>
                </label>
                <input 
                    type="text" 
                    id="reporting_officer" 
                    name="reporting_officer" 
                    class="ni" 
                    required 
                    value="{{ auth()->user()->name }}"
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 0.9rem; transition: var(--visa-transition); background-color: #fff;">
            </div>
            <div>
                <label class="report-label" for="service_no" style="font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 8px; display: block;">
                    Service Number <span style="color:var(--color-danger)">*</span>
                </label>
                <input 
                    type="text" 
                    id="service_no" 
                    name="service_no" 
                    class="ni" 
                    required 
                    value="{{ auth()->user()->service_number }}"
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
                style="width: 100%; padding: 12px 14px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 0.9rem; transition: var(--visa-transition); background-color: #fff; resize: vertical; min-height: 100px;"></textarea>
        </div>

        <!-- Summary -->
        <div class="section-footer" style="display: flex; justify-content: flex-end; align-items: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--gray-200);">
            <div class="section-buttons">
                <button type="button" class="btn-nis btn-primary-nis" style="padding: 10px 20px; border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-check-circle"></i> Save Section
                </button>
            </div>
        </div>
    </div>
</div>
