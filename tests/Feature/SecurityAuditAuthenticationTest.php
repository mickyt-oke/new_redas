<?php

namespace Tests\Feature;

use App\Models\AuthToken;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class SecurityAuditAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private string $validPassword = 'SecurePass12!';

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'api.pwnedpasswords.com/*' => Http::response('', 200),
        ]);
    }

    private function createUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => 'Security Audit User',
            'service_number' => 'NIS/SEC/0001',
            'email' => 'security@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make($this->validPassword),
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'access_level' => 0,
            'is_enabled' => true,
        ], $overrides));
    }

    public function test_account_lockout_is_set_after_five_failed_logins(): void
    {
        $user = $this->createUser();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'login' => $user->service_number,
                'password' => 'wrong-password',
            ]);
        }

        $user->refresh();

        $this->assertNotNull($user->lockout_until);
        $this->assertSame(0, (int) $user->failed_attempts);
    }

    public function test_password_reset_revokes_refresh_tokens_and_deletes_sessions(): void
    {
        $user = $this->createUser();

        $refreshToken = RefreshToken::create([
            'user_id' => $user->id,
            'jti' => Str::random(32),
            'token_hash' => hash('sha256', Str::random(64)),
            'expires_at' => now()->addDays(30),
            'revoked_at' => null,
            'created_ip' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        DB::table('sessions')->insert([
            'id' => Str::random(40),
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => serialize([]),
            'last_activity' => time(),
        ]);

        $emailVerifyToken = AuthToken::create([
            'user_id' => $user->id,
            'type' => 'email_verify',
            'token_hash' => hash('sha256', $rawVerify = Str::random(64)),
            'expires_at' => now()->addHour(),
            'used_at' => null,
        ]);

        $resetToken = AuthToken::create([
            'user_id' => $user->id,
            'type' => 'password_reset',
            'token_hash' => hash('sha256', $rawReset = Str::random(64)),
            'expires_at' => now()->addHour(),
            'used_at' => null,
        ]);

        $newPassword = 'NewSecurePass34!';

        $this->post(route('password.reset', $rawReset), [
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])->assertRedirect(route('login'));

        $refreshToken->refresh();
        $this->assertNotNull($refreshToken->revoked_at);

        $this->assertDatabaseMissing('sessions', ['user_id' => $user->id]);

        $emailVerifyToken->refresh();
        $this->assertNotNull($emailVerifyToken->used_at);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'auth.password_reset',
            'status' => 'success',
        ]);
    }

    public function test_repeated_wrong_lockscreen_passcode_forces_reauthentication(): void
    {
        $user = $this->createUser([
            'lockscreen_passcode' => Hash::make('SecurePass12'),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $response = $this->actingAs($user)
                ->withSession(['session' => ['locked' => true]])
                ->post(route('lockscreen.unlock'), ['passcode' => 'wrong-pass']);

            if ($i < 4) {
                $response->assertSessionHasErrors('passcode');
                $response->assertSessionHas('session.locked', true);
                $this->assertAuthenticatedAs($user);
            }
        }

        // The fifth failure must log the user out and send them to login.
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_password_validation_enforces_policy_and_rejects_reuse(): void
    {
        $user = $this->createUser();

        // Too short.
        $this->actingAs($user)
            ->patch(route('user.profile.update'), [
                'section' => 'password',
                'current_password' => $this->validPassword,
                'password' => 'Short1!',
                'password_confirmation' => 'Short1!',
            ])
            ->assertSessionHasErrors('password');

        // Contains service number.
        $this->actingAs($user)
            ->patch(route('user.profile.update'), [
                'section' => 'password',
                'current_password' => $this->validPassword,
                'password' => 'NIS/SEC/0001-Extra!',
                'password_confirmation' => 'NIS/SEC/0001-Extra!',
            ])
            ->assertSessionHasErrors('password');

        // Contains email local-part.
        $this->actingAs($user)
            ->patch(route('user.profile.update'), [
                'section' => 'password',
                'current_password' => $this->validPassword,
                'password' => 'security@example-extra-A1!',
                'password_confirmation' => 'security@example-extra-A1!',
            ])
            ->assertSessionHasErrors('password');

        // Valid change.
        $firstNew = 'FirstNewPass12!';
        $this->actingAs($user)
            ->patch(route('user.profile.update'), [
                'section' => 'password',
                'current_password' => $this->validPassword,
                'password' => $firstNew,
                'password_confirmation' => $firstNew,
            ])
            ->assertSessionHasNoErrors();

        $user->refresh();

        // Reusing the previous password must fail.
        $this->actingAs($user)
            ->patch(route('user.profile.update'), [
                'section' => 'password',
                'current_password' => $firstNew,
                'password' => $this->validPassword,
                'password_confirmation' => $this->validPassword,
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_lockscreen_passcode_rejects_weak_values(): void
    {
        $user = $this->createUser();

        $weak = [
            '0000' => 'too short',
            '1234' => 'too short',
            '111111' => 'repeated digits',
            '121212' => 'too easy',
            '1234567' => 'numeric and short',
        ];

        foreach ($weak as $passcode => $reason) {
            $this->actingAs($user)
                ->patch(route('user.profile.update'), [
                    'section' => 'passcode',
                    'current_password' => $this->validPassword,
                    'lockscreen_passcode' => $passcode,
                    'lockscreen_passcode_confirmation' => $passcode,
                ])
                ->assertSessionHasErrors('lockscreen_passcode', "Failed to reject weak passcode: {$passcode} ({$reason})");
        }

        // An 8-digit numeric passcode is allowed.
        $this->actingAs($user)
            ->patch(route('user.profile.update'), [
                'section' => 'passcode',
                'current_password' => $this->validPassword,
                'lockscreen_passcode' => '12345678',
                'lockscreen_passcode_confirmation' => '12345678',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'security.lockscreen_passcode_set',
        ]);
    }
}
