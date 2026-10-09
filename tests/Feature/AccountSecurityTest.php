<?php

namespace Tests\Feature;

use App\Models\AuthToken;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Services\MfaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AccountSecurityTest extends TestCase
{
    use RefreshDatabase;

    private string $password = 'Password123!';

    private function createUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => 'Security Test User',
            'service_number' => 'NIS/SC/0001',
            'email' => 'security-test@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make($this->password),
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'access_level' => 0,
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

    private function completeMfaSetup(User $user): void
    {
        $mfaService = app(MfaService::class);

        $this->get('/mfa/setup')->assertOk();
        $user->refresh();

        $code = $mfaService->currentCode($user->mfa_secret);
        $this->post('/mfa/setup', ['code' => $code])->assertOk();
        $this->post('/mfa/complete');
    }

    public function test_login_is_blocked_for_unverified_email(): void
    {
        $user = $this->createUser(['email_verified_at' => null]);

        $response = $this->from('/login')->post('/login', $this->loginPayload($user));

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login');
        $response->assertSessionHas('unverified_login', $user->service_number);
        $this->assertGuest();
    }

    public function test_login_succeeds_once_email_is_verified(): void
    {
        $user = $this->createUser();

        $response = $this->post('/login', $this->loginPayload($user));

        $response->assertRedirect('/mfa/setup');
    }

    public function test_resend_verification_email_sends_for_unverified_account_without_leaking_status(): void
    {
        EmailTemplate::query()->create([
            'key' => 'verify_email_magic_link',
            'type' => 'workflow',
            'subject' => 'Verify your account',
            'body' => 'Hi {{ name }}, verify here: {{ magic_link }}',
            'is_active' => true,
        ]);

        $unverified = $this->createUser(['email_verified_at' => null]);
        $verified = $this->createUser([
            'service_number' => 'NIS/SC/0002',
            'email' => 'verified@example.com',
        ]);

        $unverifiedResponse = $this->post('/verify-email/resend', ['login' => $unverified->email]);
        $verifiedResponse = $this->post('/verify-email/resend', ['login' => $verified->email]);
        $unknownResponse = $this->post('/verify-email/resend', ['login' => 'nobody@example.com']);

        $unverifiedResponse->assertRedirect('/login');
        $verifiedResponse->assertRedirect('/login');
        $unknownResponse->assertRedirect('/login');

        $unverifiedResponse->assertSessionHas('status', 'If the account exists and is unverified, a new verification link has been sent.');
        $verifiedResponse->assertSessionHas('status', 'If the account exists and is unverified, a new verification link has been sent.');
        $unknownResponse->assertSessionHas('status', 'If the account exists and is unverified, a new verification link has been sent.');

        $this->assertDatabaseHas('auth_tokens', [
            'user_id' => $unverified->id,
            'type' => 'email_verify',
        ]);
        $this->assertDatabaseMissing('auth_tokens', [
            'user_id' => $verified->id,
            'type' => 'email_verify',
        ]);
    }

    public function test_admin_created_user_must_change_password_and_is_redirected_to_profile_on_first_login(): void
    {
        $user = $this->createUser(['must_change_password' => true]);

        $this->post('/login', $this->loginPayload($user))->assertRedirect('/mfa/setup');
        $this->completeMfaSetup($user);

        $this->assertAuthenticatedAs($user);

        // Any other authenticated page should redirect back to the profile page.
        $response = $this->get('/user/dashboard');
        $response->assertRedirect(route('user.profile'));
    }

    public function test_profile_update_clears_must_change_password_flag(): void
    {
        $user = $this->createUser(['must_change_password' => true]);

        $this->actingAs($user)
            ->patch('/user/profile', [
                'section' => 'password',
                'current_password' => $this->password,
                'password' => 'NewPassword456!',
                'password_confirmation' => 'NewPassword456!',
            ])
            ->assertRedirect(route('user.profile'));

        $user->refresh();
        $this->assertFalse($user->must_change_password);

        // Now other pages should be reachable again.
        $this->get('/user/notifications')->assertOk();
    }
}
