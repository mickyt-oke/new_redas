@extends('user.directorates._layout')

@section('directorate-sections')

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Breach of Immigration Laws
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Breach Cases Handled</label><input type="number" name="breach_cases" class="ni" min="0" placeholder="0" value="{{ old('breach_cases') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Arrests Made</label><input type="number" name="arrests_made" class="ni" min="0" placeholder="0" value="{{ old('arrests_made') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Cases Prosecuted</label><input type="number" name="prosecution_cases" class="ni" min="0" placeholder="0" value="{{ old('prosecution_cases') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Convictions Secured</label><input type="number" name="convictions" class="ni" min="0" placeholder="0" value="{{ old('convictions') }}"></div>
    </div>
</div>

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Specialized Operations
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Interpol Activities</label><input type="number" name="interpol_activities" class="ni" min="0" placeholder="0" value="{{ old('interpol_activities') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">DOFIT Cases</label><input type="number" name="dofit_cases" class="ni" min="0" placeholder="0" value="{{ old('dofit_cases') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Watch-listed Persons</label><input type="number" name="suspect_index_watchlist" class="ni" min="0" placeholder="0" value="{{ old('suspect_index_watchlist') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Screening Centre Detainees</label><input type="number" name="screening_centre_detainees" class="ni" min="0" placeholder="0" value="{{ old('screening_centre_detainees') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">DFU Cases Received</label><input type="number" name="dfu_cases_received" class="ni" min="0" placeholder="0" value="{{ old('dfu_cases_received') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">DFU Cases Concluded</label><input type="number" name="dfu_cases_concluded" class="ni" min="0" placeholder="0" value="{{ old('dfu_cases_concluded') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Fraudulent Documents Detected</label><input type="number" name="fraudulent_documents_detected" class="ni" min="0" placeholder="0" value="{{ old('fraudulent_documents_detected') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Fraud Networks Disrupted</label><input type="number" name="fraud_networks_disrupted" class="ni" min="0" placeholder="0" value="{{ old('fraud_networks_disrupted') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Compliance Raids/Enforcement Operations</label><input type="number" name="compliance_raids_conducted" class="ni" min="0" placeholder="0" value="{{ old('compliance_raids_conducted') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Joint Operations with Other Agencies</label><input type="number" name="joint_operations_other_agencies" class="ni" min="0" placeholder="0" value="{{ old('joint_operations_other_agencies') }}"></div>
        <div style="grid-column:1 / -1;"><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Remarks</label><textarea name="investigation_remarks" class="ni" rows="3" placeholder="Provide details...">{{ old('investigation_remarks') }}</textarea></div>
    </div>
</div>

@endsection
