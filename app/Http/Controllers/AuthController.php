<?php

namespace App\Http\Controllers;

use App\Models\AuthToken;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\GeolocationService;
use App\Services\Messaging\ResendMailService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;

    private const LOCKOUT_SECONDS = 60;

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $role = (string) $request->input('role', '');

        $request->merge([
            'service_number' => strtoupper((string) $request->input('service_number')),
            'primary_location_code' => strtoupper((string) $request->input('primary_location_code')),
            'user_category' => $request->filled('user_category')
                ? (string) $request->input('user_category')
                : $this->inferUserCategoryFromRole($role),
            'primary_location_type' => $request->filled('primary_location_type')
                ? (string) $request->input('primary_location_type')
                : $this->inferLocationTypeFromRole($role),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_number' => ['required', 'string', 'regex:/^NIS\/[A-Z]{3}\/[0-9]{4}$/', 'unique:users'],
            'role' => 'required|in:admin,zonal,state,officer,directorate',
            'user_category' => 'required|in:state_user,desk_admin,directorate_user,directorate_admin,zonal_commander,admin,super_admin',
            'primary_location_type' => 'required|in:state,directorate,zonal,headquarters',
            'primary_location_code' => 'nullable|string|max:50',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'accepted',
        ], [
            'service_number.regex' => 'Service number must be in the format NIS/XX/1234.',
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
            'service_number' => $validated['service_number'],
            'role' => $normalized['role'],
            'user_category' => $normalized['user_category'],
            'primary_location_type' => $normalized['primary_location_type'],
            'primary_location_code' => $validated['primary_location_code'] ?: null,
            'access_level' => $normalized['access_level'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            // email_verified_at intentionally left null
        ]);

        // Send email verification magic link
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

            return redirect()
                ->route('login')
                ->with('status', 'Registration successful. Please verify your email using the link sent to your inbox.');
        } catch (\Throwable $e) {
            Log::error('Registration email delivery failed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'template' => 'verify_email_magic_link',
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('login')
                ->with('status', 'Registration successful. We could not send your verification email right now. Please contact support or try again later.');
        }
    }

    /**
     * Login user.
     * Supports login by email OR service_number
     */
    public function login(Request $request)
    {
        $allowedLoginRoles = array_values(array_unique(array_merge(User::ROLE_TYPES, ['super_admin'])));

        $request->validate([
            'login' => 'required|string|max:255',
            'password' => 'required|string',
            'role' => 'required|in:'.implode(',', $allowedLoginRoles),
        ]);

        $loginInput = trim((string) $request->input('login'));
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'service_number';
        $normalizedLogin = $field === 'email' ? Str::lower($loginInput) : Str::upper($loginInput);
        $throttleKey = $this->throttleKey($normalizedLogin, $request);

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_LOGIN_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'login' => ["Too many login attempts. Try again in {$seconds} seconds."],
            ]);
        }

        $query = User::query();
        if ($field === 'email') {
            $query->whereRaw('LOWER(email) = ?', [Str::lower($loginInput)]);
        } else {
            $query->where('service_number', $normalizedLogin);
        }

        $user = $query->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password) || ! $this->userRoleMatches($user, (string) $request->input('role'))) {
            RateLimiter::hit($throttleKey, self::LOCKOUT_SECONDS);

            AuditLogger::logAuthAttempt(
                user: $user,
                status: 'failure',
                details: [
                    'reason' => 'invalid_credentials_or_role',
                    'login_field' => $field,
                    'requested_role' => $request->input('role'),
                ],
                ip: $request->ip() ?? '0.0.0.0',
                location: null,
            );

            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        // ABAC geolocation check
        $ip = $request->ip() ?? '0.0.0.0';
        $location = GeolocationService::resolve($ip);
        $abacResult = $this->checkAbacPolicy($user, $location);

        if (! $abacResult['allowed']) {
            RateLimiter::hit($throttleKey, self::LOCKOUT_SECONDS);

            AuditLogger::logAuthBlocked(
                user: $user,
                reason: $abacResult['reason'],
                details: [
                    'required_state' => $user->requiredGeoState(),
                    'detected_state' => $location['state_code'] ?? 'Unknown',
                    'detected_country' => $location['country'] ?? 'Unknown',
                    'detected_state_name' => $location['state'] ?? 'Unknown',
                ],
                ip: $ip,
                location: $location['state'] ?? 'Unknown',
            );

            throw ValidationException::withMessages([
                'login' => [$abacResult['message']],
            ]);
        }

        RateLimiter::clear($throttleKey);

        // Stage 2: require MFA before the session is fully authenticated.
        $request->session()->put('mfa.pending_user_id', $user->id);
        $request->session()->put('mfa.location', $location);
        $request->session()->put('mfa.remember', $request->boolean('remember'));
        $request->session()->put('mfa.redirect_url', $this->getRedirectUrl($user));

        if ($user->isMfaEnabled()) {
            return redirect()->route('mfa.challenge');
        }

        return redirect()->route('mfa.setup');
    }

    /**
     * Complete the login flow after MFA has been verified.
     *
     * @param array{country:string,state:string,state_code:string,provider:string} $location
     */
    public function completeLogin(Request $request, User $user, array $location, bool $remember): RedirectResponse
    {
        $redirectUrl = $request->session()->get('mfa.redirect_url', $this->getRedirectUrl($user));

        Auth::login($user, $remember);

        $request->session()->forget([
            'mfa.pending_user_id',
            'mfa.location',
            'mfa.remember',
            'mfa.redirect_url',
            'mfa.setup_verified',
        ]);

        $request->session()->put('abac.location', $location);
        $request->session()->put('abac.resolved_at', now());

        AuditLogger::logAuthAttempt(
            user: $user,
            status: 'success',
            details: [
                'detected_state' => $location['state_code'] ?? 'Unknown',
                'detected_country' => $location['country'] ?? 'Unknown',
            ],
            ip: $request->ip() ?? '0.0.0.0',
            location: $location['state'] ?? 'Unknown',
        );

        return redirect($redirectUrl);
    }

    private function userRoleMatches(User $user, string $requestedRole): bool
    {
        if ($user->role === $requestedRole) {
            return true;
        }

        if ($requestedRole === 'super_admin') {
            return $user->user_category === 'super_admin' && $user->role === 'admin';
        }

        if ($requestedRole === 'user' && $user->role === 'officer') {
            return true;
        }

        if ($requestedRole === 'state' && $user->role === 'admin') {
            return true;
        }

        if ($requestedRole === 'zonal' && $user->role === 'admin') {
            return true;
        }

        if ($requestedRole === 'directorate' && $user->role === 'directorate') {
            return true;
        }

        return false;
    }

    /**
     * Check ABAC geolocation policy for a user attempting to log in.
     *
     * @return array{allowed:bool,reason:string,message:string}
     */
    private function checkAbacPolicy(User $user, array $location): array
    {
        if (! SettingService::getBool('abac_enabled', true)) {
            return ['allowed' => true, 'reason' => '', 'message' => ''];
        }

        if (! SettingService::getBool('abac_location_enforcement', true)) {
            return ['allowed' => true, 'reason' => '', 'message' => ''];
        }

        if (SettingService::getBool('abac_hq_bypass', false) && $user->isHeadquartersUser()) {
            return ['allowed' => true, 'reason' => '', 'message' => ''];
        }

        $requiredState = $user->requiredGeoState();

        if ($requiredState === null) {
            return ['allowed' => true, 'reason' => '', 'message' => ''];
        }

        $allowedCountries = $this->allowedCountries();
        $detectedCountry = $location['country'] ?? 'Unknown';

        if (! in_array($detectedCountry, $allowedCountries, true)) {
            return [
                'allowed' => false,
                'reason' => 'Login attempt from outside allowed country.',
                'message' => 'Access denied: login is not permitted from your current country.',
            ];
        }

        $detectedState = strtoupper($location['state_code'] ?? '');

        if ($detectedState !== $requiredState) {
            return [
                'allowed' => false,
                'reason' => 'Login attempt from outside required state.',
                'message' => 'Access denied: your current location is not authorized for this account.',
            ];
        }

        return ['allowed' => true, 'reason' => '', 'message' => ''];
    }

    /**
     * Get the list of allowed countries from settings.
     *
     * @return list<string>
     */
    private function allowedCountries(): array
    {
        $value = SettingService::get('abac_allowed_countries', 'Nigeria');

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            return array_values(array_filter(array_map('trim', explode(',', $value))));
        }

        return ['Nigeria'];
    }

    /**
     * Get redirect URL based on user role
     */
    private function getRedirectUrl(User $user): string
    {
        return match ($user->user_category) {
            'super_admin' => '/superadmin/dashboard',
            'admin' => '/admin/dashboard',
            'zonal_commander' => '/dashboard/zonal',
            'desk_admin' => '/dashboard/state',
            'directorate_admin',
            'directorate_user' => '/user/directorate',
            'state_user' => '/user/dashboard',
            default => '/home',
        };
    }

    /**
     * Validate and normalize incoming access profile into canonical values.
     *
     * @return array{role:string,user_category:string,primary_location_type:string,access_level:int}|null
     */
    protected function normalizeAccessProfile(string $category, string $locationType, string $role): ?array
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
                'level' => 2,
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

    private function inferUserCategoryFromRole(string $role): string
    {
        return match ($role) {
            'officer' => 'state_user',
            'directorate' => 'directorate_user',
            'state' => 'desk_admin',
            'zonal' => 'zonal_commander',
            'admin' => 'admin',
            'super_admin', 'superAdmin' => 'super_admin',
            default => 'state_user',
        };
    }

    private function inferLocationTypeFromRole(string $role): string
    {
        return match ($role) {
            'directorate' => 'directorate',
            'state' => 'state',
            'zonal' => 'zonal',
            'admin', 'super_admin', 'superAdmin' => 'headquarters',
            default => 'state',
        };
    }

    /**
     * Build a unique throttle key for login attempts.
     */
    private function throttleKey(string $login, Request $request): string
    {
        return Str::lower($login).'|'.$request->ip();
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $ip = $request->ip() ?? '0.0.0.0';

        if ($user instanceof User) {
            AuditLogger::logLogout($user, $ip);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Logged out successfully.');
    }
}
