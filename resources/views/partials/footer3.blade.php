
    <footer style="padding:16px 24px;border-top:1px solid var(--gray-100);text-align:center;font-size:.72rem;color:var(--gray-400);">
        NIS-REDAS v2.0 &mdash; Nigeria Immigration Service &bull; ICT Directorate &copy; {{ date('Y') }}
        &nbsp;|&nbsp; <span style="color:var(--color-success);">● All Systems Operational</span>
        &nbsp;|&nbsp; Last data sync: {{ now()->format('H:i') }} WAT
    </footer>
</div>

<script>
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
    if (overlay) overlay.addEventListener('click', () => { sidebar.classList.remove('mobile-open'); overlay.classList.remove('active'); });

    /* Notification panel */
    const notifBtn = document.getElementById('notifBtn');
    const notifPanel = document.getElementById('notifPanel');
    if (notifBtn && notifPanel) {
        notifBtn.addEventListener('click', e => { e.stopPropagation(); notifPanel.style.display = notifPanel.style.display === 'block' ? 'none' : 'block'; });
        document.addEventListener('click', () => { notifPanel.style.display = 'none'; });
    }

    /* User menu */
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userMenuDrop = document.getElementById('userMenuDrop');
    if (userMenuBtn && userMenuDrop) {
        userMenuBtn.addEventListener('click', e => { e.stopPropagation(); userMenuDrop.style.display = userMenuDrop.style.display === 'block' ? 'none' : 'block'; });
        document.addEventListener('click', () => { userMenuDrop.style.display = 'none'; });
    }

    /* Period buttons */
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.card-head').querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });
</script>
</body>
</html>
