<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessagingResilienceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Password reset must return the same generic response whether the
     * account exists or not, and must not surface a 500 error, even when the
     * underlying mail template is missing and delivery fails.
     */
    public function test_password_reset_request_does_not_leak_account_existence_when_mail_delivery_fails(): void
    {
        $user = User::factory()->create(['email' => 'known@example.com']);

        // Intentionally do not seed 'password_reset_magic_link' so ResendMailService throws.
        $knownResponse = $this->from('/password/forgot')->post('/password/forgot', [
            'email' => $user->email,
        ]);

        $unknownResponse = $this->from('/password/forgot')->post('/password/forgot', [
            'email' => 'nobody@example.com',
        ]);

        $knownResponse->assertRedirect('/login');
        $knownResponse->assertSessionHas('status', 'If the email exists, a reset link has been sent.');

        $unknownResponse->assertRedirect('/login');
        $unknownResponse->assertSessionHas('status', 'If the email exists, a reset link has been sent.');
    }

    public function test_password_reset_request_succeeds_when_template_is_seeded(): void
    {
        $user = User::factory()->create(['email' => 'known2@example.com']);

        EmailTemplate::query()->create([
            'key' => 'password_reset_magic_link',
            'type' => 'workflow',
            'subject' => 'Reset your password',
            'body' => 'Hi {{ name }}, reset here: {{ magic_link }}',
            'is_active' => true,
        ]);

        $response = $this->from('/password/forgot')->post('/password/forgot', [
            'email' => $user->email,
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('auth_tokens', [
            'user_id' => $user->id,
            'type' => 'password_reset',
        ]);
    }

    public function test_otp_request_returns_friendly_error_instead_of_500_when_email_delivery_fails(): void
    {
        $user = User::factory()->create([
            'service_number' => 'NIS/AA/9999',
            'email' => 'otpuser@example.com',
        ]);

        // Intentionally do not seed 'otp_login' template so the mailer throws.
        $response = $this->postJson('/api/otp/request', [
            'login' => $user->email,
            'purpose' => 'login',
        ]);

        $response->assertStatus(502);
        $response->assertJsonPath('message', 'We could not send your verification code right now. Please try again shortly.');

        // The OTP row is still created even though delivery failed.
        $this->assertDatabaseHas('otp_codes', [
            'user_id' => $user->id,
            'purpose' => 'login',
        ]);
    }
}
