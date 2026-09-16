
    /* Notification panel toggle */
    const notifBtn = document.getElementById('notifBtn');
    const notifPanel = document.getElementById('notifPanel');
    if (notifBtn && notifPanel) {
        notifBtn.addEventListener('click', e => { e.stopPropagation(); notifPanel.style.display = notifPanel.style.display === 'block' ? 'none' : 'block'; });
        document.addEventListener('click', () => { notifPanel.style.display = 'none'; });
    }

    /* User menu toggle */
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userMenuDrop = document.getElementById('userMenuDrop');
    if (userMenuBtn && userMenuDrop) {
        userMenuBtn.addEventListener('click', e => { e.stopPropagation(); userMenuDrop.style.display = userMenuDrop.style.display === 'block' ? 'none' : 'block'; });
        document.addEventListener('click', () => { userMenuDrop.style.display = 'none'; });
    }

    /* Sidebar toggle */
    const sidebar = document.getElementById('redasSidebar');
    const main = document.getElementById('redasMain');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle = document.getElementById('sidebarToggle');

    if (toggle) {
        toggle.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
                main.classList.toggle('sidebar-collapsed');
            }
        });
    }
    if (overlay) {
        overlay.addEventListener('click', () => { sidebar.classList.remove('mobile-open'); overlay.classList.remove('active'); });
    }

    /* View modal population */
    document.querySelectorAll('[data-action="view"]').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('modalTitle').textContent = btn.dataset.title || '—';
            document.getElementById('modalDate').textContent = btn.dataset.date || '—';
            document.getElementById('modalType').textContent = btn.dataset.type || '—';
            document.getElementById('modalRemarks').textContent = btn.dataset.remarks || 'No remarks provided.';
            const s = btn.dataset.status || '';
            const statusEl = document.getElementById('modalStatus');
            const cls = s.toLowerCase().includes('approved') ? 'badge-approved'
                : s.toLowerCase().includes('pending') ? 'badge-pending'
                    : s.toLowerCase().includes('reject') || s.toLowerCase().includes('return') ? 'badge-rejected'
                        : 'badge-draft';
            statusEl.innerHTML = `<span class="status-badge ${cls}">${s}</span>`;
        });
    });
