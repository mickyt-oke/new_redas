
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
        var slug = "ict";
        function draftKey() {
            return 'redas_' + slug + '_draft_' + (document.querySelector('[name="report_period"]')?.value || 'default');
        }
        function saveDraft() {
            if (!form) return;
            var data = {};
            new FormData(form).forEach(function (v, k) { data[k] = v; });
            data.__saved_at = new Date().toISOString();
            try { localStorage.setItem(draftKey(), JSON.stringify(data)); } catch (e) {}
            alert('Draft saved successfully.');
        }
        bindOnce('.hrm-save-draft-btn', saveDraft);
        bindOnce('.border-save-draft-btn', saveDraft);

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
                if (!template) return null;
                var tbody = template.closest('tbody');
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
                var saved = localStorage.getItem(draftKey());
                if (!saved || !form) return;
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

        /* Resume a specific draft period via ?draft_period=YYYY-MM (linked from
           the directorate dashboard's Recent Submissions list). */
        var requestedDraftPeriod = new URLSearchParams(window.location.search).get('draft_period');
        if (requestedDraftPeriod && /^\d{4}-\d{2}$/.test(requestedDraftPeriod)) {
            var periodField = form ? form.querySelector('[name="report_period"]') : null;
            if (periodField && !periodField.readOnly) periodField.value = requestedDraftPeriod;
        }

        /* Restore any saved draft, then let page scripts recompute totals from the values. */
        loadDraft();
        if (form) form.dispatchEvent(new Event('input', { bubbles: true }));
    })();
    