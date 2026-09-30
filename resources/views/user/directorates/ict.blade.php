@extends('user.directorates._layout')

{{-- This view renders its own tab bar (via the shared sections partial);
     the Preview panel and action buttons come from the shared layout. --}}
@section('directorate-tabs', '1')

@section('directorate-sections')
@include('user.states.sections.ict')

{{-- Supporting documents upload: kept here, not in the shared partial, so the
     standalone page keeps its upload. The combined state form provides its own
     shared attachments input instead. --}}
<div class="redas-card" style="margin-bottom:16px;">
    <div class="card-head" style="display:flex; justify-content:space-between; align-items:center;">
        <div class="card-head-title">SUPPORTING DOCUMENTS (Optional)</div>
        <button type="button" class="btn-nis btn-ghost btn-sm" id="ictAddDocumentRow">
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
    /* The shared partial drops the Preview tab (the combined state form has no
       per-directorate preview). Re-add the tab button here so the standalone
       page still reaches the layout's generic preview panel. This runs before
       the layout and footer scripts, so their global tab/next/prev handlers
       pick the button up. */
    var tabsBar = document.getElementById('entryTabs');
    if (tabsBar && !tabsBar.querySelector('.entry-tab[data-tab="preview"]')) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'entry-tab';
        btn.setAttribute('data-tab', 'preview');
        btn.innerHTML = '<span class="tab-dot"></span><i class="fas fa-eye" style="font-size:.78rem;"></i> 8. Preview';
        tabsBar.appendChild(btn);
    }

    /* Add-row for the supporting documents upload (moved out of the partial). */
    function addDocumentRow() {
        var container = document.getElementById('documents-body');
        if (!container) return;
        var div = document.createElement('div');
        div.className = 'auth-form-group';
        div.style.marginBottom = '12px';
        div.innerHTML = '<input type="file" name="supporting_documents[]" class="ni" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg">';
        container.appendChild(div);
    }
    document.getElementById('ictAddDocumentRow')?.addEventListener('click', addDocumentRow);
})();
</script>
@endsection
