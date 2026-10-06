<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LockscreenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If the user is not authenticated, let other middleware (like Authenticate) handle it
        if (! Auth::check()) {
            return $next($request);
        }

        // If the session is marked as locked, redirect to the lockscreen
        if ($request->session()->get('session.locked')) {
            // Allow requests to the lockscreen itself and the unlock attempt to avoid infinite redirects
            if ($request->is('lockscreen*')) {
                return $next($request);
            }

            // Remember the page the user was trying to reach (GET navigations only)
            // so they are returned there once the session is unlocked.
            if ($request->isMethod('GET') && ! $request->expectsJson()) {
                $request->session()->put('url.intended', $request->fullUrl());
            }

            return redirect()->route('lockscreen.show');
        }

        return $next($request);
    }
}
