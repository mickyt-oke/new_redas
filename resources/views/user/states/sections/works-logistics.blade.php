{{-- Works & Logistics directorate return sections. Shared partial: rendered
     inside the standalone directorate page (user.directorates.works-logistics)
     and inside the combined state return form (user.states._return-layout,
     wrapped in #dir-works-logistics). The combined page supplies the form,
     report metadata, shared attachments[] input and scoped tab switching. --}}

@php
use Illuminate\Support\Arr;

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
    'Immigration Assistant III',
];

$armsJson = json_decode(file_get_contents(resource_path('js/arms.json')) ?: '{}', true);
$armsByCategory = collect($armsJson['arms'] ?? [])
    ->groupBy('category')
    ->map(fn($items) => collect($items)->pluck('name')->values())
    ->toArray();

$ammoJson = json_decode(file_get_contents(resource_path('js/ammunition.json')) ?: '{}', true);
$ammoList = $ammoJson['ammunition'] ?? [];

$storeItemsJson = json_decode(file_get_contents(resource_path('js/store_items.json')) ?: '{}', true);
$storeItemList = $storeItemsJson['items'] ?? [];

/* Pre-build option HTML once in PHP so PHP-rendered rows and JS row templates
   stay consistent without duplicating markup logic. */
$armsOptionsHtml = '<option value="">-- Select Type of Arms --</option>';
foreach ($armsByCategory as $category => $names) {
    $armsOptionsHtml .= '<optgroup label="' . e($category) . '">';
    foreach ($names as $name) {
        $armsOptionsHtml .= '<option value="' . e($name) . '">' . e($name) . '</option>';
    }
    $armsOptionsHtml .= '</optgroup>';
}

$ammoOptionsHtml = '<option value="">-- Select Ammunition Type --</option>'
    . collect($ammoList)->map(fn($i) => '<option value="' . e($i) . '">' . e($i) . '</option>')->implode('')
    . '<option value="Others">Others</option>';

$storeOptionsHtml = '<option value="">-- Select Stationery Item --</option>'
    . collect($storeItemList)->map(fn($i) => '<option value="' . e($i) . '">' . e($i) . '</option>')->implode('')
    . '<option value="Others">Others</option>';

$vehicleOptions = ['Toyota Hilux', 'Toyota Coaster', 'Ford Ranger', 'Mercedes Benz Sprinter'];
$vehicleOptionsHtml = '<option value="">Select Make &amp; Model</option>'
    . collect($vehicleOptions)->map(fn($i) => '<option value="' . e($i) . '">' . e($i) . '</option>')->implode('');

/* Editing/validation repopulation helpers. */
$worksOld = old('works', $editing->return_data['works'] ?? []);
$armsReturns = old('arms_returns', $editing->return_data['arms_returns'] ?? []);
$missingArms = old('missing_arms_returns', $editing->return_data['missing_arms_returns'] ?? []);
$ammunitionReturns = old('ammunition_returns', $editing->return_data['ammunition_returns'] ?? []);
$storeAccessories = old('store_accessories', $editing->return_data['store_accessories'] ?? []);
$storeStationery = old('store_stationery', $editing->return_data['store_stationery'] ?? []);
$storeUniforms = old('store_uniforms', $editing->return_data['store_uniforms'] ?? []);
$storeOilGas = old('store_oil_gas', $editing->return_data['store_oil_gas'] ?? []);
$tailoring = old('store_tailoring', $editing->return_data['store_tailoring'] ?? []);
$transportFleet = old('transport_fleet', $editing->return_data['transport_fleet'] ?? []);
$transportProcurement = old('transport_procurement', $editing->return_data['transport_procurement'] ?? []);
$infraProjects = old('works_infra_projects', $editing->return_data['works_infra_projects'] ?? []);
$interventionProjects = old('works_intervention_projects', $editing->return_data['works_intervention_projects'] ?? []);
$notableActivities = old('works_notable_activities', $editing->return_data['works_notable_activities'] ?? []);
$maintenanceLog = old('maintenance_log', $editing->return_data['maintenance_log'] ?? []);
$energyUtilities = old('energy_utilities', $editing->return_data['energy_utilities'] ?? []);

$tailorItems = [
    'Officer Uniform Set', 'Recruit Uniform Set', 'Thread rolls',
    'Buttons packs', 'Zippers packs', 'Tailoring Scissors',
];

$energyUnits = [
    ['renewable', '6. a. RENEWABLE ENERGY UNIT', 'TOTAL RENEWABLE ENERGY LOGS', 'Add Renewable Log', 'fas fa-solar-panel'],
    ['electrical', '6. b. ELECTRICAL UNIT', 'TOTAL ELECTRICAL UNIT LOGS', 'Add Electrical Log', 'fas fa-bolt'],
    ['generator', '6. c. GENERATOR UNIT', 'TOTAL GENERATOR UNIT LOGS', 'Add Generator Log', 'fas fa-charging-station'],
    ['welding', '6. d. WELDING UNIT', 'TOTAL WELDING UNIT LOGS', 'Add Welding Log', 'fas fa-fire'],
    ['fuel', '6. e. RECORD OF FUEL DISTRIBUTION', 'TOTAL FUEL DISTRIBUTION LOGS', 'Add Fuel Log', 'fas fa-gas-pump'],
    ['ac', '6. f. REFRIGERATION AND AIR CONDITIONING', 'TOTAL HVAC UNIT LOGS', 'Add HVAC Log', 'fas fa-wind'],
];

$energyUnitMap = [
    'renewable' => 'Renewable Energy Unit',
    'electrical' => 'Electrical Unit',
    'generator' => 'Generator Unit',
    'welding' => 'Welding Unit',
    'fuel' => 'Record of Fuel Distribution',
    'ac' => 'Refrigeration and Air Conditioning',
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
            ]; @endphp
            @foreach($tabs as $i => [$id,$icon,$label])
            <button type="button" class="entry-tab {{ $i === 0 ? 'active' : '' }}" data-tab="works-logistics-{{ $id }}">
                <span class="tab-dot"></span>
                <i class="{{ $icon }}" style="font-size:.78rem;"></i>
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>
</div>

