
(function () {
    var slug = "ict";
    var prefix = 'redas_' + slug + '_draft_';
    var tbody = document.querySelector('.searchable-table tbody');
    if (!tbody) return;

    function esc(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    var drafts = [];
    try {
        Object.keys(localStorage).forEach(function (k) {
            if (k.indexOf(prefix) !== 0) return;
            var period = k.slice(prefix.length);
            var savedAt = null;
            try { savedAt = JSON.parse(localStorage.getItem(k)).__saved_at || null; } catch (e) {}
            drafts.push({ key: k, period: period, savedAt: savedAt });
        });
    } catch (e) {}
    if (!drafts.length) return;

    drafts.sort(function (a, b) { return (b.savedAt || '').localeCompare(a.savedAt || ''); });

    var formUrl = "http:\/\/localhost\/user\/directorates\/ict";
    var emptyRow = tbody.querySelector('td[colspan]');
    if (emptyRow) emptyRow.closest('tr').style.display = 'none';

    drafts.forEach(function (draft) {
        var isDefault = draft.period === 'default';
        var periodLabel = isDefault ? "September 2026" : esc(draft.period);
        var savedLabel = 'Saved locally';
        if (draft.savedAt) {
            var d = new Date(draft.savedAt);
            if (!isNaN(d)) savedLabel = 'Saved ' + d.toLocaleDateString(undefined, { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' + d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
        }
        var url = formUrl + (isDefault ? '' : '?draft_period=' + encodeURIComponent(draft.period));

        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td><strong>' + periodLabel + '</strong></td>' +
            '<td><span class="status-badge badge-draft">Saved Draft</span></td>' +
            '<td style="font-size:.78rem;color:var(--gray-500);">' + esc(savedLabel) + '</td>' +
            '<td><div style="display:flex;gap:6px;">' +
                '<a href="' + url + '" class="btn-nis btn-sm btn-primary-nis" style="padding:6px 12px;" title="Resume draft"><i class="fas fa-play"></i> Resume</a>' +
                '<button type="button" class="btn-nis btn-sm btn-ghost draft-discard-btn" style="padding:6px 12px;color:#b91c1c;" title="Discard draft"><i class="fas fa-trash"></i></button>' +
            '</div></td>';

        tr.querySelector('.draft-discard-btn').addEventListener('click', function () {
            if (!window.confirm('Discard this saved draft? This cannot be undone.')) return;
            try { localStorage.removeItem(draft.key); } catch (e) {}
            tr.remove();
        });

        tbody.insertBefore(tr, tbody.firstChild);
    });
})();
