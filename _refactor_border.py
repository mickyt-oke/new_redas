#!/usr/bin/env python3
"""
Refactor resources/views/user/directorates/border.blade.php to match the
HRM directorate template visual structure while preserving all form field
names, IDs and JavaScript functions.
"""
import re
from pathlib import Path

FILE = Path('resources/views/user/directorates/border.blade.php')

with open(FILE, 'r', encoding='utf-8') as f:
    content = f.read()

# Locate the start of the inline script
script_start = content.find('<script>')
html_part = content[:script_start]
script_part = content[script_start:]

# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------
def first_match_group(html, pattern, flags=0):
    m = re.search(pattern, html, flags)
    return m.group(1) if m else None

def replace_exact(html, old, new, count=1):
    if old not in html:
        raise ValueError('old string not found')
    return html.replace(old, new, count)

# ---------------------------------------------------------------------------
# 1. Move @endsection after </script> and fix extra closing divs
# ---------------------------------------------------------------------------
# Remove @endsection that currently sits before <script>
html_part = re.sub(r'\s*@endsection\s*(?=\s*<script>)', '\n\n', html_part)

# After the preview tab-panel closes there are currently three stray </div>s.
# We need exactly one to close the tab-content wrapper.
html_part = re.sub(
    r'(</div>\s*<div class="hrm-actions">\s*<button type="button" class="btn-nis btn-ghost hrm-edit-btn">.*?</button>\s*</div>\s*</div>\s*</div>\s*)</div>\s*</div>\s*</div>',
    r'\1</div>',
    html_part,
    flags=re.DOTALL
)

# If that didn't catch it, also handle the explicit block we saw in the file
html_part = re.sub(
    r'(</div>\s*</div>\s*</div>\s*)\n\n\n\n\n',
    r'\1\n\n',
    html_part,
    count=1,
    flags=re.DOTALL
)

# ---------------------------------------------------------------------------
# 2. Personnel tab - remove inner redas-card wrapper and nis-section-head
# ---------------------------------------------------------------------------
personnel_old = '''    <div class="tab-panel active" id="tab-personnel">
<div class="redas-card" style="margin-bottom:14px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                    <i class="fas fa-users"></i>
                </div>
                Staff Strength
            </div>
        </div>
        <div class="card-body">
        <div class="redas-card" style="margin-bottom:14px;">
                <div class="nis-section-head">
                    <span class="sec-num">
                        <i class="fas fa-users"></i>
                    </span>
                    <div style="display:flex;flex-direction:column;">Staff Strength <small style="font-weight:400;opacity:.8;">(Current nominal roll to be attached)</small></div>
                    </div>
                    <div class="card-body">'''

personnel_new = '''    <div class="tab-panel active" id="tab-personnel">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-users"></i>
                    </div>
                    Staff Strength
                </div>
            </div>
            <div class="card-body">'''

html_part = replace_exact(html_part, personnel_old, personnel_new)

# Close the inner wrapper removal: after the table, there are two extra closing </div>s
# (closing inner card-body and inner redas-card). We drop both and keep the outer card-body closing.
personnel_close_old = '''                    </div>
                </div>
            
        </div>
    </div>
    <div class="hrm-actions">'''

personnel_close_new = '''        </div>
    </div>
    <div class="hrm-actions">'''

html_part = replace_exact(html_part, personnel_close_old, personnel_close_new)

# ---------------------------------------------------------------------------
# 3. Land Border tab - remove nis-section wrapper and nis-section-head
# ---------------------------------------------------------------------------
land_open_old = '''    <div class="nis-section">

        <div class="nis-section-head">

            <span class="sec-num">
                <i class="fas fa-map-marker-alt"></i>
            </span>

            Arrival / Departure of Passengers through Nigerian Borders by Land

        </div>

        <div class="nis-section-body">

            @foreach($states as $state => $posts)'''

land_open_new = '''            @foreach($states as $state => $posts)'''

html_part = replace_exact(html_part, land_open_old, land_open_new)

land_close_old = '''        </div> {{-- /.nis-section-body --}}

    </div> {{-- /.nis-section --}}


        </div>
    </div>'''

land_close_new = '''        </div>
    </div>'''

html_part = replace_exact(html_part, land_close_old, land_close_new)

