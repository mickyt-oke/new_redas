@include('partials.header')

<main class="redas-content">
    <div class="page-header" style="margin-bottom:18px;">
        <div>
            <h1 class="page-title" style="margin-bottom:4px;">
                <i class="{{ $directorateIcon ?? 'fas fa-building-columns' }}" style="margin-right:8px;"></i>
                {{ $directorateName ?? 'Directorate' }} Return
            </h1>
        </div>
        @if(auth()->user()?->role === 'directorate')
        <a href="{{ route('user.directorates.dashboard') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        @elseif(auth()->user()?->user_category === 'directorate_admin')
        <a href="{{ route('user.directorates.home') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to Directorates
        </a>
        @else
        <a href="{{ route('user.dashboard') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        @endif
    </div>

    @if (session('status'))
        <div style="background:#ecfdf5;border:1px solid #86efac;color:#166534;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
            <i class="fas fa-check-circle" style="margin-right:6px;"></i>{{ session('status') }}
        </div>
    @endif

    {{-- GDPR / NDPR data protection notice --}}
    <div class="redas-card" style="margin-bottom:16px;border-left:4px solid #1d4ed8;">
        <div class="card-body" style="font-size:.82rem;color:var(--gray-600);">
            <div style="display:flex;align-items:flex-start;gap:12px;">
                <i class="fas fa-shield-alt" style="color:#1d4ed8;font-size:1.1rem;margin-top:2px;"></i>
                <div>
                    <strong style="color:#1e3a8a;display:block;margin-bottom:4px;">Data Protection Notice</strong>
                    The information submitted on this form is processed for official records for the Service.
                    Only data that is adequate, relevant, and limited to what is necessary should be entered.
                    Personal data will be retained in accordance with NIS archival policy and applicable data-protection law.
                    <a href="{{ route('privacy') }}" target="_blank" style="color:#1d4ed8;text-decoration:underline;">Read the Privacy Policy</a>.
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ isset($editing) ? route('user.directorates.submissions.update', $editing) : route('user.directorates.store', $slug) }}">
        @csrf
        @isset($editing)
            @method('PUT')
        @endisset

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

        {{-- <div class="redas-card">
            <div class="card-body" style="display:flex;justify-content:flex-end;gap:10px;">
                @if(auth()->user()?->role === 'directorate')
                <a href="{{ route('user.directorates.dashboard') }}" class="btn-nis btn-ghost">Cancel</a>
                @else
                <a href="{{ route('user.dashboard') }}" class="btn-nis btn-ghost">Cancel</a>
                @endif
                <button type="submit" class="btn-nis btn-primary-nis">
                    <i class="fas fa-paper-plane"></i> Submit {{ $directorateName ?? 'Directorate' }} Return
                </button>
            </div>
        </div> --}}
    </form>

    @isset($editing)
    <script>
        (function () {
            var REDAS_PREFILL = @json($editing->return_data ?? []);
            var form = document.querySelector('form[action*="directorates"]');
            if (!form || !REDAS_PREFILL) { return; }

            // Recursively flatten nested objects into bracket-notation field names,
            // e.g. staff[deputy-comptroller-general][male].
            var entries = [];
            (function flatten(prefix, value) {
                if (value === null || value === undefined) { return; }
                if (typeof value === 'object') {
                    Object.keys(value).forEach(function (key) {
                        flatten(prefix ? prefix + '[' + key + ']' : key, value[key]);
                    });
                } else {
                    entries.push([prefix, value]);
                }
            })('', REDAS_PREFILL);

            entries.forEach(function (pair) {
                var el;
                try {
                    el = form.querySelector('[name="' + CSS.escape(pair[0]) + '"]');
                } catch (e) {
                    el = null;
                }
                if (!el || el.readOnly || el.type === 'file') { return; }
                if (el.type === 'checkbox' || el.type === 'radio') {
                    el.checked = !!pair[1] && pair[1] !== '0';
                } else {
                    el.value = pair[1];
                }
            });

            // Let each page's recompute scripts refresh totals from the restored values.
            form.dispatchEvent(new Event('input', { bubbles: true }));
        })();
    </script>
    @endisset
</main>

@include('partials.footer')
