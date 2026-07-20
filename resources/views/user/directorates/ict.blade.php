@extends('user.directorates._layout')

@section('directorate-sections')

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Project and System Activities
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Project/Programme Activities</label><input type="number" name="project_programme_activities" class="ni" min="0" placeholder="0" value="{{ old('project_programme_activities') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Systems Fully Operational</label><input type="number" name="systems_operational" class="ni" min="0" placeholder="0" value="{{ old('systems_operational') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">MIDAS Deployment Sites</label><input type="number" name="midas_sites_deployed" class="ni" min="0" placeholder="0" value="{{ old('midas_sites_deployed') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Software Deliverables</label><input type="number" name="software_delivered" class="ni" min="0" placeholder="0" value="{{ old('software_delivered') }}"></div>
    </div>
</div>

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Maintenance and Security
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Hardware Maintenance Cases</label><input type="number" name="hardware_maintenance_cases" class="ni" min="0" placeholder="0" value="{{ old('hardware_maintenance_cases') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Data Breach Incidents</label><input type="number" name="data_breach_incidents" class="ni" min="0" placeholder="0" value="{{ old('data_breach_incidents') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Cybersecurity Incidents</label><input type="number" name="cybersecurity_incidents" class="ni" min="0" placeholder="0" value="{{ old('cybersecurity_incidents') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Security Controls Deployed</label><input type="number" name="security_controls_deployed" class="ni" min="0" placeholder="0" value="{{ old('security_controls_deployed') }}"></div>
        <div style="grid-column:1 / -1;"><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Other Reports</label><textarea name="ict_other_reports" class="ni" rows="3" placeholder="Provide details...">{{ old('ict_other_reports') }}</textarea></div>
    </div>
</div>

@endsection
