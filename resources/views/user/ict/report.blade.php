{{-- ==========================================================
     ICT & Cybersecurity Directorate
     Annual Report Workspace
========================================================== --}}

@include('partials.ict-header')

<div class="page-header animate-fade-up">
    <div>
        <h1 class="page-title">
            <i class="fas fa-file-alt"></i>
            Annual ICT &amp; Cybersecurity Report
        </h1>
        <p class="page-subtitle">
            Complete every reporting section before submitting the Annual Return.
        </p>
    </div>
</div>

<form id="ictAnnualReport" method="POST" action="{{ route('ict.store') }}">
    @csrf

    {{-- =========================================
        GENERAL INFORMATION
    ========================================== --}}
    <section id="general-information">
        @include('user.ict.partials.general-information')
    </section>

    {{-- =========================================
        1. STAFF STRENGTH
    ========================================== --}}
    <section id="staff-strength">
        @include('user.ict.partials.staff-strength')
    </section>

    {{-- =========================================
        2. PROJECT/PROGRAMME ACTIVITIES
    ========================================== --}}
    <section id="projects">
        @include('user.ict.partials.projects')
    </section>

    {{-- =========================================
        3. INCIDENT: (Hardware)
    ========================================== --}}
    <section id="incidents-hardware">
        @include('user.ict.partials.incidents-hardware')
    </section>

    {{-- =========================================
        4. INCIDENT: (Software)
    ========================================== --}}
    <section id="incidents-software">
        @include('user.ict.partials.incidents-software')
    </section>

    {{-- =========================================
        5. INCIDENT: (Network)
    ========================================== --}}
    <section id="incidents-network">
        @include('user.ict.partials.incidents-network')
    </section>

    {{-- =========================================
        6. INCIDENT: (Cybersecurity)
    ========================================== --}}
    <section id="incidents-cybersecurity">
        @include('user.ict.partials.incidents-cybersecurity')
    </section>

    {{-- =========================================
        7. INCIDENT: (Power Supply System)
    ========================================== --}}
    <section id="incidents-power">
        @include('user.ict.partials.incidents-power')
    </section>

    {{-- =========================================
        8. INCIDENT: (Communication)
    ========================================== --}}
    <section id="incidents-communication">
        @include('user.ict.partials.incidents-communication')
    </section>

    {{-- =========================================
        9. INCIDENT: (Surveillance)
    ========================================== --}}
    <section id="incidents-surveillance">
        @include('user.ict.partials.incidents-surveillance')
    </section>

    {{-- =========================================
        10. INCIDENT: (Technical Services Providers)
    ========================================== --}}
    <section id="incidents-providers">
        @include('user.ict.partials.incidents-providers')
    </section>

    {{-- =========================================
        11. HARDWARE MAINTENANCE
    ========================================== --}}
    <section id="hardware-maintenance">
        @include('user.ict.partials.hardware-maintenance')
    </section>

    {{-- =========================================
        12. SOFTWARE AND DATA MANAGEMENT
    ========================================== --}}
    <section id="software-data">
        @include('user.ict.partials.software-data')
    </section>

    {{-- =========================================
        13. CYBERSECURITY
    ========================================== --}}
    <section id="cybersecurity-deployment">
        @include('user.ict.partials.cybersecurity-deployment')
    </section>

    {{-- =========================================
        14. E-DOCUMENTATION/ID CARD ACTIVITIES
    ========================================== --}}
    <section id="id-cards">
        @include('user.ict.partials.id-cards')
    </section>

    {{-- =========================================
        15. MIDAS DEPLOYMENT
    ========================================== --}}
    <section id="midas-deployment">
        @include('user.ict.partials.midas-deployment')
    </section>

    <div class="redas-card mt-4 animate-fade-up">
        <div class="card-body">
            <div class="visa-actions">
                <button type="reset" class="btn-nis btn-ghost">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
                <button type="submit" name="action" value="draft" class="btn-nis btn-success">
                    <i class="fas fa-save"></i> Save Draft
                </button>
                <button type="submit" name="action" value="submit" class="btn-nis btn-primary-nis">
                    <i class="fas fa-paper-plane"></i> Submit Annual Report
                </button>
            </div>
        </div>
    </div>
</form>

