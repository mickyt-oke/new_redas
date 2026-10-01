@include('partials.header')

<main class="redas-content">
    <style>
    .state-dir-tabs-wrap { background:#f8fafc; border:1px solid var(--gray-200); border-radius:var(--radius-md); padding:8px; margin-bottom:16px; position:sticky; top:var(--topbar-height); z-index:30; }
    .state-dir-tabs { display:flex; gap:6px; overflow-x:auto; }
    .state-dir-tab { display:inline-flex; align-items:center; gap:6px; padding:8px 12px; border:1px solid transparent; border-radius:var(--radius-sm); background:transparent; color:var(--gray-600); font-size:.78rem; font-weight:600; cursor:pointer; white-space:nowrap; }
    .state-dir-tab:hover { background:var(--nis-50); color:var(--nis-700); }
    .state-dir-tab.active { background:var(--nis-700); color:#fff; }
    .state-dir-panel { display:none; }
    .state-dir-panel.active { display:block; }
    .state-return-actions { display:flex; justify-content:space-between; align-items:center; gap:10px; padding:14px 0; margin-top:10px; border-top:1px solid var(--gray-200); position:sticky; bottom:0; background:#fff; z-index:20; }
    .state-return-actions-right { display:flex; gap:10px; }
    </style>

    <div class="page-header" style="margin-bottom:18px;">
        <div>
            <h1 class="page-title" style="margin-bottom:4px;">
                <i class="fas fa-building-columns" style="margin-right:8px;"></i>
                {{ isset($editing) ? 'Edit State Command Return' : 'State Command Return' }}
            </h1>
            <p class="page-subtitle">{{ isset($editing) ? 'Update the sections below and resubmit for review.' : 'Complete all applicable sections and submit for review.' }}</p>
        </div>
        <a href="{{ route('user.dashboard') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    @if (session('status'))
        <div style="background:#ecfdf5;border:1px solid #86efac;color:#166534;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
            <i class="fas fa-check-circle" style="margin-right:6px;"></i>{{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
            <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>{{ session('error') }}
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

    @php
        $stateDirectorates = \App\Http\Controllers\Web\DashboardController::directorates();
        $editData = isset($editing) && is_array($editing->return_data) ? $editing->return_data : [];
        $stateUser = auth()->user();
        $stateCode = $stateUser?->primary_location_code ?: $stateUser?->assigned_state_code;
        $resolvedCommand = $stateCode
            ? (\App\Services\GeolocationService::stateNameFromCode((string) $stateCode) ?? strtoupper((string) $stateCode))
            : '';
        $commandValue = old('command_name', $editData['command_name'] ?? $resolvedCommand);
        $periodValue = old('period', $editData['period'] ?? $editData['report_period'] ?? now()->format('Y-m'));
        $selectedType = old('return_type', $editData['return_type'] ?? request('type', 'monthly'));
        $officerValue = old('reporting_officer', $editData['reporting_officer'] ?? ($stateUser?->name ?? ''));
    @endphp

    <form id="stateReturnForm" method="POST" enctype="multipart/form-data" action="{{ isset($editing) ? route('user.returns.update', $editing) : route('user.returns.store') }}">
        @csrf
        @isset($editing)
            @method('PUT')
        @endisset

        {{-- Header card (always visible) --}}
        <div class="nis-section" style="margin-bottom:16px;">
            <div class="nis-section-head">
                <span class="sec-num">A</span>
                Nigeria Immigration Service — State Command Monthly Reporting Template
            </div>
            <div class="nis-section-body">
                <div class="workflow-path" style="margin-bottom:14px;">
                    <span class="workflow-step you"><i class="fas fa-user-edit" style="font-size:.68rem;"></i> You</span>
                    <span class="workflow-arrow"><i class="fas fa-arrow-right"></i></span>
                    <span class="workflow-step"><i class="fas fa-user-tie" style="font-size:.68rem;"></i> Desk Admin</span>
                    <span class="workflow-arrow"><i class="fas fa-arrow-right"></i></span>
                    <span class="workflow-step"><i class="fas fa-user-shield" style="font-size:.68rem;"></i> Zonal Commander</span>
                    <span class="workflow-arrow"><i class="fas fa-arrow-right"></i></span>
                    <span class="workflow-step" style="color:var(--gray-400);"><i class="fas fa-user-cog" style="font-size:.68rem;"></i> HQ Admin</span>
                </div>
                <div class="form-grid-3" style="align-items:end;">
                    <div class="fg">
                        <label>Command</label>
                        <input type="text" name="command_name" class="ni" value="{{ $commandValue }}" readonly>
                    </div>
                    <div class="fg">
                        <label>Return Period</label>
                        <input type="month" name="period" class="ni" required value="{{ $periodValue }}">
                    </div>
                    <div class="fg">
                        <label>Return Type</label>
                        <select name="return_type" class="ni ni-select" required>
                            <option value="monthly" @selected($selectedType === 'monthly')>Monthly Return</option>
                            <option value="quarterly" @selected($selectedType === 'quarterly')>Quarterly Return</option>
                            <option value="biannual" @selected($selectedType === 'biannual')>Bi-Annual Return</option>
                            <option value="annual" @selected($selectedType === 'annual')>Annual Return</option>
                            <option value="special" @selected($selectedType === 'special')>Special Report</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label>Reporting Officer</label>
                        <input type="text" name="reporting_officer" class="ni" value="{{ $officerValue }}" readonly>
                    </div>
                </div>
                <input type="hidden" name="workflow_path" value="zonal">
                <input type="hidden" name="status" value="pending">
            </div>
        </div>

        {{-- Top-level directorate tab bar --}}
        <div class="state-dir-tabs-wrap">
            <div class="state-dir-tabs" id="stateDirTabs">
                @foreach($stateDirectorates as $slug => $meta)
                <button type="button" class="state-dir-tab {{ $loop->first ? 'active' : '' }}" data-dir="{{ $slug }}">
                    <i class="{{ $meta['icon'] }}" style="font-size:.78rem;"></i>
                    {{ $meta['name'] }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Directorate panels --}}
        @foreach($stateDirectorates as $slug => $meta)
        <div class="state-dir-panel {{ $loop->first ? 'active' : '' }}" id="dir-{{ $slug }}">
            @include("user.states.sections.{$slug}", ['stateEmbedded' => true])
        </div>
        @endforeach

        {{-- Declaration card (always visible) --}}
        <div class="nis-section" style="margin-top:16px;" id="stateDeclaration">
            <div class="nis-section-head"><span class="sec-num">§D</span> Declaration &amp; Attachments</div>
            <div class="nis-section-body">
                <div class="fg" style="margin-bottom:8px;">
                    <label><i class="fas fa-paperclip"></i> Attachments <span style="font-weight:400;color:var(--gray-400);text-transform:none;">(PDF, Word, images — max 20MB each)</span></label>
                    <input type="file" name="attachments[]" id="stateAttachInput" class="ni" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    <p style="font-size:.74rem;color:var(--gray-400);margin-top:4px;">Nominal roll, pictures, supporting documents.</p>
                </div>
                <div style="background:var(--gray-50);border:1px solid var(--gray-200);border-radius:var(--radius-md);padding:16px;">
                    <div class="nis-subsection-title" style="border:none;margin-bottom:8px;"><i class="fas fa-pen-nib"></i> Reporting Officer Declaration</div>
                    <p style="font-size:.76rem;color:var(--gray-500);margin-top:0;margin-bottom:10px;"><i class="fas fa-lock" style="color:var(--nis-600);"></i> By submitting this return, I certify that the information provided is accurate and complete to the best of my knowledge, in compliance with NIS reporting standards.</p>
                    <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;font-size:.84rem;color:var(--gray-700);">
                        <input type="checkbox" name="data_consent" value="1" required style="accent-color:var(--nis-600);margin-top:2px;" @checked(old('data_consent'))>
                        <span>
                            I confirm that the data provided is limited to what is necessary for official NIS reporting, and that I have authority to submit it.
                            I understand the data will be processed and retained in accordance with the <a href="{{ route('privacy') }}" target="_blank" style="color:#1d4ed8;text-decoration:underline;">Privacy Policy</a>.
                        </span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Action bar --}}
        <div class="state-return-actions">
            <span id="stateDraftStatus" style="font-size:.74rem;color:var(--gray-400);"></span>
            <div class="state-return-actions-right">
                <button type="button" class="btn-nis btn-outline-nis" id="stateSaveDraftBtn"><i class="fas fa-save"></i> Save Draft</button>
                <button type="button" class="btn-nis btn-ghost" id="statePreviewBtn" style="background:var(--gold-50);border:1px solid var(--gold-400);color:var(--gold-600);"><i class="fas fa-eye"></i> Preview</button>
                <button type="submit" class="btn-nis btn-primary-nis" id="stateSubmitBtn"><i class="fas fa-paper-plane"></i> {{ isset($editing) ? 'Update & Resubmit' : 'Submit Return' }}</button>
            </div>
        </div>
    </form>

    <script>
    (function () {
        var form = document.getElementById('stateReturnForm');
        if (!form) return;

        /* Top-level directorate tabs — scoped to this page */
        var dirTabs = Array.from(document.querySelectorAll('.state-dir-tab'));
        var dirPanels = Array.from(document.querySelectorAll('.state-dir-panel'));
        dirTabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                dirTabs.forEach(function (t) { t.classList.remove('active'); });
                dirPanels.forEach(function (p) { p.classList.remove('active'); });
                tab.classList.add('active');
                var panel = document.getElementById('dir-' + tab.dataset.dir);
                if (panel) panel.classList.add('active');
                var tabsBar = document.getElementById('stateDirTabs');
                if (tabsBar) tabsBar.scrollLeft = tab.offsetLeft - 60;
            });
        });

        /* Inner section tabs — delegated, scoped to the containing directorate panel */
        document.addEventListener('click', function (e) {
            var tab = e.target.closest ? e.target.closest('.entry-tab') : null;
            if (!tab) return;
            var root = tab.closest('.state-dir-panel');
            if (!root) return;
            root.querySelectorAll('.entry-tab').forEach(function (t) { t.classList.remove('active'); });
            root.querySelectorAll('.tab-panel').forEach(function (p) { p.classList.remove('active'); });
            tab.classList.add('active');
            var panel = root.querySelector('#tab-' + tab.dataset.tab);
            if (panel) panel.classList.add('active');
        });

        /* Draft save/restore, single key for the combined state return */
        var DRAFT_KEY = 'redas_state_draft';
        function saveDraft() {
            var data = {};
            new FormData(form).forEach(function (v, k) { data[k] = v; });
            data.__saved_at = new Date().toISOString();
            try { localStorage.setItem(DRAFT_KEY, JSON.stringify(data)); } catch (e) {}
            var status = document.getElementById('stateDraftStatus');
            if (status) status.textContent = 'Draft saved at ' + new Date().toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
            alert('Draft saved successfully.');
        }
        var saveBtn = document.getElementById('stateSaveDraftBtn');
        if (saveBtn) saveBtn.addEventListener('click', saveDraft);

        /* Restore-with-rows: drafts and edited submissions may contain more
           dynamic rows than the form renders initially. For a missing field
           whose name carries a row index (e.g. ict[incidents][hardware][4][cause]),
           click the tbody's add-row button (mapped via data-row-target/data-target)
           until the row exists; for pages that key new rows with generated tokens
           instead of numbers, rename the fresh row to the saved key. */
        function makeFieldEnsurer() {
            var ensured = {};
            function queryField(name) {
                try { return form.querySelector('[name="' + CSS.escape(name) + '"]'); } catch (e) { return null; }
            }
            function findTemplate(prefix, suffix) {
                var fields = form.querySelectorAll('input, textarea, select');
                for (var i = 0; i < fields.length; i++) {
                    var n = fields[i].name;
                    if (n && n.indexOf(prefix + '[') === 0 && n.slice(-suffix.length) === suffix) return fields[i];
                }
                return null;
            }
            function renameRow(row, prefix, rowKey) {
                row.querySelectorAll('input, textarea, select').forEach(function (f) {
                    if (!f.name) return;
                    var fm = f.name.match(/^(.*)\[[^\]]+\](\[[^\]]+\])$/);
                    if (fm && fm[1] === prefix) f.name = rowKey + fm[2];
                });
            }
            /* Rename the row created by the last button click when the page
               keys new rows with generated tokens rather than numbers. */
            function renameNewTokenRow(tbody, beforeNames, prefix, rowKey) {
                var created = null;
                tbody.querySelectorAll('input, textarea, select').forEach(function (f) {
                    if (created || !f.name || beforeNames[f.name]) return;
                    var fm = f.name.match(/^(.*)\[([^\]]+)\]\[[^\]]+\]$/);
                    if (fm && fm[1] === prefix && !/^\d+$/.test(fm[2])) created = f.closest('tr');
                });
                if (created) renameRow(created, prefix, rowKey);
                return created;
            }
            return function ensureField(name) {
                if (!form) return null;
                var el = queryField(name);
                if (el) return el;
                var m = name.match(/^(.*)\[([^\]]+)\](\[[^\]]+\])$/);
                if (!m) return null;
                var prefix = m[1], idx = m[2], suffix = m[3];
                var rowKey = prefix + '[' + idx + ']';
                if (ensured[rowKey]) return queryField(name);
                var template = findTemplate(prefix, suffix);
                var tbody = template ? template.closest('tbody') : null;
                if (!tbody || !tbody.id) {
                    /* Dynamic tables that start empty render no template field;
                       fall back to the add-row button's declared field prefix. */
                    var prefixBtn = null;
                    try { prefixBtn = document.querySelector('[data-row-prefix="' + CSS.escape(prefix) + '"]'); } catch (e) {}
                    if (prefixBtn) tbody = document.getElementById(prefixBtn.getAttribute('data-row-target') || prefixBtn.getAttribute('data-target'));
                }
                if (!tbody || !tbody.id) return null;
                var btn = document.querySelector('[data-row-target="' + tbody.id + '"], [data-target="' + tbody.id + '"]');
                if (!btn) return null;
                ensured[rowKey] = true;
                var guard = 0;
                while (!el && guard++ < 100) {
                    var rowsBefore = tbody.querySelectorAll('tr').length;
                    var beforeNames = {};
                    tbody.querySelectorAll('[name]').forEach(function (f) { beforeNames[f.name] = true; });
                    btn.click();
                    el = queryField(name);
                    if (el) break;
                    if (tbody.querySelectorAll('tr').length === rowsBefore) break;
                    if (renameNewTokenRow(tbody, beforeNames, prefix, rowKey)) {
                        el = queryField(name);
                        break;
                    }
                    if (!/^\d+$/.test(idx)) break;
                }
                return el;
            };
        }
        window.redasMakeFieldEnsurer = makeFieldEnsurer;

        function loadDraft() {
            try {
                var saved = localStorage.getItem(DRAFT_KEY);
                if (!saved) return;
                var data = JSON.parse(saved);
                var ensureField = makeFieldEnsurer();
                Object.keys(data).forEach(function (k) {
                    var el = ensureField(k);
                    if (!el || el.readOnly || el.type === 'file') return;
                    if (el.type === 'checkbox' || el.type === 'radio') el.checked = data[k] === '1' || data[k] === 'on' || data[k] === true;
                    else el.value = data[k];
                });
            } catch (e) {}
        }

        /* Preview: post the current form to the read-only preview endpoint in a
           new tab, without file inputs, then restore the form untouched. */
        var previewBtn = document.getElementById('statePreviewBtn');
        if (previewBtn) {
            previewBtn.addEventListener('click', function () {
                var fileInputs = Array.from(form.querySelectorAll('input[type="file"]'));
                var methodInput = form.querySelector('input[name="_method"]');
                var originalAction = form.getAttribute('action');
                var originalTarget = form.getAttribute('target');
                fileInputs.forEach(function (f) { f.disabled = true; });
                if (methodInput) methodInput.disabled = true;
                form.setAttribute('action', @json(route('user.returns.preview')));
                form.setAttribute('target', '_blank');
                form.submit();
                form.setAttribute('action', originalAction);
                if (originalTarget) form.setAttribute('target', originalTarget);
                else form.removeAttribute('target');
                if (methodInput) methodInput.disabled = false;
                fileInputs.forEach(function (f) { f.disabled = false; });
            });
        }

        /* Restore any saved draft, then let section scripts recompute totals. */
        loadDraft();
        form.dispatchEvent(new Event('input', { bubbles: true }));
    })();
    </script>

    @if (session('status'))
    <script>
        /* The return was submitted successfully: clear the saved draft. */
        (function () {
            try { localStorage.removeItem('redas_state_draft'); } catch (e) {}
        })();
    </script>
    @endif

    @isset($editing)
    <script>
        /* Edit mode: restore the previously submitted values into the form.
           Fields the user just re-typed (flashed old input after a failed
           validation) take precedence and are not overwritten. */
        window.REDAS_EDITING = true;
        (function () {
            var REDAS_PREFILL = @json(is_array($editing->return_data) ? $editing->return_data : []);
            var OLD_KEYS = @json(array_map('strval', array_keys(session()->getOldInput())));
            var form = document.getElementById('stateReturnForm');
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

            // Recreate any extra dynamic rows saved with the submission before filling.
            var ensureField = window.redasMakeFieldEnsurer ? window.redasMakeFieldEnsurer() : null;

            entries.forEach(function (pair) {
                if (OLD_KEYS.indexOf(pair[0]) !== -1) { return; }
                var el = null;
                if (ensureField) {
                    el = ensureField(pair[0]);
                } else {
                    try {
                        el = form.querySelector('[name="' + CSS.escape(pair[0]) + '"]');
                    } catch (e) {
                        el = null;
                    }
                }
                if (!el || el.readOnly || el.type === 'file') { return; }
                if (el.type === 'checkbox' || el.type === 'radio') {
                    el.checked = !!pair[1] && pair[1] !== '0';
                } else {
                    el.value = pair[1];
                }
            });

            // Let each section's recompute scripts refresh totals from the restored values.
            form.dispatchEvent(new Event('input', { bubbles: true }));
        })();
    </script>
    @endisset
</main>

<script>window.REDAS_SCOPED_TABS = true;</script>
@include('partials.footer')
