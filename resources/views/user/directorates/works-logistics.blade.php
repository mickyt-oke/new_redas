@extends('user.directorates._layout')

@section('directorate-sections')

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Store and Warehouse
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Store Items Received</label><input type="number" name="store_items_received" class="ni" min="0" placeholder="0" value="{{ old('store_items_received') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Store Items Issued</label><input type="number" name="store_items_issued" class="ni" min="0" placeholder="0" value="{{ old('store_items_issued') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Stationery Balance</label><input type="number" name="stationery_balance" class="ni" min="0" placeholder="0" value="{{ old('stationery_balance') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Oil and Gas Balance</label><input type="number" name="oil_gas_balance" class="ni" min="0" placeholder="0" value="{{ old('oil_gas_balance') }}"></div>
    </div>
</div>

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Transport and Maintenance
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Operational Fleet Vehicles</label><input type="number" name="fleet_operational" class="ni" min="0" placeholder="0" value="{{ old('fleet_operational') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Fleet Under Repair</label><input type="number" name="fleet_under_repair" class="ni" min="0" placeholder="0" value="{{ old('fleet_under_repair') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Projects Awarded</label><input type="number" name="projects_awarded" class="ni" min="0" placeholder="0" value="{{ old('projects_awarded') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Projects Completed</label><input type="number" name="projects_completed" class="ni" min="0" placeholder="0" value="{{ old('projects_completed') }}"></div>
        <div style="grid-column:1 / -1;"><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Other Notable Activities</label><textarea name="notable_activities" class="ni" rows="3" placeholder="Provide details...">{{ old('notable_activities') }}</textarea></div>
    </div>
</div>

@endsection
