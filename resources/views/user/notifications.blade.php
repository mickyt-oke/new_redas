@include ('partials.header')
<main class="redas-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Notifications</h1>
            <p class="page-subtitle">System alerts, workflow updates, and deadline reminders.</p>
        </div>
        <button class="btn-nis btn-ghost btn-sm" id="markAllReadBtn">
            <i class="fas fa-check-double"></i> Mark all read
        </button>
    </div>

    <!-- Filter bar -->
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
        <button class="notif-filter-btn active" data-filter="all">
            All <span id="cntAll" style="background:rgba(0,0,0,.12);border-radius:999px;padding:1px 7px;font-size:.68rem;margin-left:3px;">0</span>
        </button>
        <button class="notif-filter-btn" data-filter="unread">
            Unread <span id="cntUnread" style="background:#dc2626;color:#fff;border-radius:999px;padding:1px 6px;font-size:.68rem;margin-left:3px;">0</span>
        </button>
        <button class="notif-filter-btn" data-filter="danger">Urgent</button>
        <button class="notif-filter-btn" data-filter="warning">Deadline</button>
        <button class="notif-filter-btn" data-filter="success">Approvals</button>
        <button class="notif-filter-btn" data-filter="info">System</button>
    </div>

    <!-- Notification list -->
    <div id="notifList"></div>

    <!-- Empty state -->
    <div id="emptyState" style="display:none;text-align:center;padding:60px 20px;">
        <i class="fas fa-bell-slash" style="font-size:3rem;color:var(--gray-200);margin-bottom:16px;display:block;"></i>
        <div style="font-size:.95rem;font-weight:700;color:var(--gray-400);margin-bottom:6px;">No notifications</div>
        <div style="font-size:.82rem;color:var(--gray-400);">You're all caught up.</div>
    </div>
</main>

