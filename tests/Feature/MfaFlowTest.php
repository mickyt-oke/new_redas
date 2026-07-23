<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\MfaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class MfaFlowTest extends TestCase
{
    use RefreshDatabase;

    private string $password = 'Password123!';

    private function createUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => 'MFA Test User',
            'service_number' => 'NIS/MF/0001',
            'email' => 'mfa-test@example.com',
            'password' => Hash::make($this->password),
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'access_level' => 0,
        ], $overrides));
    }

    private function loginPayload(User $user): array
    {
        RateLimiter::clear($this->throttleKey($user));

        return [
            'login' => $user->service_number,
            'password' => $this->password,
            'role' => $user->role,
        ];
    }

    private function throttleKey(User $user): string
    {
        return strtolower($user->service_number).'|127.0.0.1';
    }

    public function test_login_redirects_to_mfa_setup_for_new_user(): void
    {
        $user = $this->createUser();

        $response = $this->post('/login', $this->loginPayload($user));

        $response->assertRedirect('/mfa/setup');
        $this->assertGuest();
    }

    public function test_user_can_complete_mfa_setup_and_reach_dashboard(): void
    {
        $user = $this->createUser();
        $mfaService = app(MfaService::class);

        $response = $this->post('/login', $this->loginPayload($user));
        $response->assertRedirect('/mfa/setup');

        $setup = $this->get('/mfa/setup');
        $setup->assertOk();

        $user->refresh();
        $this->assertNotNull($user->mfa_secret);
        $this->assertFalse($user->isMfaEnabled());

        $code = $mfaService->currentCode($user->mfa_secret);
        $verify = $this->post('/mfa/setup', ['code' => $code]);
        $verify->assertOk();
        $verify->assertSee('MFA Enabled Successfully');

        $user->refresh();
        $this->assertTrue($user->isMfaEnabled());
        $this->assertNotNull($user->mfa_backup_codes);
        $this->assertCount(8, $user->mfa_backup_codes);

        $complete = $this->post('/mfa/complete');
        $complete->assertRedirect('/user/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_redirects_to_mfa_challenge_for_enabled_user(): void
    {
        $user = $this->createUser([
            'mfa_secret' => app(MfaService::class)->generateSecret(),
            'mfa_enabled' => true,
            'mfa_verified_at' => now(),
        ]);

        $response = $this->post('/login', $this->loginPayload($user));

        $response->assertRedirect('/mfa/challenge');
        $this->assertGuest();
    }

    public function test_enabled_user_can_verify_totp_challenge_and_login(): void
    {
        $user = $this->createUser([
            'mfa_secret' => app(MfaService::class)->generateSecret(),
            'mfa_enabled' => true,
            'mfa_verified_at' => now(),
        ]);
        $mfaService = app(MfaService::class);

        $this->post('/login', $this->loginPayload($user))->assertRedirect('/mfa/challenge');
        $this->get('/mfa/challenge')->assertOk();

        $code = $mfaService->currentCode($user->mfa_secret);
        $response = $this->post('/mfa/challenge', ['code' => $code]);

        $response->assertRedirect('/user/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_enabled_user_can_use_backup_code_to_login(): void
    {
        $backupCodes = ['AABBCCDD', '11223344'];
        $user = $this->createUser([
            'mfa_secret' => app(MfaService::class)->generateSecret(),
            'mfa_enabled' => true,
            'mfa_verified_at' => now(),
            'mfa_backup_codes' => $backupCodes,
        ]);

        $this->post('/login', $this->loginPayload($user))->assertRedirect('/mfa/challenge');

        $response = $this->post('/mfa/challenge', ['code' => $backupCodes[0]]);

        $response->assertRedirect('/user/dashboard');
        $this->assertAuthenticatedAs($user);

        $user->refresh();
        $this->assertCount(1, $user->mfa_backup_codes);
        $this->assertNotContains($backupCodes[0], $user->mfa_backup_codes);
    }

    public function test_setup_rejects_invalid_totp_code(): void
    {
        $user = $this->createUser();

        $this->post('/login', $this->loginPayload($user))->assertRedirect('/mfa/setup');

        $response = $this->post('/mfa/setup', ['code' => '000000']);
        $response->assertRedirect();
        $response->assertSessionHasErrors('code');

        $user->refresh();
        $this->assertFalse($user->isMfaEnabled());
    }

    public function test_challenge_rejects_invalid_code(): void
    {
        $user = $this->createUser([
            'mfa_secret' => app(MfaService::class)->generateSecret(),
            'mfa_enabled' => true,
            'mfa_verified_at' => now(),
        ]);

        $this->post('/login', $this->loginPayload($user))->assertRedirect('/mfa/challenge');

        $response = $this->post('/mfa/challenge', ['code' => '000000']);
        $response->assertRedirect();
        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_cancel_clears_pending_mfa_session(): void
    {
        $user = $this->createUser();

        $this->post('/login', $this->loginPayload($user))->assertRedirect('/mfa/setup');

        $response = $this->post('/mfa/cancel');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
