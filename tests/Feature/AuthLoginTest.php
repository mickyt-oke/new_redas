<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_login_with_service_number_and_role(): void
    {
        $user = User::create([
            'name' => 'Login Test User',
            'service_number' => '77777',
            'email' => 'login@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'access_level' => 0,
        ]);

        $response = $this->post('/login', [
            'login' => $user->service_number,
            'password' => 'Password123!',
            'role' => 'officer',
        ]);

        $response->assertRedirect('/user/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_super_admin_can_login_with_role_alias(): void
    {
        $user = User::create([
            'name' => 'Super Admin Test',
            'service_number' => '12345',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'admin',
            'user_category' => 'super_admin',
            'primary_location_type' => 'headquarters',
            'access_level' => 6,
        ]);

        $response = $this->post('/login', [
            'login' => $user->service_number,
            'password' => 'Password123!',
            'role' => 'super_admin',
        ]);

        $response->assertRedirect('/superadmin/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_when_role_does_not_match_user_record(): void
    {
        $user = User::create([
            'name' => 'Role Mismatch Test',
            'service_number' => '88888',
            'email' => 'wrongrole@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'access_level' => 0,
        ]);

        $response = $this->from('/login')->post('/login', [
            'login' => $user->service_number,
            'password' => 'Password123!',
            'role' => 'state',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_state_user_can_access_user_dashboard(): void
    {
        $user = User::create([
            'name' => 'State Dashboard User',
            'service_number' => '99991',
            'email' => 'state-dashboard@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'access_level' => 0,
        ]);

        $response = $this->actingAs($user)->get('/user/dashboard');

        $response->assertOk();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = User::create([
            'name' => 'Admin Dashboard User',
            'service_number' => '99992',
            'email' => 'admin-dashboard@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'admin',
            'user_category' => 'admin',
            'primary_location_type' => 'headquarters',
            'access_level' => 5,
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertOk();
    }

    public function test_visa_directorate_user_redirects_to_visa_dashboard(): void
    {
        $user = User::create([
            'name' => 'Visa Officer Test',
            'service_number' => '77778',
            'email' => 'visa-officer@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'directorate',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'VISA',
            'access_level' => 2,
        ]);

        $response = $this->post('/login', [
            'login' => $user->service_number,
            'password' => 'Password123!',
            'role' => 'directorate',
        ]);

        $response->assertRedirect('/user/directorate/visa/submissions');
        $this->assertAuthenticatedAs($user);
    }

    public function test_visa_directorate_admin_redirects_to_visa_dashboard_case_insensitive(): void
    {
        $user = User::create([
            'name' => 'Visa Admin Test',
            'service_number' => '88889',
            'email' => 'visa-admin@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'admin',
            'user_category' => 'directorate_admin',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'visa',
            'access_level' => 3,
        ]);

        $response = $this->post('/login', [
            'login' => $user->service_number,
            'password' => 'Password123!',
            'role' => 'admin',
        ]);

        $response->assertRedirect('/user/directorate/visa');
        $this->assertAuthenticatedAs($user);
    }

    public function test_other_directorate_user_redirects_to_directorate_home(): void
    {
        $user = User::create([
            'name' => 'HRM Officer Test',
            'service_number' => '77779',
            'email' => 'hrm-officer@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'directorate',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'HRM',
            'access_level' => 2,
        ]);

        $response = $this->post('/login', [
            'login' => $user->service_number,
            'password' => 'Password123!',
            'role' => 'directorate',
        ]);

        $response->assertRedirect('/user/directorate');
        $this->assertAuthenticatedAs($user);
    }
}
