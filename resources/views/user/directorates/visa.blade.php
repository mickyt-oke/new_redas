@extends('user.directorates._layout')

{{-- This view renders its own tab bar; the Preview panel and action buttons
     come from the shared layout. --}}
@section('directorate-tabs', '1')

@section('directorate-sections')
@include('user.states.sections.visa')

{{-- Supporting documents upload: kept here (not in the shared partial) so the
     standalone page keeps its upload while the combined state form uses the
     state layout's shared attachments[] input. --}}
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
(function() {
    /* General Report: supporting documents */
    function addDocumentRow() {
        const container = document.getElementById('documents-body');
        const div = document.createElement('div');
        div.className = 'auth-form-group';
        div.style.marginBottom = '12px';
        div.innerHTML = '<input type="file" name="supporting_documents[]" class="ni" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg">';
        container.appendChild(div);
    }
    document.getElementById('passportAddDocumentRow')?.addEventListener('click', addDocumentRow);

    /* The shared partial drops the Preview tab (the combined state form has no
       per-directorate preview); re-add it here so the standalone page keeps its
       preview tab pointing at the layout's generic #tab-preview panel. This runs
       before the layout/footer scripts, so the button is wired globally. */
    const tabsBar = document.querySelector('.entry-tabs');
    if (tabsBar && !tabsBar.querySelector('.entry-tab[data-tab="preview"]')) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'entry-tab';
        btn.dataset.tab = 'preview';
        btn.innerHTML = '<span class="tab-dot"></span><i class="fas fa-eye" style="font-size:.78rem;"></i> 12. Preview';
        tabsBar.appendChild(btn);
    }
})();
</script>
@endsection
