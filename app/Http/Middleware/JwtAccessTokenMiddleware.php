<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class JwtAccessTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = (string) $request->header('Authorization', '');
        if (! str_starts_with($header, 'Bearer ')) {
            return response()->json(['message' => 'TOKEN_INVALID'], 401);
        }

        $token = trim(substr($header, 7));

        try {
            $jwt = JwtService::fromEnv();
            $decoded = $jwt->decode($token);
        } catch (Throwable) {
            return response()->json(['message' => 'TOKEN_INVALID'], 401);
        }

        if (($decoded->type ?? null) !== 'access') {
            return response()->json(['message' => 'TOKEN_INVALID'], 401);
        }

        $userId = (int) ($decoded->sub ?? 0);
        if (! $userId) {
            return response()->json(['message' => 'TOKEN_INVALID'], 401);
        }

        $user = User::query()->find($userId);
        if (! $user) {
            return response()->json(['message' => 'TOKEN_REVOKED'], 401);
        }

        Auth::setUser($user);

        return $next($request);
    }
}
