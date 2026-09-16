
    (function () {
        /* Sidebar / topbar toggles */
        const sidebar = document.getElementById('redasSidebar');
        const main = document.getElementById('redasMain');
        const overlay = document.getElementById('sidebarOverlay');
        const toggle = document.getElementById('sidebarToggle');
        toggle?.addEventListener('click', () => {
            if (window.innerWidth <= 768) { sidebar.classList.toggle('mobile-open'); overlay.classList.toggle('active'); }
            else { sidebar.classList.toggle('collapsed'); main.classList.toggle('sidebar-collapsed'); }
        });
        overlay?.addEventListener('click', () => { sidebar.classList.remove('mobile-open'); overlay.classList.remove('active'); });

        const userBtn = document.getElementById('userMenuBtn');
        const userDrop = document.getElementById('userMenuDrop');
        userBtn?.addEventListener('click', e => { e.stopPropagation(); userDrop.style.display = userDrop.style.display === 'block' ? 'none' : 'block'; });
        document.addEventListener('click', () => { userDrop && (userDrop.style.display = 'none'); });

        /* Expand rows */
        document.querySelectorAll('.expand-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const row = document.getElementById('expand-' + btn.dataset.row);
                const chv = document.getElementById('chevron-' + btn.dataset.row);
                if (!row) return;
                const open = row.classList.toggle('open');
                chv.style.transform = open ? 'rotate(90deg)' : '';
                chv.style.transition = 'transform .2s';
            });
        });

        /* Filter chips */
        document.querySelectorAll('.filter-chip').forEach(chip => {
            chip.addEventListener('click', () => {
                document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                const f = chip.dataset.filter;
                document.querySelectorAll('.sub-main-row').forEach(row => {
                    row.style.display = (f === 'all' || row.dataset.status === f) ? '' : 'none';
                    const exp = document.getElementById('expand-' + row.querySelector('.expand-btn')?.dataset.row);
                    if (exp && row.style.display === 'none') exp.classList.remove('open');
                });
            });
        });

        /* Search */
        document.getElementById('subSearch')?.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.sub-main-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });

        /* Detail modal */
        document.querySelectorAll('[data-bs-target="#detailModal"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const s = btn.dataset.status;
                document.getElementById('detailTitle').textContent = btn.dataset.period + ' — ' + btn.dataset.type;
                document.getElementById('detailSub').textContent = 'Command: ' + btn.dataset.path;
                document.getElementById('dType').textContent = btn.dataset.type;
                document.getElementById('dDate').textContent = btn.dataset.date;
                document.getElementById('dReviewer').textContent = btn.dataset.reviewer;
                document.getElementById('dRemarks').textContent = btn.dataset.remarks;

                const statusEl = document.getElementById('dStatus');
                statusEl.innerHTML = `<span class="status-badge ${btn.dataset.badge}">${s}</span>`;

                const notifEl = document.getElementById('dNotification');
                const notifCfg = {
                    'Queried': ['#fff7ed', '#fed7aa', '#c2410c', 'fa-exclamation-triangle', 'Action Required', 'Your return has been queried. Please review the remarks and resubmit.'],
                    'Approved': ['#f0fdf4', '#bbf7d0', '#15803d', 'fa-check-circle', 'Return Approved', 'Your return has been reviewed and approved by your supervisor.'],
                    'Rejected': ['#fff1f2', '#fecaca', '#dc2626', 'fa-times-circle', 'Return Rejected', 'Your return was rejected. Review remarks and contact your supervisor.'],
                    'Pending Review': ['#eff6ff', '#bfdbfe', '#1d4ed8', 'fa-info-circle', 'Awaiting Review', 'Your return is in the supervisor\'s queue for review.'],
                };
                const cfg = notifCfg[s];
                if (cfg) {
                    notifEl.style.cssText = `display:flex;background:${cfg[0]};border:1px solid ${cfg[1]};color:${cfg[2]};border-radius:var(--radius-md);padding:12px 16px;font-size:.84rem;margin-bottom:16px;gap:10px;align-items:flex-start;`;
                    document.getElementById('dNotifIcon').className = `fas ${cfg[3]}`;
                    document.getElementById('dNotifTitle').textContent = cfg[4];
                    document.getElementById('dNotifMsg').textContent = cfg[5];
                } else {
                    notifEl.style.display = 'none';
                }

                const editBtn = document.getElementById('editBtn');
                editBtn.style.display = (s === 'Queried') ? '' : 'none';
            });
        });

        document.getElementById('downloadBtn')?.addEventListener('click', () => {
            if (window.REDAS) REDAS.showToast('Preparing PDF download…', 'info');
        });
    })();
