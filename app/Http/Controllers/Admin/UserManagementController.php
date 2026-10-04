<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ResolvesHashedModels;
use App\Http\Controllers\Controller;
use App\Models\AuthToken;
use App\Models\User;
use App\Services\HashidService;
use App\Services\Messaging\ResendMailService;
use App\Services\NisFormationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserManagementController extends Controller
{
    use ResolvesHashedModels;
    private const CGIS_UNITS = [
        'actu' => 'Anti-Corruption and Transparency Unit (ACTU)',
        'epms' => 'Electronic Passport Management System (EPMS)',
        'hostmanship' => 'Hostmanship Unit',
        'pro-media' => 'Public Relations / Media Unit',
        'protocol' => 'Protocol Unit',
        'provost' => 'Provost Unit',
        'servicom' => 'SERVICOM Unit',
    ];

    private function formViewData(): array
    {
        return [
            'categories' => [
                'state_user' => 'State User',
                'desk_admin' => 'State Desk Admin',
                'directorate_user' => 'Directorate User',
                'directorate_admin' => 'Directorate Admin',
                'zonal_user' => 'Zonal User',
                'zonal_commander' => 'Zonal Commander',
                'cgis_unit_user' => 'CGIS Unit User',
                'cgis_desk_admin' => 'CGIS Unit Desk Admin',
                'hq_admin' => 'HQ Admin (Approver)',
                'admin' => 'National Administrator',
                'super_admin' => 'Super Admin',
            ],
            'locationTypes' => [
                'state' => 'State',
                'directorate' => 'Directorate',
                'zonal' => 'Zonal',
                'unit' => 'CGIS Unit',
                'headquarters' => 'Headquarters',
            ],
            'roles' => [
                'officer' => 'Officer',
                'state' => 'State Supervisor',
                'directorate' => 'Directorate User',
                'unit_officer' => 'CGIS Unit Officer',
                'unit_admin' => 'CGIS Unit Desk Admin',
                'zonal' => 'Zonal Commander',
                'admin' => 'Administrator',
            ],
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

        return [
            'name' => 'required|string|max:255',
            'service_number' => ['required', 'string', 'regex:/^NIS\/[A-Z]{3}\/\d{4}$/', $uniqueServiceNumber],
            'role' => 'required|in:admin,zonal,state,officer,directorate,unit_officer,unit_admin',
            'user_category' => 'required|in:state_user,desk_admin,directorate_user,directorate_admin,zonal_user,zonal_commander,cgis_unit_user,cgis_desk_admin,hq_admin,admin,super_admin',
            'primary_location_type' => 'required|in:state,directorate,zonal,unit,headquarters',
            'formation_code' => 'nullable|string|max:50',
            'primary_location_code' => 'nullable|string|max:50',
            'assigned_cgis_unit_code' => 'nullable|in:'.implode(',', array_keys(self::CGIS_UNITS)),
            'geo_state' => 'nullable|string|max:10',
            'email' => ['required', 'string', 'email', 'max:255', $uniqueEmail],
            'password' => $user === null ? 'required|string|min:8|confirmed' : 'nullable|string|min:8|confirmed',
            'is_enabled' => 'sometimes|boolean',
        ];
    }

    /**
     * Resolve the primary location code and geo-state from the JSON-driven
     * formation picker when one is provided.
     */
    private function resolveFormation(?string $formationCode, ?string $fallbackCode, string $locationType): array
    {
        if ($formationCode !== null && $formationCode !== '') {
            return [
                $formationCode,
                NisFormationService::geoStateForCode($formationCode),
            ];
        }

        if (in_array($locationType, ['state', 'zonal'], true)) {
            return [$fallbackCode, NisFormationService::geoStateForCode($fallbackCode ?? '')];
        }

        return [$fallbackCode, null];
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

        $user = User::create([
            'name' => $validated['name'],
            'service_number' => strtoupper($validated['service_number']),
            'role' => $normalized['role'],
            'user_category' => $normalized['user_category'],
            'primary_location_type' => $normalized['primary_location_type'],
            'primary_location_code' => $locationCode,
            'assigned_cgis_unit_code' => $validated['assigned_cgis_unit_code'] ?? null,
            'geo_state' => $geoState,
            'access_level' => $normalized['access_level'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'must_change_password' => true,
            'is_enabled' => $request->boolean('is_enabled', true),
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

        $user->update([
            'name' => $validated['name'],
            'service_number' => strtoupper($validated['service_number']),
            'role' => $normalized['role'],
            'user_category' => $normalized['user_category'],
            'primary_location_type' => $normalized['primary_location_type'],
            'primary_location_code' => $locationCode,
            'assigned_cgis_unit_code' => $validated['assigned_cgis_unit_code'] ?? null,
            'geo_state' => $geoState,
            'access_level' => $normalized['access_level'],
            'email' => $validated['email'],
            'is_enabled' => $request->boolean('is_enabled', true),
            'password' => !empty($validated['password']) ? Hash::make($validated['password']) : $user->password,
            // An admin-assigned password must be changed by the user on their next login.
            'must_change_password' => !empty($validated['password']) ? true : $user->must_change_password,
        ]);

        return redirect()->route('admin.users.edit', ['userHash' => HashidService::encode($user->id)])
            ->with('status', 'User updated successfully.');
    }

    public function toggleStatus(string $userHash): RedirectResponse
    {
        $user = $this->resolveUser($userHash);
        $user->update(['is_enabled' => ! $user->is_enabled]);

        return redirect()->route('admin.users')
            ->with('status', sprintf('User %s has been %s.', $user->name, $user->is_enabled ? 'enabled' : 'disabled'));
    }

    private function normalizeAccessProfile(string $category, string $locationType, string $role): ?array
    {
        $map = [
            'state_user' => [
                'role' => ['officer'],
                'location' => 'state',
                'level' => 0,
                'canonical_role' => 'officer',
            ],
            'desk_admin' => [
                'role' => ['state'],
                'location' => 'state',
                'level' => 1,
                'canonical_role' => 'state',
            ],
            'directorate_user' => [
                'role' => ['directorate'],
                'location' => 'directorate',
                'level' => 0,
                'canonical_role' => 'directorate',
            ],
            'directorate_admin' => [
                'role' => ['admin'],
                'location' => 'directorate',
                'level' => 3,
                'canonical_role' => 'admin',
            ],
            'zonal_user' => [
                'role' => ['officer'],
                'location' => 'zonal',
                'level' => 0,
                'canonical_role' => 'officer',
            ],
            'zonal_commander' => [
                'role' => ['zonal'],
                'location' => 'zonal',
                'level' => 4,
                'canonical_role' => 'zonal',
            ],
            'cgis_unit_user' => [
                'role' => ['unit_officer'],
                'location' => 'unit',
                'level' => 0,
                'canonical_role' => 'unit_officer',
            ],
            'cgis_desk_admin' => [
                'role' => ['unit_admin'],
                'location' => 'unit',
                'level' => 2,
                'canonical_role' => 'unit_admin',
            ],
            'hq_admin' => [
                'role' => ['admin'],
                'location' => 'headquarters',
                'level' => 5,
                'canonical_role' => 'admin',
            ],
            'admin' => [
                'role' => ['admin'],
                'location' => 'headquarters',
                'level' => 5,
                'canonical_role' => 'admin',
            ],
            'super_admin' => [
                'role' => ['admin'],
                'location' => 'headquarters',
                'level' => 6,
                'canonical_role' => 'admin',
            ],
        ];

        if (! array_key_exists($category, $map)) {
            return null;
        }

        $profile = $map[$category];

        if (! in_array($role, $profile['role'], true)) {
            return null;
        }

        if ($locationType !== $profile['location']) {
            return null;
        }

        return [
            'role' => $profile['canonical_role'],
            'user_category' => $category,
            'primary_location_type' => $locationType,
            'access_level' => $profile['level'],
        ];
    }

    private function sendVerificationEmail(User $user): void
    {
        $ttlSeconds = (int) env('AUTH_TOKEN_TTL_SECONDS', 3600);
        $rawToken = Str::random(64);
        $tokenHash = hash('sha256', $rawToken);

        AuthToken::create([
            'user_id' => $user->id,
            'type' => 'email_verify',
            'token_hash' => $tokenHash,
            'expires_at' => now()->addSeconds($ttlSeconds),
            'used_at' => null,
        ]);

        $frontendUrl = (string) env('APP_URL', 'https://nis-redas.laravel.cloud');
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
