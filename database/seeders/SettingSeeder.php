<?php

namespace Database\Seeders;

use App\Services\SettingService;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Seed default system settings.
     */
    public function run(): void
    {
        $defaults = [
            'abac_enabled' => ['value' => true, 'type' => 'boolean', 'description' => 'Master switch for attribute-based access control.'],
            'abac_location_enforcement' => ['value' => true, 'type' => 'boolean', 'description' => 'Block login and requests from outside the user\'s registered state.'],
            'abac_hq_bypass' => ['value' => false, 'type' => 'boolean', 'description' => 'Allow HQ/admin users to authenticate from any location.'],
            'abac_allowed_countries' => ['value' => 'Nigeria', 'type' => 'string', 'description' => 'Comma-separated list of country names permitted for authentication.'],
            'geolocation_provider' => ['value' => 'ip-api', 'type' => 'string', 'description' => 'IP geolocation service provider.'],
            'geolocation_cache_ttl' => ['value' => 1440, 'type' => 'integer', 'description' => 'Cache TTL for resolved IP locations in minutes.'],
            'geolocation_fallback_state' => ['value' => 'FC', 'type' => 'string', 'description' => 'State used for private/local IP addresses during development.'],
            'geolocation_api_token' => ['value' => null, 'type' => 'string', 'description' => 'Optional API token for paid geolocation providers.'],
        ];

        foreach ($defaults as $key => $config) {
            SettingService::set($key, $config['value'], $config['type'], $config['description']);
        }
    }
}