# ---------------------------------------------------------------------------
# 4. Seaport tab - remove redundant title-only inner redas-card
# ---------------------------------------------------------------------------
seaport_title_old = '''<div class="redas-card mb-4">

    <div class="card-head">

        <div class="card-head-title">

            <div class="card-head-icon">
                <i class="fas fa-ship"></i>
            </div>

            Activities of the Service at the Seaport and Marine Base

        </div>

    </div>

</div>


@foreach($seaportStates as $state => $ports)'''

seaport_title_new = '''            @foreach($seaportStates as $state => $ports)'''

html_part = replace_exact(html_part, seaport_title_old, seaport_title_new)

# ---------------------------------------------------------------------------
# 5. Airports tab - remove redundant title-only inner redas-card
# ---------------------------------------------------------------------------
airports_title_old = '''<div class="redas-card mb-4">

    <div class="card-head">

        <div class="card-head-title">

            <div class="card-head-icon">

                <i class="fas fa-plane"></i>

            </div>

            Passenger Movement Across the International Airports

        </div>

    </div>

</div>


@foreach($internationalAirports as $airport => $state)'''

airports_title_new = '''            @foreach($internationalAirports as $airport => $state)'''

html_part = replace_exact(html_part, airports_title_old, airports_title_new)

# ---------------------------------------------------------------------------
# 6. Offshore tab - remove redundant title-only inner redas-card,
#    keep the Add State button by moving it into the card-body
# ---------------------------------------------------------------------------
offshore_title_old = '''            <div class="redas-card mb-4">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#DBEAFE;color:#1D4ED8;"><i class="fas fa-anchor"></i></div>
            Offshore Activities
        </div>
        <div class="card-head-action">
            <button type="button" id="btnAddState" class="btn btn-primary btn-sm"><i class="fas fa-plus-circle"></i>Add State</button>
        </div>
    </div>
</div>
<div id="offshoreStates"></div>'''

offshore_title_new = '''            <div class="mb-3">
                <button type="button" id="btnAddState" class="btn btn-primary btn-sm"><i class="fas fa-plus-circle"></i> Add State</button>
            </div>
            <div id="offshoreStates"></div>'''

html_part = replace_exact(html_part, offshore_title_old, offshore_title_new)

# ---------------------------------------------------------------------------
# 7. Border Nationality tab - remove redundant title-only inner redas-card,
#    keep the Add Border Command button by moving it into the card-body
# ---------------------------------------------------------------------------
border_nat_title_old = '''    <!-- ================= HEADER ================= -->

    <div class="redas-card mb-4">

        <div class="card-head">

            <div class="card-head-title">

                <div class="card-head-icon"
                     style="background:#DBEAFE;color:#1D4ED8;">

                    <i class="fas fa-passport"></i>

                </div>

                Land Border Returns by Nationality

            </div>

            <div class="card-head-action">

                <button
                    type="button"
                    id="btnAddCommand"
                    class="btn btn-success btn-sm">

                    <i class="fas fa-plus-circle"></i>

                    Add Border Command

                </button>

            </div>

        </div>

    </div>


    <!-- ================= COMMANDS WILL BE INSERTED HERE ================= -->

    <div id="commandContainer"></div>'''

border_nat_title_new = '''            <div class="mb-3">
                <button type="button" id="btnAddCommand" class="btn btn-success btn-sm"><i class="fas fa-plus-circle"></i> Add Border Command</button>
            </div>
            <div id="commandContainer"></div>'''

html_part = replace_exact(html_part, border_nat_title_old, border_nat_title_new)

# ---------------------------------------------------------------------------
# 8. Comments tab - remove nis-section wrapper and nis-section-head
# ---------------------------------------------------------------------------
comments_old = '''                    <div class="nis-section" style="margin-top:8px;" id="generalReport">
                <div class="nis-section-head"><span class="sec-num">§19</span> General Report</div>
                <div class="nis-section-body">'''

comments_new = '''                    <div id="generalReport">'''

html_part = replace_exact(html_part, comments_old, comments_new)

comments_close_old = '''                </div>
            </div>

    
        </div>
    </div>'''

comments_close_new = '''        </div>
    </div>'''

html_part = replace_exact(html_part, comments_close_old, comments_close_new)

