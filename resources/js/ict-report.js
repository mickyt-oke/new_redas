/*=========================================================================
    ICT & Cybersecurity Annual Report Module JS
=========================================================================*/

function runInit() {
    initAccordion();
    initStaffStrength();
    initIdCards();
    initDynamicRows();
    initSectionSaves();
    initSectionResets();
    initInputStatusHandlers();
    calculateICTSectionTotals();

    document.addEventListener('input', calculateICTSectionTotals);
    document.addEventListener('change', calculateICTSectionTotals);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', runInit);
} else {
    runInit();
}

/*=========================================================================
    ACCORDION & MINIMISE SECTIONS
=========================================================================*/
function initAccordion() {
    // Delegated click handler for high-reliability on all section toggles & progress badges
    document.addEventListener('click', (e) => {
        const statusBadge = e.target.closest('.section-status');
        if (statusBadge) {
            e.stopImmediatePropagation();
            e.stopPropagation();
            const card = statusBadge.closest('.visa-card');
            if (card) {
                toggleCardCollapse(card);
            }
            return;
        }

        const headerBtn = e.target.closest('.visa-accordion-header');
        if (headerBtn) {
            e.stopImmediatePropagation();
            const card = headerBtn.closest('.visa-card');
            if (card) {
                toggleCardCollapse(card);
            }
        }
    });

    // Automatically expand card when scrolled/jumped to via hash link
    const handleHashLink = (hash) => {
        if (!hash) return;
        const targetSection = document.querySelector(hash);
        if (targetSection) {
            const card = targetSection.querySelector('.visa-card');
            if (card && card.classList.contains('collapsed')) {
                card.classList.remove('collapsed');
                const icon = card.querySelector('.accordion-icon');
                if (icon) {
                    icon.classList.remove('fa-chevron-right');
                    icon.classList.add('fa-chevron-down');
                }
            }
        }
    };

    window.addEventListener('hashchange', () => {
        handleHashLink(window.location.hash);
    });

    setTimeout(() => {
        handleHashLink(window.location.hash);
    }, 100);
}

function toggleCardCollapse(card) {
    if (!card) return;
    const isCollapsed = card.classList.toggle('collapsed');
    const icon = card.querySelector('.accordion-icon');
    if (icon) {
        if (isCollapsed) {
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-right');
        } else {
            icon.classList.remove('fa-chevron-right');
            icon.classList.add('fa-chevron-down');
        }
    }
}

/*=========================================================================
    STAFF STRENGTH
=========================================================================*/
function initStaffStrength() {
    const card = document.getElementById('staffStrengthCard');
    if (card) {
        card.addEventListener('input', (e) => {
            if (e.target.classList.contains('staff-input')) {
                calculateStaffStrength();
            }
        });
        card.addEventListener('change', (e) => {
            if (e.target.classList.contains('staff-input')) {
                calculateStaffStrength();
            }
        });
    }

    const inputs = document.querySelectorAll('#staffStrengthCard .staff-input');
    inputs.forEach(input => {
        input.addEventListener('click', function() { this.select(); });
    });

    const resetButton = document.getElementById('resetStaffStrength');
    if (resetButton) {
        resetButton.addEventListener('click', () => {
            const allInputs = document.querySelectorAll('#staffStrengthCard .staff-input');
            allInputs.forEach(input => { if (!input.readOnly) input.value = 0; });
            calculateStaffStrength();
        });
    }

    calculateStaffStrength();
}

function calculateStaffStrength() {
    let grandMale = 0;
    let grandFemale = 0;
    let grandTotal = 0;

    const tbody = document.getElementById('staff_strength_tbody');
    if (!tbody) return;

    tbody.querySelectorAll('tr').forEach(row => {
        const maleInput = row.querySelector('.staff-input.male');
        const femaleInput = row.querySelector('.staff-input.female');
        const totalInput = row.querySelector('.total-input');

        if (maleInput && femaleInput) {
            let mVal = Math.max(0, parseInt(maleInput.value) || 0);
            let fVal = Math.max(0, parseInt(femaleInput.value) || 0);
            maleInput.value = mVal;
            femaleInput.value = fVal;
            let rTot = mVal + fVal;
            if (totalInput) totalInput.value = rTot;

            grandMale += mVal;
            grandFemale += fVal;
            grandTotal += rTot;
        }
    });

    const gm = document.getElementById('grandMale');
    if (gm) gm.value = grandMale;
    const gf = document.getElementById('grandFemale');
    if (gf) gf.value = grandFemale;
    const gt = document.getElementById('grandTotal');
    if (gt) gt.value = grandTotal;

    const summary = document.getElementById('staffSummary');
    if (summary) summary.innerHTML = `<strong>${grandTotal} Officers</strong>`;

    const badge = document.getElementById('staffStatus');
    if (badge) {
        badge.className = grandTotal > 0 ? 'section-status status-complete' : 'section-status status-progress';
        badge.textContent = grandTotal > 0 ? 'Completed' : 'In Progress';
    }
}

