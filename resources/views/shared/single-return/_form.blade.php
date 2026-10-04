@php
    $isEditing = isset($editing) && $editing instanceof \App\Models\Application;
    $editData = $isEditing && is_array($editing->return_data) ? $editing->return_data : [];
    $periodValue = old('period', $editData['period'] ?? $editData['report_period'] ?? now()->format('Y-m'));
    $selectedType = old('return_type', $editData['return_type'] ?? request('type', 'monthly'));
    $officerValue = old('reporting_officer', $editData['reporting_officer'] ?? (auth()->user()?->name ?? ''));
    $commandValue = old('command_name', $editData['command_name'] ?? ($commandName ?? ''));
@endphp

<div class="page-header" style="margin-bottom:18px;">
    <div>
        <h1 class="page-title" style="margin-bottom:4px;">
            <i class="fas fa-file-lines" style="margin-right:8px;"></i>
            {{ $isEditing ? 'Edit ' : '' }}{{ $title ?? 'Return' }}
        </h1>
        <p class="page-subtitle">{{ $isEditing ? 'Update and resubmit the return below.' : 'Complete all applicable sections and submit for review.' }}</p>
    </div>
    <a href="{{ route($backRoute ?? 'home') }}" class="btn-nis btn-ghost">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

@if (session('status'))
    <div style="background:#ecfdf5;border:1px solid #86efac;color:#166534;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
        <i class="fas fa-check-circle" style="margin-right:6px;"></i>{{ session('status') }}
    </div>
@endif

