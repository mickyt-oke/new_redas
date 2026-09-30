
(function () {
    'use strict';

    /* Root scoping: on the combined state page this partial lives inside
       #dir-migration; on the standalone page fall back to the document. */
    var MIG_ROOT = document.getElementById('dir-migration') || document;
    var isCombined = !!document.getElementById('dir-migration');

    // Predefined lists of countries for MoUs selection
    var countryLists = {
        ecowas: [],
        non_ecowas: [],
        asia: [],
        europe: [],
        north_am: [],
        south_am: [],
        australia: []
    };

    var refugeeIndex = 1;
    function addRefugeeRow() {
        const body = document.getElementById('refugeesBody');
        const newRow = document.createElement('tr');
        newRow.className = 'refugee-row';
        newRow.innerHTML = `
            <td>
                <select name="refugees_asylum_seekers[refugees][${refugeeIndex}][state]" class="ni ni-select" required style="padding:6px 10px;">
                    <option value="">Select State</option>
                    <option value="X">X</option>
                </select>
            </td>
            <td>
                <input type="number" min="0" class="ni refugee-apps" name="refugees_asylum_seekers[refugees][${refugeeIndex}][applications]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
            </td>
            <td>
                <input type="number" min="0" class="ni refugee-approved" name="refugees_asylum_seekers[refugees][${refugeeIndex}][approved]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
            </td>
            <td>
                <input type="number" min="0" class="ni refugee-rejected" name="refugees_asylum_seekers[refugees][${refugeeIndex}][rejected]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
            </td>
            <td>
                <input type="number" class="ni refugee-total" name="refugees_asylum_seekers[refugees][${refugeeIndex}][total]" value="0" readonly style="background:var(--gray-50);padding:6px 10px;text-align:center;font-weight:700;">
            </td>
            <td>
                <button type="button" class="btn-nis btn-ghost btn-sm" data-m-action="remove-row" style="color:var(--color-danger);"><i class="fas fa-trash"></i></button>
            </td>
        `;
        body.appendChild(newRow);

        // Listeners for calculation
        newRow.querySelectorAll('.refugee-apps, .refugee-approved, .refugee-rejected').forEach(input => {
            input.addEventListener('input', () => {
                const valApproved = parseInt(newRow.querySelector('.refugee-approved').value) || 0;
                const valRejected = parseInt(newRow.querySelector('.refugee-rejected').value) || 0;
                newRow.querySelector('.refugee-total').value = valApproved + valRejected;
            });
        });
        refugeeIndex++;
    }

    var asylumIndex = 1;
    function addAsylumRow() {
        const body = document.getElementById('asylumBody');
        const newRow = document.createElement('tr');
        newRow.className = 'asylum-row';
        newRow.innerHTML = `
            <td>
                <select name="refugees_asylum_seekers[asylum_seekers][${asylumIndex}][state]" class="ni ni-select" required style="padding:6px 10px;">
                    <option value="">Select State</option>
                    <option value="X">X</option>
                </select>
            </td>
            <td>
                <input type="number" min="0" class="ni asylum-apps" name="refugees_asylum_seekers[asylum_seekers][${asylumIndex}][applications]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
            </td>
            <td>
                <input type="number" min="0" class="ni asylum-approved" name="refugees_asylum_seekers[asylum_seekers][${asylumIndex}][approved]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
            </td>
            <td>
                <input type="number" min="0" class="ni asylum-rejected" name="refugees_asylum_seekers[asylum_seekers][${asylumIndex}][rejected]" value="0" placeholder="0" style="padding:6px 10px;text-align:center;">
            </td>
            <td>
                <input type="number" class="ni asylum-total" name="refugees_asylum_seekers[asylum_seekers][${asylumIndex}][total]" value="0" readonly style="background:var(--gray-50);padding:6px 10px;text-align:center;font-weight:700;">
            </td>
            <td>
                <button type="button" class="btn-nis btn-ghost btn-sm" data-m-action="remove-row" style="color:var(--color-danger);"><i class="fas fa-trash"></i></button>
            </td>
        `;
        body.appendChild(newRow);

        // Listeners for calculation
        newRow.querySelectorAll('.asylum-apps, .asylum-approved, .asylum-rejected').forEach(input => {
            input.addEventListener('input', () => {
                const valApproved = parseInt(newRow.querySelector('.asylum-approved').value) || 0;
                const valRejected = parseInt(newRow.querySelector('.asylum-rejected').value) || 0;
                newRow.querySelector('.asylum-total').value = valApproved + valRejected;
            });
        });
        asylumIndex++;
    }

    var mouIndices = {};
    function addMouRow(key) {
        if (!mouIndices[key]) {
            mouIndices[key] = 1;
        }
        const body = document.getElementById('mouBody-' + key);
        const newRow = document.createElement('tr');
        newRow.className = 'mou-row-' + key;

        let options = '<option value="">Select Country</option>';
        countryLists[key].forEach(c => {
            options += `<option value="${c}">${c}</option>`;
        });

        newRow.innerHTML = `
            <td>
                <select name="mou_countries[${key}][${mouIndices[key]}][country]" class="ni ni-select" required style="padding:6px 10px;">
                    ${options}
                </select>
            </td>
            <td>
                <input type="text" class="ni" name="mou_countries[${key}][${mouIndices[key]}][description]" placeholder="e.g. Agreement details" required style="padding:6px 10px;">
            </td>
            <td>
                <button type="button" class="btn-nis btn-ghost btn-sm" data-m-action="remove-row" style="color:var(--color-danger);"><i class="fas fa-trash"></i></button>
            </td>
        `;
        body.appendChild(newRow);
        mouIndices[key]++;
    }

    function removeRow(btn) {
        const row = btn.closest('tr');
        row?.remove();

        // Recalculate totals if it was staff strength
        let totalMale = 0, totalFemale = 0, grandTotal = 0;
        MIG_ROOT.querySelectorAll('.staff-male-input').forEach(input => {
            const valMale = parseInt(input.value) || 0;
            const femaleInput = input.closest('tr').querySelector('.staff-female-input');
            const valFemale = parseInt(femaleInput.value) || 0;
            totalMale += valMale;
            totalFemale += valFemale;
            grandTotal += (valMale + valFemale);
        });
        const maleEl = document.getElementById('migration-staff-male-total');
        if (maleEl) maleEl.textContent = totalMale.toLocaleString();
        const femaleEl = document.getElementById('migration-staff-female-total');
        if (femaleEl) femaleEl.textContent = totalFemale.toLocaleString();
        const grandEl = document.getElementById('migration-staff-grand-total');
        if (grandEl) grandEl.textContent = grandTotal.toLocaleString();
    }

    function addSomZone() {
        const selector = document.getElementById('som-zone-selector');
        const zone = selector.value;
        if (!zone) return;

        const zoneId = zone.toLowerCase().replace(' ', '_');
        const tbody = document.getElementById('som-zone-' + zoneId);
        if (tbody) {
            tbody.classList.remove('d-none');

            // Add badge
            const activeList = document.getElementById('som-active-list');
            const badge = document.createElement('span');
            badge.className = 'status-badge badge-approved';
            badge.id = 'som-badge-' + zoneId;
            badge.textContent = zone;
            badge.style.marginLeft = '4px';
            activeList.appendChild(badge);

            // Remove option
            const option = selector.querySelector(`option[value="${zone}"]`);
            option?.remove();
            selector.value = '';

            if (window.REDAS) {
                window.REDAS.showToast(`${zone} added to form.`, 'success');
            } else {
                alert(`${zone} added to form.`);
            }
        }
    }

    function addTipZone() {
        const selector = document.getElementById('tip-zone-selector');
        const zone = selector.value;
        if (!zone) return;

        const zoneId = zone.toLowerCase().replace(' ', '_');
        const tbody = document.getElementById('tip-zone-' + zoneId);
        if (tbody) {
            tbody.classList.remove('d-none');

            // Add badge
            const activeList = document.getElementById('tip-active-list');
            const badge = document.createElement('span');
            badge.className = 'status-badge badge-approved';
            badge.id = 'tip-badge-' + zoneId;
            badge.textContent = zone;
            badge.style.marginLeft = '4px';
            activeList.appendChild(badge);

            // Remove option
            const option = selector.querySelector(`option[value="${zone}"]`);
            option?.remove();
            selector.value = '';

            if (window.REDAS) {
                window.REDAS.showToast(`${zone} added to form.`, 'success');
            } else {
                alert(`${zone} added to form.`);
            }
        }
    }

    /* Add-row buttons carry data-m-action instead of inline onclick so the
       partial declares no global functions. */
    MIG_ROOT.querySelectorAll('[data-m-action]').forEach(btn => {
        const action = btn.getAttribute('data-m-action');
        if (action === 'add-refugee-row') btn.addEventListener('click', addRefugeeRow);
        else if (action === 'add-asylum-row') btn.addEventListener('click', addAsylumRow);
        else if (action === 'add-mou-row') btn.addEventListener('click', () => addMouRow(btn.getAttribute('data-m-key')));
        else if (action === 'add-som-zone') btn.addEventListener('click', addSomZone);
        else if (action === 'add-tip-zone') btn.addEventListener('click', addTipZone);
    });
    /* Remove buttons also appear in dynamically added rows: delegate. */
    MIG_ROOT.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-m-action="remove-row"]');
        if (btn) removeRow(btn);
    });

    document.addEventListener('DOMContentLoaded', () => {
        const tabs = MIG_ROOT.querySelectorAll('#migrationFormTabs [data-m-tab]');
        const contents = MIG_ROOT.querySelectorAll('.m-tab-content');
        const tabOrder = Array.from(tabs).map(tab => tab.dataset.mTab).filter(Boolean);

        function showTab(target) {
            const tab = MIG_ROOT.querySelector('#migrationFormTabs [data-m-tab="' + target + '"]');
            if (!tab) return;

            tabs.forEach(t => t.classList.toggle('active', t === tab));
            contents.forEach(content => {
                const isActive = content.id === 'tab-migration-' + target;
                content.style.display = isActive ? 'block' : 'none';
                content.classList.toggle('active', isActive);
            });

            // The standalone wrapper registers the review snapshot builder.
            if (target === 'review' && typeof window.migrationBuildReviewSnapshot === 'function') {
                window.migrationBuildReviewSnapshot();
            }

            updateActionButtons();
        }

        function updateActionButtons() {
            const activeTabId = MIG_ROOT.querySelector('#migrationFormTabs [data-m-tab].active')?.dataset.mTab || 'staff';
            contents.forEach(content => {
                const actions = content.querySelector('.hrm-actions');
                if (!actions) return;

                const prev = actions.querySelector('.hrm-prev-btn');
                const next = actions.querySelector('.hrm-next-btn');
                const contentKey = content.id.replace('tab-migration-', '');
                const isFirst = contentKey === tabOrder[0];
                const isLast = contentKey === tabOrder[tabOrder.length - 1];

                if (prev) {
                    prev.disabled = isFirst;
                    prev.style.opacity = isFirst ? '0.5' : '1';
                    prev.style.cursor = isFirst ? 'not-allowed' : 'pointer';
                }

                if (next) {
                    if (isLast && isCombined) {
                        /* The combined state form owns submission; this panel's
                           last tab simply ends. */
                        next.innerHTML = 'Next <i class="fas fa-arrow-right"></i>';
                        next.disabled = true;
                        next.style.opacity = '0.5';
                        next.style.cursor = 'not-allowed';
                    } else {
                        next.disabled = false;
                        next.style.opacity = '1';
                        next.style.cursor = 'pointer';
                        next.innerHTML = isLast
                            ? '<i class="fas fa-check"></i> Submit'
                            : 'Next <i class="fas fa-arrow-right"></i>';
                        next.dataset.target = isLast ? 'review' : tabOrder[Math.min(tabOrder.indexOf(contentKey) + 1, tabOrder.length - 1)];
                    }
                }
            });

            if (!(isCombined && activeTabId === tabOrder[tabOrder.length - 1])) {
                const activeContent = document.getElementById('tab-migration-' + activeTabId);
                if (activeContent) {
                    const actionBar = activeContent.querySelector('.hrm-actions');
                    if (actionBar && actionBar.querySelector('.hrm-next-btn')) {
                        const nextBtn = actionBar.querySelector('.hrm-next-btn');
                        nextBtn.disabled = false;
                    }
                }
            }
        }

        function saveDraft() {
            const form = document.querySelector('form[action*="directorates"]');
            if (!form) return;

            const data = {};
            new FormData(form).forEach((value, key) => {
                data[key] = value;
            });

            const draftKey = 'redas_migration_draft_' + (document.querySelector('[name="report_period"]')?.value || 'default');
            localStorage.setItem(draftKey, JSON.stringify(data));
            if (window.REDAS && typeof window.REDAS.showToast === 'function') {
                window.REDAS.showToast('Draft saved successfully.', 'success');
            } else {
                alert('Draft saved successfully.');
            }
        }

        contents.forEach(content => {
            if (content.querySelector('.hrm-actions')) return;

            const actions = document.createElement('div');
            actions.className = 'hrm-actions';
            actions.innerHTML = `
                <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
                <div class="hrm-actions-center">
                    <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
                </div>
                <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next <i class="fas fa-arrow-right"></i></button>
            `;

            content.appendChild(actions);
        });

        MIG_ROOT.querySelectorAll('.hrm-prev-btn').forEach(button => {
            button.addEventListener('click', () => {
                const currentContent = button.closest('.m-tab-content');
                const currentKey = currentContent?.id.replace('tab-migration-', '');
                const currentIndex = tabOrder.indexOf(currentKey);
                if (currentIndex > 0) {
                    showTab(tabOrder[currentIndex - 1]);
                }
            });
        });

        MIG_ROOT.querySelectorAll('.hrm-next-btn').forEach(button => {
            button.addEventListener('click', () => {
                const currentContent = button.closest('.m-tab-content');
                const currentKey = currentContent?.id.replace('tab-migration-', '');
                const currentIndex = tabOrder.indexOf(currentKey);

                if (currentIndex === tabOrder.length - 1) {
                    if (isCombined) return; // the combined state form owns submission
                    // On the final (Review & Submit) tab the primary button submits the return.
                    const form = document.querySelector('main form') || document.querySelector('form[action*="directorates"]');
                    if (!form) return;
                    // Required fields live on hidden tabs; an invalid control that is
                    // not focusable blocks submission silently, so switch to its tab first.
                    if (!form.checkValidity()) {
                        const invalid = form.querySelector(':invalid');
                        const panel = invalid ? invalid.closest('.m-tab-content') : null;
                        if (panel) showTab(panel.id.replace('tab-migration-', ''));
                        form.reportValidity();
                        return;
                    }
                    if (window.confirm('Are you sure you want to submit this return?\n\nPlease verify all information before continuing.')) {
                        if (form.requestSubmit) form.requestSubmit();
                        else form.submit();
                    }
                    return;
                }

                if (currentIndex >= 0) {
                    showTab(tabOrder[currentIndex + 1]);
                }
            });
        });

        MIG_ROOT.querySelectorAll('.hrm-save-draft-btn').forEach(button => {
            button.addEventListener('click', saveDraft);
        });

        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                showTab(tab.dataset.mTab);
            });
        });

        updateActionButtons();

        // Auto calculation: Staff Strength
        const recalculateStaff = () => {
            let totalMale = 0;
            let totalFemale = 0;
            let grandTotal = 0;

            MIG_ROOT.querySelectorAll('.staff-male-input').forEach(input => {
                const femaleInput = input.closest('tr').querySelector('.staff-female-input');
                const totalOutput = input.closest('tr').querySelector('.staff-total-output');

                const valMale = parseInt(input.value) || 0;
                const valFemale = parseInt(femaleInput.value) || 0;
                const valTotal = valMale + valFemale;

                totalOutput.value = valTotal;

                totalMale += valMale;
                totalFemale += valFemale;
                grandTotal += valTotal;
            });

            document.getElementById('migration-staff-male-total').textContent = totalMale.toLocaleString();
            document.getElementById('migration-staff-female-total').textContent = totalFemale.toLocaleString();
            document.getElementById('migration-staff-grand-total').textContent = grandTotal.toLocaleString();
        };

        MIG_ROOT.querySelectorAll('.staff-male-input, .staff-female-input').forEach(input => {
            input.addEventListener('input', recalculateStaff);
        });

        // Auto calculation: Smuggling of Migrants (SOM)
        const recalculateSom = (e) => {
            const row = e.target.closest('.som-row');
            let rowTotal = 0;
            row.querySelectorAll('.som-field-input').forEach(input => {
                rowTotal += parseInt(input.value) || 0;
            });
            const output = row.querySelector('.som-row-total');
            if (output) output.value = rowTotal;
        };

        MIG_ROOT.querySelectorAll('.som-field-input').forEach(input => {
            input.addEventListener('input', recalculateSom);
        });

        // Auto calculation: Trafficking In Persons (TIP)
        const recalculateTip = (e) => {
            const row = e.target.closest('.tip-row');
            let rowTotal = 0;
            row.querySelectorAll('.tip-field-input').forEach(input => {
                rowTotal += parseInt(input.value) || 0;
            });
            const output = row.querySelector('.tip-row-total');
            if (output) output.value = rowTotal;
        };

        MIG_ROOT.querySelectorAll('.tip-field-input').forEach(input => {
            input.addEventListener('input', recalculateTip);
        });

        // Auto calculation: Refugees Initial Row
        MIG_ROOT.querySelectorAll('.refugee-row').forEach(row => {
            row.querySelectorAll('.refugee-apps, .refugee-approved, .refugee-rejected').forEach(input => {
                input.addEventListener('input', () => {
                    const valApproved = parseInt(row.querySelector('.refugee-approved').value) || 0;
                    const valRejected = parseInt(row.querySelector('.refugee-rejected').value) || 0;
                    row.querySelector('.refugee-total').value = valApproved + valRejected;
                });
            });
        });

        // Auto calculation: Asylum Seekers Initial Row
        MIG_ROOT.querySelectorAll('.asylum-row').forEach(row => {
            row.querySelectorAll('.asylum-apps, .asylum-approved, .asylum-rejected').forEach(input => {
                input.addEventListener('input', () => {
                    const valApproved = parseInt(row.querySelector('.asylum-approved').value) || 0;
                    const valRejected = parseInt(row.querySelector('.asylum-rejected').value) || 0;
                    row.querySelector('.asylum-total').value = valApproved + valRejected;
                });
            });
        });
    });
})();