/*=========================================================================
    ID CARDS / E-DOCUMENTATION
=========================================================================*/
function initIdCards() {
    const card = document.getElementById('idCardsCard');
    if (card) {
        card.addEventListener('input', (e) => {
            if (e.target.classList.contains('id-card-input')) {
                calculateIdCards();
            }
        });
        card.addEventListener('change', (e) => {
            if (e.target.classList.contains('id-card-input')) {
                calculateIdCards();
            }
        });
        card.addEventListener('keyup', (e) => {
            if (e.target.classList.contains('id-card-input')) {
                calculateIdCards();
            }
        });
    }

    // Delegated fallback for extra safety across document
    document.addEventListener('input', (e) => {
        if (e.target && e.target.classList && e.target.classList.contains('id-card-input')) {
            calculateIdCards();
        }
    });

    const inputs = document.querySelectorAll('.id-card-input');
    inputs.forEach(input => {
        input.addEventListener('click', function() { this.select(); });
    });

    const resetButton = document.getElementById('resetIdCards');
    if (resetButton) {
        resetButton.addEventListener('click', () => {
            document.querySelectorAll('.id-card-input').forEach(input => {
                input.value = 0;
            });
            calculateIdCards();
        });
    }

    calculateIdCards();
}

function calculateIdCards() {
    const cardInputs = document.querySelectorAll('.id-card-input');
    if (cardInputs.length === 0) return;

    const rowSums = {};
    const colSums = {};
    let grandTotal = 0;

    cardInputs.forEach(input => {
        const row = input.getAttribute('data-row');
        const col = input.getAttribute('data-col');
        let val = parseInt(input.value);
        if (isNaN(val) || val < 0) {
            val = 0;
        }

        if (row) {
            rowSums[row] = (rowSums[row] || 0) + val;
        }
        if (col) {
            colSums[col] = (colSums[col] || 0) + val;
        }
        grandTotal += val;
    });

    // Update row total inputs
    Object.keys(rowSums).forEach(row => {
        const rowTotalEl = document.getElementById(`row_total_${row}`);
        if (rowTotalEl) rowTotalEl.value = rowSums[row];
    });

    // Update column total inputs
    Object.keys(colSums).forEach(col => {
        const colTotalEl = document.getElementById(`col_total_${col}`);
        if (colTotalEl) colTotalEl.value = colSums[col];
    });

    // Update Grand Total input
    const grandTotalEl = document.getElementById('id_card_grand_total');
    if (grandTotalEl) grandTotalEl.value = grandTotal;

    // Update summary text
    const summary = document.getElementById('idCardSummary');
    if (summary) summary.innerHTML = `<strong>${grandTotal.toLocaleString()} Cards</strong>`;

    // Update section status badge
    const badge = document.getElementById('idCardsStatus');
    if (badge) {
        badge.className = grandTotal > 0 ? 'section-status status-complete' : 'section-status status-progress';
        badge.textContent = grandTotal > 0 ? 'Completed' : 'In Progress';
    }
}

