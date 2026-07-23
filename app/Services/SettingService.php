<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    private const CACHE_PREFIX = 'settings.';

    private const CACHE_TTL_MINUTES = 60;

    /**
     * Get a setting value, falling back to the provided default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = self::CACHE_PREFIX.$key;

        return Cache::remember($cacheKey, self::CACHE_TTL_MINUTES, function () use ($key, $default) {
            $setting = Setting::where('key', '=', $key, 'and')->first();

            if (! $setting) {
                return $default;
            }

            return $setting->typedValue();
        });
    }

    /**
     * Get a boolean setting.
     */
    public static function getBool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default);

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
        }

        if (is_int($value)) {
            return (bool) $value;
        }

        return $default;
    }

    /**
     * Get an integer setting.
     */
    public static function getInt(string $key, int $default = 0): int
    {
        $value = self::get($key, $default);

        return is_numeric($value) ? (int) $value : $default;
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, mixed $value, string $type = 'string', ?string $description = null): Setting
    {
        $storedValue = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => $value === null ? null : json_encode($value),
            default => (string) $value,
        };

        $setting = Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $storedValue,
                'type' => $type,
                'description' => $description,
            ]
        );

        Cache::forget(self::CACHE_PREFIX.$key);

        return $setting;
    }

    /**
     * Clear the cached value for a setting.
     */
    public static function forget(string $key): void
    {
        Cache::forget(self::CACHE_PREFIX.$key);
    }
}