<div class="tab-content">

    <!-- TAB 1: Staff Strength by Cadre -->
    <div class="tab-panel active" id="tab-works-logistics-cadre">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-users"></i></div>
                    1. Personnel Strength Cadre
                </div>
            </div>
            <div class="card-body">
            @if($stateEmbedded ?? false)
                <p style="font-size:.82rem;color:var(--gray-500);padding:8px 0;">
                    <i class="fas fa-circle-info" style="color:var(--nis-500);margin-right:6px;"></i>
                    Staff strength for this directorate is captured once under the <strong>HRM</strong> section of this return.
                </p>
            @else
                <div class="table-responsive">
                    <table class="nis-table" id="staff-strength-table">
                        <thead>
                            <tr><th>CADRES</th><th style="width:130px;">MALE</th><th style="width:130px;">FEMALE</th><th style="width:130px;">TOTAL</th></tr>
                        </thead>
                        <tbody>
                            @foreach($cadres as $c)
                            @php $c_slug = strtolower(str_replace(' ', '_', $c)); @endphp
                            <tr class="staff-row">
                                <td>
                                    {{ $c }}
                                    <input type="hidden" name="works[staff_strength][{{ $c_slug }}][cadre]" value="{{ $c }}">
                                </td>
                                <td><input type="number" name="works[staff_strength][{{ $c_slug }}][male]" class="ni staff-male calc-staff-total" value="{{ Arr::get($worksOld, 'staff_strength.' . $c_slug . '.male', 0) }}" min="0" step="1"></td>
                                <td><input type="number" name="works[staff_strength][{{ $c_slug }}][female]" class="ni staff-female calc-staff-total" value="{{ Arr::get($worksOld, 'staff_strength.' . $c_slug . '.female', 0) }}" min="0" step="1"></td>
                                <td><input type="number" name="works[staff_strength][{{ $c_slug }}][total]" class="ni staff-total" value="{{ Arr::get($worksOld, 'staff_strength.' . $c_slug . '.total', 0) }}" readonly style="background:#f9fafb;"></td>
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
                <input type="hidden" name="works[staff_strength][totals][male]" id="staff-grand-male-input" value="0">
                <input type="hidden" name="works[staff_strength][totals][female]" id="staff-grand-female-input" value="0">
                <input type="hidden" name="works[staff_strength][totals][total]" id="staff-grand-total-input" value="0">
            @endif
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
    <div class="tab-panel" id="tab-works-logistics-rank">
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
    <div class="tab-panel" id="tab-works-logistics-ordinance-armoury">
        <!-- a. ARMS/ARMOURY RETURNS  -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-shield-alt"></i></div>
                    a. ARMS/ARMOURY RETURNS
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
                                <th>Types of Arms</th>
                                <th style="width:120px;">No. of Serviceable</th>
                                <th style="width:120px;">No. of Unserviceable</th>
                                <th style="width:100px;">Total</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="arms-body">
                            @forelse($armsReturns as $idx => $row)
                            @php $selectedType = $row['type'] ?? ''; @endphp
                            <tr class="arms-command-row">
                                <td>
                                    <select name="arms_returns[{{ $idx }}][type]" class="ni ni-select">
                                        <option value="">-- Select Type of Arms --</option>
                                        @foreach($armsByCategory as $category => $names)
                                        <optgroup label="{{ $category }}">
                                            @foreach($names as $name)
                                            <option value="{{ $name }}" @selected($selectedType === $name)>{{ $name }}</option>
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="arms_returns[{{ $idx }}][serviceable]" class="ni calc-arms" value="{{ $row['serviceable'] ?? 0 }}" min="0" step="1"></td>
                                <td><input type="number" name="arms_returns[{{ $idx }}][unserviceable]" class="ni calc-arms" value="{{ $row['unserviceable'] ?? 0 }}" min="0" step="1"></td>
                                <td><input type="number" name="arms_returns[{{ $idx }}][total]" class="ni arms-tot-disp" value="{{ $row['total'] ?? 0 }}" readonly style="background:#f9fafb;"></td>
                                <td style="text-align:center;"><button type="button" class="btn-nis btn-ghost wl-remove-row" style="color:var(--color-danger);padding:4px;"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><strong><span id="arms-grand-serviceable">0</span></strong></td>
                                <td><strong><span id="arms-grand-unserviceable">0</span></strong></td>
                                <td><strong><span id="arms-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <input type="hidden" name="works[totals][arms][serviceable]" id="arms-grand-serviceable-input" value="0">
                <input type="hidden" name="works[totals][arms][unserviceable]" id="arms-grand-unserviceable-input" value="0">
                <input type="hidden" name="works[totals][arms][total]" id="arms-grand-total-input" value="0">
            </div>
        </div>

        <!-- b. MISSING ARMS/ARMOURY RETURNS  -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#fef2f2;color:#991b1b;"><i class="fas fa-exclamation-triangle"></i></div>
                    b. MISSING ARMS/ARMOURY RETURNS
                </div>
                <button type="button" class="btn-nis btn-ghost btn-sm wl-add-row" data-row-target="missing-arms-body" data-row-prefix="missing_arms_returns">
                    <i class="fas fa-plus"></i> Add Missing Arms Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="missing-arms-table">
                        <thead>
                            <tr>
                                <th>Types of Arms</th>
                                <th>Reason for Missing</th>
                                <th style="width:350px;">Serial No.</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="missing-arms-body">
                            @forelse($missingArms as $idx => $row)
                            @php $selectedType = $row['type'] ?? ''; @endphp
                            <tr class="missing-arms-row">
                                <td>
                                    <select name="missing_arms_returns[{{ $idx }}][type]" class="ni ni-select">
                                        <option value="">-- Select Type of Arms --</option>
                                        @foreach($armsByCategory as $category => $names)
                                        <optgroup label="{{ $category }}">
                                            @foreach($names as $name)
                                            <option value="{{ $name }}" @selected($selectedType === $name)>{{ $name }}</option>
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text" name="missing_arms_returns[{{ $idx }}][reason]" class="ni" placeholder="e.g. Lost in transit" value="{{ $row['reason'] ?? '' }}"></td>
                                <td><input type="text" name="missing_arms_returns[{{ $idx }}][serial]" class="ni" placeholder="e.g. NIS-AR-001234" value="{{ $row['serial'] ?? '' }}"></td>
                                <td style="text-align:center;"><button type="button" class="btn-nis btn-ghost wl-remove-row" style="color:var(--color-danger);padding:4px;"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="3"><strong>TOTAL MISSING ARMS:</strong> <span id="missing-arms-grand-total">0</span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <input type="hidden" name="works[totals][missing_arms]" id="missing-arms-grand-total-input" value="0">
            </div>
        </div>

        <!-- c. AMMUNITION -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-bullseye"></i></div>
                    c. AMMUNITION
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
                                <th>Types of Ammunition</th>
                                <th style="width:110px;">No. of Rounds</th>
                                <th style="width:110px;">No. of Serviceable</th>
                                <th style="width:110px;">No. of Unserviceable</th>
                                <th style="width:100px;">No. of Used</th>
                                <th style="width:90px;">Total</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="ammunition-body">
                            @forelse($ammunitionReturns as $idx => $row)
                            @php $selectedType = $row['type'] ?? ''; @endphp
                            <tr class="ammo-item-row">
                                <td>
                                    <select name="ammunition_returns[{{ $idx }}][type]" class="ni ni-select">
                                        <option value="">-- Select Ammunition Type --</option>
                                        @foreach($ammoList as $ammoType)
                                        <option value="{{ $ammoType }}" @selected($selectedType === $ammoType)>{{ $ammoType }}</option>
                                        @endforeach
                                        <option value="Others" @selected($selectedType === 'Others')>Others</option>
                                    </select>
                                </td>
                                <td><input type="number" name="ammunition_returns[{{ $idx }}][rounds]" class="ni calc-ammo" value="{{ $row['rounds'] ?? 0 }}" min="0" step="1"></td>
                                <td><input type="number" name="ammunition_returns[{{ $idx }}][serviceable]" class="ni calc-ammo" value="{{ $row['serviceable'] ?? 0 }}" min="0" step="1"></td>
                                <td><input type="number" name="ammunition_returns[{{ $idx }}][unserviceable]" class="ni calc-ammo" value="{{ $row['unserviceable'] ?? 0 }}" min="0" step="1"></td>
                                <td><input type="number" name="ammunition_returns[{{ $idx }}][used]" class="ni calc-ammo" value="{{ $row['used'] ?? 0 }}" min="0" step="1"></td>
                                <td><input type="number" name="ammunition_returns[{{ $idx }}][total]" class="ni ammo-tot-disp" value="{{ $row['total'] ?? 0 }}" readonly style="background:#f9fafb;"></td>
                                <td style="text-align:center;"><button type="button" class="btn-nis btn-ghost wl-remove-row" style="color:var(--color-danger);padding:4px;"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
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
                <input type="hidden" name="works[totals][ammunition][rounds]" id="ammo-grand-rounds-input" value="0">
                <input type="hidden" name="works[totals][ammunition][serviceable]" id="ammo-grand-serviceable-input" value="0">
                <input type="hidden" name="works[totals][ammunition][unserviceable]" id="ammo-grand-unserviceable-input" value="0">
                <input type="hidden" name="works[totals][ammunition][used]" id="ammo-grand-used-input" value="0">
                <input type="hidden" name="works[totals][ammunition][total]" id="ammo-grand-total-input" value="0">
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
    <div class="tab-panel" id="tab-works-logistics-store-warehouse">
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
                                <th>Items</th>
                                <th style="width:130px;">NO. of Qty Supplied</th>
                                <th style="width:110px;">Qty Issues</th>
                                <th style="width:100px;">Total Bal</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="store-accessories-body">
                            @forelse($storeAccessories as $idx => $row)
                            <tr class="store-acc-row">
                                <td><input type="text" name="store_accessories[{{ $idx }}][item]" class="ni" placeholder="e.g. Keyboard, Fuel pump" value="{{ $row['item'] ?? '' }}"></td>
                                <td><input type="number" name="store_accessories[{{ $idx }}][qty_supplied]" class="ni calc-store-acc" value="{{ $row['qty_supplied'] ?? 0 }}" min="0" step="1"></td>
                                <td><input type="number" name="store_accessories[{{ $idx }}][qty_issues]" class="ni calc-store-acc" value="{{ $row['qty_issues'] ?? 0 }}" min="0" step="1"></td>
                                <td><input type="number" name="store_accessories[{{ $idx }}][total_bal]" class="ni store-acc-tot" value="{{ $row['total_bal'] ?? 0 }}" readonly style="background:#f9fafb;"></td>
                                <td style="text-align:center;"><button type="button" class="btn-nis btn-ghost wl-remove-row" style="color:var(--color-danger);padding:4px;"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><strong><span id="store-acc-grand-supplied">0</span></strong></td>
                                <td><strong><span id="store-acc-grand-issued">0</span></strong></td>
                                <td><strong><span id="store-acc-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <input type="hidden" name="works[totals][store_accessories][supplied]" id="store-acc-grand-supplied-input" value="0">
                <input type="hidden" name="works[totals][store_accessories][issued]" id="store-acc-grand-issued-input" value="0">
                <input type="hidden" name="works[totals][store_accessories][total]" id="store-acc-grand-total-input" value="0">
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
                                <th>Items</th>
                                <th style="width:130px;">NO. of Qty Supplied</th>
                                <th style="width:110px;">Qty Issues</th>
                                <th style="width:100px;">Total Bal</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="store-stationery-body">
                            @forelse($storeStationery as $idx => $row)
                            @php $selectedItem = $row['item'] ?? ''; @endphp
                            <tr class="store-stat-row">
                                <td>
                                    <select name="store_stationery[{{ $idx }}][item]" class="ni ni-select">
                                        <option value="">-- Select Stationery Item --</option>
                                        @foreach($storeItemList as $storeItem)
                                        <option value="{{ $storeItem }}" @selected($selectedItem === $storeItem)>{{ $storeItem }}</option>
                                        @endforeach
                                        <option value="Others" @selected($selectedItem === 'Others')>Others</option>
                                    </select>
                                </td>
                                <td><input type="number" name="store_stationery[{{ $idx }}][qty_received]" class="ni calc-store-stat" value="{{ $row['qty_received'] ?? 0 }}" min="0" step="1"></td>
                                <td><input type="number" name="store_stationery[{{ $idx }}][qty_issues]" class="ni calc-store-stat" value="{{ $row['qty_issues'] ?? 0 }}" min="0" step="1"></td>
                                <td><input type="number" name="store_stationery[{{ $idx }}][total_bal]" class="ni store-stat-tot" value="{{ $row['total_bal'] ?? 0 }}" readonly style="background:#f9fafb;"></td>
                                <td style="text-align:center;"><button type="button" class="btn-nis btn-ghost wl-remove-row" style="color:var(--color-danger);padding:4px;"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><strong><span id="store-stat-grand-supplied">0</span></strong></td>
                                <td><strong><span id="store-stat-grand-issued">0</span></strong></td>
                                <td><strong><span id="store-stat-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <input type="hidden" name="works[totals][store_stationery][supplied]" id="store-stat-grand-supplied-input" value="0">
                <input type="hidden" name="works[totals][store_stationery][issued]" id="store-stat-grand-issued-input" value="0">
                <input type="hidden" name="works[totals][store_stationery][total]" id="store-stat-grand-total-input" value="0">
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
                                <th>Items</th>
                                <th style="width:130px;">New Qty Supplied</th>
                                <th style="width:110px;">Qty Issues</th>
                                <th style="width:100px;">Bal. C/F</th>
                                <th style="width:90px;">TOTAL</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="store-uniforms-body">
                            @forelse($storeUniforms as $idx => $row)
                            <tr class="store-uni-row">
                                <td><input type="text" name="store_uniforms[{{ $idx }}][item]" class="ni" placeholder="e.g. Beret, Uniform Set" value="{{ $row['item'] ?? '' }}"></td>
                                <td><input type="number" name="store_uniforms[{{ $idx }}][qty_supplied]" class="ni calc-store-uni" value="{{ $row['qty_supplied'] ?? 0 }}" min="0" step="1"></td>
                                <td><input type="number" name="store_uniforms[{{ $idx }}][qty_issues]" class="ni calc-store-uni" value="{{ $row['qty_issues'] ?? 0 }}" min="0" step="1"></td>
                                <td><input type="number" name="store_uniforms[{{ $idx }}][bal_cf]" class="ni store-uni-bal-cf" value="{{ $row['bal_cf'] ?? 0 }}" readonly style="background:#f9fafb;"></td>
                                <td><input type="number" name="store_uniforms[{{ $idx }}][total]" class="ni store-uni-tot" value="{{ $row['total'] ?? 0 }}" readonly style="background:#f9fafb;"></td>
                                <td style="text-align:center;"><button type="button" class="btn-nis btn-ghost wl-remove-row" style="color:var(--color-danger);padding:4px;"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><strong><span id="store-uni-grand-supplied">0</span></strong></td>
                                <td><strong><span id="store-uni-grand-issued">0</span></strong></td>
                                <td><strong><span id="store-uni-grand-bal-cf">0</span></strong></td>
                                <td><strong><span id="store-uni-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <input type="hidden" name="works[totals][store_uniforms][supplied]" id="store-uni-grand-supplied-input" value="0">
                <input type="hidden" name="works[totals][store_uniforms][issued]" id="store-uni-grand-issued-input" value="0">
                <input type="hidden" name="works[totals][store_uniforms][bal_cf]" id="store-uni-grand-bal-cf-input" value="0">
                <input type="hidden" name="works[totals][store_uniforms][total]" id="store-uni-grand-total-input" value="0">
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
                                <th>Items</th>
                                <th style="width:130px;">New Qty Supplied</th>
                                <th style="width:110px;">Qty Issues</th>
                                <th style="width:100px;">Bal. C/F</th>
                                <th style="width:90px;">TOTAL</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="store-oil-gas-body">
                            @forelse($storeOilGas as $idx => $row)
                            <tr class="store-oil-row">
                                <td><input type="text" name="store_oil_gas[{{ $idx }}][item]" class="ni" placeholder="e.g. PMS, AGO" value="{{ $row['item'] ?? '' }}"></td>
                                <td><input type="number" name="store_oil_gas[{{ $idx }}][qty_supplied]" class="ni calc-store-oil" value="{{ $row['qty_supplied'] ?? 0 }}" min="0" step="1"></td>
                                <td><input type="number" name="store_oil_gas[{{ $idx }}][qty_issues]" class="ni calc-store-oil" value="{{ $row['qty_issues'] ?? 0 }}" min="0" step="1"></td>
                                <td><input type="number" name="store_oil_gas[{{ $idx }}][bal_cf]" class="ni store-oil-bal-cf" value="{{ $row['bal_cf'] ?? 0 }}" readonly style="background:#f9fafb;"></td>
                                <td><input type="number" name="store_oil_gas[{{ $idx }}][total]" class="ni store-oil-tot" value="{{ $row['total'] ?? 0 }}" readonly style="background:#f9fafb;"></td>
                                <td style="text-align:center;"><button type="button" class="btn-nis btn-ghost wl-remove-row" style="color:var(--color-danger);padding:4px;"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td><strong><span id="store-oil-grand-supplied">0</span></strong></td>
                                <td><strong><span id="store-oil-grand-issued">0</span></strong></td>
                                <td><strong><span id="store-oil-grand-bal-cf">0</span></strong></td>
                                <td><strong><span id="store-oil-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <input type="hidden" name="works[totals][store_oil_gas][supplied]" id="store-oil-grand-supplied-input" value="0">
                <input type="hidden" name="works[totals][store_oil_gas][issued]" id="store-oil-grand-issued-input" value="0">
                <input type="hidden" name="works[totals][store_oil_gas][bal_cf]" id="store-oil-grand-bal-cf-input" value="0">
                <input type="hidden" name="works[totals][store_oil_gas][total]" id="store-oil-grand-total-input" value="0">
            </div>
        </div>

        <!-- e. TAILORING -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-cut"></i></div>
                    e. TAILORING
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table" id="tailoring-table">
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
                            @php
                                $row = $tailoring[$index] ?? [];
                            @endphp
                            <tr class="tailor-row">
                                <td>{{ $index + 1 }}</td>
                                <td style="font-weight:600;">
                                    {{ $item }}
                                    <input type="hidden" name="store_tailoring[{{ $index }}][item]" value="{{ $item }}">
                                </td>
                                <td><input type="number" name="store_tailoring[{{ $index }}][bal_bf]" class="ni tailor-bf calc-tailor" value="{{ $row['bal_bf'] ?? 0 }}" min="0" step="1"></td>
                                <td><input type="number" name="store_tailoring[{{ $index }}][new_qty]" class="ni tailor-new calc-tailor" value="{{ $row['new_qty'] ?? 0 }}" min="0" step="1"></td>
                                <td><input type="number" name="store_tailoring[{{ $index }}][qty_issued]" class="ni tailor-issued calc-tailor" value="{{ $row['qty_issued'] ?? 0 }}" min="0" step="1"></td>
                                <td><input type="number" name="store_tailoring[{{ $index }}][bal_cf]" class="ni tailor-bal-cf" value="{{ $row['bal_cf'] ?? 0 }}" readonly style="background:#f9fafb;"></td>
                                <td><input type="number" name="store_tailoring[{{ $index }}][total]" class="ni tailor-total" value="{{ $row['total'] ?? 0 }}" readonly style="background:#f9fafb;"></td>
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
                <input type="hidden" name="works[totals][tailoring][bf]" id="tailor-grand-bf-input" value="0">
                <input type="hidden" name="works[totals][tailoring][new]" id="tailor-grand-new-input" value="0">
                <input type="hidden" name="works[totals][tailoring][issued]" id="tailor-grand-issued-input" value="0">
                <input type="hidden" name="works[totals][tailoring][bal_cf]" id="tailor-grand-bal-cf-input" value="0">
                <input type="hidden" name="works[totals][tailoring][total]" id="tailor-grand-total-input" value="0">
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
    <div class="tab-panel" id="tab-works-logistics-transport">
        <!-- a. VEHICLE DETAILS  -->
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-truck"></i></div>
                    a. VEHICLE DETAILS
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
                                <th>Types (Make &amp; Model)</th>
                                <th>Chassis Number</th>
                                <th>Vehicle Plate Number</th>
                                <th>Identification (VIN)</th>
                                <th>Remarks / Condition</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="transport-fleet-body">
                            @forelse($transportFleet as $idx => $row)
                            @php $selectedVehicle = $row['make_model'] ?? ''; @endphp
                            <tr class="transport-fleet-row">
                                <td>
                                    <select name="transport_fleet[{{ $idx }}][make_model]" class="ni">
                                        <option value="">Select Make &amp; Model</option>
                                        @foreach($vehicleOptions as $vehicle)
                                        <option value="{{ $vehicle }}" @selected($selectedVehicle === $vehicle)>{{ $vehicle }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text" name="transport_fleet[{{ $idx }}][chassis]" class="ni" value="{{ $row['chassis'] ?? '' }}"></td>
                                <td><input type="text" name="transport_fleet[{{ $idx }}][plate]" class="ni" value="{{ $row['plate'] ?? '' }}"></td>
                                <td><input type="text" name="transport_fleet[{{ $idx }}][vin]" class="ni" value="{{ $row['vin'] ?? '' }}"></td>
                                <td><input type="text" name="transport_fleet[{{ $idx }}][remarks]" class="ni" placeholder="e.g. Serviceable" value="{{ $row['remarks'] ?? '' }}"></td>
                                <td style="text-align:center;"><button type="button" class="btn-nis btn-ghost wl-remove-row" style="color:var(--color-danger);padding:4px;"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="4"><strong>TOTAL VEHICLES IN FLEET</strong></td>
                                <td><strong><span id="transport-fleet-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <input type="hidden" name="works[totals][transport_fleet]" id="transport-fleet-grand-total-input" value="0">
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
                                <th>File Number</th>
                                <th>Type of Vehicle</th>
                                <th>Chassis Number</th>
                                <th>Remark</th>
                                <th style="width:150px;">Date of Procurement</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="transport-procurement-body">
                            @forelse($transportProcurement as $idx => $row)
                            <tr class="transport-procurement-row">
                                <td><input type="text" name="transport_procurement[{{ $idx }}][file_no]" class="ni" placeholder="e.g. W&amp;L/04" value="{{ $row['file_no'] ?? '' }}"></td>
                                <td><input type="text" name="transport_procurement[{{ $idx }}][make_model]" class="ni" placeholder="e.g. Toyota Coaster" value="{{ $row['make_model'] ?? '' }}"></td>
                                <td><input type="text" name="transport_procurement[{{ $idx }}][chassis]" class="ni" value="{{ $row['chassis'] ?? '' }}"></td>
                                <td><input type="text" name="transport_procurement[{{ $idx }}][remark]" class="ni" value="{{ $row['remark'] ?? '' }}"></td>
                                <td><input type="date" name="transport_procurement[{{ $idx }}][date]" class="ni" value="{{ $row['date'] ?? '' }}"></td>
                                <td style="text-align:center;"><button type="button" class="btn-nis btn-ghost wl-remove-row" style="color:var(--color-danger);padding:4px;"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="4"><strong>TOTAL VEHICLES PROCURED</strong></td>
                                <td><strong><span id="transport-procurement-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <input type="hidden" name="works[totals][transport_procurement]" id="transport-procurement-grand-total-input" value="0">
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
    <div class="tab-panel" id="tab-works-logistics-works-projects">
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
                        <tbody id="infra-projects-body">
                            @forelse($infraProjects as $idx => $row)
                            <tr class="infra-project-row">
                                <td><input type="text" name="works_infra_projects[{{ $idx }}][description]" class="ni" value="{{ $row['description'] ?? '' }}"></td>
                                <td><input type="date" name="works_infra_projects[{{ $idx }}][date]" class="ni" value="{{ $row['date'] ?? '' }}"></td>
                                <td><input type="text" name="works_infra_projects[{{ $idx }}][location]" class="ni" value="{{ $row['location'] ?? '' }}"></td>
                                <td><input type="text" name="works_infra_projects[{{ $idx }}][contractor]" class="ni" value="{{ $row['contractor'] ?? '' }}"></td>
                                <td>
                                    <select name="works_infra_projects[{{ $idx }}][status]" class="ni ni-select">
                                        <option value="Completed" @selected(($row['status'] ?? '') === 'Completed')>Completed</option>
                                        <option value="Ongoing" @selected(($row['status'] ?? '') === 'Ongoing')>Ongoing</option>
                                        <option value="Abandoned" @selected(($row['status'] ?? '') === 'Abandoned')>Abandoned</option>
                                    </select>
                                </td>
                                <td><input type="number" name="works_infra_projects[{{ $idx }}][completion]" class="ni calc-infra" value="{{ $row['completion'] ?? 0 }}" min="0" max="100" step="1"></td>
                                <td style="text-align:center;"><button type="button" class="btn-nis btn-ghost wl-remove-row" style="color:var(--color-danger);padding:4px;"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
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
                        <tbody id="intervention-projects-body">
                            @forelse($interventionProjects as $idx => $row)
                            <tr class="intervention-project-row">
                                <td><input type="text" name="works_intervention_projects[{{ $idx }}][description]" class="ni" value="{{ $row['description'] ?? '' }}"></td>
                                <td><input type="number" name="works_intervention_projects[{{ $idx }}][year]" class="ni" placeholder="e.g. 2026" min="2000" max="2100" step="1" value="{{ $row['year'] ?? '' }}"></td>
                                <td><input type="text" name="works_intervention_projects[{{ $idx }}][location]" class="ni" value="{{ $row['location'] ?? '' }}"></td>
                                <td><input type="text" name="works_intervention_projects[{{ $idx }}][contractor]" class="ni" value="{{ $row['contractor'] ?? '' }}"></td>
                                <td>
                                    <select name="works_intervention_projects[{{ $idx }}][status]" class="ni ni-select">
                                        <option value="Completed" @selected(($row['status'] ?? '') === 'Completed')>Completed</option>
                                        <option value="Ongoing" @selected(($row['status'] ?? '') === 'Ongoing')>Ongoing</option>
                                        <option value="Abandoned" @selected(($row['status'] ?? '') === 'Abandoned')>Abandoned</option>
                                    </select>
                                </td>
                                <td><input type="number" name="works_intervention_projects[{{ $idx }}][completion]" class="ni calc-interv" value="{{ $row['completion'] ?? 0 }}" min="0" max="100" step="1"></td>
                                <td style="text-align:center;"><button type="button" class="btn-nis btn-ghost wl-remove-row" style="color:var(--color-danger);padding:4px;"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
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
                        <tbody id="notable-activities-body">
                            @forelse($notableActivities as $idx => $row)
                            <tr class="notable-activity-row">
                                <td><input type="text" name="works_notable_activities[{{ $idx }}][description]" class="ni" value="{{ $row['description'] ?? '' }}"></td>
                                <td><input type="text" name="works_notable_activities[{{ $idx }}][remarks]" class="ni" value="{{ $row['remarks'] ?? '' }}"></td>
                                <td style="text-align:center;"><button type="button" class="btn-nis btn-ghost wl-remove-row" style="color:var(--color-danger);padding:4px;"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
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
    <div class="tab-panel" id="tab-works-logistics-maintenance">
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
                        <tbody id="maintenance-body">
                            @forelse($maintenanceLog as $idx => $row)
                            <tr class="maintenance-log-row">
                                <td>
                                    <select name="maintenance_log[{{ $idx }}][unit]" class="ni ni-select">
                                        <option value="Carpentry" @selected(($row['unit'] ?? '') === 'Carpentry')>Carpentry</option>
                                        <option value="Plumbing" @selected(($row['unit'] ?? '') === 'Plumbing')>Plumbing</option>
                                        <option value="Painting" @selected(($row['unit'] ?? '') === 'Painting')>Painting</option>
                                        <option value="Mason" @selected(($row['unit'] ?? '') === 'Mason')>Mason</option>
                                        <option value="Others" @selected(($row['unit'] ?? '') === 'Others')>Others</option>
                                    </select>
                                </td>
                                <td><input type="text" name="maintenance_log[{{ $idx }}][activity]" class="ni" value="{{ $row['activity'] ?? '' }}"></td>
                                <td><input type="month" name="maintenance_log[{{ $idx }}][period]" class="ni" value="{{ $row['period'] ?? '' }}"></td>
                                <td><input type="text" name="maintenance_log[{{ $idx }}][location]" class="ni" value="{{ $row['location'] ?? '' }}"></td>
                                <td>
                                    <select name="maintenance_log[{{ $idx }}][status]" class="ni ni-select">
                                        <option value="Completed" @selected(($row['status'] ?? '') === 'Completed')>Completed</option>
                                        <option value="Ongoing" @selected(($row['status'] ?? '') === 'Ongoing')>Ongoing</option>
                                        <option value="Abandoned" @selected(($row['status'] ?? '') === 'Abandoned')>Abandoned</option>
                                    </select>
                                </td>
                                <td><input type="text" name="maintenance_log[{{ $idx }}][remark]" class="ni" value="{{ $row['remark'] ?? '' }}"></td>
                                <td style="text-align:center;"><button type="button" class="btn-nis btn-ghost wl-remove-row" style="color:var(--color-danger);padding:4px;"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="5"><strong>TOTAL MAINTENANCE ACTIVITIES</strong></td>
                                <td><strong><span id="maintenance-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <input type="hidden" name="works[totals][maintenance]" id="maintenance-grand-total-input" value="0">
            </div>
        </div>

        <!-- 6. ENERGY SECTION -->
        @foreach($energyUnits as [$unitKey, $unitTitle, $unitTotal, $unitBtn, $unitIcon])
        @php
            $unitRows = [];
            foreach ($energyUtilities as $eIdx => $row) {
                if (($row['unit'] ?? '') === $energyUnitMap[$unitKey]) {
                    $unitRows[$eIdx] = $row;
                }
            }
        @endphp
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
                        <tbody id="energy-{{ $unitKey }}-body">
                            @forelse($unitRows as $eIdx => $row)
                            <tr class="energy-{{ $unitKey }}-row">
                                <td>
                                    <input type="hidden" name="energy_utilities[{{ $eIdx }}][unit]" value="{{ $energyUnitMap[$unitKey] }}">
                                    <input type="text" name="energy_utilities[{{ $eIdx }}][description]" class="ni" value="{{ $row['description'] ?? '' }}">
                                </td>
                                <td><input type="date" name="energy_utilities[{{ $eIdx }}][date]" class="ni" value="{{ $row['date'] ?? '' }}"></td>
                                <td><input type="text" name="energy_utilities[{{ $eIdx }}][location]" class="ni" value="{{ $row['location'] ?? '' }}"></td>
                                <td>
                                    <select name="energy_utilities[{{ $eIdx }}][status]" class="ni ni-select">
                                        <option value="Completed" @selected(($row['status'] ?? '') === 'Completed')>Completed</option>
                                        <option value="Ongoing" @selected(($row['status'] ?? '') === 'Ongoing')>Ongoing</option>
                                        <option value="Abandoned" @selected(($row['status'] ?? '') === 'Abandoned')>Abandoned</option>
                                    </select>
                                </td>
                                <td><input type="text" name="energy_utilities[{{ $eIdx }}][remark]" class="ni" value="{{ $row['remark'] ?? '' }}"></td>
                                <td style="text-align:center;"><button type="button" class="btn-nis btn-ghost wl-remove-row" style="color:var(--color-danger);padding:4px;"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="4"><strong>{{ $unitTotal }}</strong></td>
                                <td><strong><span id="energy-{{ $unitKey }}-grand-total">0</span></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <input type="hidden" name="works[totals][energy_{{ $unitKey }}]" id="energy-{{ $unitKey }}-grand-total-input" value="0">
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
    <div class="tab-panel" id="tab-works-logistics-general-report">
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
                    <textarea name="works[general_report][challenges]" class="ni @error('works.general_report.challenges') is-invalid @enderror" rows="4" placeholder="Describe any logistics, armoury, or project challenges...">{{ old('works.general_report.challenges', $worksOld['general_report']['challenges'] ?? '') }}</textarea>
                    @error('works.general_report.challenges')<div style="color:var(--color-danger);font-size:.75rem;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Recommendations / Way Forward</label>
                    <textarea name="works[general_report][recommendations]" class="ni @error('works.general_report.recommendations') is-invalid @enderror" rows="4" placeholder="Propose solutions or recommendations...">{{ old('works.general_report.recommendations', $worksOld['general_report']['recommendations'] ?? '') }}</textarea>
                    @error('works.general_report.recommendations')<div style="color:var(--color-danger);font-size:.75rem;margin-top:4px;">{{ $message }}</div>@enderror
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
    /* When embedded in the combined state form this partial lives inside
       #dir-works-logistics; scope all lookups to that panel so works-logistics
       classes/ids cannot leak into the other nine directorate panels. On the
       standalone page the wrapper does not exist and ROOT is the document. */
    var wlPanel = document.getElementById('dir-works-logistics');
    var ROOT = wlPanel || document;
    function byId(id) { return wlPanel ? wlPanel.querySelector('#' + id) : document.getElementById(id); }
    const form = (wlPanel && wlPanel.closest('form'))
        || document.querySelector('main form')
        || document.querySelector('form[action*="directorates"]');
    const tabContent = ROOT.querySelector('.tab-content');

    /* Tab navigation, draft save/restore and submit are wired globally in
       user.directorates._layout and partials.footer. */

    /* Helpers */
    function val(el) { return parseInt(el?.value || 0, 10) || 0; }
    function setText(id, v) {
        const el = byId(id);
        if (el) el.textContent = v;
        const hidden = byId(id + '-input');
        if (hidden) hidden.value = v;
    }
    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c];
        });
    }

    /* ── Dynamic row templates, keyed by tbody id ── */
    const ARMS_OPTIONS_HTML = @json($armsOptionsHtml);
    const AMMO_OPTIONS_HTML = @json($ammoOptionsHtml);
    const STORE_OPTIONS_HTML = @json($storeOptionsHtml);
    const VEHICLE_OPTIONS_HTML = @json($vehicleOptionsHtml);

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

    function energyRow(tbodyId, idx) {
        const unitKey = tbodyId.replace('energy-', '').replace('-body', '');
        const unitLabel = ENERGY_UNIT_LABELS[tbodyId];
        return `<tr class="energy-${unitKey}-row">
            <td><input type="hidden" name="energy_utilities[${idx}][unit]" value="${esc(unitLabel)}"><input type="text" name="energy_utilities[${idx}][description]" class="ni"></td>
            <td><input type="date" name="energy_utilities[${idx}][date]" class="ni"></td>
            <td><input type="text" name="energy_utilities[${idx}][location]" class="ni"></td>
            <td><select name="energy_utilities[${idx}][status]" class="ni ni-select">${STATUS_OPTIONS}</select></td>
            <td><input type="text" name="energy_utilities[${idx}][remark]" class="ni"></td>
            ${REMOVE_CELL}
        </tr>`;
    }

    const rowTemplates = {
        'arms-body': idx => `<tr class="arms-command-row">
            <td><select name="arms_returns[${idx}][type]" class="ni ni-select">${ARMS_OPTIONS_HTML}</select></td>
            <td><input type="number" name="arms_returns[${idx}][serviceable]" class="ni calc-arms" value="0" min="0" step="1"></td>
            <td><input type="number" name="arms_returns[${idx}][unserviceable]" class="ni calc-arms" value="0" min="0" step="1"></td>
            <td><input type="number" name="arms_returns[${idx}][total]" class="ni arms-tot-disp" value="0" readonly style="background:#f9fafb;"></td>
            ${REMOVE_CELL}
        </tr>`,
        'missing-arms-body': idx => `<tr class="missing-arms-row">
            <td><select name="missing_arms_returns[${idx}][type]" class="ni ni-select">${ARMS_OPTIONS_HTML}</select></td>
            <td><input type="text" name="missing_arms_returns[${idx}][reason]" class="ni" placeholder="e.g. Lost in transit"></td>
            <td><input type="text" name="missing_arms_returns[${idx}][serial]" class="ni" placeholder="e.g. NIS-AR-001234"></td>
            ${REMOVE_CELL}
        </tr>`,
        'ammunition-body': idx => `<tr class="ammo-item-row">
            <td><select name="ammunition_returns[${idx}][type]" class="ni ni-select">${AMMO_OPTIONS_HTML}</select></td>
            <td><input type="number" name="ammunition_returns[${idx}][rounds]" class="ni calc-ammo" value="0" min="0" step="1"></td>
            <td><input type="number" name="ammunition_returns[${idx}][serviceable]" class="ni calc-ammo" value="0" min="0" step="1"></td>
            <td><input type="number" name="ammunition_returns[${idx}][unserviceable]" class="ni calc-ammo" value="0" min="0" step="1"></td>
            <td><input type="number" name="ammunition_returns[${idx}][used]" class="ni calc-ammo" value="0" min="0" step="1"></td>
            <td><input type="number" name="ammunition_returns[${idx}][total]" class="ni ammo-tot-disp" value="0" readonly style="background:#f9fafb;"></td>
            ${REMOVE_CELL}
        </tr>`,
        'store-accessories-body': idx => `<tr class="store-acc-row">
            <td><select name="store_accessories[${idx}][item]" class="ni ni-select">${STORE_OPTIONS_HTML}</select></td>
            <td><input type="number" name="store_accessories[${idx}][qty_supplied]" class="ni calc-store-acc" value="0" min="0" step="1"></td>
            <td><input type="number" name="store_accessories[${idx}][qty_issues]" class="ni calc-store-acc" value="0" min="0" step="1"></td>
            <td><input type="number" name="store_accessories[${idx}][total_bal]" class="ni store-acc-tot" value="0" readonly style="background:#f9fafb;"></td>
            ${REMOVE_CELL}
        </tr>`,
        'store-stationery-body': idx => `<tr class="store-stat-row">
            <td><select name="store_stationery[${idx}][item]" class="ni ni-select">${STORE_OPTIONS_HTML}</select></td>
            <td><input type="number" name="store_stationery[${idx}][qty_received]" class="ni calc-store-stat" value="0" min="0" step="1"></td>
            <td><input type="number" name="store_stationery[${idx}][qty_issues]" class="ni calc-store-stat" value="0" min="0" step="1"></td>
            <td><input type="number" name="store_stationery[${idx}][total_bal]" class="ni store-stat-tot" value="0" readonly style="background:#f9fafb;"></td>
            ${REMOVE_CELL}
        </tr>`,
        'store-uniforms-body': idx => `<tr class="store-uni-row">
            <td><input type="text" name="store_uniforms[${idx}][item]" class="ni" placeholder="e.g. Beret, Uniform Set"></td>
            <td><input type="number" name="store_uniforms[${idx}][qty_supplied]" class="ni calc-store-uni" value="0" min="0" step="1"></td>
            <td><input type="number" name="store_uniforms[${idx}][qty_issues]" class="ni calc-store-uni" value="0" min="0" step="1"></td>
            <td><input type="number" name="store_uniforms[${idx}][bal_cf]" class="ni store-uni-bal-cf" value="0" readonly style="background:#f9fafb;"></td>
            <td><input type="number" name="store_uniforms[${idx}][total]" class="ni store-uni-tot" value="0" readonly style="background:#f9fafb;"></td>
            ${REMOVE_CELL}
        </tr>`,
        'store-oil-gas-body': idx => `<tr class="store-oil-row">
            <td><input type="text" name="store_oil_gas[${idx}][item]" class="ni" placeholder="e.g. PMS, AGO"></td>
            <td><input type="number" name="store_oil_gas[${idx}][qty_supplied]" class="ni calc-store-oil" value="0" min="0" step="1"></td>
            <td><input type="number" name="store_oil_gas[${idx}][qty_issues]" class="ni calc-store-oil" value="0" min="0" step="1"></td>
            <td><input type="number" name="store_oil_gas[${idx}][bal_cf]" class="ni store-oil-bal-cf" value="0" readonly style="background:#f9fafb;"></td>
            <td><input type="number" name="store_oil_gas[${idx}][total]" class="ni store-oil-tot" value="0" readonly style="background:#f9fafb;"></td>
            ${REMOVE_CELL}
        </tr>`,
        'transport-fleet-body': idx => `<tr class="transport-fleet-row">
            <td><select name="transport_fleet[${idx}][make_model]" class="ni">${VEHICLE_OPTIONS_HTML}</select></td>
            <td><input type="text" name="transport_fleet[${idx}][chassis]" class="ni"></td>
            <td><input type="text" name="transport_fleet[${idx}][plate]" class="ni"></td>
            <td><input type="text" name="transport_fleet[${idx}][vin]" class="ni"></td>
            <td><input type="text" name="transport_fleet[${idx}][remarks]" class="ni" placeholder="e.g. Serviceable"></td>
            ${REMOVE_CELL}
        </tr>`,
        'transport-procurement-body': idx => `<tr class="transport-procurement-row">
            <td><input type="text" name="transport_procurement[${idx}][file_no]" class="ni" placeholder="e.g. W&amp;L/04"></td>
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
            <td><input type="number" name="works_infra_projects[${idx}][completion]" class="ni calc-infra" value="0" min="0" max="100" step="1"></td>
            ${REMOVE_CELL}
        </tr>`,
        'intervention-projects-body': idx => `<tr class="intervention-project-row">
            <td><input type="text" name="works_intervention_projects[${idx}][description]" class="ni"></td>
            <td><input type="number" name="works_intervention_projects[${idx}][year]" class="ni" placeholder="e.g. 2026" min="2000" max="2100" step="1"></td>
            <td><input type="text" name="works_intervention_projects[${idx}][location]" class="ni"></td>
            <td><input type="text" name="works_intervention_projects[${idx}][contractor]" class="ni"></td>
            <td><select name="works_intervention_projects[${idx}][status]" class="ni ni-select">${STATUS_OPTIONS}</select></td>
            <td><input type="number" name="works_intervention_projects[${idx}][completion]" class="ni calc-interv" value="0" min="0" max="100" step="1"></td>
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
    function nextEnergyIndex() {
        let idx = 0;
        const energyTbodies = Object.keys(ENERGY_UNIT_LABELS).map(id => byId(id)).filter(Boolean);
        while (energyTbodies.some(tbody => tbody.querySelector(`[name*="energy_utilities[${idx}]"]`))) idx++;
        return idx;
    }
    function nextRowIndex(tbody) {
        if (ENERGY_UNIT_LABELS[tbody.id]) return nextEnergyIndex();
        let idx = tbody.children.length;
        while (tbody.querySelector(`[name*="[${idx}]"]`)) idx++;
        return idx;
    }

    function addRow(tbodyId) {
        const tbody = byId(tbodyId);
        const template = rowTemplates[tbodyId];
        if (!tbody || !template) return;
        const tr = document.createElement('tr');
        tr.innerHTML = template(nextRowIndex(tbody)).replace(/^<tr[^>]*>|<\/tr>$/g, '');
        tbody.appendChild(tr);
        recomputeAll();
    }

    ROOT.querySelectorAll('.wl-add-row').forEach(btn => {
        btn.addEventListener('click', () => addRow(btn.dataset.rowTarget));
    });

    /* Delegated remove-row handling */
    if (tabContent) {
        tabContent.addEventListener('click', e => {
            const removeBtn = e.target.closest('.wl-remove-row');
            if (removeBtn) {
                removeBtn.closest('tr')?.remove();
                recomputeAll();
            }
        });
    }

    /* ── Totals ── */
    function calculateStaffTotals() {
        let grandMale = 0, grandFemale = 0, grandTotal = 0;
        ROOT.querySelectorAll('.staff-row').forEach(row => {
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
        ROOT.querySelectorAll('.arms-command-row').forEach(row => {
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

    function calculateMissingArmsTotals() {
        let count = 0;
        ROOT.querySelectorAll('.missing-arms-row').forEach(row => {
            const type = row.querySelector('[name*="[type]"]')?.value?.trim() || '';
            const serial = row.querySelector('[name*="[serial]"]')?.value?.trim() || '';
            if (type !== '' || serial !== '') count++;
        });
        setText('missing-arms-grand-total', count);
    }

    function calculateAmmunitionTotals() {
        let roundsGrand = 0, sGrand = 0, uGrand = 0, usedGrand = 0, totGrand = 0;
        ROOT.querySelectorAll('.ammo-item-row').forEach(row => {
            const rounds = val(row.querySelector('[name*="[rounds]"]'));
            const s = val(row.querySelector('[name*="[serviceable]"]'));
            const u = val(row.querySelector('[name*="[unserviceable]"]'));
            const used = val(row.querySelector('[name*="[used]"]'));
            const total = s + u + used;
            row.querySelector('.ammo-tot-disp').value = total;
            roundsGrand += rounds; sGrand += s; uGrand += u; usedGrand += used; totGrand += total;
        });
        setText('ammo-grand-rounds', roundsGrand);
        setText('ammo-grand-serviceable', sGrand);
        setText('ammo-grand-unserviceable', uGrand);
        setText('ammo-grand-used', usedGrand);
        setText('ammo-grand-total', totGrand);
    }

    function calculateStoreAccessoryTotals() {
        let supGrand = 0, issGrand = 0, totGrand = 0;
        ROOT.querySelectorAll('.store-acc-row').forEach(row => {
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
        ROOT.querySelectorAll('.store-stat-row').forEach(row => {
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
        ROOT.querySelectorAll('.store-uni-row').forEach(row => {
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
        ROOT.querySelectorAll('.store-oil-row').forEach(row => {
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
        ROOT.querySelectorAll('.tailor-row').forEach(row => {
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
        setText('transport-fleet-grand-total', ROOT.querySelectorAll('.transport-fleet-row').length);
    }

    function calculateTransportProcurementTotals() {
        setText('transport-procurement-grand-total', ROOT.querySelectorAll('.transport-procurement-row').length);
    }

    function calculateProjectsTotals(rowClass, countId, avgId) {
        const rows = ROOT.querySelectorAll('.' + rowClass);
        let sum = 0;
        rows.forEach(r => { sum += val(r.querySelector('[name*="[completion]"]')); });
        const avg = rows.length > 0 ? Math.round(sum / rows.length) : 0;
        setText(countId, rows.length);
        setText(avgId, `Average: ${avg}%`);
    }

    function calculateNotableActivitiesTotals() {
        setText('notable-activities-grand-total', ROOT.querySelectorAll('.notable-activity-row').length);
    }

    function calculateMaintenanceTotals() {
        setText('maintenance-grand-total', ROOT.querySelectorAll('.maintenance-log-row').length);
    }

    function calculateEnergyTotals(unit) {
        setText(`energy-${unit}-grand-total`, ROOT.querySelectorAll(`.energy-${unit}-row`).length);
    }

    /* Master recompute */
    function recomputeAll() {
        calculateStaffTotals();
        calculateArmsTotals();
        calculateMissingArmsTotals();
        calculateAmmunitionTotals();
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
        form.addEventListener('input', () => recomputeAll());
        form.addEventListener('change', () => recomputeAll());
    }

    /* ── Preview ── */
    function previewSectionTitle(num, title) {
        return `<div class="hrm-preview-section-title">${num}. ${esc(title)}</div>`;
    }
    function previewSubTitle(label) {
        return `<div style="font-size:.84rem;font-weight:700;margin:10px 0 6px;">${esc(label)}</div>`;
    }
    function previewTable(rows, headers = ['Item', 'Value']) {
        const thead = headers.map(h => `<th style="padding:6px 8px;border:1px solid var(--gray-200);background:#f8fafc;">${esc(h)}</th>`).join('');
        const tbody = rows.length
            ? rows.map(r => `<tr>${r.map(c => `<td style="padding:6px 8px;border:1px solid var(--gray-200);">${c}</td>`).join('')}</tr>`).join('')
            : `<tr><td colspan="${headers.length}" style="padding:6px 8px;border:1px solid var(--gray-200);color:var(--gray-500);">No entries.</td></tr>`;
        return `<div style="overflow-x:auto;"><table class="hrm-preview-table" style="margin-bottom:12px;"><thead><tr>${thead}</tr></thead><tbody>${tbody}</tbody></table></div>`;
    }
    function getVal(name) { return ROOT.querySelector(`[name="${name}"]`)?.value || '—'; }
    function getText(id) { return byId(id)?.textContent || '0'; }

    /* Non-empty rows of a dynamic tbody as arrays of escaped values
       (hidden inputs and the remove-button cell are skipped). */
    function tableRows(tbodyId) {
        const rows = [];
        ROOT.querySelectorAll(`#${tbodyId} tr`).forEach(tr => {
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
        const container = byId('hrmPreviewBody');
        if (!container) return;
        let html = '';

        /* 1. Reporting Officer & Command */
        html += previewSectionTitle(1, 'Reporting Officer & Command');
        html += previewTable([
            ['Officer Service Number', esc(getVal('works[reporting_officer_nis]'))],
            ['Command / Formation', esc(getVal('works[command_name]'))],
            ['Rank', esc(getVal('works[rank]'))],
            ['Phone Number', esc(getVal('works[gsm_number]'))]
        ]);

        /* 2. Staff Strength by Cadre */
        const staffRows = [];
        ROOT.querySelectorAll('.staff-row').forEach(row => {
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

        /* 3. Arms/Armoury Returns */
        let rows = tableRows('arms-body');
        if (rows.length) rows.push(strongRow(['Total', getText('arms-grand-serviceable'), getText('arms-grand-unserviceable'), getText('arms-grand-total')]));
        html += previewSectionTitle(3, 'Arms/Armoury Returns');
        html += previewTable(rows, ['Types of Arms', 'Serviceable', 'Unserviceable', 'Total']);

        /* 4. Missing Arms/Armoury Returns */
        rows = tableRows('missing-arms-body');
        if (rows.length) rows.push(strongRow(['Total Missing Arms', getText('missing-arms-grand-total'), '', '']));
        html += previewSectionTitle(4, 'Missing Arms/Armoury Returns');
        html += previewTable(rows, ['Types of Arms', 'Reason for Missing', 'Serial No.']);

        /* 5. Ammunition */
        rows = tableRows('ammunition-body');
        if (rows.length) rows.push(strongRow(['Total', getText('ammo-grand-rounds'), getText('ammo-grand-serviceable'), getText('ammo-grand-unserviceable'), getText('ammo-grand-used'), getText('ammo-grand-total')]));
        html += previewSectionTitle(5, 'Ammunition');
        html += previewTable(rows, ['Types of Ammunition', 'Rounds', 'Serviceable', 'Unserviceable', 'Used', 'Total']);

        /* 6. Store & Warehouse */
        html += previewSectionTitle(6, 'Store & Warehouse');
        rows = tableRows('store-accessories-body');
        if (rows.length) rows.push(strongRow(['Total', '', getText('store-acc-grand-supplied'), getText('store-acc-grand-issued'), getText('store-acc-grand-total')]));
        html += previewSubTitle('a. Elect/Elect Equipments, Spares & Accessories');
        html += previewTable(rows, ['Items', 'Qty Supplied', 'Qty Issues', 'Total Bal']);
        rows = tableRows('store-stationery-body');
        if (rows.length) rows.push(strongRow(['Total', '', getText('store-stat-grand-supplied'), getText('store-stat-grand-issued'), getText('store-stat-grand-total')]));
        html += previewSubTitle('b. Stationary');
        html += previewTable(rows, ['Items', 'Qty Supplied', 'Qty Issues', 'Total Bal']);
        rows = tableRows('store-uniforms-body');
        if (rows.length) rows.push(strongRow(['Total', '', getText('store-uni-grand-supplied'), getText('store-uni-grand-issued'), getText('store-uni-grand-bal-cf'), getText('store-uni-grand-total')]));
        html += previewSubTitle('c. Uniform and Accessories');
        html += previewTable(rows, ['Items', 'New Qty Supplied', 'Qty Issues', 'Bal. C/F', 'Total']);
        rows = tableRows('store-oil-gas-body');
        if (rows.length) rows.push(strongRow(['Total', '', getText('store-oil-grand-supplied'), getText('store-oil-grand-issued'), getText('store-oil-grand-bal-cf'), getText('store-oil-grand-total')]));
        html += previewSubTitle('d. Oil and Gas');
        html += previewTable(rows, ['Items', 'New Qty Supplied', 'Qty Issues', 'Bal. C/F', 'Total']);
        const tailorRows = [];
        ROOT.querySelectorAll('.tailor-row').forEach(row => {
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

        /* 7. Transport */
        html += previewSectionTitle(7, 'Transport');
        rows = tableRows('transport-fleet-body');
        if (rows.length) rows.push(strongRow(['Total Vehicles in Fleet', '', '', '', getText('transport-fleet-grand-total')]));
        html += previewSubTitle('a. Vehicle Details');
        html += previewTable(rows, ['Make & Model', 'Chassis Number', 'Plate Number', 'VIN', 'Remarks']);
        rows = tableRows('transport-procurement-body');
        if (rows.length) rows.push(strongRow(['Total Vehicles Procured', '', '', '', getText('transport-procurement-grand-total')]));
        html += previewSubTitle('b. Procurement Log');
        html += previewTable(rows, ['File Number', 'Type of Vehicle', 'Chassis Number', 'Remark', 'Date of Procurement']);

        /* 8. Works & Projects */
        html += previewSectionTitle(8, 'Works & Projects');
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

        /* 9. Maintenance & Energy */
        html += previewSectionTitle(9, 'Maintenance & Energy');
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

        /* 10. General Report */
        html += previewSectionTitle(10, 'General Report');
        [['Challenges', 'works[general_report][challenges]', 'No challenges reported.'],
         ['Recommendations / Way Forward', 'works[general_report][recommendations]', 'No recommendations provided.']
        ].forEach(([label, name, emptyText]) => {
            html += `<div style="margin-top:12px;"><strong>${esc(label)}:</strong></div>`;
            const value = ROOT.querySelector(`[name="${name}"]`)?.value.trim() || '';
            html += value
                ? `<p style="font-size:.82rem;color:var(--gray-800);margin-top:4px;white-space:pre-wrap;">${esc(value)}</p>`
                : `<p style="font-size:.82rem;color:var(--gray-600);margin-top:4px;">${emptyText}</p>`;
        });
        html += `<div style="margin-top:12px;"><strong>Supporting Documents:</strong></div>`;
        const docInputs = Array.from(ROOT.querySelectorAll('#documents-body input[type="file"]'))
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
    window.buildDirectoratePreview = window.buildDirectoratePreview || buildPreview;

    /* Init */
    recomputeAll();
})();
</script>
