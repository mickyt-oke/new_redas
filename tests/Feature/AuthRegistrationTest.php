<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase
{
    use RefreshDatabase;
    public function test_users_can_register_with_a_supported_role_selection(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Jane Doe',
            'service_number' => '12345',
            'role' => 'officer',
            'email' => 'jane@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'terms' => 'on',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'service_number' => '12345',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'role' => 'officer',
        ]);
    }
}
