@extends('user.directorates._layout')

@section('directorate-sections')

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Directorate Personnel Strength
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Comptroller Cadre</label><input type="number" name="staff_comptroller_cadre" class="ni" min="0" placeholder="0" value="{{ old('staff_comptroller_cadre') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Superintendent Cadre</label><input type="number" name="staff_superintendent_cadre" class="ni" min="0" placeholder="0" value="{{ old('staff_superintendent_cadre') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Inspectorate Cadre</label><input type="number" name="staff_inspectorate_cadre" class="ni" min="0" placeholder="0" value="{{ old('staff_inspectorate_cadre') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Assistant Cadre</label><input type="number" name="staff_assistant_cadre" class="ni" min="0" placeholder="0" value="{{ old('staff_assistant_cadre') }}"></div>
    </div>
</div>

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Career Progression and Training
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Officer Promotions</label><input type="number" name="officer_promotions" class="ni" min="0" placeholder="0" value="{{ old('officer_promotions') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Upgrading Cases</label><input type="number" name="upgrading_cases" class="ni" min="0" placeholder="0" value="{{ old('upgrading_cases') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Conversion Cases</label><input type="number" name="conversion_cases" class="ni" min="0" placeholder="0" value="{{ old('conversion_cases') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Training Programmes Conducted</label><input type="number" name="training_programmes" class="ni" min="0" placeholder="0" value="{{ old('training_programmes') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Workshops/Seminars</label><input type="number" name="workshops_count" class="ni" min="0" placeholder="0" value="{{ old('workshops_count') }}"></div>
        <div style="grid-column:1 / -1;"><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Challenges</label><textarea name="hrm_challenges" class="ni" rows="3" placeholder="Provide details...">{{ old('hrm_challenges') }}</textarea></div>
    </div>
</div>

@endsection
