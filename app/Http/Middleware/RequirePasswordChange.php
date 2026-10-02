<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    /**
     * Routes a user with a pending password change may still reach, so they
     * can actually complete the change and log out.
     */
    private const EXEMPT_ROUTES = ['user.profile', 'user.profile.update', 'logout'];

    /**
     * Force a logged-in user with a pending password change to the profile
     * page, so an admin-assigned (or default first-login) password cannot be
     * used indefinitely.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user instanceof User
            && $user->must_change_password
            && ! $request->routeIs(...self::EXEMPT_ROUTES)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You must change your password before continuing.',
                ], 403);
            }

            return redirect()->route('user.profile')
                ->with('status', 'For your security, you must change your password before continuing.');
        }

        return $next($request);
    }
}
