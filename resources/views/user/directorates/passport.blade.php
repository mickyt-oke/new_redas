@extends('user.directorates._layout')

{{-- This view renders its own tab bar (via the shared partial below); the
     Preview panel and action buttons come from the shared layout. --}}
@section('directorate-tabs', '1')

@section('directorate-sections')

{{-- The shared layout provides the <form>; do not nest another one here.
     The form sections live in the shared partial so the combined state
     return form can embed the same markup. --}}
@include('user.states.sections.passport')

{{-- Supporting documents upload: kept here (not in the shared partial) so the
     combined state form can use its own shared attachments[] input instead. --}}
<div class="redas-card" style="margin-bottom:16px;">
    <div class="card-head" style="display:flex; justify-content:space-between; align-items:center;">
        <div class="card-head-title">SUPPORTING DOCUMENTS (Optional)</div>
        <button type="button" class="btn-nis btn-ghost btn-sm" id="passportAddDocumentRow">
            <i class="fas fa-plus"></i> Add Document
        </button>
    </div>
    <div class="card-body">
        <p style="font-size:0.85rem;color:var(--gray-500);margin-bottom:12px;">You can upload supporting documents or photos (PDF, Excel, PNG, JPG, JPEG).</p>
        <div id="documents-body">
            <div class="auth-form-group" style="margin-bottom:12px;">
                <input type="file" name="supporting_documents[]" class="ni" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg">
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    /* "Add Document" row for the upload above (standalone page only). */
    document.getElementById('passportAddDocumentRow')?.addEventListener('click', function () {
        var container = document.getElementById('documents-body');
        if (!container) return;
        var div = document.createElement('div');
        div.className = 'auth-form-group';
        div.style.marginBottom = '12px';
        div.innerHTML = '<input type="file" name="supporting_documents[]" class="ni" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg">';
        container.appendChild(div);
    });

    /* The shared partial omits the Preview tab (the combined state form has its
       own review step). Re-add it here so the standalone page keeps previewing
       through the layout's generic #tab-preview panel. The button is appended
       during parse, before the footer/layout tab wiring runs, so it is picked
       up by the global handlers. */
    var tabsBar = document.getElementById('passportEntryTabs');
    if (tabsBar && !tabsBar.querySelector('.entry-tab[data-tab="preview"]')) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'entry-tab';
        btn.setAttribute('data-tab', 'preview');
        btn.innerHTML = '<span class="tab-dot"></span><i class="fas fa-eye" style="font-size:.78rem;"></i> 6. Preview';
        tabsBar.appendChild(btn);
    }
})();
</script>

@endsection