<script>
(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const typeConfig = {
        danger:  { icon: 'fas fa-exclamation-triangle', bg: 'rgba(220,38,38,.10)', color: '#dc2626', tagBg: '#fee2e2', tagColor: '#991b1b' },
        warning: { icon: 'fas fa-clock',                  bg: 'rgba(245,158,11,.10)',  color: '#f59e0b', tagBg: '#ffedd5', tagColor: '#92400e' },
        success: { icon: 'fas fa-check-circle',          bg: 'rgba(34,197,94,.10)',   color: '#15803d', tagBg: '#dcfce7', tagColor: '#166534' },
        info:    { icon: 'fas fa-info-circle',          bg: 'rgba(59,130,246,.10)',  color: '#3b82f6', tagBg: '#dbeafe', tagColor: '#1d4ed8' },
        default: { icon: 'fas fa-bell',                  bg: 'rgba(0,0,0,.06)',       color: 'var(--gray-600)', tagBg: 'var(--gray-100)', tagColor: 'var(--gray-600)' }
    };

    function escapeHtml(s) {
        return String(s ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '<')
            .replaceAll('>', '>')
            .replaceAll('"', '"')
            .replaceAll("'", '&#039;');
    }

    function formatTimeAgo(iso) {
        if (!iso) return '';
        const t = new Date(iso).getTime();
        if (Number.isNaN(t)) return '';
        const diff = Math.max(0, Date.now() - t);

        const mins = Math.floor(diff / 60000);
        if (mins < 1) return 'just now';
        if (mins < 60) return mins + 'm ago';
        const hrs = Math.floor(mins / 60);
        if (hrs < 24) return hrs + 'h ago';
        const days = Math.floor(hrs / 24);
        return days + 'd ago';
    }

    async function apiGet(url) {
        const res = await fetch(url, { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Request failed: ' + res.status);
        return res.json();
    }

    async function apiPost(url, body) {
        const res = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(body || {})
        });
        if (!res.ok) throw new Error('Request failed: ' + res.status);
        return res.json().catch(() => ({}));
    }

    window.__toast = function (message) {
        const notification = document.createElement('div');
        notification.className = 'alert alert-info notification-toast';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 320px;
            background: #111827;
            color: #fff;
            padding: 12px 14px;
            border-radius: 12px;
            box-shadow: var(--shadow-xl, 0 10px 30px rgba(0,0,0,.25));
        `;
        notification.innerHTML = `
            <div style="display:flex;align-items:center;gap:10px;">
                <i class="fas fa-circle-info"></i>
                <span>${escapeHtml(message)}</span>
                <button type="button" style="margin-left:auto;background:transparent;border:none;color:#fff;font-size:1.1rem;cursor:pointer;" onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
        `;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 4000);
    };

    function renderCards(items) {
        const listEl = document.getElementById('notifList');
        const emptyEl = document.getElementById('emptyState');
        if (!listEl || !emptyEl) return;

        if (!items || items.length === 0) {
            listEl.innerHTML = '';
            emptyEl.style.display = 'block';
            return;
        }

        emptyEl.style.display = 'none';

        listEl.innerHTML = items.map(n => {
            const cfg = typeConfig[n.type] || typeConfig.default;

            const isUnread = !n.is_read;
            const unreadDot = isUnread ? '<span class="unread-dot"></span>' : '';

            const tag = n.tag || 'System';
            const tagStyle = `background:${cfg.tagBg};color:${cfg.tagColor};`;
            const actionUrl = n.action_url ? String(n.action_url) : null;

            const actionLink = actionUrl
                ? `<a href="${escapeHtml(actionUrl)}"
                        style="color:var(--nis-600);font-weight:600;text-decoration:none;font-size:.74rem;"
                        onclick="event.stopPropagation();">
                        Open <i class="fas fa-arrow-right" style="font-size:.6rem;"></i>
                   </a>`
                : '';

            const timeText = escapeHtml(formatTimeAgo(n.created_at));

            return `
                <div class="notif-card ${isUnread ? 'unread type-' + escapeHtml(n.type) : ''}"
                     data-id="${escapeHtml(n.id)}"
                     data-type="${escapeHtml(n.type || '')}"
                     data-unread="${isUnread ? '1' : '0'}"
                     onclick="window.__notifMarkRead(${Number(n.id)})">
                    <div class="notif-card-body">
                        <div class="notif-card-icon" style="background:${cfg.bg};color:${cfg.color};">
                            <i class="${cfg.icon}"></i>
                        </div>

                        <div class="notif-card-content">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                                ${unreadDot}
                                <div class="notif-card-title">${escapeHtml(n.title || '')}</div>
                                <span class="notif-tag" style="${tagStyle}">${escapeHtml(tag)}</span>
                            </div>

                            <div class="notif-card-desc">${escapeHtml(n.description || '')}</div>

                            <div class="notif-card-meta">
                                <i class="fas fa-clock" style="font-size:.65rem;"></i> ${timeText}
                                ${actionLink ? '<span style="margin-left:4px;">•</span>' : ''}
                                ${actionLink}
                            </div>
                        </div>

                        <div class="notif-card-time">${timeText}</div>
                    </div>
                </div>
            `;
        }).join('');
    }

    async function refreshNotifications() {
        const [list, count] = await Promise.all([
            apiGet('/user/notifications/api?limit=20'),
            apiGet('/user/notifications/count')
        ]);

        renderCards(list.items || []);
        document.getElementById('cntUnread') && (document.getElementById('cntUnread').textContent = count.unread_count ?? 0);
        document.getElementById('cntAll') && (document.getElementById('cntAll').textContent = (list.items || []).length);
    }

    window.__notifMarkRead = async function (id) {
        if (!id) return;
        try {
            await apiPost('/user/notifications/' + id + '/read');
            await refreshNotifications();
        } catch (e) {
            window.__toast('Failed to mark read. Please try again.');
        }
    };

    document.getElementById('markAllReadBtn')?.addEventListener('click', async () => {
        try {
            await apiPost('/user/notifications/mark-all-read');
            await refreshNotifications();
        } catch (e) {
            window.__toast('Failed to mark all read. Please try again.');
        }
    });

    async function init() {
        try {
            await refreshNotifications();
        } catch (e) {
            window.__toast('Could not load notifications. Retrying…');
        }

        // Poll for realtime-ish updates
        setInterval(async () => {
            try {
                await refreshNotifications();
            } catch (e) {
                // soft-fail
            }
        }, 8000);
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
</script>

@include('partials.footer')
