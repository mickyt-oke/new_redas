
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
            if (form.requestSubmit) form.requestSubmit();
            else form.submit();
        }
        bindOnce('.hrm-submit-return-btn', submitReturn);
        bindOnce('.border-submit-return-btn', submitReturn);

        /* Restore any saved draft, then let page scripts recompute totals from the values. */
        loadDraft();
        if (form) form.dispatchEvent(new Event('input', { bubbles: true }));
    })();
    
