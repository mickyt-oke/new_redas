<?php

namespace App\Services;

use App\Models\PrimaryLocationCode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeolocationService
{
    /**
     * Resolve a request IP to a normalized location.
     *
     * @return array{country:string,state:string,state_code:string,provider:string}
     */
    public static function resolve(string $ip): array
    {
        $fallbackState = strtoupper(SettingService::get('geolocation_fallback_state', 'FC'));

        if (self::isPrivateIp($ip)) {
            return [
                'country' => 'Nigeria',
                'state' => self::stateNameFromCode($fallbackState) ?? 'FCT',
                'state_code' => $fallbackState,
                'provider' => 'fallback-private',
            ];
        }

        $cacheKey = 'geoip.'.md5($ip);
        $ttl = SettingService::getInt('geolocation_cache_ttl', 1440);

        return Cache::remember($cacheKey, $ttl, function () use ($ip, $fallbackState) {
            $provider = SettingService::get('geolocation_provider', 'ip-api');

            $location = match ($provider) {
                'ip-api' => self::resolveIpApi($ip),
                'ipapi' => self::resolveIpapiCo($ip),
                default => self::resolveIpApi($ip),
            };

            if ($location === null) {
                return [
                    'country' => 'Unknown',
                    'state' => self::stateNameFromCode($fallbackState) ?? 'FCT',
                    'state_code' => $fallbackState,
                    'provider' => 'fallback-unknown',
                ];
            }

            return $location;
        });
    }

    /**
     * Resolve location using ip-api.com.
     *
     * @return array{country:string,state:string,state_code:string,provider:string}|null
     */
    private static function resolveIpApi(string $ip): ?array
    {
        try {
            $response = Http::timeout(5)
                ->get("http://ip-api.com/json/{$ip}?fields=status,country,regionName,region,city");

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            if (($data['status'] ?? '') !== 'success') {
                return null;
            }

            $country = $data['country'] ?? 'Unknown';
            $state = $data['regionName'] ?? $data['region'] ?? 'Unknown';
            $stateCode = self::stateCodeFromName($state) ?? strtoupper($data['region'] ?? '');

            return [
                'country' => $country,
                'state' => $state,
                'state_code' => $stateCode,
                'provider' => 'ip-api',
            ];
        } catch (\Throwable $e) {
            Log::warning('Geolocation lookup failed', ['provider' => 'ip-api', 'ip' => $ip, 'error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Resolve location using ipapi.co.
     *
     * @return array{country:string,state:string,state_code:string,provider:string}|null
     */
    private static function resolveIpapiCo(string $ip): ?array
    {
        try {
            $token = SettingService::get('geolocation_api_token');
            $url = $token
                ? "https://ipapi.co/{$ip}/json/?token={$token}"
                : "https://ipapi.co/{$ip}/json/";

            $response = Http::timeout(5)->get($url);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            $country = $data['country_name'] ?? $data['country'] ?? 'Unknown';
            $state = $data['region'] ?? 'Unknown';
            $stateCode = self::stateCodeFromName($state) ?? strtoupper($data['region_code'] ?? '');

            return [
                'country' => $country,
                'state' => $state,
                'state_code' => $stateCode,
                'provider' => 'ipapi',
            ];
        } catch (\Throwable $e) {
            Log::warning('Geolocation lookup failed', ['provider' => 'ipapi', 'ip' => $ip, 'error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Map a detected region/state name to a Nigerian state code from the lookup table.
     */
    public static function stateCodeFromName(string $name): ?string
    {
        $normalized = self::normalizeStateName($name);

        $code = PrimaryLocationCode::query()
            ->whereRaw('LOWER(location_name) = ?', [$normalized])
            ->value('code');

        return $code ? strtoupper($code) : null;
    }

    /**
     * Get the state name for a given state code.
     */
    public static function stateNameFromCode(string $code): ?string
    {
        return PrimaryLocationCode::query()
            ->where('code', strtoupper($code))
            ->value('location_name');
    }

    /**
     * Normalize a state name for lookup.
     */
    private static function normalizeStateName(string $name): string
    {
        return strtolower(trim($name));
    }

    /**
     * Check if an IP address is private/local.
     */
    public static function isPrivateIp(string $ip): bool
    {
        if (in_array($ip, ['127.0.0.1', '::1'], true)) {
            return true;
        }

        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }
}
