/**
 * FA Dashboard JavaScript
 * Handles dashboard-specific functionality for the Finance & Accounts dashboard
 */

function initDashboard() {
    // Initialize dashboard components
    initializeSidebarToggle();
    initializeFormCalculations();
    initializeFormValidation();
    initializeDataPersistence();
    initializePrintFunctionality();
    initializeApprovalActions();
    initializeLogout();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDashboard);
} else {
    initDashboard();
}

// ==========================================
// SIDEBAR TOGGLE FUNCTIONALITY
// ==========================================
function initializeSidebarToggle() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            // Store sidebar state in localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            try {
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            } catch (e) {
                console.warn('Could not save sidebar state:', e);
            }
        });
    }

    // Restore sidebar state on page load
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (sidebarCollapsed) {
        sidebar.classList.add('collapsed');
    }
}

// ==========================================
// FORM CALCULATIONS
// ==========================================
function initializeFormCalculations() {
    // Helper to create dropdown options
    function createDropdownOptions(max) {
        let options = '<option value="">-</option>';
        for (let i = 1; i <= max; i++) {
            options += `<option value="${i}">${i}</option>`;
        }
        return options;
    }

    // 1. Personnel Strength Table
    const personnelData = [
        { cadre: "Comptroller", max: 20 },
        { cadre: "Superintendent", max: 100 },
        { cadre: "Inspectorate", max: 100 },
        { cadre: "Assistant", max: 100 }
    ];

    const personnelTable = document.getElementById('personnelTable');
    if (personnelTable) {
        personnelData.forEach(p => {
            const row = `<tr><td>${p.cadre}</td><td><select class="form-select form-select-sm personnel-select">${createDropdownOptions(p.max)}</select></td></tr>`;
            personnelTable.insertAdjacentHTML('beforeend', row);
        });
    }

    // 2. Local Revenue Performance Table
    const localRevenueItems = [
        "Passports",
        "Residence Permit (ECOWAS & African Affairs)",
        "Non-Refundable Administrative Fees for Operations",
        "Residence Permit for Non-Africans (CERPAC)",
        "Extension of Visitors Pass (E-Pass)",
        "Other Revenue (IGR)"
    ];

    const localRevenueTable = document.getElementById('localRevenueTable');
    if (localRevenueTable) {
        localRevenueItems.forEach((item, i) => {
            localRevenueTable.insertAdjacentHTML('beforeend', `<tr><td>${i+1}</td><td>${item}</td><td><input type="number" step="0.01" class="form-control form-control-sm local-revenue-input" data-index="${i}"></td></tr>`);
        });
    }

    // 3. Foreign Revenue Performance Table
    const foreignRevenueItems = [
        "Passport & Visa (Direct to JP Morgan Account)",
        "Carrier Liability/Visa on Arrival"
    ];

    const foreignRevenueTable = document.getElementById('foreignRevenueTable');
    if (foreignRevenueTable) {
        foreignRevenueItems.forEach((item, i) => {
            foreignRevenueTable.insertAdjacentHTML('beforeend', `<tr><td>${i+1}</td><td>${item}</td><td><input type="number" step="0.01" class="form-control form-control-sm foreign-revenue-input" data-index="${i}"></td></tr>`);
        });
    }

    // 4. Expenditure Profile Table
    const expenditureItems = [
        "IRIS Smart Technologies", "Newworks Solutions Ltd", "National e-Government Strategies",
        "Greater Washington", "Contec (CERPAC)", "Contec (E-Pass)", "FMI (CERPAC/E-Pass)",
        "FIRS", "NSMPC", "Sub-Treasure", "NIS (After payment to IPTELCOM)", "IPTELCOM"
    ];

    const expenditureTable = document.getElementById('expenditureTable');
    if (expenditureTable) {
        expenditureItems.forEach((item, i) => {
            expenditureTable.insertAdjacentHTML('beforeend', `<tr><td>${i+1}</td><td>${item}</td><td><input type="number" step="0.01" class="form-control form-control-sm expenditure-input" data-index="${i}"></td></tr>`);
        });
    }

    // 5. Budget and Releases Table
    const budgetItems = ["Personnel Cost", "Overhead Cost", "Capital"];
    const budgetTable = document.getElementById('budgetTable');
    if (budgetTable) {
        budgetItems.forEach((item, i) => {
            budgetTable.insertAdjacentHTML('beforeend', `<tr>
                <td>${i+1}</td>
                <td>${item}</td>
                <td><input type="number" step="0.01" class="form-control form-control-sm budget-input" data-index="${i}"></td>
                <td><input type="number" step="0.01" class="form-control form-control-sm release-input" data-index="${i}"></td>
                <td><input type="text" class="form-control form-control-sm percentage-output" readonly data-index="${i}"></td>
                <td><textarea class="form-control form-control-sm budget-remark" rows="2" data-index="${i}"></textarea></td>
            </tr>`);
        });
    }

    // Event listeners for calculations
    document.addEventListener('change', handlePersonnelCalculation);
    document.addEventListener('input', handleExpenditureCalculation);
    document.addEventListener('input', handleBudgetPercentageCalculation);
}

