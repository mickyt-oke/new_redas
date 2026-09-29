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
     * Force a logged-in user with a pending password change to the profile
     * page, so an admin-assigned password cannot be used indefinitely.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user instanceof User
            && $user->must_change_password
            && ! $request->routeIs('user.profile', 'user.profile.update', 'logout')) {
            return redirect()->route('user.profile')
                ->with('status', 'For your security, you must change your password before continuing.');
        }

        return $next($request);
    }
}
