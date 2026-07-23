@php
    $slug        = $slug ?? 'isct';
    $directorate = $directorate ?? [
        'name' => 'Standards and Compliance Unit (ISCT)',
        'icon' => 'fas fa-clipboard-check',
    ];
    $allDirectorates = $allDirectorates ?? [];

    $months = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];

    $scopeAreas = [
        'FCT',
        'SOUTH-WEST',
        'SOUTH-EAST',
        'SOUTH-SOUTH',
        'NORTH-EAST',
        'NORTH-WEST',
        'NORTH-CENTRAL',
    ];

    $caseTypes = [
        'sting_operation'    => 'Sting Operation',
        'quota_abuse'        => 'Quota Abuse',
        'quota_trafficking'  => 'Quota Trafficking',
        'petitors'           => 'Petitors',
        'visa_forged'        => 'Visa/Forged Endorsement',
        'others'             => 'Others',
    ];

$actionTypes = [
        'deportation'      => 'Deportation',
        'repatriation'     => 'Repatriation',
        'eased_out'        => 'Eased Out',
        'watch_stop_listed'=> 'Watched listed/Stoplisted',
        'disciplinary'     => 'Disciplinary Action',
    ];
@endphp

<style>
/* Responsive fixes for ISCT directorate page */
@media (max-width: 768px) {
    .isct-section-nav a {
        padding: 8px 8px !important;
        font-size: 0.68rem !important;
    }
    .isct-section-nav i {
        display: none;
    }
    .nis-table th,
    .nis-table td {
        padding: 4px 5px !important;
        font-size: 0.72rem !important;
    }
    .nis-table input.ni {
        padding: 3px 4px !important;
        font-size: 0.72rem !important;
        min-width: 0 !important;
    }
}
@media (max-width: 480px) {
    .isct-section-nav a {
        padding: 6px 6px !important;
        font-size: 0.65rem !important;
    }
}
</style>
@include('partials.header')