// Personnel Total Calculation
function handlePersonnelCalculation(e) {
    if (e.target.classList.contains('personnel-select')) {
        let total = 0;
        document.querySelectorAll('.personnel-select').forEach(sel => {
            total += Number(sel.value) || 0;
        });
        const personnelTotal = document.getElementById('personnelTotal');
        if (personnelTotal) {
            personnelTotal.value = total;
        }
    }
}

// Expenditure Total Calculation
function handleExpenditureCalculation(e) {
    if (e.target.classList.contains('expenditure-input')) {
        let total = 0;
        document.querySelectorAll('.expenditure-input').forEach(inp => {
            total += Number(inp.value) || 0;
        });
        const expenditureTotal = document.getElementById('expenditureTotal');
        if (expenditureTotal) {
            expenditureTotal.value = total.toFixed(2);
        }
    }
}

// Budget Percentage Calculation
function handleBudgetPercentageCalculation(e) {
    if (e.target.classList.contains('budget-input') || e.target.classList.contains('release-input')) {
        const row = e.target.closest('tr');
        const budgeted = Number(row.querySelector('.budget-input').value) || 0;
        const released = Number(row.querySelector('.release-input').value) || 0;
        const percentageField = row.querySelector('.percentage-output');

        if (percentageField) {
            if (budgeted > 0) {
                percentageField.value = ((released / budgeted) * 100).toFixed(2) + '%';
            } else {
                percentageField.value = '0%';
            }
        }
    }
}

// ==========================================
// FORM VALIDATION
// ==========================================
function initializeFormValidation() {
    const form = document.querySelector('#report-submission form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (validateForm()) {
            // Show success message
            showNotification('Report submitted successfully!', 'success');
            // Here you would typically send the data to the server
            console.log('Form data:', collectFormData());
        }
    });

    // Real-time validation
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            validateField(this);
        });
    });
}

function validateForm() {
    let isValid = true;
    const requiredFields = document.querySelectorAll('#report-submission [required]');

    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });

    // Validate numerical inputs
    const numberInputs = document.querySelectorAll('#report-submission input[type="number"]');
    numberInputs.forEach(input => {
        if (input.value && (isNaN(input.value) || Number(input.value) < 0)) {
            showFieldError(input, 'Please enter a valid positive number');
            isValid = false;
        }
    });

    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    let isValid = true;

    // Remove existing error messages
    removeFieldError(field);

    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'This field is required');
        isValid = false;
    }

    // Email validation
    if (field.type === 'email' && value && !isValidEmail(value)) {
        showFieldError(field, 'Please enter a valid email address');
        isValid = false;
    }

    // Phone validation
    if (field.name === 'phone' && value && !isValidPhone(value)) {
        showFieldError(field, 'Please enter a valid phone number');
        isValid = false;
    }

    return isValid;
}

