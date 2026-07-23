<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuditLogTest extends TestCase
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

        Http::fake([
            'http://ip-api.com/json/*' => Http::response([
                'status' => 'success',
                'country' => 'Nigeria',
                'regionName' => 'Lagos',
                'region' => 'LA',
                'city' => 'Lagos',
            ]),
        ]);
    }

    public function test_successful_login_is_logged(): void
    {
        $user = User::factory()->create([
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'geo_state' => 'LA',
            'access_level' => 0,
            'password' => bcrypt('password'),
        ]);

        $mfaService = app(\App\Services\MfaService::class);

        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])->post('/login', [
            'login' => $user->email,
            'password' => 'password',
            'role' => 'officer',
        ]);

        $this->get('/mfa/setup')->assertOk();
        $user->refresh();
        $code = $mfaService->currentCode($user->mfa_secret);
        $this->post('/mfa/setup', ['code' => $code])->assertOk();
        $this->post('/mfa/complete')->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'auth.attempt',
            'status' => 'success',
        ]);
    }

    public function test_failed_login_is_logged(): void
    {
        $user = User::factory()->create([
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'geo_state' => 'LA',
            'access_level' => 0,
            'password' => bcrypt('password'),
        ]);

        $this->post('/login', [
            'login' => $user->email,
            'password' => 'wrong-password',
            'role' => 'officer',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'auth.attempt',
            'status' => 'failure',
        ]);
    }

    public function test_blocked_login_is_logged(): void
    {
        $user = User::factory()->create([
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'geo_state' => 'LA',
            'access_level' => 0,
            'password' => bcrypt('password'),
        ]);

        Http::fake([
            'http://ip-api.com/json/*' => Http::response([
                'status' => 'success',
                'country' => 'Nigeria',
                'regionName' => 'Kano',
                'region' => 'KN',
                'city' => 'Kano',
            ]),
        ]);

        $this->post('/login', [
            'login' => $user->email,
            'password' => 'password',
            'role' => 'officer',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'auth.blocked',
            'status' => 'blocked',
        ]);
    }

    public function test_logout_is_logged(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'user_category' => 'admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'geo_state' => 'FC',
            'access_level' => 5,
        ]);

        $this->actingAs($user)->post('/logout');

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'auth.logout',
            'status' => 'success',
        ]);
    }

    public function test_settings_change_is_logged(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'user_category' => 'admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'geo_state' => 'FC',
            'access_level' => 5,
        ]);

        $this->actingAs($user)->put('/admin/settings', [
            'abac_enabled' => false,
            'abac_location_enforcement' => true,
            'abac_hq_bypass' => false,
            'abac_allowed_countries' => 'Nigeria',
            'geolocation_provider' => 'ip-api',
            'geolocation_cache_ttl' => 1440,
            'geolocation_fallback_state' => 'FC',
            'geolocation_api_token' => null,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'settings.update',
            'entity_type' => 'setting',
            'entity_id' => 'abac_enabled',
        ]);
    }

    public function test_audit_log_index_can_be_filtered(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'user_category' => 'admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'geo_state' => 'FC',
            'access_level' => 5,
        ]);

        $user = User::factory()->create([
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'geo_state' => 'LA',
            'access_level' => 0,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'auth.blocked',
            'status' => 'blocked',
            'ip_address' => '1.2.3.4',
            'created_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'auth.attempt',
            'status' => 'success',
            'ip_address' => '1.2.3.5',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/admin/audit-log?status=blocked');

        $response->assertStatus(200);
        $response->assertSee('1 records found');
        $response->assertSee('1.2.3.4');
        $response->assertDontSee('1.2.3.5');
    }
}