/*=========================================================================
    DYNAMIC ROWS ADDITION & DELETION
=========================================================================*/
function initDynamicRows() {
    // Delegated click handler for ANY + Add Row button on the page
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-ict-target], [data-target]');
        if (!btn) return;

        const targetId = btn.getAttribute('data-ict-target') || btn.getAttribute('data-target');
        const tbody = document.getElementById(targetId);
        if (!tbody) return;

        e.preventDefault();

        // Special handling for Staff Strength dynamic rows
        if (targetId === 'staff_strength_tbody') {
            const rowIndex = tbody.querySelectorAll('tr').length;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <input type="text" name="staff_custom[${rowIndex}][cadre]" placeholder="Cadre Name" class="ni" style="font-weight:600;">
                </td>
                <td>
                    <input type="number" min="0" value="0" name="staff_custom[${rowIndex}][male]" class="ni staff-input male">
                </td>
                <td>
                    <input type="number" min="0" value="0" name="staff_custom[${rowIndex}][female]" class="ni staff-input female">
                </td>
                <td>
                    <input readonly class="ni total-input" value="0">
                </td>
                <td class="text-center">
                    <button type="button" class="btn-nis btn-ghost btn-sm btn-delete-row" style="color:var(--color-danger);padding:4px 8px;">
                        <i class="fas fa-trash-can"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
            tr.querySelectorAll('.staff-input').forEach(input => {
                input.addEventListener('input', calculateStaffStrength);
                input.addEventListener('change', calculateStaffStrength);
                input.addEventListener('click', function() { this.select(); });
            });
            calculateStaffStrength();
            return;
        }

        // Standard tables handling with data-ict-fields or data-cols
        const fieldsAttr = btn.getAttribute('data-ict-fields') || btn.getAttribute('data-cols');
        const fields = fieldsAttr ? JSON.parse(fieldsAttr) : [];
        const prefix = btn.getAttribute('data-ict-prefix') || btn.getAttribute('data-prefix') || 'row';
        const rowIndex = tbody.querySelectorAll('tr').length;

        const tr = document.createElement('tr');
        let html = `<td class="sn-cell text-center" style="font-weight:600;color:var(--gray-500);">${rowIndex + 1}</td>`;

        if (Array.isArray(fields) && fields.length > 0) {
            fields.forEach(field => {
                let inputHtml = '';
                if (typeof field === 'string') {
                    inputHtml = `<input type="text" name="${prefix}[${rowIndex}][${field}]" class="ni">`;
                } else if (field.type === 'select') {
                    const options = field.options || [];
                    inputHtml = `<select name="${prefix}[${rowIndex}][${field.name}]" class="ni ni-select">`;
                    options.forEach(opt => {
                        inputHtml += `<option value="${opt}">${opt}</option>`;
                    });
                    inputHtml += `</select>`;
                } else if (field.type === 'textarea') {
                    inputHtml = `<textarea name="${prefix}[${rowIndex}][${field.name}]" class="ni" rows="1" style="min-height:38px;padding:8px;resize:vertical;"></textarea>`;
                } else {
                    inputHtml = `<input type="${field.type || 'text'}" name="${prefix}[${rowIndex}][${field.name}]" class="ni">`;
                }
                html += `<td>${inputHtml}</td>`;
            });
        }

        html += `<td class="text-center">
            <button type="button" class="btn-nis btn-ghost btn-sm btn-delete-row" style="color:var(--color-danger);padding:4px 8px;">
                <i class="fas fa-trash-can"></i>
            </button>
        </td>`;

        tr.innerHTML = html;
        tbody.appendChild(tr);

        tr.querySelectorAll('input:not([readonly]), textarea, select').forEach(input => {
            input.addEventListener('input', () => updateTableStatus(tbody.closest('.visa-card')));
            input.addEventListener('change', () => updateTableStatus(tbody.closest('.visa-card')));
        });

        updateTableStatus(tbody.closest('.visa-card'));
        calculateICTSectionTotals();
    });

    // Delegated row deletion
    document.addEventListener('click', (e) => {
        const deleteBtn = e.target.closest('.btn-delete-row');
        if (!deleteBtn) return;

        e.preventDefault();
        e.stopPropagation();
        const tr = deleteBtn.closest('tr');
        if (tr) {
            const tbody = tr.parentNode;
            tr.remove();
            if (tbody.id === 'staff_strength_tbody') {
                calculateStaffStrength();
            } else {
                reindexTable(tbody);
            }
            calculateICTSectionTotals();
        }
    });
}

