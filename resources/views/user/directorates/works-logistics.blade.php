@extends('user.directorates._layout')

{{-- This view renders its own tab bar (via the shared section partial); the
     Preview panel and action buttons come from the shared layout. --}}
@section('directorate-tabs', '1')

@section('directorate-sections')
@include('user.states.sections.works-logistics')

{{-- Supporting documents upload. Kept out of the shared section partial: the
     combined state form provides a single shared attachments[] input instead. --}}
{{-- <div class="redas-card" style="margin-bottom:14px;">
    <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-paperclip"></i></div>
            SUPPORTING DOCUMENTS (Optional)
        </div>
        <button type="button" class="btn-nis btn-ghost btn-sm" id="wlAddDocumentRow" data-row-target="documents-body">
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
</div> --}}

<script>
(function () {
    /* The section partial omits the Preview tab (the combined state form has
       no per-directorate preview). Re-add it here as the last tab so the
       layout's generic preview panel (#tab-preview) stays reachable. This
       runs before the layout/footer scripts bind their tab handlers. */
    var bar = document.querySelector('.entry-tabs');
    if (bar && !bar.querySelector('.entry-tab[data-tab="preview"]')) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'entry-tab';
        btn.dataset.tab = 'preview';
        btn.innerHTML = '<span class="tab-dot"></span><i class="fas fa-eye" style="font-size:.78rem;"></i> 9. Preview';
        bar.appendChild(btn);
    }

    /* Add/remove supporting-document rows (moved here with the upload card). */
    var addBtn = document.getElementById('wlAddDocumentRow');
    var container = document.getElementById('documents-body');
    if (addBtn && container) {
        addBtn.addEventListener('click', function () {
            var div = document.createElement('div');
            div.className = 'auth-form-group';
            div.style.marginBottom = '12px';
            div.style.display = 'flex';
            div.style.gap = '8px';
            div.innerHTML = '<input type="file" name="supporting_documents[]" class="ni" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg">' +
                '<button type="button" class="btn-nis btn-ghost wl-remove-document" style="color:var(--color-danger);padding:4px;"><i class="fas fa-times"></i></button>';
            container.appendChild(div);
        });
        container.addEventListener('click', function (e) {
            var removeDoc = e.target.closest('.wl-remove-document');
            if (removeDoc) removeDoc.parentElement.remove();
        });
    }
})();
</script>
@endsection
