@extends('user.directorates._layout')

{{-- This view renders its own tab bar; the Preview panel and action buttons
     come from the shared layout. --}}
@section('directorate-tabs', '1')

@section('directorate-sections')

@php
$cadres = [
    'Deputy Comptroller - General',
    'Assistant Comptroller - General',
    'Comptroller of Immigration',
    'Deputy Comptroller of Immigration',
    'Assistant Comptroller of Immigration',
    'Chief Superintendent of Immigration',
    'Superintendent of Immigration',
    'Deputy Superintendent of Immigration',
    'Assistant Superintendent I',
    'Assistant Superintendent II',
    'Inspector of Immigration',
    'Assistant Inspector of Immigration',
    'Immigration Assistant I',
    'Immigration Assistant II',
    'Immigration Assistant III'
];

$armsTypes = [
    'GPMG', 'AR70 RIFLE', 'AK 47', 'AK 103', 'GALIL RIFLE', 'TAVOR',
    'SCOPION EVO3', 'LAR RIFLE', 'G3 RIFLE', 'SMG RIFLE', 'PISTOL BARETTA',
    'DICON PISTOL', 'PISTOL CF98', 'PISTOL CZ-09', 'PISTOL HS-09', 'STONE PISTOL',
    'CEREMONIAL SWORD', 'HAND CUFF', 'TEAR GAS', 'LASER MAKING MACHINE',
    'SERVICING OIL', 'TRUNCHEON KXL'
];

$ammoTypes = [
    '5.56X45MM LIVE',
    '7.62X39MM LIVE',
    '7.62X51MM LIVE',
    '9X19MM LIVE',
    '5.56X45MM BLANK',
    '7.62X51MM BLANK',
    '9X19MM BLANK'
];

$tailorItems = [
    'Officer Uniform Set', 'Recruit Uniform Set', 'Thread rolls',
    'Buttons packs', 'Zippers packs', 'Tailoring Scissors'
];

/* The show route passes $commands; the edit route does not, so keep a local
   fallback to guarantee the command dropdowns are always populated. */
$commandList = $commands ?? [
    'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno',
    'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT', 'Gombe',
    'Imo', 'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara',
    'Lagos', 'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau',
    'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara'
];
@endphp

{{-- The shared layout provides the <form>; do not nest another one here. --}}
<div class="hrm-tabs-wrap">
    <div class="entry-tabs-wrap" style="margin-bottom:0;">
        <div class="entry-tabs" id="entryTabs">
            @php $tabs = [
                ['cadre','fas fa-users','1. Cadre'],
                ['rank','fas fa-star','2. Rank'],
                ['ordinance-armoury','fas fa-shield-alt','3. Ordinance & Armoury'],
                ['store-warehouse','fas fa-store','4. Store & Warehouse'],
                ['transport','fas fa-truck','5. Transport'],
                ['works-projects','fas fa-project-diagram','6. Works & Projects'],
                ['maintenance','fas fa-tools','7. Maintenance & Energy'],
                ['general-report','fas fa-file-alt','8. General Report'],
                ['preview','fas fa-eye','9. Preview'],
            ]; @endphp
            @foreach($tabs as $i => [$id,$icon,$label])
            <button type="button" class="entry-tab {{ $i === 0 ? 'active' : '' }}" data-tab="{{ $id }}">
                <span class="tab-dot"></span>
                <i class="{{ $icon }}" style="font-size:.78rem;"></i>
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>
</div>

