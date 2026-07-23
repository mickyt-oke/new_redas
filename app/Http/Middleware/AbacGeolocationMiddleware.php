<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\AuditLogger;
use App\Services\GeolocationService;
use App\Services\SettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AbacGeolocationMiddleware
{
    private const SESSION_LOCATION_KEY = 'abac.location';

    private const SESSION_RESOLVED_AT_KEY = 'abac.resolved_at';

    private const SESSION_IP_KEY = 'abac.ip';

    private const RECHECK_SECONDS = 300; // 5 minutes

    /**
     * Enforce ABAC geolocation rules on authenticated requests.
     */
    public function handle(Request $request, Closure $next, string ...$constraints): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $next($request);
        }

        if (! SettingService::getBool('abac_enabled', true)) {
            return $next($request);
        }

        if (! $this->shouldEnforceForUser($user)) {
            return $next($request);
        }

        $ip = $request->ip() ?? '0.0.0.0';
        $location = $this->resolveLocation($request, $ip);
        $requiredState = $user->requiredGeoState();

        if ($requiredState === null) {
            return $next($request);
        }

        $detectedState = strtoupper($location['state_code'] ?? '');

        if ($detectedState !== $requiredState) {
            AuditLogger::logAuthBlocked(
                user: $user,
                reason: 'Session request from outside required state.',
                details: [
                    'required_state' => $requiredState,
                    'detected_state' => $detectedState,
                    'detected_country' => $location['country'] ?? 'Unknown',
                    'detected_state_name' => $location['state'] ?? 'Unknown',
                ],
                ip: $ip,
                location: $location['state'] ?? 'Unknown',
            );

            abort(403, 'Access denied: your current location is not authorized for this account.');
        }

        return $next($request);
    }

    /**
     * Determine whether geolocation enforcement should apply to this user.
     */
    private function shouldEnforceForUser(User $user): bool
    {
        if (! SettingService::getBool('abac_location_enforcement', true)) {
            return false;
        }

        if (SettingService::getBool('abac_hq_bypass', false) && $user->isHeadquartersUser()) {
            return false;
        }

        return $user->requiredGeoState() !== null;
    }

    /**
     * Resolve the request IP to a location, using the session cache when fresh.
     *
     * @return array{country:string,state:string,state_code:string,provider:string}
     */
    private function resolveLocation(Request $request, string $ip): array
    {
        $resolvedAt = $request->session()->get(self::SESSION_RESOLVED_AT_KEY);
        $cachedIp = $request->session()->get(self::SESSION_IP_KEY);

        if ($cachedIp === $ip && $resolvedAt && now()->diffInSeconds($resolvedAt) < self::RECHECK_SECONDS) {
            $cached = $request->session()->get(self::SESSION_LOCATION_KEY);

            if (is_array($cached) && isset($cached['state_code'])) {
                return $cached;
            }
        }

        $location = GeolocationService::resolve($ip);

        $request->session()->put(self::SESSION_LOCATION_KEY, $location);
        $request->session()->put(self::SESSION_RESOLVED_AT_KEY, now());
        $request->session()->put(self::SESSION_IP_KEY, $ip);

        return $location;
    }
}