function calculateICTSectionTotals() {
    // 1. Projects
    const projectsBody = document.getElementById('projects_tbody');
    if (projectsBody) {
        const count = projectsBody.querySelectorAll('tr').length;
        const tot = document.getElementById('projectsTotal');
        const sum = document.getElementById('projectsSummary');
        if (tot) tot.value = count;
        if (sum) sum.innerHTML = `<strong>${count} Items</strong>`;
    }

    // 2. Hardware Maintenance
    const maintenanceBody = document.getElementById('maintenance_tbody');
    if (maintenanceBody) {
        const count = maintenanceBody.querySelectorAll('tr').length;
        const tot = document.getElementById('maintenanceTotal');
        const sum = document.getElementById('maintenanceSummary');
        if (tot) tot.value = count;
        if (sum) sum.innerHTML = `<strong>${count} Records</strong>`;
    }

    // 3. Software & Data
    const softwareBody = document.getElementById('software_tbody');
    const softwareCount = softwareBody ? softwareBody.querySelectorAll('tr').length : 0;
    const softwareTot = document.getElementById('softwareTotal');
    if (softwareTot) softwareTot.value = softwareCount;

    const dataBreachBody = document.getElementById('data_breach_tbody');
    let breachSum = 0;
    if (dataBreachBody) {
        dataBreachBody.querySelectorAll('input[name*="no_of_incident"]').forEach(inp => {
            breachSum += parseInt(inp.value) || 0;
        });
    }
    const breachTot = document.getElementById('dataBreachCountTotal');
    if (breachTot) breachTot.value = breachSum;

    const softwareDataSum = document.getElementById('softwareDataSummary');
    if (softwareDataSum) softwareDataSum.innerHTML = `<strong>${softwareCount + breachSum} Records</strong>`;

    // 4. Cybersecurity Deployment
    const cyberBody = document.getElementById('cybersecurity_tbody');
    if (cyberBody) {
        const count = cyberBody.querySelectorAll('tr').length;
        const tot = document.getElementById('cybersecurityTotal');
        const sum = document.getElementById('cybersecuritySummary');
        if (tot) tot.value = count;
        if (sum) sum.innerHTML = `<strong>${count} Deployments</strong>`;
    }

    // 5. MIDAS Deployment
    const midasBody = document.getElementById('midas_tbody');
    if (midasBody) {
        const count = midasBody.querySelectorAll('tr').length;
        const tot = document.getElementById('midasTotal');
        const sum = document.getElementById('midasSummary');
        if (tot) tot.value = count;
        if (sum) sum.innerHTML = `<strong>${count} Locations</strong>`;
    }

    // 6. Incidents
    const incidentTypes = [
        { id: 'incidents_hardware_tbody', totId: 'incidentsHardwareTotal', sumId: 'incidentsHardwareSummary' },
        { id: 'incidents_software_tbody', totId: 'incidentsSoftwareTotal', sumId: 'incidentsSoftwareSummary' },
        { id: 'incidents_network_tbody', totId: 'incidentsNetworkTotal', sumId: 'incidentsNetworkSummary' },
        { id: 'incidents_cybersecurity_tbody', totId: 'incidentsCybersecurityTotal', sumId: 'incidentsCybersecuritySummary' },
        { id: 'incidents_power_tbody', totId: 'incidentsPowerTotal', sumId: 'incidentsPowerSummary' },
        { id: 'incidents_communication_tbody', totId: 'incidentsCommunicationTotal', sumId: 'incidentsCommunicationSummary' },
        { id: 'incidents_surveillance_tbody', totId: 'incidentsSurveillanceTotal', sumId: 'incidentsSurveillanceSummary' },
        { id: 'incidents_providers_tbody', totId: 'incidentsProvidersTotal', sumId: 'incidentsProvidersSummary' },
    ];

    incidentTypes.forEach(item => {
        const tbody = document.getElementById(item.id);
        if (tbody) {
            const count = tbody.querySelectorAll('tr').length;
            const tot = document.getElementById(item.totId);
            const sum = document.getElementById(item.sumId);
            if (tot) tot.value = count;
            if (sum) sum.innerHTML = `<strong>${count} Incidents</strong>`;
        }
    });
}

