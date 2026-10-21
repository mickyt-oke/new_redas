<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\SettingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidateSessionFingerprint
{
    public const SESSION_KEY = 'session.fingerprint';

    /**
     * Routes that are exempt from fingerprint validation.
     */
    private const EXEMPT_ROUTE_NAMES = [
        'login',
        'login.submit',
        'logout',
        'register',
        'password.forgot.form',
        'password.forgot',
        'password.reset.form',
        'password.reset',
        'verify.email',
        'verify.email.resend',
        'mfa.setup',
        'mfa.setup.verify',
        'mfa.challenge',
        'mfa.challenge.verify',
        'mfa.recovery',
        'mfa.recovery.verify',
        'mfa.complete',
        'mfa.cancel',
        'home',
        'terms',
        'privacy',
        'directory',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->shouldValidate($request)) {
            return $next($request);
        }

        $current = $this->fingerprint($request);
        $expected = $request->session()->get(self::SESSION_KEY);

        if ($expected === null) {
            // First protected request after login without a stored fingerprint.
            // Store the current one rather than forcing re-auth.
            $request->session()->put(self::SESSION_KEY, $current);

            return $next($request);
        }

        if (! hash_equals((string) $expected, $current)) {
            return $this->reject($request);
        }

        return $next($request);
    }

    /**
     * Determine whether the current request should be fingerprint-validated.
     */
    private function shouldValidate(Request $request): bool
    {
        if (! (bool) env('SESSION_FINGERPRINT_ENFORCED', true)) {
            return false;
        }

        $user = $request->user();

        if (! $user instanceof User) {
            return false;
        }

        // HQ bypass users can opt out when the ABAC HQ bypass is enabled.
        if (SettingService::getBool('abac_hq_bypass', false) && $user->isHeadquartersUser()) {
            return false;
        }

        if ($request->routeIs(...self::EXEMPT_ROUTE_NAMES)) {
            return false;
        }

        // Allow the lockscreen unlock flow; the lock itself already gates access.
        if ($request->is('lockscreen*')) {
            return false;
        }

        return true;
    }

    /**
     * Compute a stable fingerprint for the request.
     */
    public function fingerprint(Request $request): string
    {
        $ip = $request->ip() ?? '0.0.0.0';
        $ua = (string) $request->userAgent();

        // Truncate the UA to avoid cache-busting from overly long or noisy
        // client hints while still capturing browser/app identity.
        $ua = substr($ua, 0, 256);

        return hash('sha256', $ip.'|'.$ua);
    }

    /**
     * Reject a request with a mismatched fingerprint.
     */
    private function reject(Request $request): Response
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Your session was invalidated for security reasons. Please log in again.',
            ], 419);
        }

        return redirect()->route('login')
            ->with('error', 'Your session was invalidated for security reasons. Please log in again.');
    }
}
