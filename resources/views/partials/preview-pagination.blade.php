{{--
    Shared section pagination for read-only preview pages.
    Wrap each preview section in an element with class "preview-section" and a
    data-label attribute. This partial adds a sticky section navigator, prev/next
    controls, keyboard navigation, and a "Show all" toggle.
--}}
<style>
    .preview-paginator { position:sticky; top:var(--topbar-height, 0); z-index:40; background:#fff; border:1px solid var(--gray-200); border-radius:12px; padding:10px 12px; margin-bottom:16px; box-shadow:0 1px 3px rgba(0,0,0,.04); }
    .preview-paginator-top { display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
    .preview-paginator-title { font-size:.8rem; font-weight:700; color:var(--gray-600); }
    .preview-paginator-controls { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .preview-paginator-controls button { padding:6px 10px; border-radius:8px; border:1px solid var(--gray-200); background:#fff; color:var(--gray-700); font-size:.78rem; font-weight:600; cursor:pointer; }
    .preview-paginator-controls button:hover { background:var(--nis-50); color:var(--nis-700); border-color:var(--nis-200); }
    .preview-paginator-controls button:disabled { opacity:.5; cursor:not-allowed; }
    .preview-section-tabs { display:flex; gap:6px; overflow-x:auto; padding:8px 0 2px; }
    .preview-section-tab { flex-shrink:0; padding:5px 10px; border-radius:999px; border:1px solid var(--gray-200); background:#fff; color:var(--gray-600); font-size:.74rem; font-weight:600; cursor:pointer; white-space:nowrap; }
    .preview-section-tab:hover { background:var(--nis-50); color:var(--nis-700); }
    .preview-section-tab.active { background:var(--nis-700); color:#fff; border-color:var(--nis-700); }
    .preview-section { display:none; animation:previewFadeIn .2s ease; }
    .preview-section.active { display:block; }
    .preview-show-all .preview-section { display:block; }
    .preview-section-empty { padding:16px; color:var(--gray-500); font-size:.84rem; }
    @keyframes previewFadeIn { from { opacity:0; transform:translateY(4px); } to { opacity:1; transform:translateY(0); } }
    @media print {
        .preview-paginator { display:none !important; }
        .preview-section { display:block !important; }
    }
</style>

<script>
(function () {
    function initPreviewPaginators() {
    var paginators = document.querySelectorAll('[data-preview-paginator]');
    paginators.forEach(function (root) {
        var sections = Array.from(root.querySelectorAll('.preview-section'));
        if (sections.length === 0) return;

        var current = 0;
        var showAll = false;

        var tabsWrap = root.querySelector('.preview-section-tabs');
        var prevBtn = root.querySelector('.preview-prev-btn');
        var nextBtn = root.querySelector('.preview-next-btn');
        var allBtn = root.querySelector('.preview-all-btn');
        var counter = root.querySelector('.preview-counter');

        function render() {
            if (showAll) {
                root.classList.add('preview-show-all');
                sections.forEach(function (s) { s.classList.add('active'); });
                if (allBtn) { allBtn.textContent = 'Collapse'; allBtn.classList.add('active'); }
                if (counter) counter.textContent = 'Showing all ' + sections.length + ' sections';
                if (prevBtn) prevBtn.disabled = true;
                if (nextBtn) nextBtn.disabled = true;
                if (tabsWrap) tabsWrap.querySelectorAll('.preview-section-tab').forEach(function (t) { t.classList.remove('active'); });
                return;
            }

            root.classList.remove('preview-show-all');
            sections.forEach(function (s, i) {
                s.classList.toggle('active', i === current);
            });
            if (tabsWrap) {
                tabsWrap.querySelectorAll('.preview-section-tab').forEach(function (t, i) {
                    t.classList.toggle('active', i === current);
                });
                var activeTab = tabsWrap.querySelector('.preview-section-tab.active');
                if (activeTab) tabsWrap.scrollLeft = activeTab.offsetLeft - 60;
            }
            if (prevBtn) prevBtn.disabled = current === 0;
            if (nextBtn) nextBtn.disabled = current === sections.length - 1;
            if (allBtn) { allBtn.textContent = 'Show all'; allBtn.classList.remove('active'); }
            if (counter) counter.textContent = 'Section ' + (current + 1) + ' of ' + sections.length;
            var activeSection = sections[current];
            if (activeSection && !showAll) activeSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function goTo(idx) {
            showAll = false;
            current = Math.max(0, Math.min(sections.length - 1, idx));
            render();
        }

        function buildTabs() {
            if (!tabsWrap) return;
            tabsWrap.innerHTML = '';
            sections.forEach(function (s, i) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'preview-section-tab';
                btn.textContent = (i + 1) + '. ' + (s.dataset.label || 'Section');
                btn.addEventListener('click', function () { goTo(i); });
                tabsWrap.appendChild(btn);
            });
        }

        if (prevBtn) prevBtn.addEventListener('click', function () { goTo(current - 1); });
        if (nextBtn) nextBtn.addEventListener('click', function () { goTo(current + 1); });
        if (allBtn) {
            allBtn.addEventListener('click', function () {
                showAll = !showAll;
                render();
            });
        }

        root.addEventListener('keydown', function (e) {
            if (showAll) return;
            if (e.key === 'ArrowRight') { e.preventDefault(); goTo(current + 1); }
            if (e.key === 'ArrowLeft') { e.preventDefault(); goTo(current - 1); }
        });

        buildTabs();
        render();
    });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPreviewPaginators);
    } else {
        initPreviewPaginators();
    }
})();
</script>
