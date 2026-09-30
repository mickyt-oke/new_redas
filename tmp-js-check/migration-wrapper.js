
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
                            input.innerHTML = '<option>' + originalInput.value + '</option>';
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
