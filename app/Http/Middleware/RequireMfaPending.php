<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireMfaPending
{
    /**
     * Ensure the request has a pending MFA user in the session.
     */
    public function handle(Request $request, Closure $next, string ...$constraints): Response
    {
        $id = $request->session()->get('mfa.pending_user_id');

        if (! is_numeric($id) || ! User::find($id, ['*']) instanceof User) {
            $request->session()->forget([
                'mfa.pending_user_id',
                'mfa.location',
                'mfa.remember',
                'mfa.redirect_url',
                'mfa.setup_verified',
            ]);

            return redirect()->route('login');
        }

        return $next($request);
    }
}
