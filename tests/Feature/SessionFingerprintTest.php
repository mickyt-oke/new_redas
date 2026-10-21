<?php

namespace Tests\Feature;

use App\Http\Middleware\ValidateSessionFingerprint;
use App\Models\User;
use App\Services\MfaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SessionFingerprintTest extends TestCase
{
    use RefreshDatabase;

    private string $password = 'Password123!';

    private function createUser(): User
    {
        return User::factory()->create([
            'service_number' => 'NIS/FP/0001',
            'email' => 'fingerprint@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make($this->password),
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'access_level' => 0,
        ]);
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

    public function test_session_fingerprint_is_stored_on_login(): void
    {
        $user = $this->createUser();

        $this->post('/login', [
            'login' => $user->service_number,
            'password' => $this->password,
            'role' => $user->role,
        ])->assertRedirect('/mfa/setup');

        $this->completeMfaSetup($user);

        $this->assertAuthenticatedAs($user);
        $this->assertTrue(session()->has(ValidateSessionFingerprint::SESSION_KEY));
    }

    public function test_fingerprint_mismatch_invalidates_session(): void
    {
        $user = $this->createUser();

        $this->post('/login', [
            'login' => $user->service_number,
            'password' => $this->password,
            'role' => $user->role,
        ])->assertRedirect('/mfa/setup');

        $this->completeMfaSetup($user);

        // Tamper with the stored fingerprint.
        session()->put(ValidateSessionFingerprint::SESSION_KEY, 'tampered-hash');

        $this->get('/user/dashboard')
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_fingerprint_check_can_be_disabled(): void
    {
        config(['app.env' => 'testing']);
        putenv('SESSION_FINGERPRINT_ENFORCED=false');

        $user = $this->createUser();

        $this->post('/login', [
            'login' => $user->service_number,
            'password' => $this->password,
            'role' => $user->role,
        ])->assertRedirect('/mfa/setup');

        $this->completeMfaSetup($user);

        session()->put(ValidateSessionFingerprint::SESSION_KEY, 'tampered-hash');

        $this->get('/user/dashboard')->assertOk();

        putenv('SESSION_FINGERPRINT_ENFORCED=true');
    }
}