<script>
(function() {
    function initIctWorkspace() {
        calculateIdCardsAuto();
        calculateStaffStrengthAuto();
        calculateTableCounts();
        calculateDataBreachTotal();
    }

    // ── 1. ID CARDS AUTOMATIC CALCULATION ──
    function calculateIdCardsAuto() {
        const inputs = document.querySelectorAll('.id-card-input');
        if (!inputs.length) return;

        const rowTotals = {};
        const colTotals = {};
        let grandTotal = 0;

        inputs.forEach(input => {
            const row = input.getAttribute('data-row');
            const col = input.getAttribute('data-col');
            let val = parseInt(input.value) || 0;
            if (val < 0) { val = 0; input.value = 0; }

            if (row) rowTotals[row] = (rowTotals[row] || 0) + val;
            if (col) colTotals[col] = (colTotals[col] || 0) + val;
            grandTotal += val;
        });

        Object.keys(rowTotals).forEach(row => {
            const el = document.getElementById('row_total_' + row);
            if (el) el.value = rowTotals[row];
        });

        Object.keys(colTotals).forEach(col => {
            const el = document.getElementById('col_total_' + col);
            if (el) el.value = colTotals[col];
        });

        const grandEl = document.getElementById('id_card_grand_total');
        if (grandEl) grandEl.value = grandTotal;

        const summaryEl = document.getElementById('idCardSummary');
        if (summaryEl) summaryEl.innerHTML = '<strong>' + grandTotal.toLocaleString() + ' Cards</strong>';

        const statusEl = document.getElementById('idCardsStatus');
        if (statusEl) {
            statusEl.className = grandTotal > 0 ? 'section-status status-complete' : 'section-status status-progress';
            statusEl.textContent = grandTotal > 0 ? 'Completed' : 'In Progress';
        }
    }

    // ── 2. STAFF STRENGTH AUTOMATIC CALCULATION ──
    function calculateStaffStrengthAuto() {
        const tbody = document.getElementById('staff_strength_tbody');
        if (!tbody) return;

        let grandMale = 0;
        let grandFemale = 0;
        let grandTotal = 0;

        tbody.querySelectorAll('tr').forEach(row => {
            const maleInput = row.querySelector('.staff-input.male');
            const femaleInput = row.querySelector('.staff-input.female');
            const totalInput = row.querySelector('.total-input');

            if (maleInput && femaleInput) {
                let mVal = Math.max(0, parseInt(maleInput.value) || 0);
                let fVal = Math.max(0, parseInt(femaleInput.value) || 0);
                maleInput.value = mVal;
                femaleInput.value = fVal;
                let rowTotal = mVal + fVal;
                if (totalInput) totalInput.value = rowTotal;

                grandMale += mVal;
                grandFemale += fVal;
                grandTotal += rowTotal;
            }
        });

        const gm = document.getElementById('grandMale');
        if (gm) gm.value = grandMale;
        const gf = document.getElementById('grandFemale');
        if (gf) gf.value = grandFemale;
        const gt = document.getElementById('grandTotal');
        if (gt) gt.value = grandTotal;

        const summary = document.getElementById('staffSummary');
        if (summary) summary.innerHTML = '<strong>' + grandTotal + ' Officers</strong>';

        const statusEl = document.getElementById('staffStatus');
        if (statusEl) {
            statusEl.className = grandTotal > 0 ? 'section-status status-complete' : 'section-status status-progress';
            statusEl.textContent = grandTotal > 0 ? 'Completed' : 'In Progress';
        }
    }

    // ── 2b. DYNAMIC TABLE COUNTS & DATA BREACH TOTAL ──
    function calculateTableCounts() {
        const tables = [
            { tbody: 'projects_tbody', total: 'projectsTotal', summary: 'projectsSummary', suffix: ' Items', card: 'projectsCard', badge: 'projectsStatus' },
            { tbody: 'incidents_hardware_tbody', total: 'incidentsHardwareTotal', summary: 'incidentsHardwareSummary', suffix: ' Incidents', card: 'incidentsHardwareCard', badge: 'incidentsHardwareStatus' },
            { tbody: 'incidents_software_tbody', total: 'incidentsSoftwareTotal', summary: 'incidentsSoftwareSummary', suffix: ' Incidents', card: 'incidentsSoftwareCard', badge: 'incidentsSoftwareStatus' },
            { tbody: 'incidents_network_tbody', total: 'incidentsNetworkTotal', summary: 'incidentsNetworkSummary', suffix: ' Incidents', card: 'incidentsNetworkCard', badge: 'incidentsNetworkStatus' },
            { tbody: 'incidents_cybersecurity_tbody', total: 'incidentsCybersecurityTotal', summary: 'incidentsCybersecuritySummary', suffix: ' Incidents', card: 'incidentsCybersecurityCard', badge: 'incidentsCybersecurityStatus' },
            { tbody: 'incidents_power_tbody', total: 'incidentsPowerTotal', summary: 'incidentsPowerSummary', suffix: ' Incidents', card: 'incidentsPowerCard', badge: 'incidentsPowerStatus' },
            { tbody: 'incidents_communication_tbody', total: 'incidentsCommunicationTotal', summary: 'incidentsCommunicationSummary', suffix: ' Incidents', card: 'incidentsCommunicationCard', badge: 'incidentsCommunicationStatus' },
            { tbody: 'incidents_surveillance_tbody', total: 'incidentsSurveillanceTotal', summary: 'incidentsSurveillanceSummary', suffix: ' Incidents', card: 'incidentsSurveillanceCard', badge: 'incidentsSurveillanceStatus' },
            { tbody: 'incidents_providers_tbody', total: 'incidentsProvidersTotal', summary: 'incidentsProvidersSummary', suffix: ' Incidents', card: 'incidentsProvidersCard', badge: 'incidentsProvidersStatus' },
            { tbody: 'maintenance_tbody', total: 'maintenanceTotal', summary: 'maintenanceSummary', suffix: ' Records', card: 'hardwareMaintenanceCard', badge: 'hardwareMaintenanceStatus' },
            { tbody: 'software_tbody', total: 'softwareTotal', summary: 'softwareDataSummary', suffix: ' Records', card: 'softwareDataCard', badge: 'softwareDataStatus', combineWith: 'data_breach_tbody' },
            { tbody: 'cybersecurity_tbody', total: 'cybersecurityTotal', summary: 'cybersecuritySummary', suffix: ' Deployments', card: 'cybersecurityDeploymentCard', badge: 'cybersecurityDeploymentStatus' },
            { tbody: 'midas_tbody', total: 'midasTotal', summary: 'midasSummary', suffix: ' Locations', card: 'midasDeploymentCard', badge: 'midasDeploymentStatus' }
        ];

        tables.forEach(t => {
            const tbody = document.getElementById(t.tbody);
            if (tbody) {
                const count = tbody.querySelectorAll('tr').length;
                const totalEl = document.getElementById(t.total);
                if (totalEl) totalEl.value = count;

                // Set summary
                if (t.summary && !t.combineWith) {
                    const summaryEl = document.getElementById(t.summary);
                    if (summaryEl) summaryEl.innerHTML = '<strong>' + count + t.suffix + '</strong>';
                }

                // Set badge
                if (t.badge) {
                    const badgeEl = document.getElementById(t.badge);
                    if (badgeEl) {
                        badgeEl.className = count > 0 ? 'section-status status-complete' : 'section-status status-progress';
                        badgeEl.textContent = count > 0 ? 'Completed' : 'In Progress';
                    }
                }
            }
        });

        // Combine Software & Data Breach count
        const softBody = document.getElementById('software_tbody');
        const breachBody = document.getElementById('data_breach_tbody');
        if (softBody && breachBody) {
            const softCount = softBody.querySelectorAll('tr').length;
            const breachCount = breachBody.querySelectorAll('tr').length;
            const sumEl = document.getElementById('softwareDataSummary');
            if (sumEl) sumEl.innerHTML = '<strong>' + (softCount + breachCount) + ' Records</strong>';
            
            const badgeEl = document.getElementById('softwareDataStatus');
            if (badgeEl) {
                const hasData = (softCount + breachCount) > 0;
                badgeEl.className = hasData ? 'section-status status-complete' : 'section-status status-progress';
                badgeEl.textContent = hasData ? 'Completed' : 'In Progress';
            }
        }
    }

    function calculateDataBreachTotal() {
        const tbody = document.getElementById('data_breach_tbody');
        if (!tbody) return;
        let total = 0;
        tbody.querySelectorAll('tr').forEach(row => {
            const input = row.querySelector('input[type="number"]');
            if (input) {
                let val = parseInt(input.value) || 0;
                if (val < 0) { val = 0; input.value = 0; }
                total += val;
            }
        });
        const totalEl = document.getElementById('dataBreachCountTotal');
        if (totalEl) totalEl.value = total;
    }

    // ── 3. INPUT EVENT LISTENERS FOR AUTO CALCULATION ──
    document.addEventListener('input', function(e) {
        if (e.target && e.target.classList.contains('id-card-input')) {
            calculateIdCardsAuto();
        }
        if (e.target && e.target.classList.contains('staff-input')) {
            calculateStaffStrengthAuto();
        }
        if (e.target && e.target.closest('#data_breach_tbody')) {
            calculateDataBreachTotal();
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('id-card-input')) {
            calculateIdCardsAuto();
        }
        if (e.target && e.target.classList.contains('staff-input')) {
            calculateStaffStrengthAuto();
        }
        if (e.target && e.target.closest('#data_breach_tbody')) {
            calculateDataBreachTotal();
        }
    });

    document.addEventListener('keyup', function(e) {
        if (e.target && e.target.classList.contains('id-card-input')) {
            calculateIdCardsAuto();
        }
        if (e.target && e.target.classList.contains('staff-input')) {
            calculateStaffStrengthAuto();
        }
        if (e.target && e.target.closest('#data_breach_tbody')) {
            calculateDataBreachTotal();
        }
    });

    // ── 4. ACCORDION & PROGRESS TOGGLE MINIMISE SECTIONS ──
    document.addEventListener('click', function(e) {
        const statusBadge = e.target.closest('.section-status');
        const headerBtn = e.target.closest('.visa-accordion-header');

        if (statusBadge) {
            e.stopImmediatePropagation();
            e.stopPropagation();
            e.preventDefault();
            const card = statusBadge.closest('.visa-card');
            if (card) {
                const isCollapsed = card.classList.toggle('collapsed');
                const icon = card.querySelector('.accordion-icon');
                if (icon) {
                    icon.classList.toggle('fa-chevron-down', !isCollapsed);
                    icon.classList.toggle('fa-chevron-right', isCollapsed);
                }
            }
            return;
        }

        if (headerBtn) {
            e.stopImmediatePropagation();
            const card = headerBtn.closest('.visa-card');
            if (card) {
                const isCollapsed = card.classList.toggle('collapsed');
                const icon = card.querySelector('.accordion-icon');
                if (icon) {
                    icon.classList.toggle('fa-chevron-down', !isCollapsed);
                    icon.classList.toggle('fa-chevron-right', isCollapsed);
                }
            }
        }
    });

    // ── 5. + ADD ROW & DELETE ROW DELEGATION ──
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-ict-target], [data-target]');
        if (btn) {
            const targetId = btn.getAttribute('data-ict-target') || btn.getAttribute('data-target');
            const tbody = document.getElementById(targetId);
            if (tbody) {
                e.preventDefault();
                e.stopPropagation();

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
                    calculateStaffStrengthAuto();
                    return;
                }

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
                calculateTableCounts();
                calculateDataBreachTotal();
                return;
            }
        }

        const deleteBtn = e.target.closest('.btn-delete-row');
        if (deleteBtn) {
            e.preventDefault();
            e.stopPropagation();
            const tr = deleteBtn.closest('tr');
            if (tr) {
                const tbody = tr.parentNode;
                tr.remove();
                if (tbody && tbody.id === 'staff_strength_tbody') {
                    calculateStaffStrengthAuto();
                } else if (tbody) {
                    tbody.querySelectorAll('tr').forEach((r, idx) => {
                        const snCell = r.querySelector('.sn-cell');
                        if (snCell) snCell.textContent = idx + 1;
                    });
                    calculateTableCounts();
                    calculateDataBreachTotal();
                }
            }
        }
    });

    // Reset button handlers
    document.addEventListener('click', function(e) {
        const resetBtn = e.target.closest('.section-buttons button.btn-ghost, button[id^="reset"]');
        if (resetBtn) {
            e.preventDefault();
            const card = resetBtn.closest('.visa-card');
            if (card) {
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
                    calculateStaffStrengthAuto();
                } else if (card.id === 'idCardsCard') {
                    calculateIdCardsAuto();
                } else {
                    calculateTableCounts();
                    calculateDataBreachTotal();
                    const event = new Event('input', { bubbles: true });
                    card.dispatchEvent(event);
                }

                // 4. Update status badge
                const badge = card.querySelector('.section-status');
                if (badge) {
                    badge.className = 'section-status status-progress';
                    badge.textContent = 'In Progress';
                }
            }
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initIctWorkspace);
    } else {
        initIctWorkspace();
    }
})();
</script>

@include('partials.footer')
