<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SettingService::set('abac_enabled', true, 'boolean');
        SettingService::set('abac_location_enforcement', true, 'boolean');
        SettingService::set('abac_hq_bypass', false, 'boolean');
        SettingService::set('abac_allowed_countries', 'Nigeria', 'string');
        SettingService::set('geolocation_provider', 'ip-api', 'string');
        SettingService::set('geolocation_cache_ttl', 1440, 'integer');
        SettingService::set('geolocation_fallback_state', 'FC', 'string');
        SettingService::set('geolocation_api_token', null, 'string');
    }

    private function adminUser(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'user_category' => 'admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'geo_state' => 'FC',
            'access_level' => 5,
        ]);
    }

    public function test_admin_can_view_settings_page(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)->get('/admin/settings');

        $response->assertStatus(200);
        $response->assertSee('ABAC');
        $response->assertSee('Geolocation');
    }

    public function test_admin_can_update_settings(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)->put('/admin/settings', [
            'abac_enabled' => false,
            'abac_location_enforcement' => true,
            'abac_hq_bypass' => true,
            'abac_allowed_countries' => 'Nigeria,Ghana',
            'geolocation_provider' => 'ipapi',
            'geolocation_cache_ttl' => 60,
            'geolocation_fallback_state' => 'LA',
            'geolocation_api_token' => 'test-token',
        ]);

        $response->assertRedirect('/admin/settings');
        $response->assertSessionHas('status');

        $this->assertFalse(SettingService::getBool('abac_enabled'));
        $this->assertTrue(SettingService::getBool('abac_hq_bypass'));
        $this->assertSame('Nigeria,Ghana', SettingService::get('abac_allowed_countries'));
        $this->assertSame(60, SettingService::getInt('geolocation_cache_ttl'));
        $this->assertSame('LA', SettingService::get('geolocation_fallback_state'));
        $this->assertSame('test-token', SettingService::get('geolocation_api_token'));
    }

    public function test_non_admin_cannot_access_settings(): void
    {
        $user = User::factory()->create([
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'geo_state' => 'LA',
            'access_level' => 0,
        ]);

        $response = $this->actingAs($user)->get('/admin/settings');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_settings(): void
    {
        $response = $this->get('/admin/settings');

        $response->assertRedirect('/login');
    }

    public function test_setting_validation_rejects_invalid_provider(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)->put('/admin/settings', [
            'abac_enabled' => true,
            'abac_location_enforcement' => true,
            'abac_hq_bypass' => false,
            'abac_allowed_countries' => 'Nigeria',
            'geolocation_provider' => 'invalid-provider',
            'geolocation_cache_ttl' => 1440,
            'geolocation_fallback_state' => 'FC',
            'geolocation_api_token' => null,
        ]);

        $response->assertSessionHasErrors('geolocation_provider');
    }
}
