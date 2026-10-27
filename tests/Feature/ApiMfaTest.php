<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\MfaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class ApiMfaTest extends TestCase
{
    use RefreshDatabase;

    private string $password = 'Password123!';

    private function seedOtpTemplate(): void
    {
        EmailTemplate::query()->create([
            'key' => 'otp_login',
            'type' => 'email',
            'subject' => 'OTP {{ otp }}',
            'body' => 'Hi {{ name }}, your OTP is {{ otp }}.',
            'is_active' => true,
        ]);
    }

    private function createUser(bool $mfaEnabled = false): User
    {
        $mfaService = app(MfaService::class);
        $secret = $mfaEnabled ? $mfaService->generateSecret() : null;

        return User::factory()->create([
            'service_number' => 'NIS/AA/1234',
            'role' => 'officer',
            'email' => 'api-mfa@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make($this->password),
            'mfa_enabled' => $mfaEnabled,
            'mfa_verified_at' => $mfaEnabled ? now() : null,
            'mfa_secret' => $secret,
            'mfa_backup_codes' => $mfaEnabled ? $this->hashBackupCodes(['ABCD1234', 'EFGH5678']) : null,
        ]);
    }

    private function createOtp(User $user, string $code = '123456'): void
    {
        OtpCode::query()->create([
            'user_id' => $user->id,
            'purpose' => 'login',
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
            'consumed_at' => null,
        ]);
    }

    /**
     * Hash plaintext backup codes so they match the at-rest storage format.
     */
    private function hashBackupCodes(array $codes): array
    {
        return array_values(array_map(fn (string $code) => hash('sha256', $code), $codes));
    }

    public function test_non_mfa_user_can_complete_api_login_without_totp(): void
    {
        $user = $this->createUser(mfaEnabled: false);
        $this->seedOtpTemplate();
        $this->createOtp($user);

        $response = $this->postJson('/api/otp/verify', [
            'login' => $user->email,
            'purpose' => 'login',
            'otp' => '123456',
        ]);

        $response->assertOk();
        $response->assertJsonPath('token_type', 'bearer');
        $this->assertCount(1, PersonalAccessToken::query()->where('tokenable_id', $user->id)->get());
    }

    public function test_mfa_enabled_user_is_challenged_for_second_factor(): void
    {
        $user = $this->createUser(mfaEnabled: true);
        $this->seedOtpTemplate();
        $this->createOtp($user);

        $response = $this->postJson('/api/otp/verify', [
            'login' => $user->email,
            'purpose' => 'login',
            'otp' => '123456',
        ]);

        $response->assertStatus(401);
        $response->assertJsonPath('message', 'MFA_REQUIRED');
        $response->assertJsonPath('methods', ['totp', 'backup_code']);
        $this->assertCount(0, PersonalAccessToken::query()->where('tokenable_id', $user->id)->get());
    }

    public function test_mfa_enabled_user_is_rejected_with_invalid_totp(): void
    {
        $user = $this->createUser(mfaEnabled: true);
        $this->seedOtpTemplate();
        $this->createOtp($user);

        $response = $this->postJson('/api/otp/verify', [
            'login' => $user->email,
            'purpose' => 'login',
            'otp' => '123456',
            'totp' => '000000',
        ]);

        $response->assertStatus(401);
        $response->assertJsonPath('message', 'MFA_INVALID');
        $response->assertJsonPath('methods', ['totp', 'backup_code']);
        $this->assertCount(0, PersonalAccessToken::query()->where('tokenable_id', $user->id)->get());
    }

    public function test_mfa_enabled_user_can_login_with_valid_totp(): void
    {
        $user = $this->createUser(mfaEnabled: true);
        $this->seedOtpTemplate();
        $this->createOtp($user);

        $mfaService = app(MfaService::class);
        $validTotp = $mfaService->currentCode($user->mfa_secret);

        $response = $this->postJson('/api/otp/verify', [
            'login' => $user->email,
            'purpose' => 'login',
            'otp' => '123456',
            'totp' => $validTotp,
        ]);

        $response->assertOk();
        $response->assertJsonPath('token_type', 'bearer');
        $this->assertCount(1, PersonalAccessToken::query()->where('tokenable_id', $user->id)->get());
    }

    public function test_mfa_enabled_user_can_login_with_backup_code(): void
    {
        $user = $this->createUser(mfaEnabled: true);
        $this->seedOtpTemplate();
        $this->createOtp($user);

        $response = $this->postJson('/api/otp/verify', [
            'login' => $user->email,
            'purpose' => 'login',
            'otp' => '123456',
            'totp' => 'ABCD1234',
        ]);

        $response->assertOk();
        $response->assertJsonPath('token_type', 'bearer');
        $this->assertCount(1, PersonalAccessToken::query()->where('tokenable_id', $user->id)->get());

        $user->refresh();
        $this->assertNotContains($this->hashBackupCodes(['ABCD1234'])[0], $user->mfa_backup_codes ?? []);
    }
}
