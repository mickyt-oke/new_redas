@extends('user.directorates._layout')

@section('directorate-sections')

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Executive Summary of Passport Operations
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Passport Processing Centres Reporting</label><input type="number" name="processing_centres_reporting" class="ni" min="0" placeholder="0" value="{{ old('processing_centres_reporting') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Total Applications Received</label><input type="number" name="total_applications_received" class="ni" min="0" placeholder="0" value="{{ old('total_applications_received') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Total Applications Enrolled</label><input type="number" name="total_applications_enrolled" class="ni" min="0" placeholder="0" value="{{ old('total_applications_enrolled') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">No. of Booklets Issued</label><input type="number" name="booklets_issued" class="ni" min="0" placeholder="0" value="{{ old('booklets_issued') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Fresh Passport Issuance</label><input type="number" name="fresh_passport_issuance" class="ni" min="0" placeholder="0" value="{{ old('fresh_passport_issuance') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Passport Renewals Processed</label><input type="number" name="passport_renewals_processed" class="ni" min="0" placeholder="0" value="{{ old('passport_renewals_processed') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Express/Urgent Passport Requests Processed</label><input type="number" name="express_passport_requests" class="ni" min="0" placeholder="0" value="{{ old('express_passport_requests') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Lost/Damaged Passport Replacements</label><input type="number" name="damaged_lost_replacements" class="ni" min="0" placeholder="0" value="{{ old('damaged_lost_replacements') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Passport Production Requests Sent</label><input type="number" name="production_requests_sent" class="ni" min="0" placeholder="0" value="{{ old('production_requests_sent') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Booklets Received from Production Centre</label><input type="number" name="booklets_received_from_production" class="ni" min="0" placeholder="0" value="{{ old('booklets_received_from_production') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Booklets Dispatched to Issuing Centres</label><input type="number" name="booklets_dispatched_to_centres" class="ni" min="0" placeholder="0" value="{{ old('booklets_dispatched_to_centres') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Quality Control Rejected Booklets</label><input type="number" name="quality_control_rejected_booklets" class="ni" min="0" placeholder="0" value="{{ old('quality_control_rejected_booklets') }}"></div>
    </div>
</div>

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Revenue, Administration, Control and Stock Returns
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Passport Revenue Total (NGN)</label><input type="number" name="passport_revenue_total" class="ni" min="0" placeholder="0" value="{{ old('passport_revenue_total') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">ECOWAS Travel Certificates Issued</label><input type="number" name="etc_booklets_issued" class="ni" min="0" placeholder="0" value="{{ old('etc_booklets_issued') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">ENBIC Produced</label><input type="number" name="enbic_produced" class="ni" min="0" placeholder="0" value="{{ old('enbic_produced') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Passport Stock Balance</label><input type="number" name="passport_stock_balance" class="ni" min="0" placeholder="0" value="{{ old('passport_stock_balance') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Passport Booklets Opening Balance</label><input type="number" name="booklets_opening_balance" class="ni" min="0" placeholder="0" value="{{ old('booklets_opening_balance') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Passport Booklets Received</label><input type="number" name="booklets_received" class="ni" min="0" placeholder="0" value="{{ old('booklets_received') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Passport Booklets Utilized</label><input type="number" name="booklets_utilized" class="ni" min="0" placeholder="0" value="{{ old('booklets_utilized') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Damaged/Spoilt Booklets</label><input type="number" name="booklets_damaged_spoilt" class="ni" min="0" placeholder="0" value="{{ old('booklets_damaged_spoilt') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Stolen/Missing Booklets</label><input type="number" name="booklets_stolen_missing" class="ni" min="0" placeholder="0" value="{{ old('booklets_stolen_missing') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Passport Control/Security Incidents</label><input type="number" name="passport_control_incidents" class="ni" min="0" placeholder="0" value="{{ old('passport_control_incidents') }}"></div>
        <div style="grid-column:1 / -1;"><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Challenges</label><textarea name="passport_challenges" class="ni" rows="3" placeholder="Provide details...">{{ old('passport_challenges') }}</textarea></div>
    </div>
</div>

@endsection
