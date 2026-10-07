@extends('user.directorates._layout')

{{-- This view renders its own tab bar and its own Review & Submit tab,
     so the shared layout skips both. --}}
@section('directorate-tabs', '1')
@section('directorate-preview', '1')

@section('directorate-sections')
@include('user.states.sections.migration')

        {{-- Supporting documents upload: lives in the wrapper (not the reusable
             partial) because the combined state form provides a shared attachments[]
             input. The script below moves this card back into the General Report
             tab, where it originally sat. --}}
        <div class="redas-card" style="margin-bottom:16px;" id="migration-documents-card">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">SUPPORTING DOCUMENTS</div>
                <button type="button" class="btn-nis btn-ghost btn-sm" onclick="addDocumentInput()" style="padding:4px 10px;font-size:0.8rem;">
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

        {{-- Review & Submit tab: standalone page only. The partial's tab script
             picks up the button (appended below) and this panel on DOMContentLoaded. --}}
        <div class="m-tab-content" id="tab-migration-review" style="display:none;">
            <div style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;border-radius:12px;padding:16px;margin-bottom:16px;">
                <i class="fas fa-exclamation-triangle" style="margin-right:8px;color:#d97706;"></i>
                <strong>Review Your Submission:</strong> Please carefully review all the data you have entered below. Once you are sure everything is correct, click the Submit button at the bottom.
            </div>

            <div id="review-snapshot-container">
                <!-- Javascript will inject the locked snapshot here -->
            </div>

            {{-- <!--<div style="padding:20px;display:flex;justify-content:flex-end;align-items:center;">
                <button type="submit" class="btn-nis btn-primary" style="padding:12px 24px;font-size:1rem;">
                    <i class="fas fa-paper-plane" style="margin-right:8px;"></i> Submit Return
                </button>
            </div> --}}
        </div>

<script>
(function () {
    'use strict';

    window.addDocumentInput = function () {
        var container = document.getElementById('documents-body');
        var div = document.createElement('div');
        div.className = 'auth-form-group';
        div.style.marginBottom = '12px';
        div.innerHTML = '<input type="file" name="supporting_documents[]" class="ni" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg">';
        container.appendChild(div);
    };

    /* Move the upload card back into the General Report tab (its original
       position) so the standalone page looks exactly as before. */
    var reportsPanel = document.getElementById('tab-migration-reports');
    var documentsCard = document.getElementById('migration-documents-card');
    if (reportsPanel && documentsCard) {
        reportsPanel.appendChild(documentsCard);
    }

    /* Append the Review & Submit tab button to the partial's tab bar. This runs
       synchronously before DOMContentLoaded, so the partial's tab script sees it. */
    var tabsBar = document.getElementById('migrationFormTabs');
    if (tabsBar && !tabsBar.querySelector('[data-m-tab="review"]')) {
        var reviewBtn = document.createElement('button');
        reviewBtn.type = 'button';
        reviewBtn.setAttribute('data-m-tab', 'review');
        reviewBtn.style.cssText = 'color:var(--color-primary);font-weight:700;flex-shrink:0 !important;display:flex !important;align-items:center !important;';
        reviewBtn.innerHTML = '<i class="fas fa-check-double" style="font-size:.78rem;"></i> Review & Submit';
        tabsBar.appendChild(reviewBtn);
    }

    /* Called by the partial's tab script whenever the review tab is shown. */
    window.migrationBuildReviewSnapshot = function () {
        var container = document.getElementById('review-snapshot-container');
        if (!container) return;
        container.innerHTML = ''; // Clear previous snapshot

        // Grab all redas-cards from all tabs except the review tab
        var sourceTabs = document.querySelectorAll('.m-tab-content:not(#tab-migration-review)');

        sourceTabs.forEach(function (tab) {
            var cards = tab.querySelectorAll('.redas-card');
            cards.forEach(function (card) {
                // Clone the card
                var clone = card.cloneNode(true);

                // Remove all buttons (Add More, etc)
                clone.querySelectorAll('button').forEach(function (btn) { btn.remove(); });

                // Strip 'name' and 'id' attributes to prevent submission conflicts
                // and disable the fields
                clone.querySelectorAll('input, select, textarea').forEach(function (input) {
                    // Crucial: Copy the LIVE value from the original form to the clone
                    // because cloneNode does not copy the dynamic value state
                    var originalInput = card.querySelector('[name="' + input.getAttribute('name') + '"]');
                    if (originalInput) {
                        input.value = originalInput.value;
                        if (input.tagName === 'SELECT') {
                            input.innerHTML = '';
                            var opt = document.createElement('option');
                            opt.textContent = originalInput.value;
                            input.appendChild(opt);
                        }
                    }

                    input.removeAttribute('name');
                    input.removeAttribute('id');
                    input.setAttribute('readonly', 'readonly');
                    input.setAttribute('disabled', 'disabled');

                    // Fix styling for disabled inputs to look cleaner
                    input.style.backgroundColor = 'transparent';
                    input.style.border = 'none';
                    input.style.fontWeight = 'bold';
                    input.style.color = 'var(--nis-900)';
                    input.style.padding = '0';
                });

                // Add a subtle border to the clone for the review tab
                clone.style.border = '1px solid var(--nis-200)';
                clone.style.boxShadow = 'none';

                container.appendChild(clone);
            });
        });
    };
})();
</script>

@endsection
