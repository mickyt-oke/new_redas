
    (function () {
        /* ── Tab switching ── */
        const tabs = document.querySelectorAll('.entry-tab');
        const panels = document.querySelectorAll('.tab-panel');
        const tabsBar = document.getElementById('entryTabs');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                panels.forEach(p => p.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById('tab-' + tab.dataset.tab)?.classList.add('active');
                if (tabsBar) tabsBar.scrollLeft = tab.offsetLeft - 60;
            });
        });

        /* ── Sidebar/topbar standard toggles ── */
        const sidebar = document.getElementById('redasSidebar');
        const main = document.getElementById('redasMain');
        const overlay = document.getElementById('sidebarOverlay');
        const toggle = document.getElementById('sidebarToggle');
        if (toggle) {
            toggle.addEventListener('click', () => {
                if (window.innerWidth <= 768) { sidebar.classList.toggle('mobile-open'); overlay.classList.toggle('active'); }
                else { sidebar.classList.toggle('collapsed'); main.classList.toggle('sidebar-collapsed'); }
            });
        }
        overlay?.addEventListener('click', () => { sidebar.classList.remove('mobile-open'); overlay.classList.remove('active'); });

        /* ── Notification / user dropdowns ── */
        const notifBtn = document.getElementById('notifBtn');
        const notifPanel = document.getElementById('notifPanel');
        const userBtn = document.getElementById('userMenuBtn');
        const userDrop = document.getElementById('userMenuDrop');
        notifBtn?.addEventListener('click', e => { e.stopPropagation(); notifPanel.style.display = notifPanel.style.display === 'block' ? 'none' : 'block'; userDrop.style.display = 'none'; });
        userBtn?.addEventListener('click', e => { e.stopPropagation(); userDrop.style.display = userDrop.style.display === 'block' ? 'none' : 'block'; notifPanel.style.display = 'none'; });
        document.addEventListener('click', () => { notifPanel.style.display = 'none'; userDrop.style.display = 'none'; });

        /* ── Workflow path update based on command selection ── */
        const cmdSelect = document.getElementById('commandName');
        const nextStepEl = document.getElementById('nextStep');
        const pathInput = document.getElementById('workflowPath');
        const hqCommands = ['HRM Directorate', 'Finance & Accounts', 'Border Management', 'Migration Directorate', 'POTD Directorate',
            'Visa & Residency', 'PRS Directorate', 'Investigation & Compliance', 'ICT Directorate', 'Works & Logistics', 'NIS HQ Abuja'];

        function updateWorkflow() {
            const val = cmdSelect.value;
            const isHQ = hqCommands.some(c => val.includes(c.split(' ')[0]));
            pathInput.value = isHQ ? 'hq' : 'zonal';
            nextStepEl.innerHTML = isHQ
                ? '<i class="fas fa-user-tie" style="font-size:.68rem;"></i> HQ Coordinator'
                : '<i class="fas fa-user-tie" style="font-size:.68rem;"></i> Zonal Coordinator';
        }
        cmdSelect?.addEventListener('change', updateWorkflow);

        /* ── Staff total ── */
        document.querySelectorAll('.staff-input').forEach(inp => {
            inp.addEventListener('input', () => {
                const total = Array.from(document.querySelectorAll('.staff-input')).reduce((s, e) => s + (parseInt(e.value) || 0), 0);
                const t = document.getElementById('staffTotal');
                if (t) t.value = total;
            });
        });

        /* ── Arms subtotals ── */
        document.querySelectorAll('.arms-svc, .arms-unsvc').forEach(inp => {
            inp.addEventListener('input', () => {
                const g = inp.dataset.group;
                const sv = parseInt(document.querySelector(`.arms-svc[data-group="${g}"]`)?.value || 0);
                const un = parseInt(document.querySelector(`.arms-unsvc[data-group="${g}"]`)?.value || 0);
                const sub = document.getElementById(`arms-sub-${g}`);
                if (sub) sub.value = sv + un;
                let grand = 0;
                document.querySelectorAll('.arms-sub').forEach(s => grand += parseInt(s.value || 0));
                const gt = document.getElementById('armsGrandTotal');
                if (gt) gt.value = grand;
            });
        });

        /* ── Passport admin total ── */
        document.querySelectorAll('.ppt-admin-input').forEach(inp => {
            inp.addEventListener('input', () => {
                const t = Array.from(document.querySelectorAll('.ppt-admin-input')).reduce((s, e) => s + (parseInt(e.value) || 0), 0);
                const el = document.getElementById('pptAdminTotal');
                if (el) el.value = t;
            });
        });

        /* ── FTZ total ── */
        document.querySelectorAll('.ftz-input').forEach(inp => {
            inp.addEventListener('input', () => {
                const t = Array.from(document.querySelectorAll('.ftz-input')).reduce((s, e) => s + (parseInt(e.value) || 0), 0);
                const el = document.getElementById('ftzTotal');
                if (el) el.value = t;
            });
        });

        /* ── Revenue total ── */
        document.querySelectorAll('.rev-input').forEach(inp => {
            inp.addEventListener('input', () => {
                const t = Array.from(document.querySelectorAll('.rev-input')).reduce((s, e) => s + (parseFloat(e.value) || 0), 0);
                const el = document.getElementById('revTotal');
                if (el) el.value = t.toFixed(2);
            });
        });

        /* ── Passport revenue totals ── */
        document.querySelectorAll('.ppt-rev-amount').forEach(inp => {
            inp.addEventListener('input', () => {
                const t = Array.from(document.querySelectorAll('.ppt-rev-amount')).reduce((s, e) => s + (parseFloat(e.value) || 0), 0);
                const el = document.getElementById('pptRevAmtTotal');
                if (el) el.value = t.toFixed(2);
            });
        });

        /* ── Ereg M+F total ── */
        document.querySelectorAll('.ereg-m, .ereg-f').forEach(inp => {
            inp.addEventListener('input', () => {
                const g = inp.dataset.grp;
                const m = parseInt(document.querySelector(`.ereg-m[data-grp="${g}"]`)?.value || 0);
                const f = parseInt(document.querySelector(`.ereg-f[data-grp="${g}"]`)?.value || 0);
                const el = document.getElementById(`ereg-tot-${g}`);
                if (el) el.value = m + f;
            });
        });

        /* ── Deport M+F ── */
        document.querySelectorAll('.deport-m, .deport-f').forEach(inp => {
            inp.addEventListener('input', () => {
                const row = inp.dataset.row;
                const m = parseInt(document.querySelector(`.deport-m[data-row="${row}"]`)?.value || 0);
                const f = parseInt(document.querySelector(`.deport-f[data-row="${row}"]`)?.value || 0);
                const el = document.getElementById(row);
                if (el) el.value = m + f;
            });
        });

        /* ── Passport stock balance: BF + Collected - Issued - Voided ── */
        function calcStockBalance() {
            for (let c = 0; c <= 5; c++) {
                const bf = parseInt(document.querySelector(`.stock-r0-c${c}`)?.value || 0);
                const col = parseInt(document.querySelector(`.stock-r1-c${c}`)?.value || 0);
                const iss = parseInt(document.querySelector(`.stock-r2-c${c}`)?.value || 0);
                const vd = parseInt(document.querySelector(`.stock-r3-c${c}`)?.value || 0);
                const el = document.querySelector(`.stock-bal-${c}`);
                if (el) el.value = Math.max(0, bf + col - iss - vd);
            }
        }
        document.querySelectorAll('[class*="stock-r"]').forEach(inp => inp.addEventListener('input', calcStockBalance));

        /* ── Add dynamic rows ── */
        document.querySelectorAll('[data-target]').forEach(btn => {
            btn.addEventListener('click', () => {
                const tbody = document.getElementById(btn.dataset.target);
                if (!tbody) return;
                const rows = tbody.querySelectorAll('.data-row');
                const idx = rows.length;
                const cols = JSON.parse(btn.dataset.cols || '[]');
                const prefix = btn.dataset.prefix || 'row';
                const tr = document.createElement('tr');
                tr.className = 'data-row';
                let html = `<td>${idx + 1}</td>`;
                cols.forEach(col => {
                    if (col === 'gender' || col === 'condition' || col === 'status') {
                        const opts = col === 'gender' ? ['M', 'F']
                            : col === 'condition' ? ['Serviceable', 'Unserviceable', 'Under Repair']
                                : ['Not Started', 'In Progress', 'Completed', 'Abandoned'];
                        html += `<td><select name="${prefix}[${idx}][${col}]" class="ni ni-select">${opts.map(o => `<option>${o}</option>`).join('')}</select></td>`;
                    } else {
                        const type = ['cost', 'participants', 'male', 'female', 'total', 'rounds', 'used', 'balance', 'number', 'bf', 'rx', 'issued', 'completion', 'dependants', 'm', 'f', 'employed', 'student', 'self_emp', 'spouse', 'dependant', 'regular', 'irregular'].includes(col) ? 'number' : 'text';
                        html += `<td><input type="${type}" name="${prefix}[${idx}][${col}]" class="ni" min="0"></td>`;
                    }
                });
                html += `<td><button type="button" class="btn-nis btn-ghost btn-sm" style="color:var(--color-danger);padding:2px 6px;" onclick="this.closest('tr').remove();"><i class="fas fa-times"></i></button></td>`;
                tr.innerHTML = html;
                tbody.insertBefore(tr, tbody.lastElementChild?.classList.contains('total-row') ? tbody.lastElementChild : null);
            });
        });

        /* ── Tab fill tracker (marks dot green when tab has any filled input) ── */
        function checkTabFill() {
            let filled = 0;
            tabs.forEach(tab => {
                const panel = document.getElementById('tab-' + tab.dataset.tab);
                if (!panel) return;
                const hasVal = Array.from(panel.querySelectorAll('input,textarea,select')).some(el => el.value && !el.readOnly && el.value !== '0');
                tab.classList.toggle('filled', hasVal);
                if (hasVal) filled++;
            });
            const el = document.getElementById('progressText');
            if (el) el.textContent = `${filled} / 10 sections filled`;
        }
        document.getElementById('returnForm')?.addEventListener('input', checkTabFill);

        /* ── Auto-save draft to localStorage ── */
        let saveTimer;
        const DRAFT_KEY = 'redas_draft_' + (document.querySelector('[name="reporting_officer"]')?.value || 'officer');
        function triggerAutoSave() {
            const ind = document.getElementById('autosaveIndicator');
            const text = document.getElementById('autosaveText');
            if (ind) ind.className = 'autosave-indicator saving';
            if (text) text.textContent = 'Saving…';
            clearTimeout(saveTimer);
            saveTimer = setTimeout(() => {
                const form = document.getElementById('returnForm');
                if (!form) return;
                const data = {};
                new FormData(form).forEach((v, k) => { data[k] = v; });
                try { localStorage.setItem(DRAFT_KEY, JSON.stringify(data)); } catch (e) { }
                if (ind) ind.className = 'autosave-indicator saved';
                if (text) text.textContent = 'Draft saved at ' + new Date().toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
            }, 1500);
        }
        document.getElementById('returnForm')?.addEventListener('input', triggerAutoSave);

        /* ── Save Draft button ── */
        document.getElementById('saveDraftBtn')?.addEventListener('click', () => {
            triggerAutoSave();
            if (window.REDAS) window.REDAS.showToast('Draft saved successfully.', 'success');
        });

        /* ── Clear form ── */
        document.getElementById('clearFormBtn')?.addEventListener('click', () => {
            if (!confirm('Clear all form data? This cannot be undone.')) return;
            document.getElementById('returnForm')?.reset();
            localStorage.removeItem(DRAFT_KEY);
            if (window.REDAS) window.REDAS.showToast('Form cleared.', 'info');
        });

        /* ── Preview ── */
        document.getElementById('previewBtn')?.addEventListener('click', () => {
            const cmd = document.querySelector('[name="command_name"]')?.value || '—';
            const period = document.querySelector('[name="period"]')?.value || '—';
            const type = document.querySelector('[name="return_type"]')?.value || '—';
            document.getElementById('previewMeta').textContent = `${cmd} | ${period} | ${type}`;
            const body = document.getElementById('previewBody');
            body.innerHTML = generatePreviewHTML();
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        });

        function generatePreviewHTML() {
            const cmd = document.querySelector('[name="command_name"]')?.value || 'N/A';
            const period = document.querySelector('[name="period"]')?.value || 'N/A';
            const type = document.querySelector('[name="return_type"]')?.value || 'N/A';
            const path = document.getElementById('workflowPath')?.value === 'hq' ? 'HQ Coordinator' : 'Zonal Coordinator';
            let html = `
            <div style="text-align:center;margin-bottom:24px;padding-bottom:16px;border-bottom:2px solid var(--nis-700);">
                <img src="http://localhost/assets/images/nis.png" style="height:50px;margin-bottom:8px;"><br>
                <strong style="font-size:1rem;color:var(--nis-800);">NIGERIA IMMIGRATION SERVICE</strong><br>
                <span style="font-size:.84rem;color:var(--gray-600);">NIS REDAS — Operational Return</span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:20px;padding:12px;background:var(--gray-50);border-radius:var(--radius-md);">
                <div><div style="font-size:.68rem;font-weight:700;text-transform:uppercase;color:var(--gray-400);">Command</div><div style="font-weight:700;color:var(--gray-800);">${cmd}</div></div>
                <div><div style="font-size:.68rem;font-weight:700;text-transform:uppercase;color:var(--gray-400);">Period</div><div style="font-weight:700;color:var(--gray-800);">${period}</div></div>
                <div><div style="font-size:.68rem;font-weight:700;text-transform:uppercase;color:var(--gray-400);">Return Type</div><div style="font-weight:700;color:var(--gray-800);">${type}</div></div>
            </div>
            <div style="background:#dcfce7;border:1px solid #86efac;border-radius:var(--radius-md);padding:10px 14px;font-size:.8rem;margin-bottom:20px;">
                <i class="fas fa-route" style="color:#15803d;"></i>
                <strong>Workflow:</strong> This return will be routed to <strong>${path}</strong> upon submission.
            </div>`;

            /* Collect filled sections */
            const staffTotal = document.getElementById('staffTotal')?.value;
            if (staffTotal && staffTotal !== '0') {
                html += `<div class="preview-section"><h6>Staff Strength</h6><p>Total Staff: <strong>${staffTotal}</strong></p></div>`;
            }
            const security = document.querySelector('[name="general[security]"]')?.value;
            if (security) html += `<div class="preview-section"><h6>Security Report</h6><p style="font-size:.82rem;">${security}</p></div>`;
            const challenges = document.querySelector('[name="general[challenges]"]')?.value;
            if (challenges) html += `<div class="preview-section"><h6>Challenges</h6><p style="font-size:.82rem;">${challenges}</p></div>`;
            const rec = document.querySelector('[name="general[recommendations]"]')?.value;
            if (rec) html += `<div class="preview-section"><h6>Recommendations</h6><p style="font-size:.82rem;">${rec}</p></div>`;

            html += `<div style="margin-top:24px;padding-top:16px;border-top:1px solid var(--gray-200);font-size:.78rem;color:var(--gray-500);">Prepared by: <strong>NIS REDAS</strong> &mdash; 15 September 2026</div>`;
            return html;
        }

        /* ── Print preview ── */
        document.getElementById('printPreviewBtn')?.addEventListener('click', () => {
            const content = document.getElementById('previewBody')?.innerHTML;
            const w = window.open('', '_blank');
            w.document.write(`<html><head><title>NIS Return Preview</title><link href="https://fonts.bunny.net/css?family=inter:400,600,700" rel="stylesheet"></head><body style="font-family:Inter,sans-serif;padding:40px;max-width:900px;margin:0 auto;">${content}</body></html>`);
            w.document.close();
            w.print();
        });

        /* ── Submit → confirm modal ── */
        document.getElementById('returnForm')?.addEventListener('submit', e => {
            e.preventDefault();
            const cmd = document.querySelector('[name="command_name"]')?.value || '—';
            const period = document.querySelector('[name="period"]')?.value || '—';
            const path = document.getElementById('workflowPath')?.value === 'hq' ? 'HQ Coordinator' : 'Zonal Coordinator';
            document.getElementById('confirmCommand').textContent = cmd;
            document.getElementById('confirmPeriod').textContent = period;
            document.getElementById('confirmRoute').textContent = path;
            new bootstrap.Modal(document.getElementById('confirmModal')).show();
        });

        document.getElementById('confirmSubmitBtn')?.addEventListener('click', () => {
            document.getElementById('confirmModal').querySelector('[data-bs-dismiss]').click();
            const btn = document.getElementById('submitBtn');
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting…'; }
            /* Actual POST — in production this submits the form */
            if (window.REDAS) window.REDAS.showToast('Return submitted successfully. Routed to supervisor for review.', 'success');
            setTimeout(() => { window.location.href = 'http://localhost/user/submissions'; }, 2000);
        });

        document.getElementById('submitFromPreview')?.addEventListener('click', () => {
            bootstrap.Modal.getInstance(document.getElementById('previewModal'))?.hide();
            document.getElementById('returnForm')?.dispatchEvent(new Event('submit'));
        });

        /* ── Attachment drag-and-drop ── */
        const attachZone = document.getElementById('attachZone');
        const attachInput = document.getElementById('attachInput');
        const attachList = document.getElementById('attachList');
        let attachedFiles = [];

        attachZone?.addEventListener('click', () => attachInput?.click());
        attachZone?.addEventListener('dragover', e => { e.preventDefault(); attachZone.classList.add('dragover'); });
        attachZone?.addEventListener('dragleave', () => attachZone.classList.remove('dragover'));
        attachZone?.addEventListener('drop', e => { e.preventDefault(); attachZone.classList.remove('dragover'); addFiles(e.dataTransfer.files); });
        attachInput?.addEventListener('change', () => addFiles(attachInput.files));

        function addFiles(files) {
            Array.from(files).forEach(f => {
                if (f.size > 10 * 1024 * 1024) { alert(`${f.name} exceeds 10MB limit.`); return; }
                attachedFiles.push(f);
                const item = document.createElement('div');
                item.className = 'attach-item';
                const icon = f.type.includes('pdf') ? 'fas fa-file-pdf' : f.type.includes('image') ? 'fas fa-file-image' : 'fas fa-file-word';
                item.innerHTML = `<i class="${icon}" style="color:var(--nis-600);"></i><span>${f.name}</span><span style="color:var(--gray-400);font-size:.7rem;">(${(f.size / 1024).toFixed(0)}KB)</span><button type="button" class="remove-attach" onclick="this.parentElement.remove();"><i class="fas fa-times"></i></button>`;
                attachList?.appendChild(item);
            });
        }

        /* ── Load saved draft on page load ── */
        try {
            const saved = localStorage.getItem(DRAFT_KEY);
            if (saved) {
                const data = JSON.parse(saved);
                Object.entries(data).forEach(([k, v]) => {
                    const el = document.querySelector(`[name="${CSS.escape(k)}"]`);
                    if (el && !el.readOnly) el.value = v;
                });
                const ind = document.getElementById('autosaveIndicator');
                const text = document.getElementById('autosaveText');
                if (ind) ind.className = 'autosave-indicator saved';
                if (text) text.textContent = 'Draft restored';
                updateWorkflow();
                checkTabFill();
            }
        } catch (e) { }

    })();
