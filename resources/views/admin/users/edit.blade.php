@include('partials.header3')

<main class="redas-content" style="max-width:760px;margin:32px auto;padding:0 16px;">
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit User</h1>
            <p class="page-subtitle">Update account details, RBAC profile, status, and location scope.</p>
        </div>
        <a href="{{ route('admin.users') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to users
        </a>
    </div>

    @if(session('status'))
        <div class="alert alert-success" style="margin-bottom:16px;">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom:16px;">
            <ul style="margin:0;padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="redas-card">
        <div class="card-body" style="padding:24px;">
            <form method="POST" action="{{ URL::signedRoute('admin.users.update', ['userHash' => \App\Services\HashidService::encode($user->id)]) }}" class="needs-validation" novalidate>
                @csrf
                @method('PATCH')

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="auth-input" required>
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="auth-input" required>
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Service Number</label>
                    <input type="text" name="service_number" value="{{ old('service_number', $user->service_number) }}" class="auth-input" required>
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Access Category</label>
                    <select name="user_category" id="userCategory" class="auth-input" required>
                        <option value="" disabled>Select category</option>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" {{ old('user_category', $user->user_category) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Role</label>
                    <select name="role" id="userRole" class="auth-input" required>
                        <option value="" disabled>Select role</option>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}" {{ old('role', $user->role) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Primary Location Type</label>
                    <select name="primary_location_type" id="locationType" class="auth-input" required>
                        <option value="" disabled>Select location</option>
                        @foreach($locationTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('primary_location_type', $user->primary_location_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                @php
                    $formationCodes = array_merge(
                        array_column($zonalOffices, 'code'),
                        array_column($stateCommands, 'code'),
                        array_column($specialCommands, 'code')
                    );
                    $currentFormation = old('formation_code', in_array($user->primary_location_code, $formationCodes, true) ? $user->primary_location_code : '');
                    $currentScopeCode = old('primary_location_code', in_array($user->primary_location_code, $formationCodes, true) ? '' : $user->primary_location_code);
                    $currentCgisUnit = old('assigned_cgis_unit_code', $user->assigned_cgis_unit_code);
                @endphp

                <div class="auth-form-group" id="formationGroup" style="margin-bottom:18px;">
                    <label class="form-label-nis">Formation</label>
                    <select name="formation_code" class="auth-input" id="formationSelect">
                        <option value="" disabled {{ $currentFormation === '' ? 'selected' : '' }}>Select formation</option>
                    </select>
                    <small class="text-muted" id="formationHelp">Pick the state command, zone or special command this account belongs to.</small>
                </div>

                <div class="auth-form-group" id="locationCodeGroup" style="margin-bottom:18px;display:none;">
                    <label class="form-label-nis">Scope Code</label>
                    <input type="text" name="primary_location_code" id="primaryLocationCode" value="{{ $currentScopeCode }}" class="auth-input" placeholder="e.g. HRM, ACTU">
                    <small class="text-muted">Enter the directorate code or unit slug for this account.</small>
                </div>

                <div class="auth-form-group" id="cgisUnitGroup" style="margin-bottom:18px;display:none;">
                    <label class="form-label-nis">CGIS Unit</label>
                    <select name="assigned_cgis_unit_code" id="cgisUnit" class="auth-input">
                        <option value="" {{ $currentCgisUnit === null ? 'selected' : '' }}>None</option>
                        @foreach($cgisUnits as $value => $label)
                            <option value="{{ $value }}" {{ $currentCgisUnit === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Required for CGIS Unit User and CGIS Unit Desk Admin accounts.</small>
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;display:flex;align-items:center;gap:12px;">
                    <label class="switch">
                        <input type="checkbox" name="is_enabled" value="1" {{ old('is_enabled', $user->is_enabled) ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                    <span>Account enabled</span>
                </div>

                <div class="auth-form-group pw-wrap" style="margin-bottom:18px;">
                    <label class="form-label-nis">New Password</label>
                    <input type="password" id="password" name="password" class="auth-input" placeholder="Leave blank to keep current password">
                    <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                    <button type="button" class="pw-toggle" aria-label="Toggle password visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="auth-form-group pw-wrap" style="margin-bottom:24px;">
                    <label class="form-label-nis">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="auth-input">
                    <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                    <button type="button" class="pw-toggle" aria-label="Toggle password visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <button type="submit" class="btn-nis btn-primary-nis full-width">
                    Save changes
                </button>
            </form>
        </div>
    </div>
</main>

<script>
(function () {
    var categorySelect = document.getElementById('userCategory');
    var roleSelect = document.getElementById('userRole');
    var locationSelect = document.getElementById('locationType');
    var formationGroup = document.getElementById('formationGroup');
    var formationSelect = document.getElementById('formationSelect');
    var locationCodeGroup = document.getElementById('locationCodeGroup');
    var locationCodeInput = document.getElementById('primaryLocationCode');
    var cgisUnitGroup = document.getElementById('cgisUnitGroup');
    var cgisUnitSelect = document.getElementById('cgisUnit');

    var zonalOffices = @json($zonalOffices);
    var stateCommands = @json($stateCommands);
    var specialCommands = @json($specialCommands);
    var selectedFormation = @json($currentFormation);

    var categoryRoles = {
        state_user: ['officer'],
        desk_admin: ['state'],
        zonal_user: ['officer'],
        zonal_commander: ['zonal'],
        directorate_user: ['directorate'],
        directorate_admin: ['admin'],
        cgis_unit_user: ['unit_officer', 'officer'],
        cgis_desk_admin: ['unit_admin', 'admin'],
        hq_admin: ['admin', 'super_admin'],
        admin: ['admin'],
        super_admin: ['admin', 'super_admin'],
    };

    var categoryLocations = {
        state_user: 'state',
        desk_admin: 'state',
        zonal_user: 'zonal',
        zonal_commander: 'zonal',
        directorate_user: 'directorate',
        directorate_admin: 'directorate',
        cgis_unit_user: 'unit',
        cgis_desk_admin: 'unit',
        hq_admin: 'headquarters',
        admin: 'headquarters',
        super_admin: 'headquarters',
    };

    function buildOptions() {
        formationSelect.innerHTML = '<option value="" disabled>Select formation</option>';
        var category = categorySelect ? categorySelect.value : '';
        var location = locationSelect ? locationSelect.value : '';

        if (location === 'zonal' || category === 'zonal_user' || category === 'zonal_commander') {
            addGroup('Zonal Offices', zonalOffices);
        }

        if (location === 'state' || category === 'state_user' || category === 'desk_admin') {
            addGroup('State Commands', stateCommands);
            addGroup('Special Commands', specialCommands);
        }

        if (selectedFormation) {
            var option = formationSelect.querySelector('option[value="' + CSS.escape(selectedFormation) + '"]');
            if (option) {
                option.selected = true;
            }
        }
    }

    function addGroup(label, items) {
        if (!items.length) return;
        var optgroup = document.createElement('optgroup');
        optgroup.label = label;
        items.forEach(function (item) {
            var option = document.createElement('option');
            option.value = item.code;
            option.textContent = item.label;
            if (item.code === selectedFormation) option.selected = true;
            optgroup.appendChild(option);
        });
        formationSelect.appendChild(optgroup);
    }

    function filterRoles() {
        var category = categorySelect ? categorySelect.value : '';
        var allowed = categoryRoles[category] || [];
        var currentVisible = false;

        Array.from(roleSelect.options).forEach(function (option) {
            if (!option.value) return;
            var visible = allowed.indexOf(option.value) !== -1;
            option.style.display = visible ? '' : 'none';
            option.disabled = !visible;
            if (visible && option.selected) {
                currentVisible = true;
            }
        });

        if (!currentVisible && allowed.length) {
            roleSelect.value = allowed[0];
        }
    }

    function updateCgisUnit() {
        var category = categorySelect ? categorySelect.value : '';
        var needsUnit = category === 'cgis_unit_user' || category === 'cgis_desk_admin';

        cgisUnitGroup.style.display = needsUnit ? '' : 'none';
    }

    function updateLocationCodePlaceholder() {
        var category = categorySelect ? categorySelect.value : '';
        var location = locationSelect ? locationSelect.value : '';

        if (category === 'directorate_user' || category === 'directorate_admin') {
            locationCodeInput.placeholder = 'e.g. HRM, PRS, FIN';
        } else if (location === 'unit') {
            locationCodeInput.placeholder = 'e.g. actu, epms, servicom';
        } else {
            locationCodeInput.placeholder = 'e.g. HRM, ACTU';
        }
    }

    function updateForm() {
        var location = locationSelect ? locationSelect.value : '';
        var needsFormation = (location === 'state' || location === 'zonal');

        filterRoles();
        updateCgisUnit();
        updateLocationCodePlaceholder();

        if (needsFormation) {
            formationGroup.style.display = '';
            locationCodeGroup.style.display = 'none';
            buildOptions();
        } else {
            formationGroup.style.display = 'none';
            locationCodeGroup.style.display = '';
            formationSelect.innerHTML = '<option value="" disabled>Select formation</option>';
        }
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', function () {
            updateForm();
        });
    }
    if (locationSelect) locationSelect.addEventListener('change', updateForm);

    updateForm();
})();
</script>

@include('partials.footer')