<main class="redas-content">

    <div class="page-header" style="margin-bottom:18px;">
        <div>
            <h1 class="page-title" style="margin-bottom:4px;">
                <i class="{{ $directorate['icon'] }}" style="margin-right:8px;"></i>
                {{ $directorate['name'] }} Return
            </h1>
            <p class="page-subtitle">Monthly return &mdash; Standards and Compliance Unit (ISCT) reporting template.</p>
        </div>
        @if(auth()->user()?->role === 'directorate')
        <a href="{{ route('user.directorate.home') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to Directorates
        </a>
        @else
        <a href="{{ route('user.dashboard') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        @endif
    </div>

    @if (session('status'))
        <div style="background:#ecfdf5;border:1px solid #86efac;color:#166534;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
            <i class="fas fa-check-circle" style="margin-right:6px;"></i>{{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
            <i class="fas fa-triangle-exclamation" style="margin-right:6px;"></i>
            Please correct the {{ $errors->count() }} error(s) below before submitting.
        </div>
    @endif

    {{-- ============ STICKY SECTION NAVIGATION ============ --}}
    <div style="position:sticky;top:var(--topbar-height);z-index:95;background:#fff;border-bottom:2px solid var(--gray-100);box-shadow:0 2px 8px rgba(0,0,0,.04);margin-bottom:16px;border-radius:var(--radius-md);overflow:hidden;">
        <div style="display:flex;overflow-x:auto;scrollbar-width:none;gap:0;padding:0 4px;">
            <div style="display:flex;gap:0;padding:6px 0;">
                @php
                    $sections = [
                        ['id' => 'section-meta', 'label' => 'Report Meta', 'icon' => 'fa-file-signature'],
                        ['id' => 'section-1', 'label' => '1. Staff Strength', 'icon' => 'fa-users'],
                        ['id' => 'section-2', 'label' => '2. Staff Development', 'icon' => 'fa-chalkboard-user'],
                        ['id' => 'section-3', 'label' => '3. Inspections', 'icon' => 'fa-building'],
                        ['id' => 'section-4', 'label' => '4. Cases', 'icon' => 'fa-gavel'],
                        ['id' => 'section-5', 'label' => '5. Action Taken', 'icon' => 'fa-scale-balanced'],
                    ];
                @endphp
                @foreach($sections as $sec)
                    <a href="#{{ $sec['id'] }}"
                       style="display:flex;align-items:center;gap:6px;padding:10px 14px;font-size:.75rem;font-weight:600;color:var(--gray-600);white-space:nowrap;text-decoration:none;border-bottom:3px solid transparent;transition:all .15s;border-radius:var(--radius-sm);"
                       onmouseover="this.style.background='var(--nis-50)';this.style.color='var(--nis-700)'"
                       onmouseout="this.style.background='';this.style.color='var(--gray-600)'">
                        <i class="fas {{ $sec['icon'] }}" style="font-size:.75rem;color:var(--nis-500);"></i>
                        <span>{{ $sec['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @if(count($allDirectorates))
    <div class="redas-card" style="margin-bottom:16px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);">
                    <i class="fas fa-sitemap"></i>
                </div>
                Directorate Overview
            </div>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:10px;">
                @foreach($allDirectorates as $dirSlug => $dir)
                    <a href="{{ route('user.directorates.show', $dirSlug) }}"
                       class="btn-nis {{ $slug === $dirSlug ? 'btn-primary-nis' : 'btn-ghost' }}"
                       style="justify-content:flex-start;">
                        <i class="{{ $dir['icon'] }}"></i>
                        {{ $dir['name'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('user.directorates.store', $slug) }}" id="isct-form">
        @csrf

        {{-- ============ REPORT META ============ --}}
        <div id="section-meta" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:var(--gold-100);color:var(--gold-600);">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    Monthly Return
                </div>
            </div>
            <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Period of Returns</label>
                    <input type="month" class="ni" name="report_period" required value="{{ old('report_period', now()->format('Y-m')) }}">
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Reporting Officer</label>
                    <input type="text" class="ni" name="reporting_officer" required value="{{ old('reporting_officer', auth()->user()->name) }}">
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Directorate</label>
                    <input type="text" class="ni" value="{{ $directorate['name'] }}" readonly>
                </div>
            </div>
        </div>

        {{-- ============ 1. STAFF STRENGTH ============ --}}
        @php
            $staffCadres = [
                'comptroller'      => 'Compt. Cadre',
                'superintendent'   => 'Superintendent Cadre',
                'inspectorate'     => 'Inspectorate Cadre',
                'assistant'        => 'Assistant Cadre',
            ];
        @endphp
        <div id="section-1" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-users"></i>
                    </div>
                    1. Staff Strength <span style="font-size:.7rem;font-weight:400;color:var(--gray-500);margin-left:6px;">(Current nominal roll to be attached)</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="staff-strength-table">
                        <thead>
                            <tr>
                                <th style="min-width:200px;"></th>
                                <th style="width:120px;">Male</th>
                                <th style="width:120px;">Female</th>
                                <th style="width:120px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staffCadres as $cadreKey => $cadreLabel)
                                <tr>
                                    <td>{{ $cadreLabel }}</td>
                                    <td>
                                        <input type="number" min="0" class="ni qty-input staff-male"
                                               name="staff_strength[{{ $cadreKey }}][male]"
                                               value="{{ old('staff_strength.'.$cadreKey.'.male') }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="ni qty-input staff-female"
                                               name="staff_strength[{{ $cadreKey }}][female]"
                                               value="{{ old('staff_strength.'.$cadreKey.'.female') }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="ni row-total" readonly tabindex="-1"
                                               name="staff_strength[{{ $cadreKey }}][total]"
                                               value="{{ old('staff_strength.'.$cadreKey.'.total') }}" placeholder="0">
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td id="staff-total-male"><strong>0</strong></td>
                                <td id="staff-total-female"><strong>0</strong></td>
                                <td id="staff-total-all"><strong>0</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============ 2. STAFF DEVELOPMENT ============ --}}
        <div id="section-2" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-chalkboard-user"></i>
                    </div>
                    2. Staff Development
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="staff-dev-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th style="min-width:200px;">Title of Workshop/Seminar</th>
                                <th style="min-width:150px;">Location</th>
                                <th style="width:150px;">No. of Participants</th>
                                <th style="min-width:130px;">Date</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="staff-dev-tbody">
                            @forelse(old('staff_development', []) as $i => $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><input type="text" class="ni" name="staff_development[{{ $i }}][title]" value="{{ $row['title'] ?? '' }}" placeholder="Workshop/Seminar Title"></td>
                                    <td><input type="text" class="ni" name="staff_development[{{ $i }}][location]" value="{{ $row['location'] ?? '' }}" placeholder="Location"></td>
                                    <td><input type="number" min="0" class="ni qty-input" name="staff_development[{{ $i }}][participants]" value="{{ $row['participants'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="date" class="ni" name="staff_development[{{ $i }}][date]" value="{{ $row['date'] ?? '' }}"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @empty
                                @for($i = 0; $i < 5; $i++)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><input type="text" class="ni" name="staff_development[{{ $i }}][title]" placeholder="Workshop/Seminar Title"></td>
                                    <td><input type="text" class="ni" name="staff_development[{{ $i }}][location]" placeholder="Location"></td>
                                    <td><input type="number" min="0" class="ni" name="staff_development[{{ $i }}][participants]" placeholder="0"></td>
                                    <td><input type="date" class="ni" name="staff_development[{{ $i }}][date]"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                                @endfor
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="3" style="text-align:right;"><strong>Total</strong></td>
                                <td id="staff-dev-total"><strong>0</strong></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="add-staff-dev-row"><i class="fas fa-plus"></i> Add Row</button>
            </div>
        </div>

        {{-- ============ 3. COMPANY INSPECTION/ THEIR EXPATRIATES ============ --}}
        <div id="section-3" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-building"></i>
                    </div>
                    3. Company Inspection/their Expatriates
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="inspection-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th style="min-width:160px;">Scope of Activity</th>
                                <th style="min-width:140px;">Location</th>
                                <th style="min-width:180px;">Theme</th>
                                <th style="width:140px;">No. of Companies</th>
                                <th style="min-width:130px;">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $sn = 0; @endphp
                            @foreach($scopeAreas as $area)
                                @php $sn++; @endphp
                                <tr>
                                    <td>{{ $sn }}</td>
                                    <td><input type="text" class="ni" name="inspection[{{ $sn }}][scope]" value="{{ old('inspection.'.$sn.'.scope') }}" placeholder="Scope of Activity"></td>
                                    <td><input type="text" class="ni" name="inspection[{{ $sn }}][location]" value="{{ old('inspection.'.$sn.'.location', $area) }}" placeholder="Location"></td>
                                    <td><input type="text" class="ni" name="inspection[{{ $sn }}][theme]" value="{{ old('inspection.'.$sn.'.theme') }}" placeholder="Theme"></td>
                                    <td><input type="number" min="0" class="ni" name="inspection[{{ $sn }}][no_of_companies]" value="{{ old('inspection.'.$sn.'.no_of_companies') }}" placeholder="0"></td>
                                    <td><input type="date" class="ni" name="inspection[{{ $sn }}][date]" value="{{ old('inspection.'.$sn.'.date') }}"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============ 4. CASES ============ --}}
        <div id="section-4" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-gavel"></i>
                    </div>
                    4. Cases
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="cases-table" style="min-width:1400px;">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th style="min-width:180px;">Cases</th>
                                @foreach($months as $m)
                                    <th style="width:70px;">{{ $m }}</th>
                                @endforeach
                                <th style="width:80px;">Total</th>
                                <th style="width:80px;">Per (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $sn = 0; @endphp
                            @foreach($caseTypes as $caseKey => $caseLabel)
                                @php $sn++; @endphp
                                <tr>
                                    <td>{{ $sn }}.</td>
                                    <td><strong>{{ $caseLabel }}</strong></td>
                                    @foreach($months as $mIdx => $m)
                                        <td>
                                            <input type="number" min="0" class="ni qty-input case-input"
                                                   name="cases[{{ $caseKey }}][{{ strtolower($m) }}]"
                                                   value="{{ old('cases.'.$caseKey.'.'.strtolower($m)) }}" placeholder="0"
                                                   data-case="{{ $caseKey }}">
                                        </td>
                                    @endforeach
                                    <td>
                                        <input type="number" min="0" class="ni case-total" readonly tabindex="-1"
                                               name="cases[{{ $caseKey }}][total]"
                                               value="{{ old('cases.'.$caseKey.'.total') }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input type="text" class="ni case-percent" readonly tabindex="-1"
                                               name="cases[{{ $caseKey }}][percent]"
                                               value="{{ old('cases.'.$caseKey.'.percent') }}" placeholder="0%">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td></td>
                                <td><strong>Total</strong></td>
                                @foreach($months as $mIdx => $m)
                                    <td id="case-month-total-{{ $mIdx }}"><strong>0</strong></td>
                                @endforeach
                                <td id="case-grand-total"><strong>0</strong></td>
                                <td id="case-grand-percent"><strong>100%</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============ 5. ACTION TAKEN ============ --}}
        <div id="section-5" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-scale-balanced"></i>
                    </div>
                    5. Action Taken
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="action-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th style="min-width:250px;">Name</th>
                                <th style="width:150px;">Numbers</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $sn = 0; @endphp
                            @foreach($actionTypes as $actionKey => $actionLabel)
                                @php $sn++; @endphp
                                <tr>
                                    <td>{{ $sn }}</td>
                                    <td><strong>{{ $actionLabel }}</strong></td>
                                    <td>
                                        <input type="number" min="0" class="ni"
                                               name="action_taken[{{ $actionKey }}]"
                                               value="{{ old('action_taken.'.$actionKey) }}" placeholder="0">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============ SUBMIT ============ --}}
        <div class="redas-card">
            <div class="card-body" style="display:flex;justify-content:flex-end;gap:10px;">
                <a href="{{ route('user.dashboard') }}" class="btn-nis btn-ghost">Cancel</a>
                <button type="submit" class="btn-nis btn-primary-nis">
                    <i class="fas fa-paper-plane"></i> Submit {{ $directorate['name'] }} Return
                </button>
            </div>
        </div>
    </form>
