@extends('user.directorates._layout')

{{-- This view renders its own tab bar (via the shared sections partial); the
     Preview panel and action buttons come from the shared layout. --}}
@section('directorate-tabs', '1')

@section('directorate-sections')
@include('user.states.sections.hrm', ['hrmIncludePreviewTab' => true])

{{-- Supporting documents upload. Kept here (out of the reusable partial) so the
     standalone page keeps its upload; the combined state form provides a shared
     attachments[] input instead. --}}
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
    function addDocumentRow() {
        const container = document.getElementById('documents-body');
        const div = document.createElement('div');
        div.className = 'auth-form-group';
        div.style.marginBottom = '12px';
        div.innerHTML = '<input type="file" name="supporting_documents[]" class="ni" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg">';
        container.appendChild(div);
    }
    document.getElementById('passportAddDocumentRow')?.addEventListener('click', addDocumentRow);
})();
</script>
@endsection
