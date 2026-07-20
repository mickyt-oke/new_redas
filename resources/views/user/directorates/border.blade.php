@extends('user.directorates._layout')

@section('directorate-sections')

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Land and Sea Border Returns
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Land Border Arrivals</label><input type="number" name="land_arrivals" class="ni" min="0" placeholder="0" value="{{ old('land_arrivals') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Land Border Departures</label><input type="number" name="land_departures" class="ni" min="0" placeholder="0" value="{{ old('land_departures') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Sea Border Arrivals</label><input type="number" name="sea_arrivals" class="ni" min="0" placeholder="0" value="{{ old('sea_arrivals') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Sea Border Departures</label><input type="number" name="sea_departures" class="ni" min="0" placeholder="0" value="{{ old('sea_departures') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Passengers/Crew Processed</label><input type="number" name="passenger_crew_count" class="ni" min="0" placeholder="0" value="{{ old('passenger_crew_count') }}"></div>
    </div>
</div>

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Ordinance and Deportation Activities
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Serviceable Arms</label><input type="number" name="arms_serviceable" class="ni" min="0" placeholder="0" value="{{ old('arms_serviceable') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Unserviceable Arms</label><input type="number" name="arms_unserviceable" class="ni" min="0" placeholder="0" value="{{ old('arms_unserviceable') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Ammunition Rounds Used</label><input type="number" name="ammunition_rounds_used" class="ni" min="0" placeholder="0" value="{{ old('ammunition_rounds_used') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Migrants Deported from Nigeria</label><input type="number" name="migrants_deported" class="ni" min="0" placeholder="0" value="{{ old('migrants_deported') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Migrants Repatriated</label><input type="number" name="migrants_repatriated" class="ni" min="0" placeholder="0" value="{{ old('migrants_repatriated') }}"></div>
    </div>
</div>

@endsection
