
    (function () {
        /* Sidebar / topbar */
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

        /* Mark single as read */
        window.markRead = function (card) {
            card.classList.remove('unread', 'type-danger', 'type-warning', 'type-success', 'type-info');
            card.dataset.unread = '0';
            const dot = card.querySelector('.unread-dot');
            if (dot) dot.remove();
            updateCounts();
        };

        /* Mark all read */
        document.getElementById('markAllReadBtn')?.addEventListener('click', () => {
            document.querySelectorAll('.notif-card.unread').forEach(c => markRead(c));
            if (window.REDAS) REDAS.showToast('All notifications marked as read.', 'success');
        });

        /* Counts */
        function updateCounts() {
            const unread = document.querySelectorAll('.notif-card[data-unread="1"]').length;
            const cnt = document.getElementById('cntUnread');
            if (cnt) cnt.textContent = unread;
        }

        /* Filters */
        document.querySelectorAll('.notif-filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.notif-filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const f = btn.dataset.filter;
                let visible = 0;
                document.querySelectorAll('.notif-card').forEach(card => {
                    let show = false;
                    if (f === 'all') show = true;
                    else if (f === 'unread') show = card.dataset.unread === '1';
                    else show = card.dataset.type === f;
                    card.style.display = show ? '' : 'none';
                    if (show) visible++;
                });
                document.getElementById('emptyState').style.display = visible === 0 ? '' : 'none';
            });
        });
    })();
