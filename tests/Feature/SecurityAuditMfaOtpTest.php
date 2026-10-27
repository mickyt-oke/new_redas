<?php

namespace Tests\Feature;

use App\Console\Commands\MfaResetCommand;
use App\Models\EmailTemplate;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\MfaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityAuditMfaOtpTest extends TestCase
{
    use RefreshDatabase;

    private string $password = 'Password123!';

    private function seedOtpTemplates(): void
    {
        EmailTemplate::query()->insert([
            [
                'key' => 'otp_login',
                'type' => 'email',
                'subject' => 'Your login code is {{ otp }}',
                'body' => 'Hi {{ name }}, your otp is {{ otp }}.',
                'is_active' => true,
            ],
            [
                'key' => 'otp_verify_email',
                'type' => 'email',
                'subject' => 'Verify your email - OTP {{ otp }}',
                'body' => 'Hi {{ name }}, verify with {{ otp }}.',
                'is_active' => true,
            ],
        ]);
    }

    private function createApiUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'service_number' => 'NIS/'.strtoupper(uniqid()).'/1234',
            'role' => 'officer',
            'email' => 'api-user-'.uniqid().'@example.com',
            'email_verified_at' => now(),
            'is_enabled' => true,
            'password' => Hash::make($this->password),
        ], $overrides));
    }

    private function loginPayload(User $user): array
    {
        RateLimiter::clear(strtolower($user->service_number).'|127.0.0.1');

        return [
            'login' => $user->service_number,
            'password' => $this->password,
            'role' => $user->role,
        ];
    }

    public function test_otp_request_returns_identical_response_for_missing_enabled_and_disabled_accounts(): void
    {
        $this->seedOtpTemplates();

        $enabledUser = $this->createApiUser(['email' => 'enabled@example.com']);
        $disabledUser = $this->createApiUser([
            'email' => 'disabled@example.com',
            'is_enabled' => false,
        ]);

        $expectedMessage = 'If the account exists, a verification code has been sent.';

        $missing = $this->postJson('/api/otp/request', [
            'login' => 'does-not-exist@example.com',
            'purpose' => 'login',
        ]);
        $missing->assertOk();
        $missing->assertJsonPath('message', $expectedMessage);

        $disabled = $this->postJson('/api/otp/request', [
            'login' => $disabledUser->email,
            'purpose' => 'login',
        ]);
        $disabled->assertOk();
        $disabled->assertJsonPath('message', $expectedMessage);

        $enabled = $this->postJson('/api/otp/request', [
            'login' => $enabledUser->email,
            'purpose' => 'login',
        ]);
        $enabled->assertOk();
        $enabled->assertJsonPath('message', $expectedMessage);
    }

    public function test_otp_re_request_invalidates_previous_codes(): void
    {
        $this->seedOtpTemplates();
        $user = $this->createApiUser();

        $this->postJson('/api/otp/request', [
            'login' => $user->email,
            'purpose' => 'login',
        ])->assertOk();

        $this->postJson('/api/otp/request', [
            'login' => $user->email,
            'purpose' => 'login',
        ])->assertOk();

        $liveCount = OtpCode::query()
            ->where('user_id', $user->id)
            ->where('purpose', 'login')
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->count();

        $this->assertSame(1, $liveCount);
    }

    public function test_otp_verify_increments_attempts_and_locks_after_max(): void
    {
        $this->seedOtpTemplates();
        $user = $this->createApiUser();

        $knownOtp = '123456';
        OtpCode::query()->create([
            'user_id' => $user->id,
            'purpose' => 'login',
            'code_hash' => Hash::make($knownOtp),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
            'consumed_at' => null,
        ]);

        $maxAttempts = config('auth.otp.max_attempts');

        for ($i = 0; $i < $maxAttempts; $i++) {
            $response = $this->postJson('/api/otp/verify', [
                'login' => $user->email,
                'purpose' => 'login',
                'otp' => '000000',
            ]);

            if ($i < $maxAttempts - 1) {
                $response->assertStatus(401);
                $response->assertJsonPath('message', 'OTP_INVALID');
            } else {
                $response->assertStatus(403);
                $response->assertJsonPath('message', 'OTP_MAX_ATTEMPTS_EXCEEDED');
            }
        }

        $user->refresh();
        $this->assertNotNull($user->otp_locked_until);
        $this->assertTrue($user->otp_locked_until->isFuture());
    }

    public function test_mfa_challenge_locks_after_five_failed_attempts(): void
    {
        $mfaService = app(MfaService::class);
        $user = User::factory()->create([
            'name' => 'MFA Test User',
            'service_number' => 'NIS/MF/0001',
            'email' => 'mfa-test@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make($this->password),
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'access_level' => 0,
            'mfa_secret' => $mfaService->generateSecret(),
            'mfa_enabled' => true,
            'mfa_verified_at' => now(),
        ]);

        $this->post('/login', $this->loginPayload($user))->assertRedirect('/mfa/challenge');

        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/mfa/challenge', ['code' => '000000']);
            $response->assertRedirect();
            $response->assertSessionHasErrors('code');
        }

        $user->refresh();
        $this->assertNotNull($user->mfa_locked_until);
        $this->assertTrue($user->mfa_locked_until->isFuture());
    }

    public function test_mfa_reset_command_clears_mfa_fields(): void
    {
        $mfaService = app(MfaService::class);
        $user = User::factory()->create([
            'service_number' => 'NIS/MF/0002',
            'mfa_secret' => $mfaService->generateSecret(),
            'mfa_backup_codes' => $mfaService->hashBackupCodes(['AABBCCDD', '11223344']),
            'mfa_enabled' => true,
            'mfa_verified_at' => now(),
            'mfa_failed_attempts' => 3,
            'mfa_locked_until' => now()->addMinutes(5),
            'mfa_last_timestep' => 12345,
        ]);

        $this->artisan(MfaResetCommand::class, ['service_number' => $user->service_number])
            ->assertSuccessful();

        $user->refresh();
        $this->assertNull($user->mfa_secret);
        $this->assertNull($user->mfa_backup_codes);
        $this->assertFalse($user->mfa_enabled);
        $this->assertNull($user->mfa_verified_at);
        $this->assertSame(0, $user->mfa_failed_attempts);
        $this->assertNull($user->mfa_locked_until);
        $this->assertNull($user->mfa_last_timestep);
    }
}
