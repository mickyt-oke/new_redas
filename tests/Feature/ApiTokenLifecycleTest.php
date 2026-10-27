<?php

namespace Tests\Feature;

use App\Models\OtpCode;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class ApiTokenLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private string $password = 'Password123!';

    private function createUser(): User
    {
        return User::factory()->create([
            'service_number' => 'NIS/AA/1234',
            'role' => 'officer',
            'email' => 'api@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make($this->password),
        ]);
    }

    private function seedOtpTemplate(): void
    {
        \App\Models\EmailTemplate::query()->create([
            'key' => 'otp_login',
            'type' => 'email',
            'subject' => 'OTP {{ otp }}',
            'body' => 'Hi {{ name }}, your OTP is {{ otp }}.',
            'is_active' => true,
        ]);
    }

    private function issueTokens(User $user): array
    {
        $this->seedOtpTemplate();

        $knownOtp = '123456';
        OtpCode::query()->create([
            'user_id' => $user->id,
            'purpose' => 'login',
            'code_hash' => Hash::make($knownOtp),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
            'consumed_at' => null,
        ]);

        $response = $this->postJson('/api/otp/verify', [
            'login' => $user->email,
            'purpose' => 'login',
            'otp' => $knownOtp,
        ]);

        $response->assertOk();

        $accessToken = $response->json('access_token');
        $refreshCookie = $this->getCookie($response, 'refresh_token');

        return [
            'access_token' => $accessToken,
            'refresh_cookie' => $refreshCookie,
        ];
    }

    private function getCookie(\Illuminate\Testing\TestResponse $response, string $name): ?string
    {
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === $name) {
                return $cookie->getValue();
            }
        }

        return null;
    }

    public function test_otp_verify_issues_sanctum_access_and_refresh_cookies(): void
    {
        $user = $this->createUser();
        $tokens = $this->issueTokens($user);

        $this->assertNotNull($tokens['access_token']);
        $this->assertNotNull($tokens['refresh_cookie']);

        [$id] = explode('|', $tokens['access_token']);
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $id,
            'tokenable_id' => $user->id,
            'name' => 'api-access',
        ]);

        $this->assertDatabaseHas('refresh_tokens', [
            'user_id' => $user->id,
        ]);
    }

    public function test_refresh_rotates_refresh_token_and_issues_new_access_token(): void
    {
        $user = $this->createUser();
        $tokens = $this->issueTokens($user);

        $oldRefresh = RefreshToken::query()->where('user_id', $user->id)->first();
        $this->assertNotNull($oldRefresh);

        $response = $this->call('POST', '/api/refresh', [], ['refresh_token' => $tokens['refresh_cookie']], [], [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
        ])->assertOk();

        $newRefreshCookie = $this->getCookie($response, 'refresh_token');
        $this->assertNotNull($newRefreshCookie);

        $oldRefresh->refresh();
        $this->assertNotNull($oldRefresh->revoked_at);

        $this->assertCount(1, RefreshToken::query()->where('user_id', $user->id)->whereNull('revoked_at')->get());
        $this->assertNotNull($response->json('access_token'));
    }

    public function test_reusing_revoked_refresh_token_triggers_security_breach_response(): void
    {
        $user = $this->createUser();
        $tokens = $this->issueTokens($user);

        // First refresh rotates the token.
        $this->call('POST', '/api/refresh', [], ['refresh_token' => $tokens['refresh_cookie']], [], [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
        ])->assertOk();

        // Re-send the original refresh cookie to simulate reuse.
        $this->call('POST', '/api/refresh', [], ['refresh_token' => $tokens['refresh_cookie']], [], [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
        ])
            ->assertStatus(401)
            ->assertJsonPath('message', 'SECURITY_BREACH_DETECTED');

        $this->assertCount(0, RefreshToken::query()->where('user_id', $user->id)->whereNull('revoked_at')->get());
    }

    public function test_logout_revokes_access_and_refresh_tokens(): void
    {
        $user = $this->createUser();
        $tokens = $this->issueTokens($user);

        $this->call('POST', '/api/logout', [], ['refresh_token' => $tokens['refresh_cookie']], [], [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
            'HTTP_AUTHORIZATION' => 'Bearer '.$tokens['access_token'],
        ])->assertOk();

        $this->assertCount(0, PersonalAccessToken::query()->where('tokenable_id', $user->id)->get());
        $this->assertCount(0, RefreshToken::query()->where('user_id', $user->id)->whereNull('revoked_at')->get());
    }

    public function test_access_token_is_required_for_protected_routes(): void
    {
        $this->getJson('/api/user')->assertStatus(401);
    }

    public function test_disabled_user_cannot_use_access_token(): void
    {
        $user = $this->createUser();
        $tokens = $this->issueTokens($user);

        $user->update(['is_enabled' => false]);

        $this->getJson('/api/user', [
            'Authorization' => 'Bearer '.$tokens['access_token'],
        ])
            ->assertStatus(401)
            ->assertJsonPath('message', 'ACCOUNT_DISABLED');
    }
}
