@extends('user.directorates._layout')

@section('directorate-sections')

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Migrant Data and Permits
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Migrant E-Registrations</label><input type="number" name="migrant_ereg_count" class="ni" min="0" placeholder="0" value="{{ old('migrant_ereg_count') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Foreigners Issued Work Permits</label><input type="number" name="work_permits_issued" class="ni" min="0" placeholder="0" value="{{ old('work_permits_issued') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Migrant Students Recorded</label><input type="number" name="migrant_students" class="ni" min="0" placeholder="0" value="{{ old('migrant_students') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Seasonal Migrants Employed</label><input type="number" name="seasonal_migrants_employed" class="ni" min="0" placeholder="0" value="{{ old('seasonal_migrants_employed') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Border Migrants Employed</label><input type="number" name="border_migrants_employed" class="ni" min="0" placeholder="0" value="{{ old('border_migrants_employed') }}"></div>
    </div>
</div>

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            International Cooperation
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">MoUs with African Countries</label><input type="number" name="mou_africa" class="ni" min="0" placeholder="0" value="{{ old('mou_africa') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">MoUs with European Countries</label><input type="number" name="mou_europe" class="ni" min="0" placeholder="0" value="{{ old('mou_europe') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">MoUs with Asian Countries</label><input type="number" name="mou_asia" class="ni" min="0" placeholder="0" value="{{ old('mou_asia') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">MoUs with Americas</label><input type="number" name="mou_americas" class="ni" min="0" placeholder="0" value="{{ old('mou_americas') }}"></div>
        <div style="grid-column:1 / -1;"><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Summary / Remarks</label><textarea name="migration_summary" class="ni" rows="3" placeholder="Provide details...">{{ old('migration_summary') }}</textarea></div>
    </div>
</div>

@endsection
