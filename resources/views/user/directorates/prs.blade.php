@extends('user.directorates._layout')

@section('directorate-sections')


<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Research and Statistics
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">No. of Research Topics</label><input type="number" name="research_topics" class="ni" min="0" placeholder="0" value="{{ old('research_topics') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Completed Research Studies</label><input type="number" name="research_completed" class="ni" min="0" placeholder="0" value="{{ old('research_completed') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Research Cost Implication (NGN)</label><input type="number" name="cost_implication" class="ni" min="0" placeholder="0" value="{{ old('cost_implication') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Nominal Roll Updates</label><input type="number" name="nominal_roll_updates" class="ni" min="0" placeholder="0" value="{{ old('nominal_roll_updates') }}"></div>
    </div>
</div>

<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                <i class="fas fa-list-check"></i>
            </div>
            Monitoring and Evaluation
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Personnel Reports Reviewed</label><input type="number" name="personnel_reports" class="ni" min="0" placeholder="0" value="{{ old('personnel_reports') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Financial Reports Reviewed</label><input type="number" name="financial_reports" class="ni" min="0" placeholder="0" value="{{ old('financial_reports') }}"></div>
        <div><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Capital Expenditure Reviews</label><input type="number" name="capital_expenditure_reviews" class="ni" min="0" placeholder="0" value="{{ old('capital_expenditure_reviews') }}"></div>
        <div style="grid-column:1 / -1;"><label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Recommendations / Way Forward</label><textarea name="prs_recommendations" class="ni" rows="3" placeholder="Provide details...">{{ old('prs_recommendations') }}</textarea></div>
    </div>
</div>

@endsection