function showFieldError(field, message) {
    field.classList.add('is-invalid');

    let errorElement = field.parentNode.querySelector('.invalid-feedback');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'invalid-feedback';
        field.parentNode.appendChild(errorElement);
    }
    errorElement.textContent = message;
}

function removeFieldError(field) {
    field.classList.remove('is-invalid');
    const errorElement = field.parentNode.querySelector('.invalid-feedback');
    if (errorElement) {
        errorElement.remove();
    }
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
}

// ==========================================
// DATA PERSISTENCE
// ==========================================
function initializeDataPersistence() {
    // Load saved data on page load
    loadSavedData();

    // Auto-save functionality
    const formInputs = document.querySelectorAll('#report-submission input, #report-submission textarea, #report-submission select');
    formInputs.forEach(input => {
        input.addEventListener('input', debounce(saveFormData, 1000));
    });

    // Save draft button
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', function() {
            saveFormData();
            showNotification('Draft saved successfully!', 'success');
        });
    }

    // Clear draft button
    const clearDraftBtn = document.getElementById('clearDraftBtn');
    if (clearDraftBtn) {
        clearDraftBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to clear the form? This will erase any unsaved data.')) {
                clearDraftData();
                showNotification('Draft cleared successfully.', 'info');
            }
        });
    }
}

function saveFormData() {
    const formData = collectFormData();
    try {
        localStorage.setItem('fa-dashboard-draft', JSON.stringify(formData));
        localStorage.setItem('fa-dashboard-draft-timestamp', new Date().toISOString());
    } catch (e) {
        console.error('Error saving to localStorage:', e);
        showNotification('Failed to save draft. Storage might be full or disabled.', 'error');
    }
}

function loadSavedData() {
    const savedData = localStorage.getItem('fa-dashboard-draft');
    if (savedData) {
        try {
            const formData = JSON.parse(savedData);
            populateForm(formData);

            const timestamp = localStorage.getItem('fa-dashboard-draft-timestamp');
            if (timestamp) {
                const date = new Date(timestamp);
                showNotification(`Draft loaded from ${date.toLocaleString()}`, 'info');
            }
        } catch (e) {
            console.error('Error loading saved data:', e);
        }
    }
}

function clearDraftData() {
    const form = document.querySelector('#report-submission form');
    if (form) {
        form.reset();
    }
    // Manually trigger change/input events to reset calculated fields
    document.querySelectorAll('.personnel-select, .expenditure-input, .budget-input, .release-input').forEach(el => {
        el.dispatchEvent(new Event('change', { bubbles: true }));
        el.dispatchEvent(new Event('input', { bubbles: true }));
    });
    
    try {
        localStorage.removeItem('fa-dashboard-draft');
        localStorage.removeItem('fa-dashboard-draft-timestamp');
    } catch (e) {
        console.error('Error clearing draft from localStorage:', e);
    }
}

