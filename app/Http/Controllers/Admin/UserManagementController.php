<?php

namespace App\Http\Controllers\Admin;

use App\Console\Commands\MfaResetCommand;
use App\Http\Controllers\Concerns\ResolvesHashedModels;
use App\Http\Controllers\Controller;
use App\Models\AuthToken;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\HashidService;
use App\Services\Messaging\ResendMailService;
use App\Services\NisFormationService;
use App\Services\PasswordRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserManagementController extends Controller
{
    use ResolvesHashedModels;

    private const ASSIGNABLE_CATEGORIES = [
        'hq_admin' => ['state_user', 'desk_admin', 'zonal_user', 'zonal_commander', 'directorate_user', 'directorate_admin', 'cgis_unit_user', 'cgis_desk_admin', 'hq_admin'],
        'super_admin' => ['state_user', 'desk_admin', 'zonal_user', 'zonal_commander', 'directorate_user', 'directorate_admin', 'cgis_unit_user', 'cgis_desk_admin', 'hq_admin', 'admin', 'super_admin'],
    ];

    private const CGIS_UNITS = [
        'actu' => 'Anti-Corruption and Transparency Unit',
        'epms' => 'Electronic Passport Management System',
        'hostmanship' => 'Hostmanship Unit',
        'pro-media' => 'Public Relations / Media Unit',
        'protocol' => 'Protocol Unit',
        'provost' => 'Provost Unit',
        'servicom' => 'SERVICOM Unit',
    ];

    /**
     * Canonical directorate slugs mapped back to the 3-letter codes stored
     * on user accounts.
     */
    private const DIRECTORATE_SLUGS = [
        'hrm' => 'HRM',
        'prs' => 'PRS',
        'finance' => 'FIN',
        'investigation' => 'INV',
        'passport' => 'PAS',
        'visa' => 'VIS',
        'migration' => 'MIG',
        'border' => 'BOR',
        'ict' => 'ICT',
        'works-logistics' => 'WKS',
    ];

    /**
     * Human-readable labels for every user category defined by the
     * application RBAC.
     */
    private function categoryLabels(): array
    {
        return [
            'state_user' => 'State User',
            'desk_admin' => 'State Desk Admin',
            'zonal_user' => 'Zonal User',
            'zonal_commander' => 'Zonal Commander',
            'directorate_user' => 'Directorate User',
            'directorate_admin' => 'Directorate Admin',
            'cgis_unit_user' => 'CGIS Unit User',
            'cgis_desk_admin' => 'CGIS Unit Desk Admin',
            'hq_admin' => 'HQ Admin',
            'admin' => 'National Administrator',
            'super_admin' => 'Super Admin',
        ];
    }

    /**
     * Canonical roles that may be assigned through the user-management UI.
     */
    private function roleLabels(): array
    {
        return [
            'officer' => 'Officer',
            'state' => 'State Admin',
            'zonal' => 'Zonal Commander',
            'directorate' => 'Directorate User',
            'unit_officer' => 'CGIS Unit Officer',
            'unit_admin' => 'CGIS Unit Desk Admin',
            'admin' => 'Administrator',
            'super_admin' => 'Super Admin',
        ];
    }

    private function assertMayAssignCategory(User $actor, string $targetCategory): void
    {
        $allowed = self::ASSIGNABLE_CATEGORIES[$actor->user_category] ?? [];

        abort_unless(
            in_array($targetCategory, $allowed, true),
            403,
            'You are not authorised to assign this account type.'
        );
    }

    private function formViewData(): array
    {
        return [
            'categories' => $this->categoryLabels(),
            'locationTypes' => [
                'state' => 'State',
                'zonal' => 'Zonal',
                'directorate' => 'Directorate',
                'unit' => 'CGIS Unit',
                'headquarters' => 'Headquarters',
            ],
            'roles' => $this->roleLabels(),
            'cgisUnits' => self::CGIS_UNITS,
            'zonalOffices' => NisFormationService::zonalOffices(),
            'stateCommands' => NisFormationService::stateCommands(),
            'specialCommands' => NisFormationService::specialCommands(),
            'formationCategories' => NisFormationService::formationCategories(),
        ];
    }

    private function userValidationRules(?User $user = null): array
    {
        $uniqueServiceNumber = $user === null
            ? 'unique:users'
            : 'unique:users,service_number,'.$user->id;

        $uniqueEmail = $user === null
            ? 'unique:users'
            : 'unique:users,email,'.$user->id;

        $roleOptions = implode(',', array_keys($this->roleLabels()));
        $categoryOptions = implode(',', array_keys($this->categoryLabels()));
        $locationOptions = implode(',', User::LOCATION_TYPES);
        $cgisUnitOptions = implode(',', array_keys(self::CGIS_UNITS));

        return [
            'name' => 'required|string|max:255',
            'service_number' => ['required', 'string', 'regex:/^NIS\/[A-Z]{2,3}\/\d{3,4}$/', $uniqueServiceNumber],
            'role' => 'required|in:'.$roleOptions,
            'user_category' => 'required|in:'.$categoryOptions,
            'primary_location_type' => 'required|in:'.$locationOptions,
            'formation_code' => 'nullable|string|max:50',
            'primary_location_code' => ['nullable', 'required_if:user_category,directorate_user,directorate_admin', 'string', 'max:50'],
            'assigned_cgis_unit_code' => ['nullable', 'required_if:user_category,cgis_unit_user,cgis_desk_admin', 'in:'.$cgisUnitOptions],
            'email' => ['required', 'string', 'email', 'max:255', $uniqueEmail],
            'password' => PasswordRule::rules($user, required: $user === null, confirmed: true),
            'is_enabled' => 'sometimes|boolean',
        ];
    }

    /**
     * Resolve the primary location code and geo-state from the JSON-driven
     * formation picker when one is provided.
     */
    private function resolveFormation(?string $formationCode, ?string $fallbackCode, string $locationType): array
    {
        if ($locationType === 'zonal') {
            $code = NisFormationService::normalizeZoneCode($formationCode)
                ?? NisFormationService::normalizeZoneCode($fallbackCode)
                ?? ($formationCode ?: $fallbackCode);

            return [$code, NisFormationService::geoStateForCode($code)];
        }

        if ($formationCode !== null && $formationCode !== '') {
            return [
                $formationCode,
                NisFormationService::geoStateForCode($formationCode),
            ];
        }

        if ($locationType === 'state') {
            return [$fallbackCode, NisFormationService::geoStateForCode($fallbackCode ?? '')];
        }

        return [$fallbackCode, null];
    }

    /**
     * Derive the operational-scope fields that match the access profile.
     */
    private function scopeFieldsForProfile(string $category, ?string $locationCode, ?string $geoState, ?string $assignedCgisUnit): array
    {
        $fields = [];

        switch ($category) {
            case 'state_user':
            case 'desk_admin':
                $stateCode = $geoState ?: $locationCode;
                $fields['assigned_state_code'] = $stateCode;

                if ($stateCode !== null && $stateCode !== '') {
                    $fields['assigned_zonal_command_code'] = NisFormationService::zoneForState($stateCode);
                }
                break;

            case 'zonal_user':
            case 'zonal_commander':
                if ($locationCode !== null && $locationCode !== '') {
                    $fields['assigned_zonal_command_code'] = NisFormationService::normalizeZoneCode($locationCode) ?: $locationCode;
                }
                break;

            case 'directorate_user':
            case 'directorate_admin':
                $fields['assigned_directorate_code'] = $this->normalizeDirectorateCode($locationCode);
                break;

            case 'cgis_unit_user':
            case 'cgis_desk_admin':
                $fields['assigned_cgis_unit_code'] = $assignedCgisUnit ?: $locationCode;
                break;
        }

        return $fields;
    }

    /**
     * Normalize a directorate slug or code to the uppercase code used by
     * the rest of the application.
     */
    private function normalizeDirectorateCode(?string $code): ?string
    {
        if ($code === null || $code === '') {
            return null;
        }

        $key = strtolower(trim($code));

        if (array_key_exists($key, self::DIRECTORATE_SLUGS)) {
            return self::DIRECTORATE_SLUGS[$key];
        }

        return strtoupper($code);
    }

    public function index(Request $request)
    {
        $search = $request->string('search')->trim()->toString();

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('service_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function create()
    {
        return view('admin.users.create', $this->formViewData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->userValidationRules());

        $this->assertMayAssignCategory($request->user(), $validated['user_category']);

        $normalized = $this->normalizeAccessProfile(
            $validated['user_category'],
            $validated['primary_location_type'],
            $validated['role']
        );

        if ($normalized === null) {
            throw ValidationException::withMessages([
                'user_category' => ['Invalid access profile combination.'],
            ]);
        }

        [$locationCode, $geoState] = $this->resolveFormation(
            $validated['formation_code'] ?? null,
            $validated['primary_location_code'] ?? null,
            $normalized['primary_location_type']
        );

        $scopeFields = $this->scopeFieldsForProfile(
            $normalized['user_category'],
            $locationCode,
            $geoState,
            $validated['assigned_cgis_unit_code'] ?? null
        );

        // Ensure unit/directorate accounts carry a usable primary location code.
        if ($normalized['primary_location_type'] === 'unit' && empty($locationCode) && ! empty($scopeFields['assigned_cgis_unit_code'])) {
            $locationCode = $scopeFields['assigned_cgis_unit_code'];
        }

        if ($normalized['primary_location_type'] === 'directorate' && empty($locationCode) && ! empty($scopeFields['assigned_directorate_code'])) {
            $locationCode = $scopeFields['assigned_directorate_code'];
        }

        if (in_array($normalized['primary_location_type'], ['unit', 'directorate'], true) && empty($locationCode)) {
            throw ValidationException::withMessages([
                $normalized['primary_location_type'] === 'unit' ? 'assigned_cgis_unit_code' : 'primary_location_code' => ['A scope code is required for this account type.'],
            ]);
        }

        $reviewerCategoriesRequiringScope = ['desk_admin', 'zonal_commander', 'directorate_admin', 'cgis_desk_admin'];
        if (in_array($normalized['user_category'], $reviewerCategoriesRequiringScope, true) && blank($locationCode)) {
            throw ValidationException::withMessages([
                'formation_code' => ['A formation/scope is required for reviewer accounts.'],
            ]);
        }

        $user = User::create([
            'name' => $validated['name'],
            'service_number' => strtoupper($validated['service_number']),
            'role' => $normalized['role'],
            'user_category' => $normalized['user_category'],
            'primary_location_type' => $normalized['primary_location_type'],
            'primary_location_code' => $locationCode,
            'geo_state' => $geoState,
            'access_level' => $normalized['access_level'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'must_change_password' => true,
            'is_enabled' => $request->boolean('is_enabled', true),
            ...$scopeFields,
        ]);

        $this->sendVerificationEmail($user);

        return redirect()->route('admin.users')
            ->with('status', 'User created successfully.');
    }

    public function edit(string $userHash)
    {
        $user = $this->resolveUser($userHash);
        return view('admin.users.edit', array_merge(
            $this->formViewData(),
            ['user' => $user]
        ));
    }

    public function update(Request $request, string $userHash): RedirectResponse
    {
        $user = $this->resolveUser($userHash);
        $validated = $request->validate($this->userValidationRules($user));

        $this->assertMayAssignCategory($request->user(), $validated['user_category']);

        $normalized = $this->normalizeAccessProfile(
            $validated['user_category'],
            $validated['primary_location_type'],
            $validated['role']
        );

        if ($normalized === null) {
            throw ValidationException::withMessages([
                'user_category' => ['Invalid access profile combination.'],
            ]);
        }

        [$locationCode, $geoState] = $this->resolveFormation(
            $validated['formation_code'] ?? null,
            $validated['primary_location_code'] ?? null,
            $normalized['primary_location_type']
        );

        $scopeFields = $this->scopeFieldsForProfile(
            $normalized['user_category'],
            $locationCode,
            $geoState,
            $validated['assigned_cgis_unit_code'] ?? null
        );

        if ($normalized['primary_location_type'] === 'unit' && empty($locationCode) && ! empty($scopeFields['assigned_cgis_unit_code'])) {
            $locationCode = $scopeFields['assigned_cgis_unit_code'];
        }

        if ($normalized['primary_location_type'] === 'directorate' && empty($locationCode) && ! empty($scopeFields['assigned_directorate_code'])) {
            $locationCode = $scopeFields['assigned_directorate_code'];
        }

        if (in_array($normalized['primary_location_type'], ['unit', 'directorate'], true) && empty($locationCode)) {
            throw ValidationException::withMessages([
                $normalized['primary_location_type'] === 'unit' ? 'assigned_cgis_unit_code' : 'primary_location_code' => ['A scope code is required for this account type.'],
            ]);
        }

        $reviewerCategoriesRequiringScope = ['desk_admin', 'zonal_commander', 'directorate_admin', 'cgis_desk_admin'];
        if (in_array($normalized['user_category'], $reviewerCategoriesRequiringScope, true) && blank($locationCode)) {
            throw ValidationException::withMessages([
                'formation_code' => ['A formation/scope is required for reviewer accounts.'],
            ]);
        }

        if ($request->user()->id === $user->id) {
            $requestedEnabled = $request->boolean('is_enabled', $user->is_enabled);

            if (
                $validated['user_category'] !== $user->user_category
                || $validated['role'] !== $user->role
                || $normalized['access_level'] !== $user->access_level
                || $requestedEnabled !== (bool) $user->is_enabled
                || ! empty($validated['password'])
            ) {
                abort(403, 'You cannot change your own account type, role, access level, enabled status, or password from this screen.');
            }
        }

        $updateData = [
            'name' => $validated['name'],
            'service_number' => strtoupper($validated['service_number']),
            'role' => $normalized['role'],
            'user_category' => $normalized['user_category'],
            'primary_location_type' => $normalized['primary_location_type'],
            'primary_location_code' => $locationCode,
            'geo_state' => $geoState,
            'access_level' => $normalized['access_level'],
            'email' => $validated['email'],
            'is_enabled' => $request->boolean('is_enabled', true),
            ...$scopeFields,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
            // An admin-assigned password must be changed by the user on their next login.
            $updateData['must_change_password'] = true;
        }

        $oldCategory = $user->user_category;
        $oldRole = $user->role;
        $oldAccessLevel = $user->access_level;

        $user->update($updateData);

        if (
            $oldCategory !== $user->user_category
            || $oldRole !== $user->role
            || $oldAccessLevel !== $user->access_level
        ) {
            AuditLogger::logCrud(
                $request->user(),
                'user.privilege_assigned',
                'user',
                $user->id,
                [
                    'old_category' => $oldCategory,
                    'new_category' => $user->user_category,
                    'old_role' => $oldRole,
                    'new_role' => $user->role,
                    'old_access_level' => $oldAccessLevel,
                    'new_access_level' => $user->access_level,
                ]
            );
        }

        return redirect()->route('admin.users.edit', ['userHash' => HashidService::encode($user->id)])
            ->with('status', 'User updated successfully.');
    }

    public function toggleStatus(string $userHash): RedirectResponse
    {
        $user = $this->resolveUser($userHash);
        $actor = request()->user();

        abort_if(
            $actor === null || $actor->id === $user->id || $user->user_category === 'super_admin',
            403,
            'You cannot disable or downgrade this account.'
        );

        $user->update(['is_enabled' => ! $user->is_enabled]);

        return redirect()->route('admin.users')
            ->with('status', sprintf('User %s has been %s.', $user->name, $user->is_enabled ? 'enabled' : 'disabled'));
    }

    private function normalizeAccessProfile(string $category, string $locationType, string $role): ?array
    {
        $map = [
            'state_user' => [
                'roles' => ['officer', 'user'],
                'locations' => ['state'],
                'level' => 0,
                'canonical_role' => 'officer',
                'canonical_location' => 'state',
            ],
            'desk_admin' => [
                'roles' => ['state'],
                'locations' => ['state'],
                'level' => 1,
                'canonical_role' => 'state',
                'canonical_location' => 'state',
            ],
            'zonal_user' => [
                'roles' => ['officer', 'user'],
                'locations' => ['zonal', 'zone'],
                'level' => 0,
                'canonical_role' => 'officer',
                'canonical_location' => 'zonal',
            ],
            'zonal_commander' => [
                'roles' => ['zonal'],
                'locations' => ['zonal', 'zone'],
                'level' => 4,
                'canonical_role' => 'zonal',
                'canonical_location' => 'zonal',
            ],
            'directorate_user' => [
                'roles' => ['directorate'],
                'locations' => ['directorate'],
                'level' => 0,
                'canonical_role' => 'directorate',
                'canonical_location' => 'directorate',
            ],
            'directorate_admin' => [
                'roles' => ['admin'],
                'locations' => ['directorate'],
                'level' => 3,
                'canonical_role' => 'admin',
                'canonical_location' => 'directorate',
            ],
            'cgis_unit_user' => [
                'roles' => ['unit_officer', 'officer'],
                'locations' => ['unit'],
                'level' => 0,
                'canonical_role' => 'unit_officer',
                'canonical_location' => 'unit',
            ],
            'cgis_desk_admin' => [
                'roles' => ['unit_admin', 'admin'],
                'locations' => ['unit'],
                'level' => 2,
                'canonical_role' => 'unit_admin',
                'canonical_location' => 'unit',
            ],
            'hq_admin' => [
                'roles' => ['admin', 'hq_admin'],
                'locations' => ['headquarters'],
                'level' => 5,
                'canonical_role' => 'admin',
                'canonical_location' => 'headquarters',
            ],
            'admin' => [
                'roles' => ['admin'],
                'locations' => ['headquarters'],
                'level' => 5,
                'canonical_role' => 'admin',
                'canonical_location' => 'headquarters',
            ],
            'super_admin' => [
                'roles' => ['admin', 'super_admin'],
                'locations' => ['headquarters'],
                'level' => 6,
                'canonical_role' => 'admin',
                'canonical_location' => 'headquarters',
            ],
        ];

        if (! array_key_exists($category, $map)) {
            return null;
        }

        $profile = $map[$category];

        if (! in_array($role, $profile['roles'], true)) {
            return null;
        }

        if (! in_array($locationType, $profile['locations'], true)) {
            return null;
        }

        return [
            'role' => $profile['canonical_role'],
            'user_category' => $category,
            'primary_location_type' => $profile['canonical_location'],
            'access_level' => $profile['level'],
        ];
    }

    /**
     * Reset MFA enrolment for the given user.
     */
    public function resetMfa(Request $request, string $userHash): RedirectResponse
    {
        $target = $this->resolveUser($userHash);
        $actor = $request->user();

        $this->assertMayManageCategory($actor, $target->user_category);

        $exitCode = Artisan::call(MfaResetCommand::class, [
            'service_number' => $target->service_number,
        ]);

        if ($exitCode !== 0) {
            return redirect()->route('admin.users')
                ->with('error', 'Unable to reset MFA for this user.');
        }

        return redirect()->route('admin.users')
            ->with('status', sprintf('MFA enrolment has been reset for %s.', $target->name));
    }

    /**
     * Categories each acting category is permitted to manage (create, update,
     * or reset MFA for). Mirrors the assignment-authority rule from the
     * security audit so an HQ admin cannot escalate to or manage Super Admins.
     */
    private const MANAGEABLE_CATEGORIES = [
        'hq_admin' => ['state_user', 'desk_admin', 'zonal_user', 'zonal_commander',
            'directorate_user', 'directorate_admin',
            'cgis_unit_user', 'cgis_desk_admin', 'hq_admin'],
        'super_admin' => ['state_user', 'desk_admin', 'zonal_user', 'zonal_commander',
            'directorate_user', 'directorate_admin',
            'cgis_unit_user', 'cgis_desk_admin', 'hq_admin', 'admin', 'super_admin'],
    ];

    private function assertMayManageCategory(?User $actor, string $targetCategory): void
    {
        if (! $actor instanceof User) {
            abort(403, 'You are not authorised to perform this action.');
        }

        $allowed = self::MANAGEABLE_CATEGORIES[$actor->user_category] ?? [];

        abort_unless(
            in_array($targetCategory, $allowed, true),
            403,
            'You are not authorised to manage this account type.'
        );
    }

    private function sendVerificationEmail(User $user): void
    {
        $ttlSeconds = config('auth.magic_link_ttl');
        $rawToken = Str::random(64);
        $tokenHash = hash('sha256', $rawToken);

        AuthToken::create([
            'user_id' => $user->id,
            'type' => 'email_verify',
            'token_hash' => $tokenHash,
            'expires_at' => now()->addSeconds($ttlSeconds),
            'used_at' => null,
        ]);

        $frontendUrl = config('app.url');
        $magicLink = rtrim($frontendUrl, '/').'/verify-email/'.$rawToken;

        try {
            $mailer = new ResendMailService;
            $mailer->sendFromTemplate(
                'verify_email_magic_link',
                [
                    'name' => $user->name,
                    'magic_link' => $magicLink,
                    'expires_in_minutes' => (int) ceil($ttlSeconds / 60),
                ],
                $user->email,
                $user->name
            );
        } catch (\Throwable $e) {
            Log::error('Admin user creation email delivery failed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
