<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LockscreenController extends Controller
{
    /**
     * Show the lockscreen. Only meaningful while the session is locked.
     */
    public function show(Request $request)
    {
        if (! $request->session()->get('session.locked')) {
            return redirect($this->redirectUrlForUser($request->user()));
        }

        return view('auth.lockscreen');
    }

    /**
     * Lock the current session.
     *
     * Responds with JSON for AJAX callers (the idle timer in app.js) and with a
     * redirect for regular form submissions (the "Lock Session" menu item), so
     * the user lands on the lockscreen instead of seeing a raw JSON payload.
     */
    public function lock(Request $request): JsonResponse|RedirectResponse
    {
        $request->session()->put('session.locked', true);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'locked']);
        }

        return redirect()->route('lockscreen.show');
    }

    /**
     * Unlock the session using the lockscreen passcode (or the account
     * password when no lockscreen passcode has been configured).
     */
    public function unlock(Request $request)
    {
        $request->validate([
            'passcode' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! $user instanceof User) {
            return redirect()->route('login');
        }

        $passcode = (string) $request->input('passcode');

        $isValid = $user->lockscreen_passcode
            ? Hash::check($passcode, $user->lockscreen_passcode)
            : Hash::check($passcode, $user->password);

        if (! $isValid) {
            throw ValidationException::withMessages([
                'passcode' => ['The provided passcode is incorrect.'],
            ]);
        }

        $request->session()->forget('session.locked');

        return redirect()->intended($this->redirectUrlForUser($user));
    }

    /**
     * Resolve the landing page for a user, mirroring AuthController's mapping.
     */
    private function redirectUrlForUser(?User $user): string
    {
        return match ($user?->user_category) {
            'super_admin' => '/superadmin/dashboard',
            'admin', 'hq_admin' => '/admin/dashboard',
            'zonal_commander' => '/zonal/dashboard',
            'desk_admin', 'directorate_admin', 'cgis_desk_admin' => '/desk-admin/dashboard',
            'directorate_user' => '/user/directorates/dashboard',
            'cgis_unit_user' => '/user/cgis-units/dashboard',
            'state_user' => '/user/dashboard',
            'special_command_user' => '/special-commands/dashboard',
            'zone_user' => '/user/zones/dashboard',
            default => '/',
        };
    }
}