<div class="tab-content">

    <!-- TAB 1: Reporting Officer & Staff Strength by Cadre -->
    <div class="tab-panel active" id="tab-cadre">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-id-card"></i></div>
                    Reporting Officer &amp; Command
                </div>
            </div>
            <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Officer Service Number</label>
                    <input type="text" name="reporting_officer_nis" class="ni" pattern="[0-9]*" inputmode="numeric" value="{{ old('reporting_officer_nis', preg_replace('/[^0-9]/', '', auth()->user()->service_number)) }}" placeholder="e.g. 002" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Command / Formation</label>
                    <input type="text" name="command_name" class="ni" value="{{ old('command_name') }}">
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Rank</label>
                    <input type="text" name="rank" class="ni" value="{{ old('rank') }}">
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Phone Number</label>
                    <input type="text" name="gsm_number" class="ni" value="{{ old('gsm_number') }}">
                </div>
            </div>
        </div>

        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-users"></i></div>
                    1. Staff Strength Cadre breakdown
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th>CADRES</th><th style="width:130px;">MALE</th><th style="width:130px;">FEMALE</th><th style="width:130px;">TOTAL</th></tr>
                        </thead>
                        <tbody>
                            @foreach($cadres as $c)
                            @php $c_slug = strtolower(str_replace(' ', '_', $c)); @endphp
                            <tr class="staff-row">
                                <td>
                                    {{ $c }}
                                    <input type="hidden" name="staff_strength[{{ $c_slug }}][cadre]" value="{{ $c }}">
                                </td>
                                <td><input type="number" name="staff_strength[{{ $c_slug }}][male]" class="ni staff-male calc-staff-total" value="0" min="0"></td>
                                <td><input type="number" name="staff_strength[{{ $c_slug }}][female]" class="ni staff-female calc-staff-total" value="0" min="0"></td>
                                <td><input type="number" name="staff_strength[{{ $c_slug }}][total]" class="ni staff-total" value="0" readonly style="background:#f9fafb;"></td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><strong><span id="staff-grand-male">0</span></strong></td>
                                <td><strong><span id="staff-grand-female">0</span></strong></td>
                                <td><strong><span id="staff-grand-total">0</span></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn" disabled><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    <!-- TAB 2: Rank -->
    <div class="tab-panel" id="tab-rank">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-star"></i></div>
                    2. Staff Strength by Rank
                </div>
            </div>
            <div class="card-body">
                <p style="font-size:.84rem;color:var(--gray-600);margin:0;">
                    No separate rank return is required for this directorate. Staff strength is captured by cadre
                    in <strong>1. Cadre</strong>, where each row corresponds to a rank.
                </p>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    <!-- TAB 3: Ordinance & Armoury -->
    <div class="tab-panel" id="tab-ordinance-armoury">
        <!-- a. ARMS/ARMOURY RETURNS BY COMMAND -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-shield-alt"></i></div>
                    a. ARMS/ARMOURY RETURNS BY COMMAND
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm wl-add-row" data-row-target="arms-body" data-row-prefix="arms_returns">
                    <i class="fas fa-plus"></i> Add Arms Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="arms-table">
                        <thead>
                            <tr>
                                <th>Command</th>
                                <th>Types of Arms</th>
                                <th style="width:120px;">No. of Serviceable</th>
                                <th style="width:120px;">No. of Unserviceable</th>
                                <th style="width:100px;">Total</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="arms-body"></tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td><strong><span id="arms-grand-serviceable">0</span></strong></td>
                                <td><strong><span id="arms-grand-unserviceable">0</span></strong></td>
                                <td><strong><span id="arms-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- b. AMMUNITION BY COMMAND -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-bullseye"></i></div>
                    b. AMMUNITION BY COMMAND
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm wl-add-row" data-row-target="ammunition-body" data-row-prefix="ammunition_returns">
                    <i class="fas fa-plus"></i> Add Ammunition Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="ammunition-table">
                        <thead>
                            <tr>
                                <th>Command</th>
                                <th>Types of Ammunition</th>
                                <th style="width:110px;">No. of Rounds</th>
                                <th style="width:110px;">No. of Serviceable</th>
                                <th style="width:110px;">No. of Unserviceable</th>
                                <th style="width:100px;">No. of Used</th>
                                <th style="width:90px;">Total</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="ammunition-body"></tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td><strong><span id="ammo-grand-rounds">0</span></strong></td>
                                <td><strong><span id="ammo-grand-serviceable">0</span></strong></td>
                                <td><strong><span id="ammo-grand-unserviceable">0</span></strong></td>
                                <td><strong><span id="ammo-grand-used">0</span></strong></td>
                                <td><strong><span id="ammo-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- c. ARMS SUMMARY -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-list"></i></div>
                    c. ARMS SUMMARY (Entire Directorate)
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr><th style="width:50px;">S/N</th><th>Types</th><th style="width:130px;">Serviceable</th><th style="width:130px;">Unserviceable</th><th style="width:110px;">Total</th></tr>
                        </thead>
                        <tbody>
                            @foreach($armsTypes as $index => $type)
                            <tr class="summary-arms-row">
                                <td>{{ $index + 1 }}</td>
                                <td style="font-weight:600;">
                                    {{ $type }}
                                    <input type="hidden" name="arms_summary[{{ $index }}][type]" value="{{ $type }}">
                                </td>
                                <td><input type="number" name="arms_summary[{{ $index }}][serviceable]" class="ni sum-arms-s calc-sum-arms" value="0" min="0"></td>
                                <td><input type="number" name="arms_summary[{{ $index }}][unserviceable]" class="ni sum-arms-u calc-sum-arms" value="0" min="0"></td>
                                <td><input type="number" name="arms_summary[{{ $index }}][total]" class="ni sum-arms-total" value="0" readonly style="background:#f9fafb;"></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td><strong><span id="arms-sum-grand-serviceable">0</span></strong></td>
                                <td><strong><span id="arms-sum-grand-unserviceable">0</span></strong></td>
                                <td><strong><span id="arms-sum-grand-total">0</span></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- d. AMMUNITION SUMMARY -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-list"></i></div>
                    d. AMMUNITION SUMMARY
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th>Types of Ammunition</th>
                                <th style="width:120px;">No. of Rounds</th>
                                <th style="width:110px;">Total</th>
                                <th style="width:150px;">No. of Rounds of Ammunition</th>
                                <th style="width:110px;">Bal C/F</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ammoTypes as $index => $type)
                            <tr class="summary-ammo-row">
                                <td>{{ $index + 1 }}</td>
                                <td style="font-weight:600;">
                                    {{ $type }}
                                    <input type="hidden" name="ammunition_summary[{{ $index }}][type]" value="{{ $type }}">
                                </td>
                                <td><input type="number" name="ammunition_summary[{{ $index }}][rounds]" class="ni calc-sum-ammo-rounds" value="0" min="0"></td>
                                <td><input type="number" name="ammunition_summary[{{ $index }}][total]" class="ni calc-sum-ammo-total" value="0" min="0"></td>
                                <td><input type="number" name="ammunition_summary[{{ $index }}][rounds_ammo]" class="ni calc-sum-ammo-rounds-ammo" value="0" min="0"></td>
                                <td><input type="number" name="ammunition_summary[{{ $index }}][bal_cf]" class="ni calc-sum-ammo-bal-cf" value="0" min="0"></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td><strong><span id="ammo-sum-grand-rounds">0</span></strong></td>
                                <td><strong><span id="ammo-sum-grand-total">0</span></strong></td>
                                <td><strong><span id="ammo-sum-grand-rounds-ammo">0</span></strong></td>
                                <td><strong><span id="ammo-sum-grand-bal-cf">0</span></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    <!-- TAB 4: Store & Warehouse -->
    <div class="tab-panel" id="tab-store-warehouse">
        <!-- a. ELECT/ELECT EQUIPMENTS, SPARES & ACCESSORIES -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-plug"></i></div>
                    a. ELECT/ELECT EQUIPMENTS, SPARES &amp; ACCESSORIES
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm wl-add-row" data-row-target="store-accessories-body" data-row-prefix="store_accessories">
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="store-accessories-table">
                        <thead>
                            <tr>
                                <th>Command</th>
                                <th>Items</th>
                                <th style="width:130px;">NO. of Qty Supplied</th>
                                <th style="width:110px;">Qty Issues</th>
                                <th style="width:100px;">Total Bal</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="store-accessories-body"></tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td><strong><span id="store-acc-grand-supplied">0</span></strong></td>
                                <td><strong><span id="store-acc-grand-issued">0</span></strong></td>
                                <td><strong><span id="store-acc-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- b. STATIONARY -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-paperclip"></i></div>
                    b. STATIONARY
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm wl-add-row" data-row-target="store-stationery-body" data-row-prefix="store_stationery">
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="store-stationery-table">
                        <thead>
                            <tr>
                                <th>Command</th>
                                <th>Items</th>
                                <th style="width:130px;">NO. of Qty Supplied</th>
                                <th style="width:110px;">Qty Issues</th>
                                <th style="width:100px;">Total Bal</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="store-stationery-body"></tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td><strong><span id="store-stat-grand-supplied">0</span></strong></td>
                                <td><strong><span id="store-stat-grand-issued">0</span></strong></td>
                                <td><strong><span id="store-stat-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- c. UNIFORM AND ACCESSORIES -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-tshirt"></i></div>
                    c. UNIFORM AND ACCESSORIES
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm wl-add-row" data-row-target="store-uniforms-body" data-row-prefix="store_uniforms">
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="store-uniforms-table">
                        <thead>
                            <tr>
                                <th>Command</th>
                                <th>Items</th>
                                <th style="width:130px;">New Qty Supplied</th>
                                <th style="width:110px;">Qty Issues</th>
                                <th style="width:100px;">Bal. C/F</th>
                                <th style="width:90px;">TOTAL</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="store-uniforms-body"></tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td><strong><span id="store-uni-grand-supplied">0</span></strong></td>
                                <td><strong><span id="store-uni-grand-issued">0</span></strong></td>
                                <td><strong><span id="store-uni-grand-bal-cf">0</span></strong></td>
                                <td><strong><span id="store-uni-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- d. OIL AND GAS -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-gas-pump"></i></div>
                    d. OIL AND GAS
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm wl-add-row" data-row-target="store-oil-gas-body" data-row-prefix="store_oil_gas">
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="store-oil-gas-table">
                        <thead>
                            <tr>
                                <th>Command</th>
                                <th>Items</th>
                                <th style="width:130px;">New Qty Supplied</th>
                                <th style="width:110px;">Qty Issues</th>
                                <th style="width:100px;">Bal. C/F</th>
                                <th style="width:90px;">TOTAL</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="store-oil-gas-body"></tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td><strong><span id="store-oil-grand-supplied">0</span></strong></td>
                                <td><strong><span id="store-oil-grand-issued">0</span></strong></td>
                                <td><strong><span id="store-oil-grand-bal-cf">0</span></strong></td>
                                <td><strong><span id="store-oil-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- e. TAILORING SUMMARY -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-cut"></i></div>
                    e. TAILORING
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">S/N</th>
                                <th>Items</th>
                                <th style="width:100px;">Bal. B/F</th>
                                <th style="width:140px;">New Quantity Supplied</th>
                                <th style="width:120px;">Quantity Issued</th>
                                <th style="width:100px;">Bal. C/F</th>
                                <th style="width:90px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tailorItems as $index => $item)
                            <tr class="tailor-row">
                                <td>{{ $index + 1 }}</td>
                                <td style="font-weight:600;">
                                    {{ $item }}
                                    <input type="hidden" name="store_tailoring[{{ $index }}][item]" value="{{ $item }}">
                                </td>
                                <td><input type="number" name="store_tailoring[{{ $index }}][bal_bf]" class="ni tailor-bf calc-tailor" value="0" min="0"></td>
                                <td><input type="number" name="store_tailoring[{{ $index }}][new_qty]" class="ni tailor-new calc-tailor" value="0" min="0"></td>
                                <td><input type="number" name="store_tailoring[{{ $index }}][qty_issued]" class="ni tailor-issued calc-tailor" value="0" min="0"></td>
                                <td><input type="number" name="store_tailoring[{{ $index }}][bal_cf]" class="ni tailor-bal-cf" value="0" readonly style="background:#f9fafb;"></td>
                                <td><input type="number" name="store_tailoring[{{ $index }}][total]" class="ni tailor-total" value="0" readonly style="background:#f9fafb;"></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td><strong><span id="tailor-grand-bf">0</span></strong></td>
                                <td><strong><span id="tailor-grand-new">0</span></strong></td>
                                <td><strong><span id="tailor-grand-issued">0</span></strong></td>
                                <td><strong><span id="tailor-grand-bal-cf">0</span></strong></td>
                                <td><strong><span id="tailor-grand-total">0</span></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    <!-- TAB 5: Transport -->
    <div class="tab-panel" id="tab-transport">
        <!-- a. VEHICLE DETAILS BY COMMAND -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-truck"></i></div>
                    a. VEHICLE DETAILS BY COMMAND
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm wl-add-row" data-row-target="transport-fleet-body" data-row-prefix="transport_fleet">
                    <i class="fas fa-plus"></i> Add Vehicle Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="transport-fleet-table">
                        <thead>
                            <tr>
                                <th>Command</th>
                                <th>Types (Make &amp; Model)</th>
                                <th>Chassis Number</th>
                                <th>Vehicle Plate Number</th>
                                <th>Identification (VIN)</th>
                                <th>Remarks / Condition</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="transport-fleet-body"></tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="5"><strong>TOTAL VEHICLES IN FLEET</strong></td>
                                <td><strong><span id="transport-fleet-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- b. PROCUREMENT LOG -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-file-invoice"></i></div>
                    b. PROCUREMENT LOG
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm wl-add-row" data-row-target="transport-procurement-body" data-row-prefix="transport_procurement">
                    <i class="fas fa-plus"></i> Add Procurement Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="transport-procurement-table">
                        <thead>
                            <tr>
                                <th>Location / Deploy Command</th>
                                <th>File Number</th>
                                <th>Type of Vehicle</th>
                                <th>Chassis Number</th>
                                <th>Remark</th>
                                <th style="width:150px;">Date of Procurement</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="transport-procurement-body"></tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="5"><strong>TOTAL VEHICLES PROCURED</strong></td>
                                <td><strong><span id="transport-procurement-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    <!-- TAB 6: Works & Projects -->
    <div class="tab-panel" id="tab-works-projects">
        <!-- a. STATUS OF INFRASTRUCTURAL PROJECTS EXECUTED -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-building"></i></div>
                    a. STATUS OF INFRASTRUCTURAL PROJECTS EXECUTED
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm wl-add-row" data-row-target="infra-projects-body" data-row-prefix="works_infra_projects">
                    <i class="fas fa-plus"></i> Add Project
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="infra-projects-table">
                        <thead>
                            <tr>
                                <th>Project Description</th>
                                <th style="width:140px;">Date of Award</th>
                                <th>Location</th>
                                <th>Name of the Contractor</th>
                                <th style="width:130px;">Status</th>
                                <th style="width:130px;">Percentage Completion (%)</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="infra-projects-body"></tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="4"><strong>TOTAL INFRASTRUCTURAL PROJECTS</strong></td>
                                <td><strong><span id="infra-projects-count">0</span></strong></td>
                                <td><strong><span id="infra-projects-avg-completion">Average: 0%</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- b. INTERVENTION PROJECTS EXECUTED -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-hands-helping"></i></div>
                    b. INTERVENTION PROJECTS EXECUTED
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm wl-add-row" data-row-target="intervention-projects-body" data-row-prefix="works_intervention_projects">
                    <i class="fas fa-plus"></i> Add Project
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="intervention-projects-table">
                        <thead>
                            <tr>
                                <th>Project Description</th>
                                <th style="width:120px;">Year of Award</th>
                                <th>Location</th>
                                <th>Name of the Contractor</th>
                                <th style="width:130px;">Status</th>
                                <th style="width:130px;">Percentage Completion (%)</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="intervention-projects-body"></tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="4"><strong>TOTAL INTERVENTION PROJECTS</strong></td>
                                <td><strong><span id="intervention-projects-count">0</span></strong></td>
                                <td><strong><span id="intervention-projects-avg-completion">Average: 0%</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- c. OTHER NOTABLE ACTIVITIES -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-clipboard-list"></i></div>
                    c. OTHER NOTABLE ACTIVITIES (Within review period)
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm wl-add-row" data-row-target="notable-activities-body" data-row-prefix="works_notable_activities">
                    <i class="fas fa-plus"></i> Add Activity
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="notable-activities-table">
                        <thead>
                            <tr>
                                <th>Activity Description</th>
                                <th>Remarks</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="notable-activities-body"></tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td><strong>TOTAL NOTABLE ACTIVITIES</strong></td>
                                <td><strong><span id="notable-activities-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    <!-- TAB 7: Maintenance & Energy -->
    <div class="tab-panel" id="tab-maintenance">
        <!-- 5. MAINTENANCE SECTION -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-tools"></i></div>
                    5. MAINTENANCE SECTION (Summary of activities)
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm wl-add-row" data-row-target="maintenance-body" data-row-prefix="maintenance_log">
                    <i class="fas fa-plus"></i> Add Maintenance Log
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="maintenance-table">
                        <thead>
                            <tr>
                                <th style="width:130px;">Unit</th>
                                <th>Activity Details</th>
                                <th style="width:150px;">Period (Month)</th>
                                <th>Location</th>
                                <th style="width:130px;">Status</th>
                                <th>Remark</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="maintenance-body"></tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="5"><strong>TOTAL MAINTENANCE ACTIVITIES</strong></td>
                                <td><strong><span id="maintenance-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- 6. ENERGY SECTION -->
        @php
        $energyUnits = [
            ['renewable', '6. a. RENEWABLE ENERGY UNIT', 'TOTAL RENEWABLE ENERGY LOGS', 'Add Renewable Log', 'fas fa-solar-panel'],
            ['electrical', '6. b. ELECTRICAL UNIT', 'TOTAL ELECTRICAL UNIT LOGS', 'Add Electrical Log', 'fas fa-bolt'],
            ['generator', '6. c. GENERATOR UNIT', 'TOTAL GENERATOR UNIT LOGS', 'Add Generator Log', 'fas fa-charging-station'],
            ['welding', '6. d. WELDING UNIT', 'TOTAL WELDING UNIT LOGS', 'Add Welding Log', 'fas fa-fire'],
            ['fuel', '6. e. RECORD OF FUEL DISTRIBUTION', 'TOTAL FUEL DISTRIBUTION LOGS', 'Add Fuel Log', 'fas fa-gas-pump'],
            ['ac', '6. f. REFRIGERATION AND AIR CONDITIONING', 'TOTAL HVAC UNIT LOGS', 'Add HVAC Log', 'fas fa-wind'],
        ];
        @endphp
        @foreach($energyUnits as [$unitKey, $unitTitle, $unitTotal, $unitBtn, $unitIcon])
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="{{ $unitIcon }}"></i></div>
                    {{ $unitTitle }}
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm wl-add-row" data-row-target="energy-{{ $unitKey }}-body" data-row-prefix="energy_utilities">
                    <i class="fas fa-plus"></i> {{ $unitBtn }}
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="energy-{{ $unitKey }}-table">
                        <thead>
                            <tr>
                                <th>Work Description</th>
                                <th style="width:140px;">Date</th>
                                <th>Location</th>
                                <th style="width:130px;">Status</th>
                                <th>Remark</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="energy-{{ $unitKey }}-body"></tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="4"><strong>{{ $unitTotal }}</strong></td>
                                <td><strong><span id="energy-{{ $unitKey }}-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    <!-- TAB 8: General Report -->
    <div class="tab-panel" id="tab-general-report">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-file-alt"></i></div>
                    7. CHALLENGES &amp; WAY FORWARD
                </div>
            </div>
            <div class="card-body">
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Major Challenges Faced</label>
                    <textarea name="general_report[challenges]" class="ni" rows="4" placeholder="Describe any logistics, armoury, or project challenges...">{{ old('general_report.challenges') }}</textarea>
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Recommendations / Way Forward</label>
                    <textarea name="general_report[recommendations]" class="ni" rows="4" placeholder="Propose solutions or recommendations...">{{ old('general_report.recommendations') }}</textarea>
                </div>
            </div>
        </div>

        <div class="redas-card" style="margin-bottom:14px;">
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
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next: Preview <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>
</div>

