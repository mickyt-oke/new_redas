<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LockscreenTest extends TestCase
{
    use RefreshDatabase;

    private string $password = 'Password123!';

    private function createStateUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => 'Lockscreen Test User',
            'service_number' => 'NIS/LK/0001',
            'email' => 'lockscreen-test@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make($this->password),
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'access_level' => 0,
        ], $overrides));
    }

    public function test_lock_route_rejects_get_requests(): void
    {
        $user = $this->createStateUser();

        // The bug: the "Lock Session" button navigated with GET, which the
        // POST-only route rejected with a 405 MethodNotAllowedHttpException.
        $this->actingAs($user)->get('/lockscreen/lock')->assertStatus(405);
    }

    public function test_form_lock_redirects_to_lockscreen_and_marks_session_locked(): void
    {
        $user = $this->createStateUser();

        $response = $this->actingAs($user)->post(route('lockscreen.lock'));

        $response->assertRedirect(route('lockscreen.show'));
        $response->assertSessionHas('session.locked', true);
    }

    public function test_ajax_lock_returns_json(): void
    {
        $user = $this->createStateUser();

        $response = $this->actingAs($user)->postJson(route('lockscreen.lock'));

        $response->assertOk();
        $response->assertJson(['status' => 'locked']);
        $response->assertSessionHas('session.locked', true);
    }

    public function test_lockscreen_redirects_when_session_is_not_locked(): void
    {
        $user = $this->createStateUser();

        $this->actingAs($user)
            ->get(route('lockscreen.show'))
            ->assertRedirect('/user/dashboard');
    }

    public function test_lockscreen_is_shown_when_session_is_locked(): void
    {
        $user = $this->createStateUser();

        $this->actingAs($user)
            ->withSession(['session' => ['locked' => true]])
            ->get(route('lockscreen.show'))
            ->assertOk()
            ->assertSee('Session Locked');
    }

    public function test_locked_session_is_redirected_away_from_protected_pages(): void
    {
        $user = $this->createStateUser();

        $this->actingAs($user)
            ->withSession(['session' => ['locked' => true]])
            ->get('/user/profile')
            ->assertRedirect(route('lockscreen.show'));
    }

    public function test_unlock_with_lockscreen_passcode_returns_to_intended_page(): void
    {
        $user = $this->createStateUser([
            'lockscreen_passcode' => Hash::make('4321'),
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'session' => ['locked' => true],
                'url' => ['intended' => '/user/profile'],
            ])
            ->post(route('lockscreen.unlock'), ['passcode' => '4321']);

        $response->assertRedirect('/user/profile');
        $response->assertSessionMissing('session.locked');

        // The intended URL is consumed, so a later request is not bounced again.
        $this->get('/user/profile')->assertOk();
    }

    public function test_unlock_falls_back_to_account_password_when_no_passcode_is_set(): void
    {
        $user = $this->createStateUser(['lockscreen_passcode' => null]);

        $response = $this->actingAs($user)
            ->withSession(['session' => ['locked' => true]])
            ->post(route('lockscreen.unlock'), ['passcode' => $this->password]);

        $response->assertRedirect('/user/dashboard');
        $response->assertSessionMissing('session.locked');
    }

    public function test_unlock_with_wrong_passcode_fails_and_keeps_session_locked(): void
    {
        $user = $this->createStateUser([
            'lockscreen_passcode' => Hash::make('4321'),
        ]);

        $response = $this->actingAs($user)
            ->withSession(['session' => ['locked' => true]])
            ->post(route('lockscreen.unlock'), ['passcode' => '0000']);

        $response->assertSessionHasErrors('passcode');
        $response->assertSessionHas('session.locked', true);
    }

    public function test_header_renders_lock_session_action_as_a_post_form(): void
    {
        $user = $this->createStateUser();

        $response = $this->actingAs($user)->get('/user/profile');

        $response->assertOk();
        $response->assertSee('action="'.route('lockscreen.lock').'"', false);
        $response->assertSee('method="POST"', false);
    }
}
