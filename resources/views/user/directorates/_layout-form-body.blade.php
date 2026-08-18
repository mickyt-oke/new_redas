{{-- Shared body of every directorate return form: report metadata, the
     directorate-specific sections and the declaration. Rendered inside the
     layout's <form>, either directly (tabbed views) or inside #tab-form. --}}
<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:var(--gold-100);color:var(--gold-600);">
                <i class="fas fa-file-signature"></i>
            </div>
            Monthly Return
        </div>
    </div>
    <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
        <div>
            <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Report Period</label>
            <input type="month" class="ni" name="report_period" required value="{{ old('report_period', $editing->return_data['report_period'] ?? now()->format('Y-m')) }}">
        </div>
        <div>
            <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Reporting Officer</label>
            <input type="text" class="ni" name="reporting_officer" required value="{{ old('reporting_officer', $editing->return_data['reporting_officer'] ?? auth()->user()->name) }}" autocomplete="off" readonly>
        </div>
        <div>
            <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Directorate</label>
            <input type="text" class="ni" value="{{ $directorateName ?? 'Directorate' }}" readonly>
        </div>
    </div>
</div>

@yield('directorate-sections')

@hasSection('directorate-declaration')
    {{-- Child view renders its own declaration card inside its sections. --}}
@else
<div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#f0fdf4;color:#15803d;">
                <i class="fas fa-user-check"></i>
            </div>
            Declaration &amp; Consent
        </div>
    </div>
    <div class="card-body">
        <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;font-size:.84rem;color:var(--gray-700);">
            <input type="checkbox" name="data_consent" value="1" required style="accent-color:var(--nis-600);margin-top:2px;">
            <span>
                I confirm that the information provided is accurate, limited to what is necessary for official NIS reporting,
                and that I have authority to submit it. I understand that this data will be processed and retained in accordance with
                the <a href="{{ route('privacy') }}" target="_blank" style="color:#1d4ed8;text-decoration:underline;">Privacy Policy</a>.
            </span>
        </label>
    </div>
</div>
@endif
