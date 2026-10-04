<?php

namespace App\Http\Middleware;

use App\Services\NisFormationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSpecialCommand
{
    /**
     * Ensure the authenticated user is provisioned to a special command.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! NisFormationService::isSpecialCommandCode($user->primary_location_code)) {
            abort(403, 'This area is restricted to special command accounts.');
        }

        return $next($request);
    }
}
