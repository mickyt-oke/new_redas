<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_only_see_users_from_their_own_directorate()
    {
        // Creator Admin
        $visaAdmin = User::create([
            'name' => 'Visa Admin',
            'email' => 'vadmin@example.com',
            'service_number' => '10009',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'user_category' => 'directorate_admin',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'VISA',
            'access_level' => 5,
        ]);

        // Same directorate user
        $visaOfficer = User::create([
            'name' => 'Visa Officer',
            'email' => 'vofficer@example.com',
            'service_number' => '12345',
            'password' => bcrypt('password123'),
            'role' => 'officer',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'VISA',
            'access_level' => 3,
        ]);

        // Different directorate user
        $ictOfficer = User::create([
            'name' => 'ICT Officer',
            'email' => 'iofficer@example.com',
            'service_number' => '54321',
            'password' => bcrypt('password123'),
            'role' => 'officer',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'ICT',
            'access_level' => 3,
        ]);

        $response = $this->actingAs($visaAdmin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('Visa Officer');
        $response->assertDontSee('ICT Officer');
    }

    public function test_admin_can_create_user_which_auto_assigns_location_details()
    {
        $visaAdmin = User::create([
            'name' => 'Visa Admin',
            'email' => 'vadmin@example.com',
            'service_number' => '10009',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'user_category' => 'directorate_admin',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'VISA',
            'access_level' => 5,
        ]);

        $response = $this->actingAs($visaAdmin)->post('/admin/users', [
            'name' => 'New Visa Officer',
            'email' => 'nvofficer@example.com',
            'service_number' => '99999',
            'password' => 'password123',
            'role' => 'officer',
        ]);

        $response->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', [
            'name' => 'New Visa Officer',
            'email' => 'nvofficer@example.com',
            'service_number' => '99999',
            'role' => 'officer',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'VISA',
            'access_level' => 3,
        ]);
    }

    public function test_creating_user_with_invalid_service_number_fails_validation()
    {
        $visaAdmin = User::create([
            'name' => 'Visa Admin',
            'email' => 'vadmin@example.com',
            'service_number' => '10009',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'user_category' => 'directorate_admin',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'VISA',
            'access_level' => 5,
        ]);

        // 6 digits (invalid)
        $response = $this->actingAs($visaAdmin)->from('/admin/users/create')->post('/admin/users', [
            'name' => 'New Officer',
            'email' => 'nofficer@example.com',
            'service_number' => '123456',
            'password' => 'password123',
            'role' => 'officer',
        ]);

        $response->assertSessionHasErrors('service_number');
    }
}