function reindexTable(tbody) {
    const rows = tbody.querySelectorAll('tr');
    rows.forEach((row, idx) => {
        const snCell = row.querySelector('.sn-cell');
        if (snCell) snCell.textContent = idx + 1;

        row.querySelectorAll('input, select, textarea').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                const reindexed = name.replace(/\[\d+\]/, `[${idx}]`);
                input.setAttribute('name', reindexed);
            }
        });
    });
    updateTableStatus(tbody.closest('.visa-card'));
    calculateICTSectionTotals();
}

function updateTableStatus(card) {
    if (!card) return;
    const badge = card.querySelector('.section-status');
    if (!badge) return;

    let hasValue = false;
    card.querySelectorAll('input:not([readonly]), textarea').forEach(input => {
        if (input.disabled || input.type === 'button' || input.type === 'submit') return;
        if (input.value && input.value.trim() !== '' && input.value !== '0') {
            hasValue = true;
        }
    });

    badge.className = hasValue ? 'section-status status-complete' : 'section-status status-progress';
    badge.textContent = hasValue ? 'Completed' : 'In Progress';
}

function initInputStatusHandlers() {
    document.querySelectorAll('.visa-card').forEach(card => {
        if (card.id === 'staffStrengthCard' || card.id === 'idCardsCard') return;

        card.querySelectorAll('input:not([readonly]), textarea').forEach(input => {
            input.addEventListener('input', () => updateTableStatus(card));
            input.addEventListener('change', () => updateTableStatus(card));
        });

        updateTableStatus(card);
    });
}

/*=========================================================================
    SECTION SAVE SPINNERS
=========================================================================*/
function initSectionSaves() {
    document.querySelectorAll('.section-buttons .btn-primary-nis').forEach(saveBtn => {
        saveBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const button = e.currentTarget;
            const originalHtml = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            button.disabled = true;

            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-check"></i> Saved';
                button.className = 'btn-nis btn-success';

                const card = button.closest('.visa-card');
                if (card) {
                    const badge = card.querySelector('.section-status');
                    if (badge) {
                        badge.className = 'section-status status-complete';
                        badge.textContent = 'Completed';
                    }
                }

                setTimeout(() => {
                    button.innerHTML = originalHtml;
                    button.className = 'btn-nis btn-primary-nis';
                    button.disabled = false;
                }, 1500);
            }, 800);
        });
    });
}

function initSectionResets() {
    document.addEventListener('click', (e) => {
        const resetBtn = e.target.closest('.section-buttons button.btn-ghost, button[id^="reset"]');
        if (!resetBtn) return;

        const card = resetBtn.closest('.visa-card');
        if (!card) return;

        e.preventDefault();
        resetCardSection(card);
    });
}

function resetCardSection(card) {
    if (!card) return;

    // 1. Reset inputs to default values
    const inputs = card.querySelectorAll('input:not([readonly]), textarea, select');
    inputs.forEach(input => {
        if (input.type === 'number') {
            input.value = 0;
        } else if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        } else {
            input.value = '';
        }
    });

    // 2. For dynamic tables, remove all rows except the first row
    const tbodies = card.querySelectorAll('tbody');
    tbodies.forEach(tbody => {
        if (tbody.id === 'staff_strength_tbody') {
            // Staff strength rows are static ranks, just reset their values
            tbody.querySelectorAll('input:not([readonly])').forEach(input => {
                input.value = 0;
            });
        } else {
            // For other tbodies, keep the first row and delete the rest
            const rows = tbody.querySelectorAll('tr');
            rows.forEach((row, idx) => {
                if (idx > 0) {
                    row.remove();
                } else {
                    // Clear the first row's inputs
                    row.querySelectorAll('input:not([readonly]), textarea, select').forEach(input => {
                        if (input.type === 'number') {
                            input.value = 0;
                        } else if (input.tagName === 'SELECT') {
                            input.selectedIndex = 0;
                        } else {
                            input.value = '';
                        }
                    });
                }
            });
            // Reindex table S/N if sn-cell exists
            const snCells = tbody.querySelectorAll('.sn-cell');
            snCells.forEach((cell, i) => { cell.textContent = i + 1; });
        }
    });

    // 3. Recalculate totals
    if (card.id === 'staffStrengthCard') {
        calculateStaffStrength();
    } else if (card.id === 'idCardsCard') {
        calculateIdCards();
    } else {
        calculateICTSectionTotals();
    }

    // 4. Update status badge
    updateTableStatus(card);
}
