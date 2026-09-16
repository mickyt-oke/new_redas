
(function() {
    const form = document.querySelector('main form') || document.querySelector('form[action*="directorates"]');

    /* Tab navigation (previous/next), draft save/restore and submit are wired
       globally in user.directorates._layout for all directorate forms. */

    /* Helpers */
    function val(el) { return parseInt(el?.value || 0) || 0; }
    function set(id, v) { const el = document.getElementById(id); if (el) el.value = v; }
    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c];
        });
    }

    const LOCATION_COLS = ['shq','zone_a','zone_b','zone_c','zone_d','zone_e','zone_f','zone_g','zone_h','icsc','itsk','nitsol','nitsa'];
    const LOCATION_LABELS = ['SHQ',"ZONE 'A'","ZONE 'B'","ZONE 'C'","ZONE 'D'","ZONE 'E'","ZONE 'F'","ZONE 'G'","ZONE 'H'",'ICSC','ITSK','NITSOL','NITSA'];

    /* Section 1: Cadre */
    function recomputeCadre() {
        let mT = 0, fT = 0;
        document.querySelectorAll('.ict-cadre-m').forEach(m => {
            const row = m.dataset.row;
            const mV = val(m), fV = val(document.querySelector(`.ict-cadre-f[data-row="${row}"]`));
            set(`ict-cadre-total-${row}`, mV + fV);
            mT += mV; fT += fV;
        });
        set('ict-cadre-grand-male', mT);
        set('ict-cadre-grand-female', fT);
        set('ict-cadre-grand-total', mT + fT);
    }

     /* Section 2: Rank */
     function recomputeRank() {
        let mT = 0, fT = 0;
        document.querySelectorAll('.ict-rank-m').forEach(m => {
            const row = m.dataset.row;
            const mV = val(m), fV = val(document.querySelector(`.ict-rank-f[data-row="${row}"]`));
            set(`ict-rank-total-${row}`, mV + fV);
            mT += mV; fT += fV;
        });
        set('ict-rank-grand-male', mT);
        set('ict-rank-grand-female', fT);
        set('ict-rank-grand-total', mT + fT);
    }

    /* Section 7: rank-by-location matrix (E-Documentation/ID Card Activities) */
    function recomputeMatrix(prefix) {
        const rowTotals = {}, colTotals = {};
        let grand = 0;
        document.querySelectorAll(`.${prefix}-cell`).forEach(cell => {
            const v = val(cell);
            rowTotals[cell.dataset.rank] = (rowTotals[cell.dataset.rank] || 0) + v;
            colTotals[cell.dataset.loc] = (colTotals[cell.dataset.loc] || 0) + v;
            grand += v;
        });
        Object.keys(rowTotals).forEach(rank => set(`${prefix}-total-${rank}`, rowTotals[rank]));
        LOCATION_COLS.forEach(loc => set(`${prefix}-col-${loc}`, colTotals[loc] || 0));
        set(`${prefix}-grand-total`, grand);
    }

    /* Dynamic rows: clone the last row of a tbody, clear it and bump field indices */
    function renumber(tbody) {
        tbody.querySelectorAll('.ict-sn').forEach((cell, i) => { cell.textContent = i + 1; });
    }
    function addRow(tbodyId) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        const rows = tbody.querySelectorAll('tr');
        if (!rows.length) return;
        const clone = rows[rows.length - 1].cloneNode(true);
        const newIdx = rows.length;
        clone.querySelectorAll('input, textarea, select').forEach(el => {
            if (el.name) el.name = el.name.replace(/\[(\d+)\]/, `[${newIdx}]`);
            if (el.type === 'checkbox' || el.type === 'radio') el.checked = false;
            else el.value = '';
        });
        tbody.appendChild(clone);
        renumber(tbody);
    }

    function addDocumentRow() {
        const container = document.getElementById('documents-body');
        const div = document.createElement('div');
        div.className = 'auth-form-group';
        div.style.marginBottom = '12px';
        div.innerHTML = '<input type="file" name="supporting_documents[]" class="ni" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg">';
        container.appendChild(div);
    }

    /* Preview builder helpers */
    function previewSectionTitle(num, title) {
        return `<div class="hrm-preview-section-title">${num}. ${title}</div>`;
    }
    function previewTable(rows, headers = ['Item', 'Value']) {
        let thead = headers.map(h => `<th style="padding:6px 8px;border:1px solid var(--gray-200);background:#f8fafc;">${h}</th>`).join('');
        let tbody = rows.length
            ? rows.map(r => `<tr>${r.map(c => `<td style="padding:6px 8px;border:1px solid var(--gray-200);">${c}</td>`).join('')}</tr>`).join('')
            : `<tr><td colspan="${headers.length}" style="padding:6px 8px;border:1px solid var(--gray-200);color:var(--gray-500);">No entries.</td></tr>`;
        return `<div style="overflow-x:auto;"><table class="hrm-preview-table" style="margin-bottom:12px;"><thead><tr>${thead}</tr></thead><tbody>${tbody}</tbody></table></div>`;
    }
    function getVal(name) { return document.querySelector(`[name="${name}"]`)?.value || '0'; }
    function getValById(id) { return document.getElementById(id)?.value || '0'; }

    /* Collect non-empty rows from a dynamic tbody as arrays of escaped values */
    function tableRows(tbodyId) {
        const rows = [];
        document.querySelectorAll(`#${tbodyId} tr`).forEach(tr => {
            const inputs = tr.querySelectorAll('input, textarea, select');
            const vals = Array.from(inputs).map(i => esc(i.value.trim()));
            if (vals.some(v => v !== '')) rows.push(vals);
        });
        return rows;
    }

    function matrixPreviewRows(prefix) {
        const rows = [];
        const ranks = new Set();
        document.querySelectorAll(`.${prefix}-cell`).forEach(c => ranks.add(c.dataset.rank));
        ranks.forEach(rank => {
            const label = document.querySelector(`.${prefix}-cell[data-rank="${rank}"]`)?.closest('tr')?.querySelector('td')?.textContent.trim() || rank;
            const cells = LOCATION_COLS.map(loc => document.querySelector(`.${prefix}-cell[data-rank="${rank}"][data-loc="${loc}"]`)?.value || '0');
            rows.push([esc(label), ...cells, getValById(`${prefix}-total-${rank}`)]);
        });
        rows.push(['<strong>TOTAL</strong>', ...LOCATION_COLS.map(loc => `<strong>${getValById(`${prefix}-col-${loc}`)}</strong>`), `<strong>${getValById(`${prefix}-grand-total`)}</strong>`]);
        return rows;
    }

    function buildPreview() {
        const container = document.getElementById('hrmPreviewBody');
        if (!container) return;
        let html = '';

        /* 1. Staff Strength by Cadre */
        let cadreRows = [];
        document.querySelectorAll('.ict-cadre-m').forEach(m => {
            const row = m.dataset.row;
            const label = m.closest('tr').querySelector('td').textContent;
            cadreRows.push([
                esc(label),
                m.value || '0',
                document.querySelector(`.ict-cadre-f[data-row="${row}"]`)?.value || '0',
                getValById(`ict-cadre-total-${row}`)
            ]);
        });
        cadreRows.push(['<strong>Grand Total</strong>', `<strong>${getValById('ict-cadre-grand-male')}</strong>`, `<strong>${getValById('ict-cadre-grand-female')}</strong>`, `<strong>${getValById('ict-cadre-grand-total')}</strong>`]);
        html += previewSectionTitle(1, 'Staff Strength by Cadre');
        html += previewTable(cadreRows, ['Cadre', 'Male', 'Female', 'Total']);

        /* 2. Staff Strength by Rank */
        let rankRows = [];
        document.querySelectorAll('.ict-rank-m').forEach(m => {
            const row = m.dataset.row;
            const label = m.closest('tr').querySelector('td').textContent;
            rankRows.push([
                esc(label),
                m.value || '0',
                document.querySelector(`.ict-rank-f[data-row="${row}"]`)?.value || '0',
                getValById(`ict-rank-total-${row}`)
            ]);
        });
        rankRows.push(['<strong>Grand Total</strong>', `<strong>${getValById('ict-rank-grand-male')}</strong>`, `<strong>${getValById('ict-rank-grand-female')}</strong>`, `<strong>${getValById('ict-rank-grand-total')}</strong>`]);
        html += previewSectionTitle(2, 'Staff Strength by Rank');
        html += previewTable(rankRows, ['Rank', 'Male', 'Female', 'Total']);

        /* 3. Incident Report */
        const incidentCategories = [
            ['hardware', 'a. Hardware'],
            ['software', 'b. Software'],
            ['network', 'c. Network'],
            ['cybersecurity', 'd. Cybersecurity'],
            ['power_supply', 'e. Power Supply System'],
            ['communication', 'f. Communication'],
            ['surveillance', 'g. Surveillance']
        ];
        const incidentHeaders = ['Incident', 'Date/Time', 'Systems Affected', 'Cause', 'Action Taken', 'Impact Assessment', 'Recommendation'];
        html += previewSectionTitle(3, 'Incident Report');
        incidentCategories.forEach(([key, label]) => {
            html += `<div style="font-size:.84rem;font-weight:700;margin:10px 0 6px;">${label}</div>`;
            html += previewTable(tableRows(`ict-incident-body-${key}`), incidentHeaders);
        });
        html += `<div style="font-size:.84rem;font-weight:700;margin:10px 0 6px;">h. Technical Services Providers</div>`;
        html += previewTable(tableRows('ict-tsp-body'), ['Name of Service Provider', 'Services provided', ...incidentHeaders]);

        /* 4. Hardware Maintenance */
        html += previewSectionTitle(4, 'Hardware Maintenance');
        html += previewTable(tableRows('ict-hardware-body'), ['Equipment Type', 'Product', 'Status', 'Location']);

        /* 5. Project/Programme Activities */
        html += previewSectionTitle(5, 'Project/Programme Activities');
        html += previewTable(tableRows('ict-projects-body'), ['Project/Programme', 'Type Of System', 'Status Report', 'Remarks']);

        /* 6. Software and Data Management */
        html += previewSectionTitle(6, 'Software and Data Management');
        html += `<div style="font-size:.84rem;font-weight:700;margin:10px 0 6px;">a. Software</div>`;
        html += previewTable(tableRows('ict-software-body'), ['Software Developed', 'Description', 'Status', 'Deployment']);
        html += `<div style="font-size:.84rem;font-weight:700;margin:10px 0 6px;">b. Data</div>`;
        html += previewTable(tableRows('ict-data-body'), ['Data Breached Incident', 'No. of Incident', 'Status', 'Breach Location']);
        html += `<div style="font-size:.84rem;font-weight:700;margin:10px 0 6px;">c. Cybersecurity</div>`;
        html += previewTable(tableRows('ict-cyber-body'), ['Security Deployed', 'Type of Security', 'Incident Report', 'Location', 'Status']);

        /* 7. E-Documentation/ID Card Activities */
        html += previewSectionTitle(7, 'E-Documentation/ID Card Activities');
        html += previewTable(matrixPreviewRows('ict-edoc'), ['Rank', ...LOCATION_LABELS, 'Total']);

        /* 8. MIDAS Deployment */
        html += previewSectionTitle(8, 'MIDAS Deployment');
        html += previewTable(tableRows('ict-midas-body'), ['State Command', 'Location', 'Location', 'Status/Remark']);

        /* 9. Summary statistics + General Report */
        html += previewSectionTitle(9, 'Summary and General Report');
        html += previewTable([
            ['Project/Programme Activities', getVal('project_programme_activities')],
            ['Systems Fully Operational', getVal('systems_operational')],
            ['MIDAS Deployment Sites', getVal('midas_sites_deployed')],
            ['Software Deliverables', getVal('software_delivered')],
            ['Hardware Maintenance Cases', getVal('hardware_maintenance_cases')],
            ['Data Breach Incidents', getVal('data_breach_incidents')],
            ['Cybersecurity Incidents', getVal('cybersecurity_incidents')],
            ['Security Controls Deployed', getVal('security_controls_deployed')]
        ], ['Item', 'Value']);

        const generalReportFields = [
            ['Other Reports', 'ict_other_reports', 'No other reports.'],
            ['Challenges', 'general_report[challenges]', 'No challenges reported.'],
            ['Recommendations', 'general_report[recommendations]', 'No recommendations provided.'],
            ['Conclusion', 'general_report[conclusion]', 'No conclusion provided.']
        ];
        generalReportFields.forEach(([label, name, emptyText]) => {
            html += `<div style="margin-top:12px;"><strong>${label}:</strong></div>`;
            const value = document.querySelector(`[name="${name}"]`)?.value.trim() || '';
            if (!value) {
                html += `<p style="font-size:.82rem;color:var(--gray-600);margin-top:4px;">${emptyText}</p>`;
            } else {
                html += `<p style="font-size:.82rem;color:var(--gray-800);margin-top:4px;white-space:pre-wrap;">${esc(value)}</p>`;
            }
        });
        html += `<div style="margin-top:12px;"><strong>Supporting Documents:</strong></div>`;
        const docInputs = Array.from(document.querySelectorAll('#documents-body input[type="file"]'))
            .filter(input => input.files && input.files.length > 0);
        if (docInputs.length === 0) {
            html += `<p style="font-size:.82rem;color:var(--gray-600);margin-top:4px;">No supporting documents uploaded.</p>`;
        } else {
            html += `<ul style="margin-top:4px;">`;
            docInputs.forEach((input, idx) => {
                html += `<li style="font-size:.82rem;color:var(--gray-800);">Document ${idx + 1}: ${esc(input.files[0].name)}</li>`;
            });
            html += `</ul>`;
        }

        container.innerHTML = html;
    }

    /* Master recompute */
    function recomputeAll() {
        recomputeCadre();
        recomputeRank();
        recomputeMatrix('ict-edoc');
        buildPreview();
    }

    /* Bindings */
    document.querySelectorAll('.ict-add-row').forEach(btn => {
        btn.addEventListener('click', () => addRow(btn.dataset.target));
    });
    document.getElementById('ictAddDocumentRow')?.addEventListener('click', addDocumentRow);

    /* Submit from preview is wired globally in the shared layout. */

    /* Listen for input */
    if (form) form.addEventListener('input', recomputeAll);

    /* Let the layout's preview tab use this page-specific renderer. */
    window.buildDirectoratePreview = buildPreview;

    /* Init */
    recomputeAll();
})();
