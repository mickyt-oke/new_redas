<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_self_service_registration_is_disabled(): void
    {
        // Registration is managed by HQ administrators. The public form is gone:
        // visitors are redirected to login with an explanatory notice.
        $response = $this->get('/register');

        $response->assertRedirect('/login');
        $response->assertSessionHas('status');

        // There is no public POST endpoint for registration.
        $this->post('/register', [
            'name' => 'Jane Doe',
            'service_number' => 'NIS/ADM/1234',
            'role' => 'officer',
            'email' => 'jane@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'terms' => 'on',
        ])->assertStatus(405);

        $this->assertDatabaseMissing('users', [
            'email' => 'jane@example.com',
        ]);
    }
}
