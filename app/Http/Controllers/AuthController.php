<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_SECONDS = 60;

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $request->merge([
            'service_number' => strtoupper((string) $request->input('service_number')),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_number' => ['required', 'string', 'regex:/^NIS\/[A-Z]{2}\/[0-9]{4}$/', 'unique:users'],
            'role' => 'required|in:admin,zonal,state,officer,directorate',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'accepted',
        ], [
            'service_number.regex' => 'Service number must be in the format NIS/XX/1234.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'service_number' => $validated['service_number'],
            'role' => $validated['role'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('login')->with('status', 'Registration successful. Please login with your credentials.');
    }

    /**
     * Login user.
     * Supports login by email OR service_number
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string|max:255',
            'password' => 'required|string',
            'role' => 'required|in:admin,zonal,state,officer,directorate',
        ]);

        $loginInput = trim((string) $request->input('login'));
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'service_number';
        $normalizedLogin = $field === 'email' ? Str::lower($loginInput) : $loginInput;
        $throttleKey = $this->throttleKey($normalizedLogin, $request);

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_LOGIN_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'login' => ["Too many login attempts. Try again in {$seconds} seconds."],
            ]);
        }

        $user = User::query()->where([$field => $normalizedLogin])->first();

        if (!$user || !Hash::check($request->input('password'), $user->password) || $user->role !== $request->input('role')) {
            RateLimiter::hit($throttleKey, self::LOCKOUT_SECONDS);

            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        RateLimiter::clear($throttleKey);
        Auth::login($user, $request->boolean('remember'));

        return redirect($this->getRedirectUrl($user->role));
    }

    /**
     * Get redirect URL based on user role
     */
    private function getRedirectUrl(string $role): string
    {
        return match($role) {
            'admin'       => '/admin/dashboard',
            'zonal'       => '/dashboard/zonal',
            'state'       => '/dashboard/state',
            'officer'     => '/user/dashboard',
            'directorate' => '/user/directorate',
            default       => '/home',
        };
    }

    /**
     * Build a unique throttle key for login attempts.
     */
    private function throttleKey(string $login, Request $request): string
    {
        return Str::lower($login).'|'.$request->ip();
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Logged out successfully.');
    }

}
