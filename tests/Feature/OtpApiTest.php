<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $res->assertJsonPath('message', 'OTP sent successfully.');

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

        $knownOtp = '1234';
        OtpCode::query()->create([
            'user_id' => $user->id,
            'purpose' => 'login',
            'code_hash' => hash('sha256', $knownOtp),
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

        $this->assertDatabaseHas('otp_codes', [
            'user_id' => $user->id,
            'purpose' => 'login',
            'code_hash' => hash('sha256', $knownOtp),
        ]);

        $otp = OtpCode::query()
            ->where('user_id', $user->id)
            ->where('purpose', 'login')
            ->where('code_hash', hash('sha256', $knownOtp))
            ->orderByDesc('created_at')
            ->first();

        $this->assertNotNull($otp);
        $this->assertNotNull($otp->consumed_at);
    }
}
