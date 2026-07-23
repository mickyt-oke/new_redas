<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AbacGeolocationTest extends TestCase
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
    }

    private function fakeIpApi(string $ip, string $country, string $regionName, string $region): void
    {
        Http::fake([
            "http://ip-api.com/json/{$ip}?fields=status,country,regionName,region,city" => Http::response([
                'status' => 'success',
                'country' => $country,
                'regionName' => $regionName,
                'region' => $region,
                'city' => 'Test City',
            ]),
        ]);
    }

    private function completeMfaSetup(User $user): void
    {
        $mfaService = app(\App\Services\MfaService::class);

        $this->get('/mfa/setup')->assertOk();
        $user->refresh();

        $code = $mfaService->currentCode($user->mfa_secret);
        $this->post('/mfa/setup', ['code' => $code])->assertOk();
        $this->post('/mfa/complete')->assertRedirect();
    }

    public function test_login_succeeds_when_location_matches_user_state(): void
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

        $this->fakeIpApi('1.2.3.4', 'Nigeria', 'Lagos', 'LA');

        $response = $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])->post('/login', [
            'login' => $user->email,
            'password' => 'password',
            'role' => 'officer',
        ]);

        $response->assertRedirect('/mfa/setup');
        $this->completeMfaSetup($user);
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_blocked_when_location_mismatches_user_state(): void
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

        $this->fakeIpApi('1.2.3.5', 'Nigeria', 'Kano', 'KN');

        $response = $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.5'])->post('/login', [
            'login' => $user->email,
            'password' => 'password',
            'role' => 'officer',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_login_blocked_when_outside_allowed_country(): void
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

        $this->fakeIpApi('8.8.8.8', 'United States', 'California', 'CA');

        $response = $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])->post('/login', [
            'login' => $user->email,
            'password' => 'password',
            'role' => 'officer',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_hq_bypass_allows_admin_from_any_location(): void
    {
        SettingService::set('abac_hq_bypass', true, 'boolean');

        $user = User::factory()->create([
            'role' => 'admin',
            'user_category' => 'admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'geo_state' => 'FC',
            'access_level' => 5,
            'password' => bcrypt('password'),
        ]);

        $this->fakeIpApi('8.8.8.8', 'United States', 'California', 'CA');

        $response = $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])->post('/login', [
            'login' => $user->email,
            'password' => 'password',
            'role' => 'admin',
        ]);

        $response->assertRedirect('/mfa/setup');
        $this->completeMfaSetup($user);
        $this->assertAuthenticatedAs($user);
    }

    public function test_abac_disabled_allows_any_location(): void
    {
        SettingService::set('abac_enabled', false, 'boolean');

        $user = User::factory()->create([
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'geo_state' => 'LA',
            'access_level' => 0,
            'password' => bcrypt('password'),
        ]);

        $this->fakeIpApi('8.8.8.8', 'United States', 'California', 'CA');

        $response = $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])->post('/login', [
            'login' => $user->email,
            'password' => 'password',
            'role' => 'officer',
        ]);

        $response->assertRedirect('/mfa/setup');
        $this->completeMfaSetup($user);
        $this->assertAuthenticatedAs($user);
    }

    public function test_session_request_blocked_after_location_change(): void
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

        $this->fakeIpApi('1.2.3.4', 'Nigeria', 'Lagos', 'LA');

        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])->post('/login', [
            'login' => $user->email,
            'password' => 'password',
            'role' => 'officer',
        ]);

        $this->completeMfaSetup($user);
        $this->assertAuthenticatedAs($user);

        Http::fake([
            'http://ip-api.com/json/*' => Http::response([
                'status' => 'success',
                'country' => 'Nigeria',
                'regionName' => 'Kano',
                'region' => 'KN',
                'city' => 'Kano',
            ]),
        ]);

        $response = $this->actingAs($user)->withServerVariables(['REMOTE_ADDR' => '1.2.3.6'])->get('/user/dashboard');

        $response->assertStatus(403);
    }
}