</main>

<script>
(function () {
    'use strict';

    /* ---------- Row total auto-calculation ---------- */
    function recalcRow(tr) {
        if (!tr) return;
        const totalField = tr.querySelector('.row-total');
        if (!totalField) return;
        let sum = 0;
        tr.querySelectorAll('.qty-input').forEach(function (input) {
            sum += parseFloat(input.value) || 0;
        });
        totalField.value = sum;
    }

    document.querySelectorAll('.qty-input').forEach(function (input) {
        input.addEventListener('input', function () {
            recalcRow(input.closest('tr'));
        });
    });

    /* ---------- Staff Strength grand total ---------- */
    function recalcStaffTotals() {
        let male = 0, female = 0;
        document.querySelectorAll('.staff-male').forEach(function (i) { male += parseFloat(i.value) || 0; });
        document.querySelectorAll('.staff-female').forEach(function (i) { female += parseFloat(i.value) || 0; });
        const mEl = document.getElementById('staff-total-male');
        const fEl = document.getElementById('staff-total-female');
        const aEl = document.getElementById('staff-total-all');
        if (mEl) mEl.textContent = male;
        if (fEl) fEl.textContent = female;
        if (aEl) aEl.textContent = male + female;
    }
    document.querySelectorAll('.staff-male, .staff-female').forEach(function (input) {
        input.addEventListener('input', recalcStaffTotals);
    });
    recalcStaffTotals();

    /* ---------- Staff Development total ---------- */
    function recalcStaffDevTotal() {
        let total = 0;
        document.querySelectorAll('#staff-dev-tbody input[name$="[participants]"]').forEach(function (i) {
            total += parseFloat(i.value) || 0;
        });
        const el = document.getElementById('staff-dev-total');
        if (el) el.textContent = total;
    }
    document.querySelectorAll('#staff-dev-tbody input[name$="[participants]"]').forEach(function (input) {
        input.addEventListener('input', recalcStaffDevTotal);
    });
    recalcStaffDevTotal();

    /* ---------- Cases: monthly totals, row totals, percentages ---------- */
    function recalcCases() {
        const monthTotals = {};
        let grandTotal = 0;
        const caseRows = document.querySelectorAll('#cases-table tbody tr');
        for (let m = 0; m < 12; m++) monthTotals[m] = 0;

        caseRows.forEach(function (row) {
            let rowTotal = 0;
            const inputs = row.querySelectorAll('.case-input');
            inputs.forEach(function (input, idx) {
                const val = parseFloat(input.value) || 0;
                rowTotal += val;
                if (monthTotals[idx] !== undefined) monthTotals[idx] += val;
            });
            const totalField = row.querySelector('.case-total');
            if (totalField) totalField.value = rowTotal;
            grandTotal += rowTotal;
        });

        for (let m = 0; m < 12; m++) {
            const el = document.getElementById('case-month-total-' + m);
            if (el) el.textContent = monthTotals[m];
        }
        const grandEl = document.getElementById('case-grand-total');
        if (grandEl) grandEl.textContent = grandTotal;

        caseRows.forEach(function (row) {
            const totalField = row.querySelector('.case-total');
            const percentField = row.querySelector('.case-percent');
            if (totalField && percentField) {
                const rowTotal = parseFloat(totalField.value) || 0;
                const pct = grandTotal > 0 ? ((rowTotal / grandTotal) * 100).toFixed(1) : 0;
                percentField.value = pct + '%';
            }
        });
        const grandPct = document.getElementById('case-grand-percent');
        if (grandPct) grandPct.textContent = '100%';
    }

    document.querySelectorAll('.case-input').forEach(function (input) {
        input.addEventListener('input', recalcCases);
    });
    recalcCases();

    /* ---------- Remove-row buttons ---------- */
    function bindRemove(tr) {
        const btn = tr.querySelector('.remove-row-btn');
        if (btn && !btn.dataset.bound) {
            btn.dataset.bound = '1';
            btn.addEventListener('click', function () { tr.remove(); recalcStaffDevTotal(); });
        }
    }
    document.querySelectorAll('#staff-dev-tbody tr').forEach(bindRemove);

    /* ---------- Add row: Staff Development ---------- */
    const addStaffDevBtn = document.getElementById('add-staff-dev-row');
    if (addStaffDevBtn) {
        var rowCounter = 0;
        addStaffDevBtn.addEventListener('click', function () {
            rowCounter++;
            var tbody = document.getElementById('staff-dev-tbody');
            if (!tbody) return;
            var idx = 'new_' + Date.now() + '_' + rowCounter;
            var tr = document.createElement('tr');
            var sn = tbody.querySelectorAll('tr').length + 1;
            tr.innerHTML = '<td>' + sn + '</td>' +
                '<td><input type="text" class="ni" name="staff_development[' + idx + '][title]" placeholder="Workshop/Seminar Title"></td>' +
                '<td><input type="text" class="ni" name="staff_development[' + idx + '][location]" placeholder="Location"></td>' +
                '<td><input type="number" min="0" class="ni" name="staff_development[' + idx + '][participants]" placeholder="0"></td>' +
                '<td><input type="date" class="ni" name="staff_development[' + idx + '][date]"></td>' +
                '<td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>';
            tbody.appendChild(tr);
            bindRemove(tr);
        });
    }

    /* ---------- Form submit validation ---------- */
    const form = document.getElementById('isct-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            const period = form.querySelector('[name="report_period"]');
            if (period && !period.value) {
                e.preventDefault();
                period.focus();
                alert('Please select the report period before submitting.');
            }
        });
    }
})();
</script>

@include('partials.footer')