<script>
(function() {
    const form = document.querySelector('main form') || document.querySelector('form[action*="directorates"]');
    const tabContent = document.querySelector('.tab-content');

    /* Tab navigation (previous/next), tab switching, draft save/restore and
       submit are wired globally in user.directorates._layout and
       partials.footer for all directorate forms. */

    /* Helpers */
    function val(el) { return parseInt(el?.value || 0) || 0; }
    function setText(id, v) { const el = document.getElementById(id); if (el) el.textContent = v; }
    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c];
        });
    }

    /* ── Command dropdowns with per-table exclusivity ── */
    const availableCommands = @json($commandList);
    const commandTables = [
        'arms-table', 'ammunition-table', 'store-accessories-table', 'store-stationery-table',
        'store-uniforms-table', 'store-oil-gas-table', 'transport-fleet-table', 'transport-procurement-table'
    ];
    const activeSelections = {};

    function buildCommandOptions(tableId, selectedValue = '') {
        if (!activeSelections[tableId]) activeSelections[tableId] = new Set();
        let html = '<option value="">-- Select Command --</option>';
        availableCommands.forEach(cmd => {
            if (!activeSelections[tableId].has(cmd) || cmd === selectedValue) {
                html += `<option value="${esc(cmd)}"${cmd === selectedValue ? ' selected' : ''}>${esc(cmd)}</option>`;
            }
        });
        return html;
    }

    function refreshCommandDropdowns(tableId) {
        const table = document.getElementById(tableId);
        if (!table) return;
        const current = new Set();
        table.querySelectorAll('.command-selector').forEach(sel => { if (sel.value) current.add(sel.value); });
        activeSelections[tableId] = current;
        table.querySelectorAll('.command-selector').forEach(sel => {
            sel.innerHTML = buildCommandOptions(tableId, sel.value);
        });
    }

    function resyncAllCommandSelections() {
        commandTables.forEach(refreshCommandDropdowns);
    }

    /* ── Dynamic row templates, keyed by tbody id ── */
    const ARMS_TYPE_OPTIONS = `
        <option value="G3 Rifle">G3 Rifle</option>
        <option value="AR 70">AR 70</option>
        <option value="AK 47">AK 47</option>
        <option value="Galil">Galil</option>
        <option value="LAR Rifle">LAR Rifle</option>
        <option value="SMG Rifle">SMG Rifle</option>
        <option value="Pistol">Pistol</option>
        <option value="Dicon">Dicon</option>
        <option value="Stone">Stone</option>
        <option value="Others">Others</option>`;
    const AMMO_TYPE_OPTIONS = `
        <option value="5.56X45MM LIVE">5.56X45MM LIVE</option>
        <option value="7.62X39MM LIVE">7.62X39MM LIVE</option>
        <option value="7.62X51MM LIVE">7.62X51MM LIVE</option>
        <option value="9X19MM LIVE">9X19MM LIVE</option>
        <option value="5.56X45MM BLANK">5.56X45MM BLANK</option>
        <option value="7.62X51MM BLANK">7.62X51MM BLANK</option>
        <option value="9X19MM BLANK">9X19MM BLANK</option>
        <option value="Others">Others</option>`;
    const STATUS_OPTIONS = `
        <option value="Completed">Completed</option>
        <option value="Ongoing">Ongoing</option>
        <option value="Abandoned">Abandoned</option>`;
    const MAINTENANCE_UNIT_OPTIONS = `
        <option value="Carpentry">Carpentry</option>
        <option value="Plumbing">Plumbing</option>
        <option value="Painting">Painting</option>
        <option value="Mason">Mason</option>
        <option value="Others">Others</option>`;

    const ENERGY_UNIT_LABELS = {
        'energy-renewable-body': 'Renewable Energy Unit',
        'energy-electrical-body': 'Electrical Unit',
        'energy-generator-body': 'Generator Unit',
        'energy-welding-body': 'Welding Unit',
        'energy-fuel-body': 'Record of Fuel Distribution',
        'energy-ac-body': 'Refrigeration and Air Conditioning'
    };

    const REMOVE_CELL = `<td style="text-align:center;">
        <button type="button" class="btn-nis btn-ghost wl-remove-row" style="color:var(--color-danger);padding:4px;"><i class="fas fa-trash"></i></button>
    </td>`;

    function commandCell(name, tableId) {
        return `<td><select name="${name}" class="ni ni-select command-selector" data-table="${tableId}">${buildCommandOptions(tableId)}</select></td>`;
    }

    function energyRow(tbodyId, idx) {
        const unitKey = tbodyId.replace('energy-', '').replace('-body', '');
        const unitLabel = ENERGY_UNIT_LABELS[tbodyId];
        return `<tr class="energy-${unitKey}-row">
            <input type="hidden" name="energy_utilities[${idx}][unit]" value="${unitLabel}">
            <td><input type="text" name="energy_utilities[${idx}][description]" class="ni"></td>
            <td><input type="date" name="energy_utilities[${idx}][date]" class="ni"></td>
            <td><input type="text" name="energy_utilities[${idx}][location]" class="ni"></td>
            <td><select name="energy_utilities[${idx}][status]" class="ni ni-select">${STATUS_OPTIONS}</select></td>
            <td><input type="text" name="energy_utilities[${idx}][remark]" class="ni"></td>
            ${REMOVE_CELL}
        </tr>`;
    }

    const rowTemplates = {
        'arms-body': idx => `<tr class="arms-command-row">
            ${commandCell(`arms_returns[${idx}][command]`, 'arms-table')}
            <td><select name="arms_returns[${idx}][type]" class="ni ni-select">${ARMS_TYPE_OPTIONS}</select></td>
            <td><input type="number" name="arms_returns[${idx}][serviceable]" class="ni calc-arms" value="0" min="0"></td>
            <td><input type="number" name="arms_returns[${idx}][unserviceable]" class="ni calc-arms" value="0" min="0"></td>
            <td><input type="number" name="arms_returns[${idx}][total]" class="ni arms-tot-disp" value="0" readonly style="background:#f9fafb;"></td>
            ${REMOVE_CELL}
        </tr>`,
        'ammunition-body': idx => `<tr class="ammo-item-row">
            ${commandCell(`ammunition_returns[${idx}][command]`, 'ammunition-table')}
            <td><select name="ammunition_returns[${idx}][type]" class="ni ni-select">${AMMO_TYPE_OPTIONS}</select></td>
            <td><input type="number" name="ammunition_returns[${idx}][rounds]" class="ni calc-ammo" value="0" min="0"></td>
            <td><input type="number" name="ammunition_returns[${idx}][serviceable]" class="ni calc-ammo" value="0" min="0"></td>
            <td><input type="number" name="ammunition_returns[${idx}][unserviceable]" class="ni calc-ammo" value="0" min="0"></td>
            <td><input type="number" name="ammunition_returns[${idx}][used]" class="ni calc-ammo" value="0" min="0"></td>
            <td><input type="number" name="ammunition_returns[${idx}][total]" class="ni ammo-tot-disp" value="0" readonly style="background:#f9fafb;"></td>
            ${REMOVE_CELL}
        </tr>`,
        'store-accessories-body': idx => `<tr class="store-acc-row">
            ${commandCell(`store_accessories[${idx}][command]`, 'store-accessories-table')}
            <td><input type="text" name="store_accessories[${idx}][item]" class="ni" placeholder="e.g. Keyboard, Fuel pump"></td>
            <td><input type="number" name="store_accessories[${idx}][qty_supplied]" class="ni calc-store-acc" value="0" min="0"></td>
            <td><input type="number" name="store_accessories[${idx}][qty_issues]" class="ni calc-store-acc" value="0" min="0"></td>
            <td><input type="number" name="store_accessories[${idx}][total_bal]" class="ni store-acc-tot" value="0" readonly style="background:#f9fafb;"></td>
            ${REMOVE_CELL}
        </tr>`,
        'store-stationery-body': idx => `<tr class="store-stat-row">
            ${commandCell(`store_stationery[${idx}][command]`, 'store-stationery-table')}
            <td><input type="text" name="store_stationery[${idx}][item]" class="ni" placeholder="e.g. A4 Paper, Biro"></td>
            <td><input type="number" name="store_stationery[${idx}][qty_received]" class="ni calc-store-stat" value="0" min="0"></td>
            <td><input type="number" name="store_stationery[${idx}][qty_issues]" class="ni calc-store-stat" value="0" min="0"></td>
            <td><input type="number" name="store_stationery[${idx}][total_bal]" class="ni store-stat-tot" value="0" readonly style="background:#f9fafb;"></td>
            ${REMOVE_CELL}
        </tr>`,
        'store-uniforms-body': idx => `<tr class="store-uni-row">
            ${commandCell(`store_uniforms[${idx}][command]`, 'store-uniforms-table')}
            <td><input type="text" name="store_uniforms[${idx}][item]" class="ni" placeholder="e.g. Beret, Uniform Set"></td>
            <td><input type="number" name="store_uniforms[${idx}][qty_supplied]" class="ni calc-store-uni" value="0" min="0"></td>
            <td><input type="number" name="store_uniforms[${idx}][qty_issues]" class="ni calc-store-uni" value="0" min="0"></td>
            <td><input type="number" name="store_uniforms[${idx}][bal_cf]" class="ni store-uni-bal-cf" value="0" readonly style="background:#f9fafb;"></td>
            <td><input type="number" name="store_uniforms[${idx}][total]" class="ni store-uni-tot" value="0" readonly style="background:#f9fafb;"></td>
            ${REMOVE_CELL}
        </tr>`,
        'store-oil-gas-body': idx => `<tr class="store-oil-row">
            ${commandCell(`store_oil_gas[${idx}][command]`, 'store-oil-gas-table')}
            <td><input type="text" name="store_oil_gas[${idx}][item]" class="ni" placeholder="e.g. PMS, AGO"></td>
            <td><input type="number" name="store_oil_gas[${idx}][qty_supplied]" class="ni calc-store-oil" value="0" min="0"></td>
            <td><input type="number" name="store_oil_gas[${idx}][qty_issues]" class="ni calc-store-oil" value="0" min="0"></td>
            <td><input type="number" name="store_oil_gas[${idx}][bal_cf]" class="ni store-oil-bal-cf" value="0" readonly style="background:#f9fafb;"></td>
            <td><input type="number" name="store_oil_gas[${idx}][total]" class="ni store-oil-tot" value="0" readonly style="background:#f9fafb;"></td>
            ${REMOVE_CELL}
        </tr>`,
        'transport-fleet-body': idx => `<tr class="transport-fleet-row">
            ${commandCell(`transport_fleet[${idx}][command]`, 'transport-fleet-table')}
            <td><input type="text" name="transport_fleet[${idx}][make_model]" class="ni" placeholder="e.g. Toyota Hilux"></td>
            <td><input type="text" name="transport_fleet[${idx}][chassis]" class="ni"></td>
            <td><input type="text" name="transport_fleet[${idx}][plate]" class="ni"></td>
            <td><input type="text" name="transport_fleet[${idx}][vin]" class="ni"></td>
            <td><input type="text" name="transport_fleet[${idx}][remarks]" class="ni" placeholder="e.g. Serviceable"></td>
            ${REMOVE_CELL}
        </tr>`,
        'transport-procurement-body': idx => `<tr class="transport-procurement-row">
            ${commandCell(`transport_procurement[${idx}][location]`, 'transport-procurement-table')}
            <td><input type="text" name="transport_procurement[${idx}][file_no]" class="ni" placeholder="e.g. W&L/04"></td>
            <td><input type="text" name="transport_procurement[${idx}][make_model]" class="ni" placeholder="e.g. Toyota Coaster"></td>
            <td><input type="text" name="transport_procurement[${idx}][chassis]" class="ni"></td>
            <td><input type="text" name="transport_procurement[${idx}][remark]" class="ni"></td>
            <td><input type="date" name="transport_procurement[${idx}][date]" class="ni"></td>
            ${REMOVE_CELL}
        </tr>`,
        'infra-projects-body': idx => `<tr class="infra-project-row">
            <td><input type="text" name="works_infra_projects[${idx}][description]" class="ni"></td>
            <td><input type="date" name="works_infra_projects[${idx}][date]" class="ni"></td>
            <td><input type="text" name="works_infra_projects[${idx}][location]" class="ni"></td>
            <td><input type="text" name="works_infra_projects[${idx}][contractor]" class="ni"></td>
            <td><select name="works_infra_projects[${idx}][status]" class="ni ni-select">${STATUS_OPTIONS}</select></td>
            <td><input type="number" name="works_infra_projects[${idx}][completion]" class="ni calc-infra" value="0" min="0" max="100"></td>
            ${REMOVE_CELL}
        </tr>`,
        'intervention-projects-body': idx => `<tr class="intervention-project-row">
            <td><input type="text" name="works_intervention_projects[${idx}][description]" class="ni"></td>
            <td><input type="number" name="works_intervention_projects[${idx}][year]" class="ni" placeholder="e.g. 2026" min="2000" max="2100"></td>
            <td><input type="text" name="works_intervention_projects[${idx}][location]" class="ni"></td>
            <td><input type="text" name="works_intervention_projects[${idx}][contractor]" class="ni"></td>
            <td><select name="works_intervention_projects[${idx}][status]" class="ni ni-select">${STATUS_OPTIONS}</select></td>
            <td><input type="number" name="works_intervention_projects[${idx}][completion]" class="ni calc-interv" value="0" min="0" max="100"></td>
            ${REMOVE_CELL}
        </tr>`,
        'notable-activities-body': idx => `<tr class="notable-activity-row">
            <td><input type="text" name="works_notable_activities[${idx}][description]" class="ni"></td>
            <td><input type="text" name="works_notable_activities[${idx}][remarks]" class="ni"></td>
            ${REMOVE_CELL}
        </tr>`,
        'maintenance-body': idx => `<tr class="maintenance-log-row">
            <td><select name="maintenance_log[${idx}][unit]" class="ni ni-select">${MAINTENANCE_UNIT_OPTIONS}</select></td>
            <td><input type="text" name="maintenance_log[${idx}][activity]" class="ni"></td>
            <td><input type="month" name="maintenance_log[${idx}][period]" class="ni"></td>
            <td><input type="text" name="maintenance_log[${idx}][location]" class="ni"></td>
            <td><select name="maintenance_log[${idx}][status]" class="ni ni-select">${STATUS_OPTIONS}</select></td>
            <td><input type="text" name="maintenance_log[${idx}][remark]" class="ni"></td>
            ${REMOVE_CELL}
        </tr>`,
        'energy-renewable-body': idx => energyRow('energy-renewable-body', idx),
        'energy-electrical-body': idx => energyRow('energy-electrical-body', idx),
        'energy-generator-body': idx => energyRow('energy-generator-body', idx),
        'energy-welding-body': idx => energyRow('energy-welding-body', idx),
        'energy-fuel-body': idx => energyRow('energy-fuel-body', idx),
        'energy-ac-body': idx => energyRow('energy-ac-body', idx)
    };

    /* All six energy tables submit under the shared energy_utilities[] key, so
       their indices must stay unique across tables (PHP keeps only the last
       value for duplicate array keys). Other tables index per tbody. */
    let energyIndex = 0;
    function nextRowIndex(tbody) {
        if (ENERGY_UNIT_LABELS[tbody.id]) return energyIndex++;
        let idx = tbody.children.length;
        while (tbody.querySelector(`[name*="[${idx}]"]`)) idx++;
        return idx;
    }

    function addRow(tbodyId) {
        const tbody = document.getElementById(tbodyId);
        const template = rowTemplates[tbodyId];
        if (!tbody || !template) return;
        const tr = document.createElement('tr');
        tr.innerHTML = template(nextRowIndex(tbody)).replace(/^<tr[^>]*>|<\/tr>$/g, '');
        tbody.appendChild(tr);
        const tableId = tbody.closest('table')?.id;
        if (tableId && commandTables.includes(tableId)) refreshCommandDropdowns(tableId);
        recomputeAll();
    }

    document.querySelectorAll('.wl-add-row').forEach(btn => {
        btn.addEventListener('click', () => addRow(btn.dataset.rowTarget));
    });

    function addDocumentRow() {
        const container = document.getElementById('documents-body');
        if (!container) return;
        const div = document.createElement('div');
        div.className = 'auth-form-group';
        div.style.marginBottom = '12px';
        div.style.display = 'flex';
        div.style.gap = '8px';
        div.innerHTML = `
            <input type="file" name="supporting_documents[]" class="ni" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg">
            <button type="button" class="btn-nis btn-ghost wl-remove-document" style="color:var(--color-danger);padding:4px;"><i class="fas fa-times"></i></button>`;
        container.appendChild(div);
    }
    document.getElementById('wlAddDocumentRow')?.addEventListener('click', addDocumentRow);

    /* Delegated remove-row and command-exclusivity handling */
    if (tabContent) {
        tabContent.addEventListener('click', e => {
            const removeBtn = e.target.closest('.wl-remove-row');
            if (removeBtn) {
                const tr = removeBtn.closest('tr');
                const tableId = tr?.closest('table')?.id;
                tr?.remove();
                if (tableId && commandTables.includes(tableId)) refreshCommandDropdowns(tableId);
                recomputeAll();
                return;
            }
            const removeDoc = e.target.closest('.wl-remove-document');
            if (removeDoc) removeDoc.parentElement.remove();
        });
        tabContent.addEventListener('change', e => {
            if (e.target.matches('.command-selector')) refreshCommandDropdowns(e.target.dataset.table);
        });
    }

    /* ── Totals ── */
    function calculateStaffTotals() {
        let grandMale = 0, grandFemale = 0, grandTotal = 0;
        document.querySelectorAll('.staff-row').forEach(row => {
            const male = val(row.querySelector('.staff-male'));
            const female = val(row.querySelector('.staff-female'));
            const total = male + female;
            row.querySelector('.staff-total').value = total;
            grandMale += male; grandFemale += female; grandTotal += total;
        });
        setText('staff-grand-male', grandMale);
        setText('staff-grand-female', grandFemale);
        setText('staff-grand-total', grandTotal);
    }

    function calculateArmsTotals() {
        let sGrand = 0, uGrand = 0, totGrand = 0;
        document.querySelectorAll('.arms-command-row').forEach(row => {
            const s = val(row.querySelector('[name*="[serviceable]"]'));
            const u = val(row.querySelector('[name*="[unserviceable]"]'));
            const total = s + u;
            row.querySelector('.arms-tot-disp').value = total;
            sGrand += s; uGrand += u; totGrand += total;
        });
        setText('arms-grand-serviceable', sGrand);
        setText('arms-grand-unserviceable', uGrand);
        setText('arms-grand-total', totGrand);
    }

    function calculateAmmunitionTotals() {
        let roundsGrand = 0, sGrand = 0, uGrand = 0, usedGrand = 0, totGrand = 0;
        document.querySelectorAll('.ammo-item-row').forEach(row => {
            const rounds = val(row.querySelector('[name*="[rounds]"]'));
            const s = val(row.querySelector('[name*="[serviceable]"]'));
            const u = val(row.querySelector('[name*="[unserviceable]"]'));
            const used = val(row.querySelector('[name*="[used]"]'));
            const total = s + u;
            row.querySelector('.ammo-tot-disp').value = total;
            roundsGrand += rounds; sGrand += s; uGrand += u; usedGrand += used; totGrand += total;
        });
        setText('ammo-grand-rounds', roundsGrand);
        setText('ammo-grand-serviceable', sGrand);
        setText('ammo-grand-unserviceable', uGrand);
        setText('ammo-grand-used', usedGrand);
        setText('ammo-grand-total', totGrand);
    }

    function calculateArmsSummaryTotals() {
        let sGrand = 0, uGrand = 0, totGrand = 0;
        document.querySelectorAll('.summary-arms-row').forEach(row => {
            const s = val(row.querySelector('.sum-arms-s'));
            const u = val(row.querySelector('.sum-arms-u'));
            const total = s + u;
            row.querySelector('.sum-arms-total').value = total;
            sGrand += s; uGrand += u; totGrand += total;
        });
        setText('arms-sum-grand-serviceable', sGrand);
        setText('arms-sum-grand-unserviceable', uGrand);
        setText('arms-sum-grand-total', totGrand);
    }

    function calculateAmmunitionSummaryTotals() {
        let roundsGrand = 0, totGrand = 0, roundsAmmoGrand = 0, balCfGrand = 0;
        document.querySelectorAll('.summary-ammo-row').forEach(row => {
            roundsGrand += val(row.querySelector('.calc-sum-ammo-rounds'));
            totGrand += val(row.querySelector('.calc-sum-ammo-total'));
            roundsAmmoGrand += val(row.querySelector('.calc-sum-ammo-rounds-ammo'));
            balCfGrand += val(row.querySelector('.calc-sum-ammo-bal-cf'));
        });
        setText('ammo-sum-grand-rounds', roundsGrand);
        setText('ammo-sum-grand-total', totGrand);
        setText('ammo-sum-grand-rounds-ammo', roundsAmmoGrand);
        setText('ammo-sum-grand-bal-cf', balCfGrand);
    }

    function calculateStoreAccessoryTotals() {
        let supGrand = 0, issGrand = 0, totGrand = 0;
        document.querySelectorAll('.store-acc-row').forEach(row => {
            const sup = val(row.querySelector('[name*="[qty_supplied]"]'));
            const iss = val(row.querySelector('[name*="[qty_issues]"]'));
            const total = sup - iss;
            row.querySelector('.store-acc-tot').value = total;
            supGrand += sup; issGrand += iss; totGrand += total;
        });
        setText('store-acc-grand-supplied', supGrand);
        setText('store-acc-grand-issued', issGrand);
        setText('store-acc-grand-total', totGrand);
    }

    function calculateStoreStationeryTotals() {
        let supGrand = 0, issGrand = 0, totGrand = 0;
        document.querySelectorAll('.store-stat-row').forEach(row => {
            const sup = val(row.querySelector('[name*="[qty_received]"]'));
            const iss = val(row.querySelector('[name*="[qty_issues]"]'));
            const total = sup - iss;
            row.querySelector('.store-stat-tot').value = total;
            supGrand += sup; issGrand += iss; totGrand += total;
        });
        setText('store-stat-grand-supplied', supGrand);
        setText('store-stat-grand-issued', issGrand);
        setText('store-stat-grand-total', totGrand);
    }

    function calculateStoreUniformTotals() {
        let supGrand = 0, issGrand = 0, balCfGrand = 0, totGrand = 0;
        document.querySelectorAll('.store-uni-row').forEach(row => {
            const sup = val(row.querySelector('[name*="[qty_supplied]"]'));
            const iss = val(row.querySelector('[name*="[qty_issues]"]'));
            const balCf = sup - iss;
            row.querySelector('.store-uni-bal-cf').value = balCf;
            row.querySelector('.store-uni-tot').value = balCf;
            supGrand += sup; issGrand += iss; balCfGrand += balCf; totGrand += balCf;
        });
        setText('store-uni-grand-supplied', supGrand);
        setText('store-uni-grand-issued', issGrand);
        setText('store-uni-grand-bal-cf', balCfGrand);
        setText('store-uni-grand-total', totGrand);
    }

    function calculateStoreOilTotals() {
        let supGrand = 0, issGrand = 0, balCfGrand = 0, totGrand = 0;
        document.querySelectorAll('.store-oil-row').forEach(row => {
            const sup = val(row.querySelector('[name*="[qty_supplied]"]'));
            const iss = val(row.querySelector('[name*="[qty_issues]"]'));
            const balCf = sup - iss;
            row.querySelector('.store-oil-bal-cf').value = balCf;
            row.querySelector('.store-oil-tot').value = balCf;
            supGrand += sup; issGrand += iss; balCfGrand += balCf; totGrand += balCf;
        });
        setText('store-oil-grand-supplied', supGrand);
        setText('store-oil-grand-issued', issGrand);
        setText('store-oil-grand-bal-cf', balCfGrand);
        setText('store-oil-grand-total', totGrand);
    }

    function calculateStoreTailoringTotals() {
        let bfGrand = 0, supGrand = 0, issGrand = 0, balCfGrand = 0, totGrand = 0;
        document.querySelectorAll('.tailor-row').forEach(row => {
            const bf = val(row.querySelector('.tailor-bf'));
            const sup = val(row.querySelector('.tailor-new'));
            const iss = val(row.querySelector('.tailor-issued'));
            const balCf = bf + sup - iss;
            row.querySelector('.tailor-bal-cf').value = balCf;
            row.querySelector('.tailor-total').value = balCf;
            bfGrand += bf; supGrand += sup; issGrand += iss; balCfGrand += balCf; totGrand += balCf;
        });
        setText('tailor-grand-bf', bfGrand);
        setText('tailor-grand-new', supGrand);
        setText('tailor-grand-issued', issGrand);
        setText('tailor-grand-bal-cf', balCfGrand);
        setText('tailor-grand-total', totGrand);
    }

    function calculateTransportFleetTotals() {
        setText('transport-fleet-grand-total', document.querySelectorAll('.transport-fleet-row').length);
    }

    function calculateTransportProcurementTotals() {
        setText('transport-procurement-grand-total', document.querySelectorAll('.transport-procurement-row').length);
    }

    function calculateProjectsTotals(rowClass, countId, avgId) {
        const rows = document.querySelectorAll('.' + rowClass);
        let sum = 0;
        rows.forEach(r => { sum += val(r.querySelector('[name*="[completion]"]')); });
        const avg = rows.length > 0 ? Math.round(sum / rows.length) : 0;
        setText(countId, rows.length);
        setText(avgId, `Average: ${avg}%`);
    }

    function calculateNotableActivitiesTotals() {
        setText('notable-activities-grand-total', document.querySelectorAll('.notable-activity-row').length);
    }

    function calculateMaintenanceTotals() {
        setText('maintenance-grand-total', document.querySelectorAll('.maintenance-log-row').length);
    }

    function calculateEnergyTotals(unit) {
        setText(`energy-${unit}-grand-total`, document.querySelectorAll(`.energy-${unit}-row`).length);
    }

    /* Master recompute */
    function recomputeAll() {
        calculateStaffTotals();
        calculateArmsTotals();
        calculateAmmunitionTotals();
        calculateArmsSummaryTotals();
        calculateAmmunitionSummaryTotals();
        calculateStoreAccessoryTotals();
        calculateStoreStationeryTotals();
        calculateStoreUniformTotals();
        calculateStoreOilTotals();
        calculateStoreTailoringTotals();
        calculateTransportFleetTotals();
        calculateTransportProcurementTotals();
        calculateProjectsTotals('infra-project-row', 'infra-projects-count', 'infra-projects-avg-completion');
        calculateProjectsTotals('intervention-project-row', 'intervention-projects-count', 'intervention-projects-avg-completion');
        calculateNotableActivitiesTotals();
        calculateMaintenanceTotals();
        ['renewable', 'electrical', 'generator', 'welding', 'fuel', 'ac'].forEach(calculateEnergyTotals);
    }

    /* Recalculate on any field input. The synthetic input event the layout
       dispatches on the form after draft/edit restore targets the form itself,
       so the listener lives on the form (events bubble up, not down). */
    if (form) {
        form.addEventListener('input', e => {
            if (e.target === form) resyncAllCommandSelections();
            recomputeAll();
        });
    }

    /* ── Preview ── */
    function previewSectionTitle(num, title) {
        return `<div class="hrm-preview-section-title">${num}. ${title}</div>`;
    }
    function previewSubTitle(label) {
        return `<div style="font-size:.84rem;font-weight:700;margin:10px 0 6px;">${label}</div>`;
    }
    function previewTable(rows, headers = ['Item', 'Value']) {
        const thead = headers.map(h => `<th style="padding:6px 8px;border:1px solid var(--gray-200);background:#f8fafc;">${h}</th>`).join('');
        const tbody = rows.length
            ? rows.map(r => `<tr>${r.map(c => `<td style="padding:6px 8px;border:1px solid var(--gray-200);">${c}</td>`).join('')}</tr>`).join('')
            : `<tr><td colspan="${headers.length}" style="padding:6px 8px;border:1px solid var(--gray-200);color:var(--gray-500);">No entries.</td></tr>`;
        return `<div style="overflow-x:auto;"><table class="hrm-preview-table" style="margin-bottom:12px;"><thead><tr>${thead}</tr></thead><tbody>${tbody}</tbody></table></div>`;
    }
    function getVal(name) { return document.querySelector(`[name="${name}"]`)?.value || '—'; }
    function getText(id) { return document.getElementById(id)?.textContent || '0'; }

    /* Non-empty rows of a dynamic tbody as arrays of escaped values
       (hidden inputs and the remove-button cell are skipped). */
    function tableRows(tbodyId) {
        const rows = [];
        document.querySelectorAll(`#${tbodyId} tr`).forEach(tr => {
            const inputs = Array.from(tr.querySelectorAll('input, textarea, select'))
                .filter(i => i.type !== 'hidden');
            const vals = inputs.map(i => esc(i.value.trim()));
            if (vals.some(v => v !== '')) rows.push(vals);
        });
        return rows;
    }

    function strongRow(cells) {
        return cells.map(c => `<strong>${c}</strong>`);
    }

    function buildPreview() {
        const container = document.getElementById('hrmPreviewBody');
        if (!container) return;
        let html = '';

        /* 1. Reporting Officer & Command */
        html += previewSectionTitle(1, 'Reporting Officer & Command');
        html += previewTable([
            ['Officer Service Number', esc(getVal('reporting_officer_nis'))],
            ['Command / Formation', esc(getVal('command_name'))],
            ['Rank', esc(getVal('rank'))],
            ['Phone Number', esc(getVal('gsm_number'))]
        ]);

        /* 2. Staff Strength by Cadre */
        const staffRows = [];
        document.querySelectorAll('.staff-row').forEach(row => {
            staffRows.push([
                esc(row.querySelector('td').textContent.trim()),
                row.querySelector('.staff-male').value || '0',
                row.querySelector('.staff-female').value || '0',
                row.querySelector('.staff-total').value || '0'
            ]);
        });
        staffRows.push(strongRow(['Grand Total', getText('staff-grand-male'), getText('staff-grand-female'), getText('staff-grand-total')]));
        html += previewSectionTitle(2, 'Staff Strength by Cadre');
        html += previewTable(staffRows, ['Cadre', 'Male', 'Female', 'Total']);

        /* 3. Arms/Armoury Returns by Command */
        let rows = tableRows('arms-body');
        if (rows.length) rows.push(strongRow(['Total', '', getText('arms-grand-serviceable'), getText('arms-grand-unserviceable'), getText('arms-grand-total')]));
        html += previewSectionTitle(3, 'Arms/Armoury Returns by Command');
        html += previewTable(rows, ['Command', 'Types of Arms', 'Serviceable', 'Unserviceable', 'Total']);

        /* 4. Ammunition by Command */
        rows = tableRows('ammunition-body');
        if (rows.length) rows.push(strongRow(['Total', '', getText('ammo-grand-rounds'), getText('ammo-grand-serviceable'), getText('ammo-grand-unserviceable'), getText('ammo-grand-used'), getText('ammo-grand-total')]));
        html += previewSectionTitle(4, 'Ammunition by Command');
        html += previewTable(rows, ['Command', 'Types of Ammunition', 'Rounds', 'Serviceable', 'Unserviceable', 'Used', 'Total']);

        /* 5. Arms Summary */
        const armsSumRows = [];
        document.querySelectorAll('.summary-arms-row').forEach(row => {
            armsSumRows.push([
                esc(row.querySelector('td:nth-child(2)').textContent.trim()),
                row.querySelector('.sum-arms-s').value || '0',
                row.querySelector('.sum-arms-u').value || '0',
                row.querySelector('.sum-arms-total').value || '0'
            ]);
        });
        armsSumRows.push(strongRow(['TOTAL', getText('arms-sum-grand-serviceable'), getText('arms-sum-grand-unserviceable'), getText('arms-sum-grand-total')]));
        html += previewSectionTitle(5, 'Arms Summary (Entire Directorate)');
        html += previewTable(armsSumRows, ['Type', 'Serviceable', 'Unserviceable', 'Total']);

        /* 6. Ammunition Summary */
        const ammoSumRows = [];
        document.querySelectorAll('.summary-ammo-row').forEach(row => {
            ammoSumRows.push([
                esc(row.querySelector('td:nth-child(2)').textContent.trim()),
                row.querySelector('.calc-sum-ammo-rounds').value || '0',
                row.querySelector('.calc-sum-ammo-total').value || '0',
                row.querySelector('.calc-sum-ammo-rounds-ammo').value || '0',
                row.querySelector('.calc-sum-ammo-bal-cf').value || '0'
            ]);
        });
        ammoSumRows.push(strongRow(['TOTAL', getText('ammo-sum-grand-rounds'), getText('ammo-sum-grand-total'), getText('ammo-sum-grand-rounds-ammo'), getText('ammo-sum-grand-bal-cf')]));
        html += previewSectionTitle(6, 'Ammunition Summary');
        html += previewTable(ammoSumRows, ['Type', 'No. of Rounds', 'Total', 'Rounds of Ammunition', 'Bal C/F']);

        /* 7. Store & Warehouse */
        html += previewSectionTitle(7, 'Store & Warehouse');
        rows = tableRows('store-accessories-body');
        if (rows.length) rows.push(strongRow(['Total', '', getText('store-acc-grand-supplied'), getText('store-acc-grand-issued'), getText('store-acc-grand-total')]));
        html += previewSubTitle('a. Elect/Elect Equipments, Spares & Accessories');
        html += previewTable(rows, ['Command', 'Items', 'Qty Supplied', 'Qty Issues', 'Total Bal']);
        rows = tableRows('store-stationery-body');
        if (rows.length) rows.push(strongRow(['Total', '', getText('store-stat-grand-supplied'), getText('store-stat-grand-issued'), getText('store-stat-grand-total')]));
        html += previewSubTitle('b. Stationary');
        html += previewTable(rows, ['Command', 'Items', 'Qty Supplied', 'Qty Issues', 'Total Bal']);
        rows = tableRows('store-uniforms-body');
        if (rows.length) rows.push(strongRow(['Total', '', getText('store-uni-grand-supplied'), getText('store-uni-grand-issued'), getText('store-uni-grand-bal-cf'), getText('store-uni-grand-total')]));
        html += previewSubTitle('c. Uniform and Accessories');
        html += previewTable(rows, ['Command', 'Items', 'New Qty Supplied', 'Qty Issues', 'Bal. C/F', 'Total']);
        rows = tableRows('store-oil-gas-body');
        if (rows.length) rows.push(strongRow(['Total', '', getText('store-oil-grand-supplied'), getText('store-oil-grand-issued'), getText('store-oil-grand-bal-cf'), getText('store-oil-grand-total')]));
        html += previewSubTitle('d. Oil and Gas');
        html += previewTable(rows, ['Command', 'Items', 'New Qty Supplied', 'Qty Issues', 'Bal. C/F', 'Total']);
        const tailorRows = [];
        document.querySelectorAll('.tailor-row').forEach(row => {
            tailorRows.push([
                esc(row.querySelector('td:nth-child(2)').textContent.trim()),
                row.querySelector('.tailor-bf').value || '0',
                row.querySelector('.tailor-new').value || '0',
                row.querySelector('.tailor-issued').value || '0',
                row.querySelector('.tailor-bal-cf').value || '0',
                row.querySelector('.tailor-total').value || '0'
            ]);
        });
        tailorRows.push(strongRow(['TOTAL', getText('tailor-grand-bf'), getText('tailor-grand-new'), getText('tailor-grand-issued'), getText('tailor-grand-bal-cf'), getText('tailor-grand-total')]));
        html += previewSubTitle('e. Tailoring');
        html += previewTable(tailorRows, ['Item', 'Bal. B/F', 'New Qty Supplied', 'Qty Issued', 'Bal. C/F', 'Total']);

        /* 8. Transport */
        html += previewSectionTitle(8, 'Transport');
        rows = tableRows('transport-fleet-body');
        if (rows.length) rows.push(strongRow(['Total Vehicles in Fleet', '', '', '', '', getText('transport-fleet-grand-total')]));
        html += previewSubTitle('a. Vehicle Details by Command');
        html += previewTable(rows, ['Command', 'Make & Model', 'Chassis Number', 'Plate Number', 'VIN', 'Remarks']);
        rows = tableRows('transport-procurement-body');
        if (rows.length) rows.push(strongRow(['Total Vehicles Procured', '', '', '', '', getText('transport-procurement-grand-total')]));
        html += previewSubTitle('b. Procurement Log');
        html += previewTable(rows, ['Location / Deploy Command', 'File Number', 'Type of Vehicle', 'Chassis Number', 'Remark', 'Date of Procurement']);

        /* 9. Works & Projects */
        html += previewSectionTitle(9, 'Works & Projects');
        rows = tableRows('infra-projects-body');
        if (rows.length) rows.push(strongRow(['Total Infrastructural Projects', '', '', '', getText('infra-projects-count'), getText('infra-projects-avg-completion')]));
        html += previewSubTitle('a. Status of Infrastructural Projects Executed');
        html += previewTable(rows, ['Description', 'Date of Award', 'Location', 'Contractor', 'Status', 'Completion (%)']);
        rows = tableRows('intervention-projects-body');
        if (rows.length) rows.push(strongRow(['Total Intervention Projects', '', '', '', getText('intervention-projects-count'), getText('intervention-projects-avg-completion')]));
        html += previewSubTitle('b. Intervention Projects Executed');
        html += previewTable(rows, ['Description', 'Year of Award', 'Location', 'Contractor', 'Status', 'Completion (%)']);
        rows = tableRows('notable-activities-body');
        if (rows.length) rows.push(strongRow(['Total Notable Activities', getText('notable-activities-grand-total')]));
        html += previewSubTitle('c. Other Notable Activities');
        html += previewTable(rows, ['Activity Description', 'Remarks']);

        /* 10. Maintenance & Energy */
        html += previewSectionTitle(10, 'Maintenance & Energy');
        rows = tableRows('maintenance-body');
        if (rows.length) rows.push(strongRow(['Total Maintenance Activities', '', '', '', '', getText('maintenance-grand-total')]));
        html += previewSubTitle('5. Maintenance Section');
        html += previewTable(rows, ['Unit', 'Activity Details', 'Period', 'Location', 'Status', 'Remark']);
        const energyLabels = [
            ['renewable', '6. a. Renewable Energy Unit'],
            ['electrical', '6. b. Electrical Unit'],
            ['generator', '6. c. Generator Unit'],
            ['welding', '6. d. Welding Unit'],
            ['fuel', '6. e. Record of Fuel Distribution'],
            ['ac', '6. f. Refrigeration and Air Conditioning']
        ];
        energyLabels.forEach(([unit, label]) => {
            let uRows = tableRows(`energy-${unit}-body`);
            if (uRows.length) uRows.push(strongRow(['Total Logs', '', '', '', getText(`energy-${unit}-grand-total`)]));
            html += previewSubTitle(label);
            html += previewTable(uRows, ['Work Description', 'Date', 'Location', 'Status', 'Remark']);
        });

        /* 11. General Report */
        html += previewSectionTitle(11, 'General Report');
        [['Challenges', 'general_report[challenges]', 'No challenges reported.'],
         ['Recommendations / Way Forward', 'general_report[recommendations]', 'No recommendations provided.']
        ].forEach(([label, name, emptyText]) => {
            html += `<div style="margin-top:12px;"><strong>${label}:</strong></div>`;
            const value = document.querySelector(`[name="${name}"]`)?.value.trim() || '';
            html += value
                ? `<p style="font-size:.82rem;color:var(--gray-800);margin-top:4px;white-space:pre-wrap;">${esc(value)}</p>`
                : `<p style="font-size:.82rem;color:var(--gray-600);margin-top:4px;">${emptyText}</p>`;
        });
        html += `<div style="margin-top:12px;"><strong>Supporting Documents:</strong></div>`;
        const docInputs = Array.from(document.querySelectorAll('#documents-body input[type="file"]'))
            .filter(input => input.files && input.files.length > 0);
        if (!docInputs.length) {
            html += `<p style="font-size:.82rem;color:var(--gray-600);margin-top:4px;">No supporting documents uploaded.</p>`;
        } else {
            html += `<ul style="margin-top:4px;">`;
            docInputs.forEach((input, idx) => {
                html += `<li style="font-size:.82rem;color:var(--gray-800);">Document ${idx + 1}: ${esc(input.files[0].name)}</li>`;
            });
            html += `</ul>`;
        }

        container.innerHTML = html;
    }

    /* Let the layout's preview tab use this page-specific renderer. */
    window.buildDirectoratePreview = buildPreview;

    /* Init */
    recomputeAll();
})();
</script>

@endsection