# ---------------------------------------------------------------------------
# 9. Replace the bottom navigation IIFE with the HRM version
# ---------------------------------------------------------------------------
# Find the start of the navigation IIFE
nav_start_marker = '/* HRM-style Previous / Next / Save Draft navigation for Border returns */'
nav_start = script_part.find(nav_start_marker)
if nav_start == -1:
    nav_start = script_part.rfind('(function() {\n')

if nav_start != -1:
    # Find the end of the last IIFE before </script>
    script_end_tag = script_part.find('</script>')
    nav_end = script_part.rfind('})();', nav_start, script_end_tag)
    if nav_end != -1:
        nav_end += len('})();')
        new_nav = '''/* HRM-style Previous / Next / Save Draft navigation for Border returns */
(function() {
    const form = document.querySelector('main form') || document.querySelector('form[action*="directorates"]');
    const tabs = document.querySelectorAll('.entry-tab');
    const panels = document.querySelectorAll('.tab-panel');
    const tabIds = Array.from(tabs).map(t => t.dataset.tab);

    function goToTab(id) {
        const tab = document.querySelector(`.entry-tab[data-tab="${id}"]`);
        if (tab) tab.click();
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    function currentTabId() {
        return document.querySelector('.tab-panel.active')?.id.replace('tab-','');
    }

    function nextTab() {
        const idx = tabIds.indexOf(currentTabId());
        if (idx >= 0 && idx < tabIds.length - 1) goToTab(tabIds[idx + 1]);
    }

    function prevTab() {
        const idx = tabIds.indexOf(currentTabId());
        if (idx > 0) goToTab(tabIds[idx - 1]);
    }

    document.querySelectorAll('.hrm-next-btn').forEach(b => b.addEventListener('click', nextTab));
    document.querySelectorAll('.hrm-prev-btn').forEach(b => b.addEventListener('click', prevTab));
    document.querySelectorAll('.hrm-edit-btn').forEach(b => b.addEventListener('click', () => goToTab('personnel')));

    /* Save / restore draft */
    const DRAFT_KEY = 'redas_border_draft_' + (document.querySelector('[name="report_period"]')?.value || 'default');
    function saveDraft() {
        if (!form) return;
        const data = {};
        new FormData(form).forEach((v, k) => { data[k] = v; });
        try { localStorage.setItem(DRAFT_KEY, JSON.stringify(data)); } catch (e) {}
        alert('Draft saved successfully.');
    }
    document.querySelectorAll('.hrm-save-draft-btn').forEach(b => b.addEventListener('click', saveDraft));

    function loadDraft() {
        try {
            const saved = localStorage.getItem(DRAFT_KEY);
            if (!saved || !form) return;
            const data = JSON.parse(saved);
            Object.entries(data).forEach(([k, v]) => {
                const el = form.querySelector(`[name="${CSS.escape(k)}"]`);
                if (!el || el.readOnly) return;
                if (el.type === 'checkbox') el.checked = v === '1' || v === 'on' || v === true;
                else el.value = v;
            });
        } catch (e) {}
    }

    /* Preview submit wiring */
    document.querySelectorAll('.hrm-submit-return-btn').forEach(b => b.addEventListener('click', () => {
        if (form) {
            if (form.requestSubmit) form.requestSubmit();
            else form.submit();
        }
    }));

    /* Init */
    loadDraft();
})();'''
        script_part = script_part[:nav_start] + new_nav + script_part[nav_end:]

# ---------------------------------------------------------------------------
# 10. Append @endsection after </script>
# ---------------------------------------------------------------------------
if '@endsection' not in script_part:
    script_part = script_part.rstrip() + '\n\n@endsection\n'

# ---------------------------------------------------------------------------
# 11. General indentation / whitespace cleanup
# ---------------------------------------------------------------------------
# Ensure consistent indentation on outer redas-card wrappers
html_part = re.sub(r'<div class="tab-panel(.*?)">\s*<div class="redas-card', r'<div class="tab-panel\1">\n        <div class="redas-card', html_part)

# ---------------------------------------------------------------------------
# Write the refactored file
# ---------------------------------------------------------------------------
with open(FILE, 'w', encoding='utf-8') as f:
    f.write(html_part + script_part)

print(f'Refactored {FILE}')