function collectFormData() {
    const formData = {
        period: document.querySelector('[placeholder="e.g., January 2026"]').value,
        personnel: {},
        localRevenue: {},
        foreignRevenue: {},
        expenditure: {},
        budget: {},
        generalReport: {
            otherReports: document.querySelector('textarea').value,
            challenges: document.querySelectorAll('textarea')[1].value,
            recommendations: document.querySelectorAll('textarea')[2].value,
            conclusion: document.querySelectorAll('textarea')[3].value
        },
        officer: {
            name: document.querySelector('[placeholder="Name"]').value,
            rank: document.querySelector('[placeholder="Rank"]').value,
            phone: document.querySelector('[placeholder="Phone Number"]').value
        }
    };

    // Collect personnel data
    document.querySelectorAll('.personnel-select').forEach((select, index) => {
        const cadre = personnelData[index].cadre;
        formData.personnel[cadre] = select.value;
    });

    // Collect revenue and expenditure data
    document.querySelectorAll('.local-revenue-input').forEach(input => {
        const index = input.dataset.index;
        formData.localRevenue[localRevenueItems[index]] = input.value;
    });

    document.querySelectorAll('.foreign-revenue-input').forEach(input => {
        const index = input.dataset.index;
        formData.foreignRevenue[foreignRevenueItems[index]] = input.value;
    });

    document.querySelectorAll('.expenditure-input').forEach(input => {
        const index = input.dataset.index;
        formData.expenditure[expenditureItems[index]] = input.value;
    });

    document.querySelectorAll('.budget-input').forEach(input => {
        const index = input.dataset.index;
        if (!formData.budget[index]) formData.budget[index] = {};
        formData.budget[index].budgeted = input.value;
    });

    document.querySelectorAll('.release-input').forEach(input => {
        const index = input.dataset.index;
        if (!formData.budget[index]) formData.budget[index] = {};
        formData.budget[index].released = input.value;
    });

    document.querySelectorAll('.budget-remark').forEach(textarea => {
        const index = textarea.dataset.index;
        if (!formData.budget[index]) formData.budget[index] = {};
        formData.budget[index].remark = textarea.value;
    });

    return formData;
}

function populateForm(data) {
    // Populate basic fields
    if (data.period) {
        document.querySelector('[placeholder="e.g., January 2026"]').value = data.period;
    }

    // Populate personnel
    if (data.personnel) {
        document.querySelectorAll('.personnel-select').forEach((select, index) => {
            const cadre = personnelData[index].cadre;
            if (data.personnel[cadre]) {
                select.value = data.personnel[cadre];
            }
        });
    }

    // Populate revenue and expenditure
    if (data.localRevenue) {
        document.querySelectorAll('.local-revenue-input').forEach(input => {
            const index = input.dataset.index;
            const item = localRevenueItems[index];
            if (data.localRevenue[item]) {
                input.value = data.localRevenue[item];
            }
        });
    }

    if (data.foreignRevenue) {
        document.querySelectorAll('.foreign-revenue-input').forEach(input => {
            const index = input.dataset.index;
            const item = foreignRevenueItems[index];
            if (data.foreignRevenue[item]) {
                input.value = data.foreignRevenue[item];
            }
        });
    }

    if (data.expenditure) {
        document.querySelectorAll('.expenditure-input').forEach(input => {
            const index = input.dataset.index;
            const item = expenditureItems[index];
            if (data.expenditure[item]) {
                input.value = data.expenditure[item];
            }
        });
    }

    if (data.budget) {
        document.querySelectorAll('.budget-input').forEach(input => {
            const index = input.dataset.index;
            if (data.budget[index] && data.budget[index].budgeted) {
                input.value = data.budget[index].budgeted;
            }
        });

        document.querySelectorAll('.release-input').forEach(input => {
            const index = input.dataset.index;
            if (data.budget[index] && data.budget[index].released) {
                input.value = data.budget[index].released;
            }
        });

        document.querySelectorAll('.budget-remark').forEach(textarea => {
            const index = textarea.dataset.index;
            if (data.budget[index] && data.budget[index].remark) {
                textarea.value = data.budget[index].remark;
            }
        });
    }

    // Populate general report
    if (data.generalReport) {
        const textareas = document.querySelectorAll('#report-submission textarea');
        if (textareas[0] && data.generalReport.otherReports) {
            textareas[0].value = data.generalReport.otherReports;
        }
        if (textareas[1] && data.generalReport.challenges) {
            textareas[1].value = data.generalReport.challenges;
        }
        if (textareas[2] && data.generalReport.recommendations) {
            textareas[2].value = data.generalReport.recommendations;
        }
        if (textareas[3] && data.generalReport.conclusion) {
            textareas[3].value = data.generalReport.conclusion;
        }
    }

    // Populate officer details
    if (data.officer) {
        const officerInputs = document.querySelectorAll('.row.g-3.mb-4 input');
        if (officerInputs[0] && data.officer.name) {
            officerInputs[0].value = data.officer.name;
        }
        if (officerInputs[1] && data.officer.rank) {
            officerInputs[1].value = data.officer.rank;
        }
        if (officerInputs[2] && data.officer.phone) {
            officerInputs[2].value = data.officer.phone;
        }
    }
}

