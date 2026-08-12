<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthToken;
use App\Models\User;
use App\Services\Messaging\ResendMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create', [
            'categories' => [
                'state_user' => 'State User',
                'desk_admin' => 'State Desk Admin',
                'directorate_user' => 'Directorate User',
                'directorate_admin' => 'Directorate Admin',
                'zonal_commander' => 'Zonal Commander',
                'admin' => 'National Administrator',
                'super_admin' => 'Super Admin',
            ],
            'locationTypes' => [
                'state' => 'State',
                'directorate' => 'Directorate',
                'zonal' => 'Zonal',
                'headquarters' => 'Headquarters',
            ],
            'roles' => [
                'officer' => 'Officer',
                'state' => 'State Supervisor',
                'directorate' => 'Directorate User',
                'zonal' => 'Zonal Commander',
                'admin' => 'Administrator',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_number' => ['required', 'string', 'regex:/^NIS\/[A-Z]{3}\/\d{4}$/', 'unique:users'],
            'role' => 'required|in:admin,zonal,state,officer,directorate',
            'user_category' => 'required|in:state_user,desk_admin,directorate_user,directorate_admin,zonal_commander,admin,super_admin',
            'primary_location_type' => 'required|in:state,directorate,zonal,headquarters',
            'primary_location_code' => 'nullable|string|max:50',
            'geo_state' => 'nullable|string|max:10',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_enabled' => 'sometimes|boolean',
        ]);

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

        $user = User::create([
            'name' => $validated['name'],
            'service_number' => strtoupper($validated['service_number']),
            'role' => $normalized['role'],
            'user_category' => $normalized['user_category'],
            'primary_location_type' => $normalized['primary_location_type'],
            'primary_location_code' => $validated['primary_location_code'] ?: null,
            'geo_state' => $validated['geo_state'] ?: null,
            'access_level' => $normalized['access_level'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_enabled' => $request->boolean('is_enabled', true),
        ]);

        $this->sendVerificationEmail($user);

        return redirect()->route('admin.users')
            ->with('status', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user,
            'categories' => [
                'state_user' => 'State User',
                'desk_admin' => 'State Desk Admin',
                'directorate_user' => 'Directorate User',
                'directorate_admin' => 'Directorate Admin',
                'zonal_commander' => 'Zonal Commander',
                'admin' => 'National Administrator',
                'super_admin' => 'Super Admin',
            ],
            'locationTypes' => [
                'state' => 'State',
                'directorate' => 'Directorate',
                'zonal' => 'Zonal',
                'headquarters' => 'Headquarters',
            ],
            'roles' => [
                'officer' => 'Officer',
                'state' => 'State Supervisor',
                'directorate' => 'Directorate User',
                'zonal' => 'Zonal Commander',
                'admin' => 'Administrator',
            ],
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_number' => ['required', 'string', 'regex:/^NIS\/[A-Z]{3}\/\d{4}$/', 'unique:users,service_number,'.$user->id],
            'role' => 'required|in:admin,zonal,state,officer,directorate',
            'user_category' => 'required|in:state_user,desk_admin,directorate_user,directorate_admin,zonal_commander,admin,super_admin',
            'primary_location_type' => 'required|in:state,directorate,zonal,headquarters',
            'primary_location_code' => 'nullable|string|max:50',
            'geo_state' => 'nullable|string|max:10',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'is_enabled' => 'sometimes|boolean',
        ]);

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

        $user->update([
            'name' => $validated['name'],
            'service_number' => strtoupper($validated['service_number']),
            'role' => $normalized['role'],
            'user_category' => $normalized['user_category'],
            'primary_location_type' => $normalized['primary_location_type'],
            'primary_location_code' => $validated['primary_location_code'] ?: null,
            'geo_state' => $validated['geo_state'] ?: null,
            'access_level' => $normalized['access_level'],
            'email' => $validated['email'],
            'is_enabled' => $request->boolean('is_enabled', true),
            'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
        ]);

        return redirect()->route('admin.users.edit', $user)
            ->with('status', 'User updated successfully.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
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
            'zonal_commander' => [
                'role' => ['zonal'],
                'location' => 'zonal',
                'level' => 4,
                'canonical_role' => 'zonal',
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

        $frontendUrl = (string) env('APP_URL', 'http://localhost');
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
