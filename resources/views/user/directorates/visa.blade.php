@extends('user.directorates._layout')

@section('directorate-sections')

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            State Coordination Activities
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Applications Received</label><input type="number" name="state_applications_received" class="ni" min="0" placeholder="0" value="{{ old('state_applications_received') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Applications Treated</label><input type="number" name="state_applications_treated" class="ni" min="0" placeholder="0" value="{{ old('state_applications_treated') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Cards Issued</label><input type="number" name="state_cards_issued" class="ni" min="0" placeholder="0" value="{{ old('state_cards_issued') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Pending Cases</label><input type="number" name="state_pending_cases" class="ni" min="0" placeholder="0" value="{{ old('state_pending_cases') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Temporary Work Permits (TWP) Issued</label><input type="number" name="temporary_work_permits_issued" class="ni" min="0" placeholder="0" value="{{ old('temporary_work_permits_issued') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">ECOWAS Residence Cards Issued</label><input type="number" name="ecowas_residence_cards_issued" class="ni" min="0" placeholder="0" value="{{ old('ecowas_residence_cards_issued') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">CERPAC Cards Issued</label><input type="number" name="combined_expatriate_residence_permit_cards_issued" class="ni" min="0" placeholder="0" value="{{ old('combined_expatriate_residence_permit_cards_issued') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Regularization Requests Processed</label><input type="number" name="regularization_requests_processed" class="ni" min="0" placeholder="0" value="{{ old('regularization_requests_processed') }}"></div>
    </div>
</div>

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Visa, Quota and Expatriate Administration
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Quota Files Treated</label><input type="number" name="quota_files_treated" class="ni" min="0" placeholder="0" value="{{ old('quota_files_treated') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Expatriate Quota Approvals Granted</label><input type="number" name="quota_approvals_granted" class="ni" min="0" placeholder="0" value="{{ old('quota_approvals_granted') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Expatriate Quota Renewals Processed</label><input type="number" name="quota_renewals_processed" class="ni" min="0" placeholder="0" value="{{ old('quota_renewals_processed') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Diplomatic Facilities Issued</label><input type="number" name="diplomatic_facilities_issued" class="ni" min="0" placeholder="0" value="{{ old('diplomatic_facilities_issued') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">CERPAC Cards Issued</label><input type="number" name="cerpac_cards_issued" class="ni" min="0" placeholder="0" value="{{ old('cerpac_cards_issued') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">STR Visa Endorsements</label><input type="number" name="str_visa_issued" class="ni" min="0" placeholder="0" value="{{ old('str_visa_issued') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Entry Visas Issued/Endorsed</label><input type="number" name="entry_visa_issued" class="ni" min="0" placeholder="0" value="{{ old('entry_visa_issued') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">E-Visa Applications Received</label><input type="number" name="evisa_received" class="ni" min="0" placeholder="0" value="{{ old('evisa_received') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">E-Visa Applications Approved</label><input type="number" name="evisa_approved" class="ni" min="0" placeholder="0" value="{{ old('evisa_approved') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">E-Visa Applications Denied</label><input type="number" name="evisa_denied" class="ni" min="0" placeholder="0" value="{{ old('evisa_denied') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Visa on Arrival Cases Processed</label><input type="number" name="visa_on_arrival_processed" class="ni" min="0" placeholder="0" value="{{ old('visa_on_arrival_processed') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Overstay Penalties Processed</label><input type="number" name="overstay_penalties_processed" class="ni" min="0" placeholder="0" value="{{ old('overstay_penalties_processed') }}"></div>
    </div>
</div>

@endsection
