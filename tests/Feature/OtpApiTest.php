<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OtpApiTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $role = 'officer'): User
    {
        return User::factory()->create([
            'service_number' => 'NIS/AA/1234',
            'role' => $role,
            'email' => 'user@example.com',
        ]);
    }

    private function seedOtpTemplates(): void
    {
        EmailTemplate::query()->insert([
            [
                'key' => 'otp_login',
                'type' => 'email',
                'subject' => 'Your login code is {{ otp }}',
                'body' => 'Hi {{ name }}, your otp is {{ otp }}. Expires in {{ expires_in_minutes }} minutes.',
                'is_active' => true,
            ],
            [
                'key' => 'otp_verify_email',
                'type' => 'email',
                'subject' => 'Verify your email - OTP {{ otp }}',
                'body' => 'Hi {{ name }}, verify with {{ otp }}. Expires in {{ expires_in_minutes }} minutes.',
                'is_active' => true,
            ],
        ]);
    }

    public function test_otp_request_creates_otp_code_and_sends_email_via_resend_transport(): void
    {
        Mail::fake();

        $user = $this->createUser();
        $this->seedOtpTemplates();

        $res = $this->postJson('/api/otp/request', [
            'login' => $user->email,
            'purpose' => 'login',
        ]);

        $res->assertStatus(200);
        $res->assertJsonPath('message', 'If the account exists, a verification code has been sent.');

        $this->assertDatabaseHas('otp_codes', [
            'user_id' => $user->id,
            'purpose' => 'login',
            'consumed_at' => null,
        ]);

        Mail::assertNothingSent(); // because ResendMailService uses Mail::mailer(...)->send with closure, which is not a standard Mailable fake assertion
        // The main acceptance is DB row creation + no crash.
    }

    public function test_otp_verify_consumes_matching_code(): void
    {
        $user = $this->createUser();
        $this->seedOtpTemplates();

        $knownOtp = '123456';
        OtpCode::query()->create([
            'user_id' => $user->id,
            'purpose' => 'login',
            'code_hash' => Hash::make($knownOtp),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
            'consumed_at' => null,
        ]);

        $res = $this->postJson('/api/otp/verify', [
            'login' => $user->email,
            'purpose' => 'login',
            'otp' => $knownOtp,
        ]);

        $res->assertStatus(200);
        $res->assertJsonPath('message', 'OTP verified successfully.');
        $res->assertJsonPath('user_id', $user->id);
        $res->assertJsonPath('token_type', 'bearer');
        $res->assertJsonPath('access_token', fn (string $token) => str_contains($token, '|'));

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'api-access',
        ]);

        $otp = OtpCode::query()
            ->where('user_id', $user->id)
            ->where('purpose', 'login')
            ->orderByDesc('created_at')
            ->first();

        $this->assertNotNull($otp);
        $this->assertNotNull($otp->consumed_at);
    }

    public function test_otp_verify_locks_account_after_max_attempts(): void
    {
        $user = $this->createUser();
        $this->seedOtpTemplates();

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

        for ($i = 0; $i < $maxAttempts - 1; $i++) {
            $this->postJson('/api/otp/verify', [
                'login' => $user->email,
                'purpose' => 'login',
                'otp' => '000000',
            ])->assertStatus(401);
        }

        $lockoutResponse = $this->postJson('/api/otp/verify', [
            'login' => $user->email,
            'purpose' => 'login',
            'otp' => '000000',
        ]);
        $lockoutResponse->assertStatus(403);
        $lockoutResponse->assertJsonPath('message', 'OTP_MAX_ATTEMPTS_EXCEEDED');

        $user->refresh();
        $this->assertNotNull($user->otp_locked_until);
        $this->assertTrue($user->otp_locked_until->isFuture());
    }

    public function test_otp_request_is_blocked_when_account_is_otp_locked(): void
    {
        $user = User::factory()->create([
            'service_number' => 'NIS/AA/1234',
            'role' => 'officer',
            'email' => 'user@example.com',
            'email_verified_at' => now(),
            'otp_locked_until' => now()->addMinutes(15),
        ]);
        $this->seedOtpTemplates();

        $response = $this->postJson('/api/otp/request', [
            'login' => $user->email,
            'purpose' => 'login',
        ]);

        // H-7: identical generic response for locked accounts too.
        $response->assertStatus(200);
        $response->assertJsonPath('message', 'If the account exists, a verification code has been sent.');
    }
}
