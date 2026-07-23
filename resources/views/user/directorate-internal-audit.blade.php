@php
    $slug        = $slug ?? 'internal-audit';
    $directorate = $directorate ?? [
        'name' => 'Internal Audit',
        'icon' => 'fas fa-file-invoice',
    ];
    $allDirectorates = $allDirectorates ?? [];

    $quarters = ['1st', '2nd', '3rd', '4th'];

    $passportTypes = [
        'passport_32' => 'Passport: 32 Page',
        'passport_64' => 'Passport: 64 Page',
        'etc'         => 'ECOWAS Travelling Certificate (ETC)',
        'erc'         => 'ECOWAS Residence Card (ERC)',
        'operations'  => 'Operations',
    ];
@endphp

<style>
/* Responsive fixes for Internal Audit directorate page */
@media (max-width: 768px) {
    .audit-section-nav a {
        padding: 8px 8px !important;
        font-size: 0.68rem !important;
    }
    .audit-section-nav i {
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
    .audit-section-nav a {
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
                {{ $directorate['name'] }} Report
            </h1>
            <p class="page-subtitle">Quarterly audit report &mdash; Internal Audit Directorate reporting template.</p>
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
        <div class="audit-section-nav" style="display:flex;overflow-x:auto;scrollbar-width:none;gap:0;padding:0 4px;">
            <div style="display:flex;gap:0;padding:6px 0;">
                @php
                    $sections = [
                        ['id' => 'section-meta', 'label' => 'Report Meta', 'icon' => 'fa-file-signature'],
                        ['id' => 'section-1', 'label' => '1. Introduction', 'icon' => 'fa-align-left'],
                        ['id' => 'section-2', 'label' => '2. Commands', 'icon' => 'fa-list'],
                        ['id' => 'section-3', 'label' => '3. Funds', 'icon' => 'fa-coins'],
                        ['id' => 'section-4', 'label' => '4. Revenue', 'icon' => 'fa-chart-line'],
                        ['id' => 'section-5', 'label' => '5. Stock (66pg)', 'icon' => 'fa-book'],
                        ['id' => 'section-6', 'label' => '6. Stock (34pg)', 'icon' => 'fa-book-open'],
                        ['id' => 'section-7', 'label' => '7. Stock (ETC)', 'icon' => 'fa-ticket'],
                        ['id' => 'section-8', 'label' => '8. Stock (ERC)', 'icon' => 'fa-id-card'],
                        ['id' => 'section-9', 'label' => '9. Observations', 'icon' => 'fa-clipboard-list'],
                        ['id' => 'section-10', 'label' => '10. Conclusion', 'icon' => 'fa-check-double'],
                        ['id' => 'section-sign', 'label' => 'Sign Off', 'icon' => 'fa-pen-fancy'],
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

    <form method="POST" action="{{ route('user.directorates.store', $slug) }}" id="internal-audit-form">
        @csrf

        {{-- ============ REPORT META ============ --}}
        <div id="section-meta" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:var(--gold-100);color:var(--gold-600);">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    Audit Report Meta
                </div>
            </div>
            <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Year</label>
                    <select class="ni ni-select" name="report_year" required>
                        <option value="">Select Year</option>
                        @for($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ old('report_year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Quarter</label>
                    <select class="ni ni-select" name="report_quarter" required>
                        <option value="">Select Quarter</option>
                        @foreach($quarters as $q)
                            @php $qVal = strtolower($q); @endphp
                            <option value="{{ $qVal }}" {{ old('report_quarter') == $qVal ? 'selected' : '' }}>{{ $q }} Quarter</option>
                        @endforeach
                    </select>
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

        {{-- ============ 1. INTRODUCTION ============ --}}
        <div id="section-1" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-align-left"></i>
                    </div>
                    1. Introduction
                </div>
            </div>
            <div class="card-body">
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">
                        <i class="fas fa-info-circle" style="color:var(--nis-500);"></i>
                        Audit Context
                    </label>
                    <textarea class="ni" name="introduction_context" rows="4" placeholder="The audit of the account books and records of the Commands and Formations was carried out in accordance with the provisions of the Financial Regulations, Public Service Rules and the Service Audit Guide...">{{ old('introduction_context') }}</textarea>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">
                        <i class="fas fa-bullseye" style="color:var(--nis-500);"></i>
                        Objective and Scope
                    </label>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:10px;">
                        <div style="padding:10px 14px;background:var(--nis-50);border-radius:var(--radius-sm);font-size:.82rem;">
                            <i class="fas fa-check-circle" style="color:var(--nis-600);margin-right:6px;"></i>
                            Ensure compliance with Financial Regulations and Extant Circulars
                        </div>
                        <div style="padding:10px 14px;background:var(--nis-50);border-radius:var(--radius-sm);font-size:.82rem;">
                            <i class="fas fa-check-circle" style="color:var(--nis-600);margin-right:6px;"></i>
                            Assess adequacy of internal controls
                        </div>
                        <div style="padding:10px 14px;background:var(--nis-50);border-radius:var(--radius-sm);font-size:.82rem;">
                            <i class="fas fa-check-circle" style="color:var(--nis-600);margin-right:6px;"></i>
                            Express audit opinion on economy, efficiency and effectiveness
                        </div>
                    </div>
                    <textarea class="ni" name="introduction_objective" rows="3" placeholder="Additional objectives or scope notes...">{{ old('introduction_objective') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ============ 2. COMMANDS/FORMATIONS COVERED ============ --}}
        <div id="section-2" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-list"></i>
                    </div>
                    2. Commands/Formations Covered
                </div>
            </div>
            <div class="card-body">
                <p style="font-size:.82rem;color:var(--gray-500);margin-bottom:10px;">
                    The audit inspection covered the following Commands/Formations:
                </p>
                <div class="table-responsive">
                    <table class="nis-table" id="commands-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th style="min-width:200px;">Command</th>
                                <th style="min-width:200px;">Period Covered</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="commands-tbody">
                            @forelse(old('commands', []) as $i => $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><input type="text" class="ni" name="commands[{{ $i }}][name]" value="{{ $row['name'] ?? '' }}" placeholder="Command Name"></td>
                                    <td><input type="text" class="ni" name="commands[{{ $i }}][period]" value="{{ $row['period'] ?? '' }}" placeholder="e.g. January, 20.. to December, 20.."></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @empty
                                @for($i = 0; $i < 5; $i++)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><input type="text" class="ni" name="commands[{{ $i }}][name]" placeholder="Command Name"></td>
                                    <td><input type="text" class="ni" name="commands[{{ $i }}][period]" placeholder="e.g. January, 20.. to December, 20.."></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                                @endfor
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="add-command-row"><i class="fas fa-plus"></i> Add Row</button>
            </div>
        </div>

        {{-- ============ 3. FUNDS ALLOCATED AND UTILISED ============ --}}
        <div id="section-3" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-coins"></i>
                    </div>
                    3. Funds Allocated and Utilised
                </div>
            </div>
            <div class="card-body">
                <p style="font-size:.82rem;color:var(--gray-500);margin-bottom:10px;">
                    AIE's released, utilised and the balance (if any) by the Commands covered under the audit inspection.
                </p>
                <div class="table-responsive">
                    <table class="nis-table" id="funds-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th style="min-width:200px;">Command / Formation</th>
                                <th style="min-width:140px;">Amount Allocated (₦)</th>
                                <th style="min-width:140px;">Amount Utilised (₦)</th>
                                <th style="min-width:120px;">Balance (₦)</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="funds-tbody">
                            @forelse(old('funds', []) as $i => $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><input type="text" class="ni" name="funds[{{ $i }}][command]" value="{{ $row['command'] ?? '' }}" placeholder="Command / Formation"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni qty-input funds-allocated" name="funds[{{ $i }}][allocated]" value="{{ $row['allocated'] ?? '' }}" placeholder="0.00"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni qty-input funds-utilised" name="funds[{{ $i }}][utilised]" value="{{ $row['utilised'] ?? '' }}" placeholder="0.00"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni row-total" readonly tabindex="-1" name="funds[{{ $i }}][balance]" value="{{ $row['balance'] ?? '' }}" placeholder="0.00"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @empty
                                @for($i = 0; $i < 3; $i++)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><input type="text" class="ni" name="funds[{{ $i }}][command]" placeholder="Command / Formation"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni qty-input funds-allocated" name="funds[{{ $i }}][allocated]" placeholder="0.00"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni qty-input funds-utilised" name="funds[{{ $i }}][utilised]" placeholder="0.00"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni row-total funds-balance" readonly tabindex="-1" name="funds[{{ $i }}][balance]" placeholder="0.00"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                                @endfor
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2" style="text-align:right;"><strong>TOTAL</strong></td>
                                <td><strong id="funds-total-allocated">0.00</strong></td>
                                <td><strong id="funds-total-utilised">0.00</strong></td>
                                <td><strong id="funds-total-balance">0.00</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="add-funds-row"><i class="fas fa-plus"></i> Add Row</button>
            </div>
        </div>

        {{-- ============ 4. REVENUE GENERATED ============ --}}
        <div id="section-4" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    4. Revenue Generated by Commands/Formations
                </div>
            </div>
            <div class="card-body">
                <p style="font-size:.82rem;color:var(--gray-500);margin-bottom:10px;">
                    Revenue generated by Commands/Formations during the period.
                </p>
                <div class="table-responsive">
                    <table class="nis-table" id="revenue-table" style="min-width:1200px;">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th style="min-width:160px;">Command</th>
                                <th>Passport: 32 Page</th>
                                <th>Passport: 64 Page</th>
                                <th>ECOWAS Travelling Cert. (ETC)</th>
                                <th>ECOWAS Residence Card (ERC)</th>
                                <th>Operations</th>
                                <th style="width:110px;">Total</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="revenue-tbody">
                            @forelse(old('revenue', []) as $i => $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><input type="text" class="ni" name="revenue[{{ $i }}][command]" value="{{ $row['command'] ?? '' }}" placeholder="Command"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni qty-input rev-input" name="revenue[{{ $i }}][passport_32]" value="{{ $row['passport_32'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni qty-input rev-input" name="revenue[{{ $i }}][passport_64]" value="{{ $row['passport_64'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni qty-input rev-input" name="revenue[{{ $i }}][etc]" value="{{ $row['etc'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni qty-input rev-input" name="revenue[{{ $i }}][erc]" value="{{ $row['erc'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni qty-input rev-input" name="revenue[{{ $i }}][operations]" value="{{ $row['operations'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni row-total" readonly tabindex="-1" name="revenue[{{ $i }}][total]" value="{{ $row['total'] ?? '' }}" placeholder="0"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @empty
                                @for($i = 0; $i < 3; $i++)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><input type="text" class="ni" name="revenue[{{ $i }}][command]" placeholder="Command"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni qty-input rev-input" name="revenue[{{ $i }}][passport_32]" placeholder="0"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni qty-input rev-input" name="revenue[{{ $i }}][passport_64]" placeholder="0"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni qty-input rev-input" name="revenue[{{ $i }}][etc]" placeholder="0"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni qty-input rev-input" name="revenue[{{ $i }}][erc]" placeholder="0"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni qty-input rev-input" name="revenue[{{ $i }}][operations]" placeholder="0"></td>
                                    <td><input type="number" min="0" step="0.01" class="ni row-total" readonly tabindex="-1" name="revenue[{{ $i }}][total]" placeholder="0"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                                @endfor
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2" style="text-align:right;"><strong>TOTAL</strong></td>
                                <td id="rev-total-32"><strong>0</strong></td>
                                <td id="rev-total-64"><strong>0</strong></td>
                                <td id="rev-total-etc"><strong>0</strong></td>
                                <td id="rev-total-erc"><strong>0</strong></td>
                                <td id="rev-total-ops"><strong>0</strong></td>
                                <td id="rev-grand-total"><strong>0</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="add-revenue-row"><i class="fas fa-plus"></i> Add Row</button>
            </div>
        </div>

        {{-- ============ 5. STOCK OF 66-PAGE PASSPORT BOOKLETS ============ --}}
        <div id="section-5" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-book"></i>
                    </div>
                    5. Stock of 66-Page Passport Booklets
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="min-width:1000px;">
                    <table class="nis-table" id="stock66-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th style="min-width:160px;">Command</th>
                                <th style="min-width:100px;">Opening Stock</th>
                                <th style="min-width:100px;">Supplies from SHQ</th>
                                <th style="min-width:100px;">Total Stock Available</th>
                                <th style="min-width:100px;">Facility Issued</th>
                                <th style="min-width:100px;">Damage</th>
                                <th style="min-width:110px;">Closing Stock</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="stock66-tbody">
                            @forelse(old('stock_66', []) as $i => $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><input type="text" class="ni" name="stock_66[{{ $i }}][command]" value="{{ $row['command'] ?? '' }}" placeholder="Command"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk66-opening" name="stock_66[{{ $i }}][opening]" value="{{ $row['opening'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk66-supplies" name="stock_66[{{ $i }}][supplies]" value="{{ $row['supplies'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni stk66-total-avail" readonly tabindex="-1" name="stock_66[{{ $i }}][total_available]" value="{{ $row['total_available'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk66-issued" name="stock_66[{{ $i }}][issued]" value="{{ $row['issued'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk66-damage" name="stock_66[{{ $i }}][damage]" value="{{ $row['damage'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni stk66-closing" readonly tabindex="-1" name="stock_66[{{ $i }}][closing]" value="{{ $row['closing'] ?? '' }}" placeholder="0"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @empty
                                @for($i = 0; $i < 3; $i++)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><input type="text" class="ni" name="stock_66[{{ $i }}][command]" placeholder="Command"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk66-opening" name="stock_66[{{ $i }}][opening]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk66-supplies" name="stock_66[{{ $i }}][supplies]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni stk66-total-avail" readonly tabindex="-1" name="stock_66[{{ $i }}][total_available]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk66-issued" name="stock_66[{{ $i }}][issued]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk66-damage" name="stock_66[{{ $i }}][damage]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni stk66-closing" readonly tabindex="-1" name="stock_66[{{ $i }}][closing]" placeholder="0"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                                @endfor
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="add-stock66-row"><i class="fas fa-plus"></i> Add Row</button>
            </div>
        </div>

        {{-- ============ 6. STOCK OF 34-PAGE PASSPORT BOOKLETS ============ --}}
        <div id="section-6" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-book-open"></i>
                    </div>
                    6. Stock of 34-Page Passport Booklets
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="min-width:1000px;">
                    <table class="nis-table" id="stock34-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th style="min-width:160px;">Command</th>
                                <th style="min-width:100px;">Opening Stock</th>
                                <th style="min-width:100px;">Supplies from SHQ</th>
                                <th style="min-width:100px;">Total Stock Available</th>
                                <th style="min-width:100px;">Facility Issued</th>
                                <th style="min-width:100px;">Damage</th>
                                <th style="min-width:110px;">Closing Stock</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="stock34-tbody">
                            @forelse(old('stock_34', []) as $i => $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><input type="text" class="ni" name="stock_34[{{ $i }}][command]" value="{{ $row['command'] ?? '' }}" placeholder="Command"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk34-opening" name="stock_34[{{ $i }}][opening]" value="{{ $row['opening'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk34-supplies" name="stock_34[{{ $i }}][supplies]" value="{{ $row['supplies'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni stk34-total-avail" readonly tabindex="-1" name="stock_34[{{ $i }}][total_available]" value="{{ $row['total_available'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk34-issued" name="stock_34[{{ $i }}][issued]" value="{{ $row['issued'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk34-damage" name="stock_34[{{ $i }}][damage]" value="{{ $row['damage'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni stk34-closing" readonly tabindex="-1" name="stock_34[{{ $i }}][closing]" value="{{ $row['closing'] ?? '' }}" placeholder="0"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @empty
                                @for($i = 0; $i < 3; $i++)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><input type="text" class="ni" name="stock_34[{{ $i }}][command]" placeholder="Command"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk34-opening" name="stock_34[{{ $i }}][opening]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk34-supplies" name="stock_34[{{ $i }}][supplies]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni stk34-total-avail" readonly tabindex="-1" name="stock_34[{{ $i }}][total_available]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk34-issued" name="stock_34[{{ $i }}][issued]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk34-damage" name="stock_34[{{ $i }}][damage]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni stk34-closing" readonly tabindex="-1" name="stock_34[{{ $i }}][closing]" placeholder="0"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                                @endfor
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="add-stock34-row"><i class="fas fa-plus"></i> Add Row</button>
            </div>
        </div>

        {{-- ============ 7. STOCK OF ECOWAS TRAVELLING CERTIFICATE (ETC) ============ --}}
        <div id="section-7" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-ticket"></i>
                    </div>
                    7. Stock of ECOWAS Travelling Certificate (ETC)
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="min-width:1000px;">
                    <table class="nis-table" id="stock-etc-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th style="min-width:160px;">Command</th>
                                <th style="min-width:100px;">Opening Stock</th>
                                <th style="min-width:100px;">Supplies from SHQ</th>
                                <th style="min-width:100px;">Total Stock Available</th>
                                <th style="min-width:100px;">Facility Issued</th>
                                <th style="min-width:100px;">Damage</th>
                                <th style="min-width:110px;">Closing Stock</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="stock-etc-tbody">
                            @forelse(old('stock_etc', []) as $i => $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><input type="text" class="ni" name="stock_etc[{{ $i }}][command]" value="{{ $row['command'] ?? '' }}" placeholder="Command"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk-etc-opening" name="stock_etc[{{ $i }}][opening]" value="{{ $row['opening'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk-etc-supplies" name="stock_etc[{{ $i }}][supplies]" value="{{ $row['supplies'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni stk-etc-total-avail" readonly tabindex="-1" name="stock_etc[{{ $i }}][total_available]" value="{{ $row['total_available'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk-etc-issued" name="stock_etc[{{ $i }}][issued]" value="{{ $row['issued'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk-etc-damage" name="stock_etc[{{ $i }}][damage]" value="{{ $row['damage'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni stk-etc-closing" readonly tabindex="-1" name="stock_etc[{{ $i }}][closing]" value="{{ $row['closing'] ?? '' }}" placeholder="0"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @empty
                                @for($i = 0; $i < 3; $i++)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><input type="text" class="ni" name="stock_etc[{{ $i }}][command]" placeholder="Command"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk-etc-opening" name="stock_etc[{{ $i }}][opening]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk-etc-supplies" name="stock_etc[{{ $i }}][supplies]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni stk-etc-total-avail" readonly tabindex="-1" name="stock_etc[{{ $i }}][total_available]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk-etc-issued" name="stock_etc[{{ $i }}][issued]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk-etc-damage" name="stock_etc[{{ $i }}][damage]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni stk-etc-closing" readonly tabindex="-1" name="stock_etc[{{ $i }}][closing]" placeholder="0"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                                @endfor
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="add-stock-etc-row"><i class="fas fa-plus"></i> Add Row</button>
            </div>
        </div>

        {{-- ============ 8. STOCK OF ECOWAS RESIDENCE CARDS (ERC) ============ --}}
        <div id="section-8" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-id-card"></i>
                    </div>
                    8. Stock of ECOWAS Residence Cards (ERC)
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="min-width:1000px;">
                    <table class="nis-table" id="stock-erc-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th style="min-width:160px;">Command / Formation</th>
                                <th style="min-width:100px;">Opening Stock</th>
                                <th style="min-width:100px;">Supplies from SHQ</th>
                                <th style="min-width:100px;">Total Stock Available</th>
                                <th style="min-width:100px;">Facility Issued</th>
                                <th style="min-width:100px;">Damage</th>
                                <th style="min-width:110px;">Closing Stock</th>
                                <th style="width:44px;"></th>
                            </tr>
                        </thead>
                        <tbody id="stock-erc-tbody">
                            @forelse(old('stock_erc', []) as $i => $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><input type="text" class="ni" name="stock_erc[{{ $i }}][command]" value="{{ $row['command'] ?? '' }}" placeholder="Command / Formation"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk-erc-opening" name="stock_erc[{{ $i }}][opening]" value="{{ $row['opening'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk-erc-supplies" name="stock_erc[{{ $i }}][supplies]" value="{{ $row['supplies'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni stk-erc-total-avail" readonly tabindex="-1" name="stock_erc[{{ $i }}][total_available]" value="{{ $row['total_available'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk-erc-issued" name="stock_erc[{{ $i }}][issued]" value="{{ $row['issued'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk-erc-damage" name="stock_erc[{{ $i }}][damage]" value="{{ $row['damage'] ?? '' }}" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni stk-erc-closing" readonly tabindex="-1" name="stock_erc[{{ $i }}][closing]" value="{{ $row['closing'] ?? '' }}" placeholder="0"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @empty
                                @for($i = 0; $i < 3; $i++)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><input type="text" class="ni" name="stock_erc[{{ $i }}][command]" placeholder="Command / Formation"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk-erc-opening" name="stock_erc[{{ $i }}][opening]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk-erc-supplies" name="stock_erc[{{ $i }}][supplies]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni stk-erc-total-avail" readonly tabindex="-1" name="stock_erc[{{ $i }}][total_available]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk-erc-issued" name="stock_erc[{{ $i }}][issued]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni qty-input stk-erc-damage" name="stock_erc[{{ $i }}][damage]" placeholder="0"></td>
                                    <td><input type="number" min="0" class="ni stk-erc-closing" readonly tabindex="-1" name="stock_erc[{{ $i }}][closing]" placeholder="0"></td>
                                    <td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                                @endfor
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="add-stock-erc-row"><i class="fas fa-plus"></i> Add Row</button>
            </div>
        </div>

        {{-- ============ 9. BREAKDOWN OF OBSERVATIONS AND RECOMMENDATIONS ============ --}}
        <div id="section-9" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    9. Breakdown of Observations and Recommendations
                </div>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:14px;">
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">
                            <i class="fas fa-eye" style="color:var(--nis-500);"></i>
                            Observations
                        </label>
                        <textarea class="ni" name="observations" rows="8" placeholder="Enter detailed observations across the Commands/Formations...">{{ old('observations') }}</textarea>
                    </div>
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">
                            <i class="fas fa-lightbulb" style="color:var(--nis-500);"></i>
                            Recommendations
                        </label>
                        <textarea class="ni" name="recommendations" rows="8" placeholder="Enter corresponding recommendations for each observation...">{{ old('recommendations') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ 10. GENERAL CONCLUSION AND RECOMMENDATIONS ============ --}}
        <div id="section-10" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-check-double"></i>
                    </div>
                    10. General Conclusion and Recommendations
                </div>
            </div>
            <div class="card-body">
                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">
                        <i class="fas fa-flag-checkered" style="color:var(--nis-500);"></i>
                        General Conclusion
                    </label>
                    <textarea class="ni" name="general_conclusion" rows="6" placeholder="Enter the general conclusion of the audit inspection...">{{ old('general_conclusion') }}</textarea>
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">
                        <i class="fas fa-list-check" style="color:var(--nis-500);"></i>
                        Overall Recommendations
                    </label>
                    <textarea class="ni" name="general_recommendations" rows="6" placeholder="Enter overall recommendations arising from the audit inspection...">{{ old('general_recommendations') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ============ SIGN OFF ============ --}}
        <div id="section-sign" class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                        <i class="fas fa-pen-fancy"></i>
                    </div>
                    Sign Off
                </div>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;">
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Signatory</label>
                        <input type="text" class="ni" name="signatory_name" value="{{ old('signatory_name', auth()->user()->name) }}" placeholder="Director (Internal Audit)">
                    </div>
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Designation</label>
                        <input type="text" class="ni" name="signatory_designation" value="{{ old('signatory_designation', 'Director (Internal Audit)') }}" placeholder="Designation">
                    </div>
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Date</label>
                        <input type="date" class="ni" name="signature_date" value="{{ old('signature_date', now()->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ SUBMIT ============ --}}
        <div class="redas-card">
            <div class="card-body" style="display:flex;justify-content:flex-end;gap:10px;">
                <a href="{{ route('user.dashboard') }}" class="btn-nis btn-ghost">Cancel</a>
                <button type="submit" class="btn-nis btn-primary-nis">
                    <i class="fas fa-paper-plane"></i> Submit {{ $directorate['name'] }} Report
                </button>
            </div>
        </div>
    </form>
</main>

<script>
(function () {
    'use strict';

    /* ---------- Row total auto-calculation for qty-input + row-total combos ---------- */
    function recalcRow(tr) {
        if (!tr) return;
        const totalField = tr.querySelector('.row-total');
        if (!totalField) return;
        let sum = 0;
        tr.querySelectorAll('.qty-input').forEach(function (input) {
            sum += parseFloat(input.value) || 0;
        });
        totalField.value = sum.toFixed(2);
    }

    document.querySelectorAll('.qty-input').forEach(function (input) {
        input.addEventListener('input', function () {
            recalcRow(input.closest('tr'));
        });
    });

    /* ---------- Funds table totals ---------- */
    function recalcFundsTotals() {
        let totalAllocated = 0, totalUtilised = 0, totalBalance = 0;
        document.querySelectorAll('.funds-allocated').forEach(function (i) { totalAllocated += parseFloat(i.value) || 0; });
        document.querySelectorAll('.funds-utilised').forEach(function (i) { totalUtilised += parseFloat(i.value) || 0; });
        document.querySelectorAll('.funds-balance').forEach(function (i) { totalBalance += parseFloat(i.value) || 0; });
        document.getElementById('funds-total-allocated').textContent = totalAllocated.toFixed(2);
        document.getElementById('funds-total-utilised').textContent = totalUtilised.toFixed(2);
        document.getElementById('funds-total-balance').textContent = totalBalance.toFixed(2);
    }

    document.querySelectorAll('.funds-allocated, .funds-utilised').forEach(function (input) {
        input.addEventListener('input', function () {
            recalcRow(input.closest('tr'));
            recalcFundsTotals();
        });
    });
    recalcFundsTotals();

    /* ---------- Revenue table totals ---------- */
    function recalcRevenueTotals() {
        let t32 = 0, t64 = 0, tEtc = 0, tErc = 0, tOps = 0, grand = 0;
        document.querySelectorAll('#revenue-tbody tr').forEach(function (row) {
            const revInputs = row.querySelectorAll('.rev-input');
            if (revInputs.length >= 5) {
                t32  += parseFloat(revInputs[0].value) || 0;
                t64  += parseFloat(revInputs[1].value) || 0;
                tEtc += parseFloat(revInputs[2].value) || 0;
                tErc += parseFloat(revInputs[3].value) || 0;
                tOps += parseFloat(revInputs[4].value) || 0;
            }
        });
        grand = t32 + t64 + tEtc + tErc + tOps;
        document.getElementById('rev-total-32').textContent = t32.toFixed(2);
        document.getElementById('rev-total-64').textContent = t64.toFixed(2);
        document.getElementById('rev-total-etc').textContent = tEtc.toFixed(2);
        document.getElementById('rev-total-erc').textContent = tErc.toFixed(2);
        document.getElementById('rev-total-ops').textContent = tOps.toFixed(2);
        document.getElementById('rev-grand-total').textContent = grand.toFixed(2);
    }

    document.querySelectorAll('.rev-input').forEach(function (input) {
        input.addEventListener('input', function () {
            recalcRow(input.closest('tr'));
            recalcRevenueTotals();
        });
    });
    recalcRevenueTotals();

    /* ---------- Stock helper: total_available = opening + supplies; closing = total_available - issued - damage ---------- */
    function recalcStockRow(tr, prefix) {
        if (!tr) return;
        const opening = parseFloat(tr.querySelector('.' + prefix + '-opening')?.value) || 0;
        const supplies = parseFloat(tr.querySelector('.' + prefix + '-supplies')?.value) || 0;
        const issued = parseFloat(tr.querySelector('.' + prefix + '-issued')?.value) || 0;
        const damage = parseFloat(tr.querySelector('.' + prefix + '-damage')?.value) || 0;

        const totalAvail = tr.querySelector('.' + prefix + '-total-avail');
        const closing = tr.querySelector('.' + prefix + '-closing');
        if (totalAvail) totalAvail.value = opening + supplies;
        if (closing) closing.value = Math.max(0, (opening + supplies) - issued - damage);
    }

    function bindStockInputs(prefix) {
        document.querySelectorAll('.' + prefix + '-opening, .' + prefix + '-supplies, .' + prefix + '-issued, .' + prefix + '-damage').forEach(function (input) {
            input.addEventListener('input', function () {
                recalcStockRow(input.closest('tr'), prefix);
            });
        });
        // initial calculation
        document.querySelectorAll('.' + prefix + '-opening').forEach(function (input) {
            recalcStockRow(input.closest('tr'), prefix);
        });
    }

    bindStockInputs('stk66');
    bindStockInputs('stk34');
    bindStockInputs('stk-etc');
    bindStockInputs('stk-erc');

    /* ---------- Remove-row buttons ---------- */
    function bindRemove(tr) {
        const btn = tr.querySelector('.remove-row-btn');
        if (btn && !btn.dataset.bound) {
            btn.dataset.bound = '1';
            btn.addEventListener('click', function () {
                tr.remove();
                // Recalc totals
                recalcFundsTotals();
                recalcRevenueTotals();
            });
        }
    }
    document.querySelectorAll('#commands-tbody tr, #funds-tbody tr, #revenue-tbody tr, #stock66-tbody tr, #stock34-tbody tr, #stock-etc-tbody tr, #stock-erc-tbody tr').forEach(bindRemove);

    /* ---------- Generic dynamic-row adder ---------- */
    let rowCounters = {};
    function addDynamicRow(tbodyId, prefix, buildRowHtml) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        rowCounters[tbodyId] = (rowCounters[tbodyId] || tbody.querySelectorAll('tr').length) + 1;
        const idx = 'n' + Date.now() + '_' + rowCounters[tbodyId];
        const tr = document.createElement('tr');
        tr.innerHTML = buildRowHtml(prefix, idx);
        tbody.appendChild(tr);
        bindRemove(tr);
        const firstInput = tr.querySelector('input, select');
        if (firstInput) firstInput.focus();
        // Re-bind qty-input listeners
        tr.querySelectorAll('.qty-input').forEach(function (input) {
            input.addEventListener('input', function () {
                recalcRow(input.closest('tr'));
                recalcFundsTotals();
                recalcRevenueTotals();
            });
        });
        // Re-bind stock listeners
        tr.querySelectorAll('.stk66-opening, .stk66-supplies, .stk66-issued, .stk66-damage').forEach(function (input) {
            input.addEventListener('input', function () { recalcStockRow(input.closest('tr'), 'stk66'); });
        });
        tr.querySelectorAll('.stk34-opening, .stk34-supplies, .stk34-issued, .stk34-damage').forEach(function (input) {
            input.addEventListener('input', function () { recalcStockRow(input.closest('tr'), 'stk34'); });
        });
        tr.querySelectorAll('.stk-etc-opening, .stk-etc-supplies, .stk-etc-issued, .stk-etc-damage').forEach(function (input) {
            input.addEventListener('input', function () { recalcStockRow(input.closest('tr'), 'stk-etc'); });
        });
        tr.querySelectorAll('.stk-erc-opening, .stk-erc-supplies, .stk-erc-issued, .stk-erc-damage').forEach(function (input) {
            input.addEventListener('input', function () { recalcStockRow(input.closest('tr'), 'stk-erc'); });
        });
        tr.querySelectorAll('.rev-input').forEach(function (input) {
            input.addEventListener('input', function () { recalcRevenueTotals(); });
        });
        tr.querySelectorAll('.funds-allocated, .funds-utilised').forEach(function (input) {
            input.addEventListener('input', function () { recalcFundsTotals(); });
        });
    }

    const addBtn = function (id, handler) {
        const btn = document.getElementById(id);
        if (btn) btn.addEventListener('click', handler);
    };

    addBtn('add-command-row', function () {
        addDynamicRow('commands-tbody', 'commands', function (prefix, idx) {
            const sn = parseInt(document.querySelectorAll('#commands-tbody tr').length) + 1;
            return '' +
                '<td>' + sn + '</td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][name]" placeholder="Command Name"></td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][period]" placeholder="e.g. January, 20.. to December, 20.."></td>' +
                '<td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>';
        });
    });

    addBtn('add-funds-row', function () {
        addDynamicRow('funds-tbody', 'funds', function (prefix, idx) {
            const sn = parseInt(document.querySelectorAll('#funds-tbody tr').length) + 1;
            return '' +
                '<td>' + sn + '</td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][command]" placeholder="Command / Formation"></td>' +
                '<td><input type="number" min="0" step="0.01" class="ni qty-input funds-allocated" name="' + prefix + '[' + idx + '][allocated]" placeholder="0.00"></td>' +
                '<td><input type="number" min="0" step="0.01" class="ni qty-input funds-utilised" name="' + prefix + '[' + idx + '][utilised]" placeholder="0.00"></td>' +
                '<td><input type="number" min="0" step="0.01" class="ni row-total funds-balance" readonly tabindex="-1" name="' + prefix + '[' + idx + '][balance]" placeholder="0.00"></td>' +
                '<td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>';
        });
    });

    addBtn('add-revenue-row', function () {
        addDynamicRow('revenue-tbody', 'revenue', function (prefix, idx) {
            const sn = parseInt(document.querySelectorAll('#revenue-tbody tr').length) + 1;
            return '' +
                '<td>' + sn + '</td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][command]" placeholder="Command"></td>' +
                '<td><input type="number" min="0" step="0.01" class="ni qty-input rev-input" name="' + prefix + '[' + idx + '][passport_32]" placeholder="0"></td>' +
                '<td><input type="number" min="0" step="0.01" class="ni qty-input rev-input" name="' + prefix + '[' + idx + '][passport_64]" placeholder="0"></td>' +
                '<td><input type="number" min="0" step="0.01" class="ni qty-input rev-input" name="' + prefix + '[' + idx + '][etc]" placeholder="0"></td>' +
                '<td><input type="number" min="0" step="0.01" class="ni qty-input rev-input" name="' + prefix + '[' + idx + '][erc]" placeholder="0"></td>' +
                '<td><input type="number" min="0" step="0.01" class="ni qty-input rev-input" name="' + prefix + '[' + idx + '][operations]" placeholder="0"></td>' +
                '<td><input type="number" min="0" step="0.01" class="ni row-total" readonly tabindex="-1" name="' + prefix + '[' + idx + '][total]" placeholder="0"></td>' +
                '<td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>';
        });
    });

    addBtn('add-stock66-row', function () {
        addDynamicRow('stock66-tbody', 'stock_66', function (prefix, idx) {
            const sn = parseInt(document.querySelectorAll('#stock66-tbody tr').length) + 1;
            return '' +
                '<td>' + sn + '</td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][command]" placeholder="Command"></td>' +
                '<td><input type="number" min="0" class="ni qty-input stk66-opening" name="' + prefix + '[' + idx + '][opening]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input stk66-supplies" name="' + prefix + '[' + idx + '][supplies]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni stk66-total-avail" readonly tabindex="-1" name="' + prefix + '[' + idx + '][total_available]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input stk66-issued" name="' + prefix + '[' + idx + '][issued]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input stk66-damage" name="' + prefix + '[' + idx + '][damage]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni stk66-closing" readonly tabindex="-1" name="' + prefix + '[' + idx + '][closing]" placeholder="0"></td>' +
                '<td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>';
        });
    });

    addBtn('add-stock34-row', function () {
        addDynamicRow('stock34-tbody', 'stock_34', function (prefix, idx) {
            const sn = parseInt(document.querySelectorAll('#stock34-tbody tr').length) + 1;
            return '' +
                '<td>' + sn + '</td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][command]" placeholder="Command"></td>' +
                '<td><input type="number" min="0" class="ni qty-input stk34-opening" name="' + prefix + '[' + idx + '][opening]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input stk34-supplies" name="' + prefix + '[' + idx + '][supplies]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni stk34-total-avail" readonly tabindex="-1" name="' + prefix + '[' + idx + '][total_available]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input stk34-issued" name="' + prefix + '[' + idx + '][issued]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input stk34-damage" name="' + prefix + '[' + idx + '][damage]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni stk34-closing" readonly tabindex="-1" name="' + prefix + '[' + idx + '][closing]" placeholder="0"></td>' +
                '<td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>';
        });
    });

    addBtn('add-stock-etc-row', function () {
        addDynamicRow('stock-etc-tbody', 'stock_etc', function (prefix, idx) {
            const sn = parseInt(document.querySelectorAll('#stock-etc-tbody tr').length) + 1;
            return '' +
                '<td>' + sn + '</td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][command]" placeholder="Command"></td>' +
                '<td><input type="number" min="0" class="ni qty-input stk-etc-opening" name="' + prefix + '[' + idx + '][opening]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input stk-etc-supplies" name="' + prefix + '[' + idx + '][supplies]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni stk-etc-total-avail" readonly tabindex="-1" name="' + prefix + '[' + idx + '][total_available]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input stk-etc-issued" name="' + prefix + '[' + idx + '][issued]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input stk-etc-damage" name="' + prefix + '[' + idx + '][damage]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni stk-etc-closing" readonly tabindex="-1" name="' + prefix + '[' + idx + '][closing]" placeholder="0"></td>' +
                '<td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>';
        });
    });

    addBtn('add-stock-erc-row', function () {
        addDynamicRow('stock-erc-tbody', 'stock_erc', function (prefix, idx) {
            const sn = parseInt(document.querySelectorAll('#stock-erc-tbody tr').length) + 1;
            return '' +
                '<td>' + sn + '</td>' +
                '<td><input type="text" class="ni" name="' + prefix + '[' + idx + '][command]" placeholder="Command / Formation"></td>' +
                '<td><input type="number" min="0" class="ni qty-input stk-erc-opening" name="' + prefix + '[' + idx + '][opening]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input stk-erc-supplies" name="' + prefix + '[' + idx + '][supplies]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni stk-erc-total-avail" readonly tabindex="-1" name="' + prefix + '[' + idx + '][total_available]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input stk-erc-issued" name="' + prefix + '[' + idx + '][issued]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni qty-input stk-erc-damage" name="' + prefix + '[' + idx + '][damage]" placeholder="0"></td>' +
                '<td><input type="number" min="0" class="ni stk-erc-closing" readonly tabindex="-1" name="' + prefix + '[' + idx + '][closing]" placeholder="0"></td>' +
                '<td><button type="button" class="remove-row-btn" title="Remove row"><i class="fas fa-trash"></i></button></td>';
        });
    });

    /* ---------- Form submit validation ---------- */
    const form = document.getElementById('internal-audit-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            const year = form.querySelector('[name="report_year"]');
            const quarter = form.querySelector('[name="report_quarter"]');
            if (year && !year.value) {
                e.preventDefault();
                year.focus();
                alert('Please select the report year before submitting.');
                return;
            }
            if (quarter && !quarter.value) {
                e.preventDefault();
                quarter.focus();
                alert('Please select the quarter before submitting.');
                return;
            }
        });
    }
})();
</script>

@include('partials.footer')

