@include('partials.header')

<main class="redas-content">
    {{-- Styles shared by every directorate form: tab bar, action buttons and preview tables.
         The .hrm-* class names are kept as shared hooks used by the directorate views and the
         global script at the bottom of this layout. --}}
    <style>
    .hrm-tabs-wrap { background:#f8fafc; border-bottom:1px solid var(--gray-200); padding:10px 0; margin-bottom:14px; position:sticky; top:var(--topbar-height); z-index:30; }
    .hrm-actions { display:flex; justify-content:space-between; align-items:center; gap:10px; padding:14px 0; margin-top:10px; border-top:1px solid var(--gray-200); position:sticky; bottom:0; background:#fff; z-index:20; }
    .hrm-actions-center { display:flex; gap:10px; }
    .hrm-preview-table { width:100%; border-collapse:collapse; font-size:.82rem; margin-bottom:14px; }
    .hrm-preview-table th, .hrm-preview-table td { border:1px solid var(--gray-200); padding:6px 8px; text-align:left; }
    .hrm-preview-table th { background:#f8fafc; font-weight:700; }
    .hrm-preview-section-title { font-size:.9rem; font-weight:700; margin:18px 0 8px; color:var(--nis-700); border-bottom:1px solid var(--gray-200); padding-bottom:4px; }
    </style>

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
        <a href="{{ route('user.directorates.home') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        @endif
    </div>

    @if (session('status'))
        <div style="background:#ecfdf5;border:1px solid #86efac;color:#166534;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
            <i class="fas fa-check-circle" style="margin-right:6px;"></i>{{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
            <div style="font-weight:700;"><i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>The return could not be submitted. Please fix the following:</div>
            <ul style="margin:6px 0 0 22px;font-size:.84rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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
                    <a href="{{ url('/privacy') }}" target="_blank" style="color:#1d4ed8;text-decoration:underline;">Read the Privacy Policy</a>.
                </div>
            </div>
        </div>
    </div>

    {{-- Tab bar: directorate views with their own tab system declare
         @section('directorate-tabs', '1'); all other forms get a simple Form / Preview bar. --}}
    @hasSection('directorate-tabs')
        {{-- Child view renders its own tab bar inside its section. --}}
    @else
    <div class="hrm-tabs-wrap">
        <div class="entry-tabs-wrap" style="margin-bottom:0;">
            <div class="entry-tabs" id="entryTabs">
                <button type="button" class="entry-tab active" data-tab="form">
                    <span class="tab-dot"></span>
                    <i class="fas fa-edit" style="font-size:.78rem;"></i>
                    1. Form
                </button>
                <button type="button" class="entry-tab" data-tab="preview">
                    <span class="tab-dot"></span>
                    <i class="fas fa-eye" style="font-size:.78rem;"></i>
                    2. Preview
                </button>
            </div>
        </div>
    </div>
    @endif

    <form method="POST" enctype="multipart/form-data" action="{{ isset($editing) ? route('user.directorates.submissions.update', $editing) : route('user.directorates.store', $slug) }}">
        @csrf
        @isset($editing)
            @method('PUT')
        @endisset

        @hasSection('directorate-tabs')
            {{-- Child view renders its own tab panels; cards stay always visible. --}}
            @include('user.directorates._layout-form-body')
        @else
        {{-- Simple forms: everything lives in the Form tab, Preview is added globally below. --}}
        <div class="tab-panel active" id="tab-form">
            @include('user.directorates._layout-form-body')

            <div class="hrm-actions">
                <span></span>
                <div class="hrm-actions-center">
                    <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
                </div>
                <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next: Preview <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>
        @endif
    </form>

    {{-- Preview panel + action buttons, global for all directorate forms.
         Views with a fully self-contained preview declare @section('directorate-preview', '1'). --}}
    @hasSection('directorate-preview')
        {{-- Child view renders its own preview panel and actions. --}}
    @else
    <div class="tab-panel" id="tab-preview">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#f0fdf4;color:#15803d;"><i class="fas fa-eye"></i></div>
                    Preview
                </div>
            </div>
            <div class="card-body">
                <p style="font-size:.82rem;color:var(--gray-600);margin-bottom:14px;">Review the totals below before submitting. Use “Previous” to make changes.</p>
                <div id="hrmPreviewBody"></div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="submit" class="btn-nis btn-primary-nis hrm-submit-return-btn"><i class="fas fa-paper-plane"></i> Submit Return</button>
        </div>
    </div>
    @endif

    {{-- Global wiring for the Preview tab and action buttons of every directorate form --}}
    <script>
    (function () {
        var form = document.querySelector('main form') || document.querySelector('form[action*="directorates"]');
        var tabs = Array.from(document.querySelectorAll('.entry-tab'));
        var tabIds = tabs.map(function (t) { return t.dataset.tab; }).filter(Boolean);

        function goToTab(id) {
            var tab = document.querySelector('.entry-tab[data-tab="' + id + '"]');
            if (tab) tab.click();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        function currentTabId() {
            var active = document.querySelector('.tab-panel.active');
            return active ? active.id.replace('tab-', '') : null;
        }
        function nextTab() {
            var idx = tabIds.indexOf(currentTabId());
            if (idx >= 0 && idx < tabIds.length - 1) goToTab(tabIds[idx + 1]);
        }
        function prevTab() {
            var idx = tabIds.indexOf(currentTabId());
            if (idx > 0) goToTab(tabIds[idx - 1]);
        }

        function bindOnce(selector, handler) {
            document.querySelectorAll(selector).forEach(function (b) {
                if (b.dataset.redasBound === 'true') return;
                b.dataset.redasBound = 'true';
                b.addEventListener('click', handler);
            });
        }

        bindOnce('.hrm-next-btn', nextTab);
        bindOnce('.hrm-prev-btn', prevTab);
        bindOnce('.border-next-btn', nextTab);
        bindOnce('.border-prev-btn', prevTab);
        bindOnce('.hrm-edit-btn', function () { if (tabIds.length) goToTab(tabIds[0]); });
        bindOnce('.border-edit-btn', function () { if (tabIds.length) goToTab(tabIds[0]); });

        /* Draft save/restore, keyed per directorate + report period */
        var slug = @json($slug ?? 'directorate');
        function draftKey() {
            return 'redas_' + slug + '_draft_' + (document.querySelector('[name="report_period"]')?.value || 'default');
        }
        function saveDraft() {
            if (!form) return;
            var data = {};
            new FormData(form).forEach(function (v, k) { data[k] = v; });
            try { localStorage.setItem(draftKey(), JSON.stringify(data)); } catch (e) {}
            alert('Draft saved successfully.');
        }
        bindOnce('.hrm-save-draft-btn', saveDraft);
        bindOnce('.border-save-draft-btn', saveDraft);

        function loadDraft() {
            try {
                var saved = localStorage.getItem(draftKey());
                if (!saved || !form) return;
                var data = JSON.parse(saved);
                Object.keys(data).forEach(function (k) {
                    var el;
                    try { el = form.querySelector('[name="' + CSS.escape(k) + '"]'); } catch (e) { el = null; }
                    if (!el || el.readOnly || el.type === 'file') return;
                    if (el.type === 'checkbox' || el.type === 'radio') el.checked = data[k] === '1' || data[k] === 'on' || data[k] === true;
                    else el.value = data[k];
                });
            } catch (e) {}
        }

        /* Generic preview: summarise every labelled field, grouped by card.
           A view may define window.buildDirectoratePreview() to render its own preview. */
        function esc(s) {
            return String(s).replace(/[&<>"']/g, function (c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }
        function buildGenericPreview() {
            var container = document.getElementById('hrmPreviewBody');
            if (!container || !form) return;
            var html = '';
            form.querySelectorAll('.redas-card').forEach(function (card) {
                var rows = [];
                card.querySelectorAll('label').forEach(function (label) {
                    var field = label.parentElement.querySelector('input, textarea, select');
                    if (!field || !field.name || field.type === 'hidden') return;
                    var value;
                    if (field.type === 'checkbox' || field.type === 'radio') value = field.checked ? 'Yes' : 'No';
                    else value = field.value !== '' ? field.value : '—';
                    rows.push([label.textContent.trim(), value]);
                });
                if (!rows.length) return;
                var title = card.querySelector('.card-head-title');
                html += '<div class="hrm-preview-section-title">' + esc(title ? title.textContent.trim() : 'Section') + '</div>';
                html += '<table class="hrm-preview-table"><thead><tr><th>Item</th><th>Value</th></tr></thead><tbody>' +
                    rows.map(function (r) { return '<tr><td>' + esc(r[0]) + '</td><td>' + esc(r[1]) + '</td></tr>'; }).join('') +
                    '</tbody></table>';
            });
            container.innerHTML = html || '<p style="color:var(--gray-500);">No data entered yet.</p>';
        }

        /* (Re)build the preview every time the Preview tab is opened */
        document.querySelectorAll('.entry-tab[data-tab="preview"]').forEach(function (tab) {
            tab.addEventListener('click', function () {
                setTimeout(function () {
                    if (typeof window.buildDirectoratePreview === 'function') window.buildDirectoratePreview();
                    else buildGenericPreview();
                }, 0);
            });
        });

        /* Submit from preview */
        function submitReturn() {
            if (!form) return;
            if (!window.confirm('Are you sure you want to submit this return?\n\nPlease verify all information before continuing.')) return;
            /* Hidden tabs contain the required fields (report period, consent).
               Browsers block submission silently when an invalid control is not
               focusable, so validate first and return to the Form tab to show
               the validation message if something is missing. */
            if (!form.checkValidity()) {
                if (tabIds.length) goToTab(tabIds[0]);
                form.reportValidity();
                return;
            }
            if (form.requestSubmit) form.requestSubmit();
            else form.submit();
        }
        bindOnce('.hrm-submit-return-btn', submitReturn);
        bindOnce('.border-submit-return-btn', submitReturn);

        /* Restore any saved draft, then let page scripts recompute totals from the values. */
        loadDraft();
        if (form) form.dispatchEvent(new Event('input', { bubbles: true }));
    })();
    </script>

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
