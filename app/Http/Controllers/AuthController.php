<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_number' => 'required|string|unique:users',
            'role' => 'required|in:admin,zonal,state,officer',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'accepted',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'service_number' => $validated['service_number'],
            'role' => $validated['role'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('login')->with('status', 'Registration successful. Please login with your credentials.');
    }

    /**
     * Login user and generate token.
     * Supports login by email OR service_number
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|in:admin,zonal,state,officer',
        ]);

        $loginField = $request->input('login');
        
        // Determine if login is email or service number
        $field = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'service_number';
        
        $user = User::where($field, $loginField)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->role !== $request->input('role')) {
            throw ValidationException::withMessages([
                'role' => ['Invalid role selected for this account.'],
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        return redirect($this->getRedirectUrl($user->role));
    }

    /**
     * Get redirect URL based on user role
     */
    private function getRedirectUrl(string $role): string
    {
        return match($role) {
            'admin' => '/admin/dashboard',
            'zonal' => '/dashboard/zonal',
            'state' => '/dashboard/state',
            'officer' => '/user/dashboard',
            default => '/home',
        };
    }

    /**
     * Logout user and revoke token.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Logged out successfully.');
    }

}