// ==========================================
// PRINT FUNCTIONALITY
// ==========================================
function initializePrintFunctionality() {
    const printBtn = document.querySelector('button[onclick="window.print()"]');
    if (printBtn) {
        printBtn.addEventListener('click', function(e) {
            e.preventDefault();
            prepareForPrint();
            window.print();
        });
    }
}

function prepareForPrint() {
    // Add print-specific styles
    const printStyles = document.createElement('style');
    printStyles.textContent = `
        @media print {
            .top-nav, .sidebar, .btn, .hero-actions { display: none !important; }
            .main-content { margin-left: 0 !important; }
            .section-card { box-shadow: none; border: 1px solid #000; }
            .report-header { background: white !important; border: 2px solid #000; }
        }
    `;
    document.head.appendChild(printStyles);

    // Clean up after printing
    window.addEventListener('afterprint', function() {
        document.head.removeChild(printStyles);
    }, { once: true });
}

// ==========================================
// APPROVAL ACTIONS
// ==========================================
function initializeApprovalActions() {
    // Use event delegation on the table body
    const approvalTable = document.querySelector('.data-table tbody');
    if (!approvalTable) return;

    approvalTable.addEventListener('click', function(e) {
        const button = e.target.closest('button[data-action]');
        if (!button) return;

        const action = button.dataset.action;
        const row = button.closest('tr');
        const statusCell = row.querySelector('td:nth-child(4)');
        const actionCell = row.querySelector('td:nth-child(5)');

        if (action === 'approve') {
            if (confirm('Are you sure you want to approve this report?')) {
                statusCell.innerHTML = '<span class="badge badge-success">Approved</span>';
                actionCell.innerHTML = '<span>Approved</span>'; // Disable further actions
                showNotification('Report approved successfully!', 'success');
            }
        } else if (action === 'reject') {
            if (confirm('Are you sure you want to reject this report?')) {
                statusCell.innerHTML = '<span class="badge badge-danger">Rejected</span>';
                actionCell.innerHTML = '<span>Rejected</span>'; // Disable further actions
                showNotification('Report rejected!', 'error');
            }
        }
    });
}

// ==========================================
// UTILITY FUNCTIONS
// ==========================================
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} notification-toast`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 300px;
        animation: slideInRight 0.3s ease-out;
    `;
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;

    document.body.appendChild(notification);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ==========================================
// LOGOUT FUNCTIONALITY
// ==========================================
function initializeLogout() {
    // Attach cleanup logic to the modal's logout button if it exists (e.g., in directorate.html)
    const confirmBtn = document.getElementById('confirmLogoutBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            clearSessionData();
        });
    }
}

function clearSessionData() {
    try {
        localStorage.removeItem('fa-dashboard-draft');
        localStorage.removeItem('fa-dashboard-draft-timestamp');
    } catch (e) {
        console.error('Error clearing session data:', e);
    }
}

function logout() {
    // Check if we have a modal available (Bootstrap 5)
    const logoutModalEl = document.getElementById('logoutModal');
    if (logoutModalEl && window.bootstrap) {
        const modal = new bootstrap.Modal(logoutModalEl);
        modal.show();
    } else {
        // Fallback for pages without modal
        if (confirm('Are you sure you want to logout?')) {
            clearSessionData();
            // Redirect to login page
            window.location.href = '../login.html';
        }
    }
}

// Export functions for global access
window.logout = logout;
