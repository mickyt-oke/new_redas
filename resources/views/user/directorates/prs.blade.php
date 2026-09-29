@extends('user.directorates._layout')

{{-- This view renders its own tab bar (via the shared PRS section partial);
     the Preview panel and action buttons come from the shared layout. --}}
@section('directorate-tabs', '1')

@section('directorate-sections')
@include('user.states.sections.prs', ['prsStandalonePreview' => true])

{{-- Supporting documents upload: kept here (outside the partial) so the
     standalone page keeps its upload; the combined state form uses the
     state layout's shared attachments[] input instead. --}}
<div class="redas-card" style="margin-bottom:16px;">
    <div class="card-head" style="display:flex; justify-content:space-between; align-items:center;">
        <div class="card-head-title">SUPPORTING DOCUMENTS (Optional)</div>
        <button type="button" class="btn-nis btn-ghost btn-sm" id="prsAddDocumentRow">
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
    function addDocumentRow() {
        const container = document.getElementById('documents-body');
        if (!container) return;
        const div = document.createElement('div');
        div.className = 'auth-form-group';
        div.style.marginBottom = '12px';
        div.innerHTML = '<input type="file" name="supporting_documents[]" class="ni" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg">';
        container.appendChild(div);
    }
    document.getElementById('prsAddDocumentRow')?.addEventListener('click', addDocumentRow);
})();
</script>
@endsection
