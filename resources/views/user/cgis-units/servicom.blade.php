@include('partials.header4')

<div class="redas-content" style="padding-bottom:0;">

    {{-- ================= PAGE HEADER ================= --}}
    <div class="page-header" style="margin-bottom:16px;">
        <div>
            <h1 class="page-title">
                <i class="{{ $unitIcon ?? 'fas fa-headset' }}" style="margin-right:8px;"></i>
                {{ $unitName ?? 'SERVICOM Unit' }} MONTHLY RETURN
            </h1>

            <p class="page-subtitle">
                Complete all applicable SERVICOM Unit operational returns before submission.
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
                Unit Desk Admin
            </span>

            <span class="workflow-arrow">
                <i class="fas fa-arrow-right"></i>
            </span>

            <span class="workflow-step">
                <i class="fas fa-user-shield"></i>
                HQ Admin
            </span>
        </div>
    </div>

</div>

<form method="POST" action="{{ isset($editing) ? route('user.cgis-units.submissions.update', $editing) : route('user.cgis-units.store', 'servicom') }}" enctype="multipart/form-data">

    @csrf
    @isset($editing)
        @method('PUT')
    @endisset

    <div class="redas-content" style="padding-top:10px;">

        {{-- MONTHLY RETURN METADATA --}}
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:var(--gold-100);color:var(--gold-600);">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    Monthly Return
                </div>
            </div>
            <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Report Period</label>
                    <input type="month" class="ni" name="report_period" required value="{{ old('report_period', $editing->return_data['report_period'] ?? now()->format('Y-m')) }}">
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Reporting Officer</label>
                    <input type="text" class="ni" name="reporting_officer" required value="{{ old('reporting_officer', $editing->return_data['reporting_officer'] ?? auth()->user()->name) }}" autocomplete="off" readonly>
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Unit</label>
                    <input type="text" class="ni" value="{{ $unitName ?? 'SERVICOM Unit' }}" readonly>
                </div>
            </div>
        </div>

        {{-- SECTION 1 - SERVICE COMPLAINTS --}}
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    Service Complaints
                </div>
            </div>
            <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Complaints Received</label>
                    <input type="number" class="ni" name="complaints_received" min="0" value="{{ old('complaints_received', $editing->return_data['complaints_received'] ?? '') }}">
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Complaints Resolved</label>
                    <input type="number" class="ni" name="complaints_resolved" min="0" value="{{ old('complaints_resolved', $editing->return_data['complaints_resolved'] ?? '') }}">
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Complaints Pending</label>
                    <input type="number" class="ni" name="complaints_pending" min="0" value="{{ old('complaints_pending', $editing->return_data['complaints_pending'] ?? '') }}">
                </div>
            </div>
        </div>

        {{-- SECTION 2 - SERVICE DELIVERY MONITORING --}}
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-gauge-high"></i>
                    </div>
                    Service Delivery Monitoring
                </div>
            </div>
            <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Charter Compliance Checks</label>
                    <input type="number" class="ni" name="charter_compliance_checks" min="0" value="{{ old('charter_compliance_checks', $editing->return_data['charter_compliance_checks'] ?? '') }}">
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Service Windows Assessed</label>
                    <input type="number" class="ni" name="service_windows_assessed" min="0" value="{{ old('service_windows_assessed', $editing->return_data['service_windows_assessed'] ?? '') }}">
                </div>
                <div style="grid-column:1/-1;">
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Challenges</label>
                    <textarea class="ni" name="challenges" rows="3">{{ old('challenges', $editing->return_data['challenges'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- SUPPORTING DOCUMENTS --}}
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#fef9c3;color:#a16207;">
                        <i class="fas fa-paperclip"></i>
                    </div>
                    Supporting Documents &amp; Attachments
                </div>
            </div>
            <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Supporting Documents (PDF, XLS, XLSX, PNG, JPG)</label>
                    <input type="file" class="ni" name="supporting_documents[]" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg" multiple>
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Attachments (PDF, DOC, DOCX, JPG, PNG)</label>
                    <input type="file" class="ni" name="attachments[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" multiple>
                </div>
            </div>
        </div>

        {{-- DECLARATION & CONSENT --}}
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#f0fdf4;color:#15803d;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    Declaration &amp; Consent
                </div>
            </div>
            <div class="card-body">
                <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;font-size:.84rem;color:var(--gray-700);">
                    <input type="checkbox" name="data_consent" value="1" required style="accent-color:var(--nis-600);margin-top:2px;">
                    <span>
                        I confirm that the information provided is accurate, limited to what is necessary for official NIS reporting,
                        and that I have authority to submit it. I understand that this data will be processed and retained in accordance with
                        the <a href="{{ route('privacy') }}" target="_blank" style="color:#1d4ed8;text-decoration:underline;">Privacy Policy</a>.
                    </span>
                </label>
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="entry-action-bar">
            <div class="action-bar-left"></div>
            <div class="action-bar-right">
                <button type="submit" class="btn-nis btn-primary-nis">
                    <i class="fas fa-paper-plane"></i>
                    Submit {{ $unitName ?? 'SERVICOM' }} Return
                </button>
            </div>
        </div>

    </div>

</form>

@include('partials.footer')
