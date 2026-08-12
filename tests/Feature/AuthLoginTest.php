<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\MfaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    private string $password = 'Password123!';

    private function createUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => 'Login Test User',
            'service_number' => 'NIS/OF/7777',
            'email' => 'login@example.com',
            'password' => Hash::make($this->password),
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'access_level' => 0,
        ], $overrides));
    }

    private function completeMfaSetup(User $user): void
    {
        $mfaService = app(MfaService::class);

        $this->get('/mfa/setup')->assertOk();
        $user->refresh();
        $this->assertNotNull($user->mfa_secret);

        $code = $mfaService->currentCode($user->mfa_secret);
        $this->post('/mfa/setup', ['code' => $code])->assertOk();
        $this->post('/mfa/complete')->assertRedirect();
    }

    private function loginPayload(User $user, ?string $role = null): array
    {
        RateLimiter::clear(strtolower($user->service_number).'|127.0.0.1');

        return [
            'login' => $user->service_number,
            'password' => $this->password,
            'role' => $role ?? $user->role,
        ];
    }

    public function test_users_can_login_with_service_number_and_role(): void
    {
        $user = $this->createUser();

        $response = $this->post('/login', $this->loginPayload($user));
        $response->assertRedirect('/mfa/setup');

        $this->completeMfaSetup($user);
        $this->assertAuthenticatedAs($user);
    }

    public function test_super_admin_can_login_with_role_alias(): void
    {
        $user = $this->createUser([
            'name' => 'Super Admin Test',
            'service_number' => 'NIS/AD/1234',
            'email' => 'superadmin@example.com',
            'role' => 'admin',
            'user_category' => 'super_admin',
            'primary_location_type' => 'headquarters',
            'access_level' => 6,
        ]);

        $response = $this->post('/login', [
            'login' => $user->email,
            'password' => $this->password,
            'role' => 'super_admin',
        ]);
        $response->assertRedirect('/mfa/setup');

        $this->completeMfaSetup($user);
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_when_role_does_not_match_user_record(): void
    {
        $user = $this->createUser([
            'name' => 'Role Mismatch Test',
            'service_number' => 'NIS/OF/8888',
            'email' => 'wrongrole@example.com',
        ]);

        $response = $this->from('/login')->post('/login', [
            'login' => $user->service_number,
            'password' => $this->password,
            'role' => 'state',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_state_user_can_access_user_dashboard(): void
    {
        $user = $this->createUser([
            'name' => 'State Dashboard User',
            'service_number' => 'NIS/OF/9999',
            'email' => 'state-dashboard@example.com',
        ]);

        $this->post('/login', $this->loginPayload($user))->assertRedirect('/mfa/setup');
        $this->completeMfaSetup($user);

        $response = $this->actingAs($user)->get('/user/dashboard');
        $response->assertOk();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = $this->createUser([
            'name' => 'Admin Dashboard User',
            'service_number' => 'NIS/AD/9999',
            'email' => 'admin-dashboard@example.com',
            'role' => 'admin',
            'user_category' => 'admin',
            'primary_location_type' => 'headquarters',
            'access_level' => 5,
        ]);

        $this->post('/login', [
            'login' => $user->email,
            'password' => $this->password,
            'role' => 'admin',
        ])->assertRedirect('/mfa/setup');
        $this->completeMfaSetup($user);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertOk();
    }
}