@if (session('error'))
    <div style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
        <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>{{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
        <div style="font-weight:700;"><i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>The return could not be submitted. Please fix the following:</div>
        <ul style="margin:6px 0 0 22px;font-size:.84rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form id="singleReturnForm" method="POST" enctype="multipart/form-data" action="{{ $formAction }}">
    @csrf
    @if($isEditing)
        @method('PUT')
    @endif

    {{-- Header --}}
    <div class="nis-section" style="margin-bottom:16px;">
        <div class="nis-section-head">
            <span class="sec-num">A</span>
            Return Header
        </div>
        <div class="nis-section-body">
            <div class="form-grid-4" style="align-items:end;">
                <div class="fg">
                    <label>{{ $commandLabel ?? 'Command' }}</label>
                    <input type="text" name="command_name" class="ni" value="{{ $commandValue }}" readonly>
                </div>
                <div class="fg">
                    <label>Return Period</label>
                    <input type="month" name="period" class="ni" required value="{{ $periodValue }}">
                </div>
                <div class="fg">
                    <label>Return Type</label>
                    <select name="return_type" class="ni ni-select" required>
                        @foreach(['monthly' => 'Monthly Return', 'quarterly' => 'Quarterly Return', 'biannual' => 'Bi-Annual Return', 'annual' => 'Annual Return', 'special' => 'Special Report'] as $value => $label)
                            <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg">
                    <label>Reporting Officer</label>
                    <input type="text" name="reporting_officer" class="ni" value="{{ $officerValue }}" readonly>
                </div>
            </div>
        </div>
    </div>

    {{-- Personnel Strength --}}
    <div class="nis-section" style="margin-bottom:16px;">
        <div class="nis-section-head"><span class="sec-num">B</span> Personnel Strength</div>
        <div class="nis-section-body">
            <table class="redas-table" id="personnelTable">
                <thead>
                    <tr>
                        <th>Cadre / Rank</th>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Total</th>
                        <th style="width:40px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $personnelRows = old('personnel', $editData['personnel'] ?? [[]]);
                        if (!is_array($personnelRows) || empty($personnelRows)) $personnelRows = [[]];
                    @endphp
                    @foreach($personnelRows as $i => $row)
                    <tr class="personnel-row">
                        <td><input type="text" name="personnel[{{ $i }}][cadre]" class="ni" value="{{ is_array($row) ? ($row['cadre'] ?? '') : '' }}" placeholder="e.g. Comptroller"></td>
                        <td><input type="number" name="personnel[{{ $i }}][male]" class="ni personnel-male" value="{{ is_array($row) ? ($row['male'] ?? '') : '' }}" min="0"></td>
                        <td><input type="number" name="personnel[{{ $i }}][female]" class="ni personnel-female" value="{{ is_array($row) ? ($row['female'] ?? '') : '' }}" min="0"></td>
                        <td><input type="number" name="personnel[{{ $i }}][total]" class="ni personnel-total" value="{{ is_array($row) ? ($row['total'] ?? '') : '' }}" min="0" readonly></td>
                        <td><button type="button" class="btn-nis btn-ghost btn-sm remove-row" title="Remove row"><i class="fas fa-trash"></i></button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" class="btn-nis btn-outline-nis btn-sm" id="addPersonnelRow"><i class="fas fa-plus"></i> Add Cadre</button>
        </div>
    </div>

    {{-- Operations Summary --}}
    <div class="nis-section" style="margin-bottom:16px;">
        <div class="nis-section-head"><span class="sec-num">C</span> Operations Summary</div>
        <div class="nis-section-body">
            <div class="fg" style="margin-bottom:12px;">
                <label>Summary of Operations</label>
                <textarea name="operations[summary]" class="ni" rows="4">{{ old('operations.summary', $editData['operations']['summary'] ?? '') }}</textarea>
            </div>
            <table class="redas-table" id="operationsTable">
                <thead>
                    <tr>
                        <th>Operation / Activity</th>
                        <th>Count / Value</th>
                        <th>Remarks</th>
                        <th style="width:40px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $operationRows = old('operations.items', $editData['operations']['items'] ?? [[]]);
                        if (!is_array($operationRows) || empty($operationRows)) $operationRows = [[]];
                    @endphp
                    @foreach($operationRows as $i => $row)
                    <tr class="operation-row">
                        <td><input type="text" name="operations[items][{{ $i }}][activity]" class="ni" value="{{ is_array($row) ? ($row['activity'] ?? '') : '' }}"></td>
                        <td><input type="text" name="operations[items][{{ $i }}][count]" class="ni" value="{{ is_array($row) ? ($row['count'] ?? '') : '' }}"></td>
                        <td><input type="text" name="operations[items][{{ $i }}][remarks]" class="ni" value="{{ is_array($row) ? ($row['remarks'] ?? '') : '' }}"></td>
                        <td><button type="button" class="btn-nis btn-ghost btn-sm remove-row" title="Remove row"><i class="fas fa-trash"></i></button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" class="btn-nis btn-outline-nis btn-sm" id="addOperationRow"><i class="fas fa-plus"></i> Add Operation</button>
        </div>
    </div>

    {{-- Logistics / Equipment --}}
    <div class="nis-section" style="margin-bottom:16px;">
        <div class="nis-section-head"><span class="sec-num">D</span> Logistics / Equipment</div>
        <div class="nis-section-body">
            <table class="redas-table" id="logisticsTable">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Condition</th>
                        <th>Remarks</th>
                        <th style="width:40px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $logisticsRows = old('logistics', $editData['logistics'] ?? [[]]);
                        if (!is_array($logisticsRows) || empty($logisticsRows)) $logisticsRows = [[]];
                    @endphp
                    @foreach($logisticsRows as $i => $row)
                    <tr class="logistics-row">
                        <td><input type="text" name="logistics[{{ $i }}][item]" class="ni" value="{{ is_array($row) ? ($row['item'] ?? '') : '' }}"></td>
                        <td><input type="number" name="logistics[{{ $i }}][quantity]" class="ni" value="{{ is_array($row) ? ($row['quantity'] ?? '') : '' }}" min="0"></td>
                        <td>
                            <select name="logistics[{{ $i }}][condition]" class="ni ni-select">
                                <option value="">Select</option>
                                <option value="serviceable" @selected((is_array($row) ? ($row['condition'] ?? '') : '') === 'serviceable')>Serviceable</option>
                                <option value="unserviceable" @selected((is_array($row) ? ($row['condition'] ?? '') : '') === 'unserviceable')>Unserviceable</option>
                                <option value="under_repair" @selected((is_array($row) ? ($row['condition'] ?? '') : '') === 'under_repair')>Under Repair</option>
                            </select>
                        </td>
                        <td><input type="text" name="logistics[{{ $i }}][remarks]" class="ni" value="{{ is_array($row) ? ($row['remarks'] ?? '') : '' }}"></td>
                        <td><button type="button" class="btn-nis btn-ghost btn-sm remove-row" title="Remove row"><i class="fas fa-trash"></i></button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" class="btn-nis btn-outline-nis btn-sm" id="addLogisticsRow"><i class="fas fa-plus"></i> Add Item</button>
        </div>
    </div>

    {{-- Finance / Budget --}}
    <div class="nis-section" style="margin-bottom:16px;">
        <div class="nis-section-head"><span class="sec-num">E</span> Finance / Budget Summary</div>
        <div class="nis-section-body">
            <div class="form-grid-3" style="align-items:end;">
                <div class="fg">
                    <label>Budget Allocated (₦)</label>
                    <input type="number" name="finance[budget_allocated]" class="ni finance-input" step="0.01" min="0" value="{{ old('finance.budget_allocated', $editData['finance']['budget_allocated'] ?? '') }}">
                </div>
                <div class="fg">
                    <label>Expenditure (₦)</label>
                    <input type="number" name="finance[expenditure]" class="ni finance-input" step="0.01" min="0" value="{{ old('finance.expenditure', $editData['finance']['expenditure'] ?? '') }}">
                </div>
                <div class="fg">
                    <label>Balance (₦)</label>
                    <input type="number" name="finance[balance]" class="ni" step="0.01" min="0" value="{{ old('finance.balance', $editData['finance']['balance'] ?? '') }}" readonly>
                </div>
            </div>
        </div>
    </div>

    {{-- Challenges & Way Forward --}}
    <div class="nis-section" style="margin-bottom:16px;">
        <div class="nis-section-head"><span class="sec-num">F</span> Challenges &amp; Way Forward</div>
        <div class="nis-section-body">
            <div class="fg" style="margin-bottom:12px;">
                <label>Key Challenges</label>
                <textarea name="challenges[challenges]" class="ni" rows="4">{{ old('challenges.challenges', $editData['challenges']['challenges'] ?? '') }}</textarea>
            </div>
            <div class="fg">
                <label>Way Forward / Recommendations</label>
                <textarea name="challenges[way_forward]" class="ni" rows="4">{{ old('challenges.way_forward', $editData['challenges']['way_forward'] ?? '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Declaration & Attachments --}}
    <div class="nis-section" style="margin-bottom:16px;">
        <div class="nis-section-head"><span class="sec-num">§D</span> Declaration &amp; Attachments</div>
        <div class="nis-section-body">
            <div class="fg" style="margin-bottom:12px;">
                <label><i class="fas fa-paperclip"></i> Attachments <span style="font-weight:400;color:var(--gray-400);text-transform:none;">(PDF, Word, images — max 20MB each)</span></label>
                <input type="file" name="attachments[]" class="ni" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                @if($isEditing && !empty($editData['attachments']))
                    <p style="font-size:.74rem;color:var(--gray-500);margin-top:6px;">
                        Existing attachments (upload new files to replace): {{ count($editData['attachments']) }} file(s)
                    </p>
                @endif
            </div>
            <div style="background:var(--gray-50);border:1px solid var(--gray-200);border-radius:var(--radius-md);padding:16px;">
                <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;font-size:.84rem;color:var(--gray-700);">
                    <input type="checkbox" name="data_consent" value="1" required style="accent-color:var(--nis-600);margin-top:2px;" @checked(old('data_consent'))>
                    <span>
                        I confirm that the data provided is limited to what is necessary for official NIS reporting, and that I have authority to submit it.
                        I understand the data will be processed and retained in accordance with the <a href="{{ route('privacy') }}" target="_blank" style="color:#1d4ed8;text-decoration:underline;">Privacy Policy</a>.
                    </span>
                </label>
            </div>
        </div>
    </div>

    <div class="state-return-actions" style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
        <button type="submit" class="btn-nis btn-primary-nis" id="singleSubmitBtn"><i class="fas fa-paper-plane"></i> {{ $isEditing ? 'Update & Resubmit' : 'Submit Return' }}</button>
    </div>
</form>

<script>
(function () {
    var form = document.getElementById('singleReturnForm');
    if (!form) return;

    function recomputePersonnel(row) {
        var male = parseFloat(row.querySelector('.personnel-male')?.value) || 0;
        var female = parseFloat(row.querySelector('.personnel-female')?.value) || 0;
        var totalInput = row.querySelector('.personnel-total');
        if (totalInput) totalInput.value = male + female;
    }

    function recomputeFinance() {
        var budget = parseFloat(form.querySelector('[name="finance[budget_allocated]"]')?.value) || 0;
        var exp = parseFloat(form.querySelector('[name="finance[expenditure]"]')?.value) || 0;
        var balanceInput = form.querySelector('[name="finance[balance]"]');
        if (balanceInput) balanceInput.value = Math.max(0, budget - exp).toFixed(2);
    }

    form.addEventListener('input', function (e) {
        var row = e.target.closest('.personnel-row');
        if (row) recomputePersonnel(row);
        if (e.target.classList.contains('finance-input')) recomputeFinance();
    });

    function addRow(tableBody, templateRow) {
        var count = tableBody.querySelectorAll('tr').length;
        var newRow = templateRow.cloneNode(true);
        newRow.querySelectorAll('input, select, textarea').forEach(function (input) {
            input.name = input.name.replace(/\[\d+\]/g, '[' + count + ']');
            input.value = '';
            if (input.tagName === 'SELECT') input.selectedIndex = 0;
        });
        tableBody.appendChild(newRow);
    }

    document.getElementById('addPersonnelRow')?.addEventListener('click', function () {
        var tbody = document.querySelector('#personnelTable tbody');
        var template = tbody.querySelector('.personnel-row');
        if (tbody && template) addRow(tbody, template);
    });

    document.getElementById('addOperationRow')?.addEventListener('click', function () {
        var tbody = document.querySelector('#operationsTable tbody');
        var template = tbody.querySelector('.operation-row');
        if (tbody && template) addRow(tbody, template);
    });

    document.getElementById('addLogisticsRow')?.addEventListener('click', function () {
        var tbody = document.querySelector('#logisticsTable tbody');
        var template = tbody.querySelector('.logistics-row');
        if (tbody && template) addRow(tbody, template);
    });

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.remove-row');
        if (!btn) return;
        var tbody = btn.closest('tbody');
        var rows = tbody.querySelectorAll('tr');
        if (rows.length > 1) btn.closest('tr').remove();
    });

    var submitBtn = document.getElementById('singleSubmitBtn');
    if (submitBtn) {
        submitBtn.addEventListener('click', function (e) {
            if (!form.checkValidity()) return;
            if (!window.confirm('Submit this return for review? Please verify all sections before continuing.')) {
                e.preventDefault();
                return;
            }
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        });
    }

    form.dispatchEvent(new Event('input', { bubbles: true }));
})();
</script>
