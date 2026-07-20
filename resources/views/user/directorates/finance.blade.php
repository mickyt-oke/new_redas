@extends('user.directorates._layout')

@section('directorate-sections')

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Revenue Performance Report
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Passport Revenue (Local)</label><input type="number" name="local_revenue_passport" class="ni" min="0" placeholder="0" value="{{ old('local_revenue_passport') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Visa Revenue (Local)</label><input type="number" name="local_revenue_visa" class="ni" min="0" placeholder="0" value="{{ old('local_revenue_visa') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">E-Pass Revenue (Local)</label><input type="number" name="local_revenue_e_pass" class="ni" min="0" placeholder="0" value="{{ old('local_revenue_e_pass') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Other Revenue (IGR)</label><input type="number" name="local_revenue_other" class="ni" min="0" placeholder="0" value="{{ old('local_revenue_other') }}"></div>
    </div>
</div>

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Expenditure and Budget
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Foreign Revenue Total</label><input type="number" name="foreign_revenue_total" class="ni" min="0" placeholder="0" value="{{ old('foreign_revenue_total') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Expenditure Profile Total</label><input type="number" name="expenditure_profile_total" class="ni" min="0" placeholder="0" value="{{ old('expenditure_profile_total') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Amount Budgeted</label><input type="number" name="budget_amount" class="ni" min="0" placeholder="0" value="{{ old('budget_amount') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Amount Released</label><input type="number" name="release_amount" class="ni" min="0" placeholder="0" value="{{ old('release_amount') }}"></div>
        <div style="grid-column:1 / -1;"><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Challenges</label><textarea name="finance_challenges" class="ni" rows="3" placeholder="Provide details...">{{ old('finance_challenges') }}</textarea></div>
    </div>
</div>

@endsection
