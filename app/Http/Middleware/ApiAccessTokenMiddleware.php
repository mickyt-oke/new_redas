<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAccessTokenMiddleware
{
    private const ACCESS_ABILITY = 'access';

    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $this->resolveBearerToken($request);

        if (! $plainToken) {
            return $this->unauthorized('TOKEN_INVALID');
        }

        try {
            $accessToken = PersonalAccessToken::findToken($plainToken);
        } catch (Throwable) {
            return $this->unauthorized('TOKEN_INVALID');
        }

        if (! $accessToken) {
            return $this->unauthorized('TOKEN_INVALID');
        }

        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return $this->unauthorized('TOKEN_INVALID');
        }

        if (! in_array(self::ACCESS_ABILITY, $accessToken->abilities ?? [], true)) {
            return $this->unauthorized('TOKEN_INVALID');
        }

        /** @var User|null $user */
        $user = $accessToken->tokenable;

        if (! $user instanceof User) {
            return $this->unauthorized('TOKEN_REVOKED');
        }

        if ($user->is_enabled === false) {
            return $this->unauthorized('ACCOUNT_DISABLED');
        }

        if (! $user->email_verified_at) {
            return $this->unauthorized('EMAIL_NOT_VERIFIED');
        }

        // Persist the token so logout can revoke it without re-parsing.
        $request->attributes->set('api_access_token', $accessToken);

        Auth::guard('sanctum')->setUser($user);
        Auth::setUser($user);

        return $next($request);
    }

    /**
     * Resolve the bearer token from the Authorization header or the HttpOnly
     * access_token cookie.
     */
    private function resolveBearerToken(Request $request): ?string
    {
        $header = (string) $request->header('Authorization', '');

        if (str_starts_with($header, 'Bearer ')) {
            $token = trim(substr($header, 7));

            return $token !== '' ? $token : null;
        }

        $cookieToken = (string) $request->cookie('access_token');

        return $cookieToken !== '' ? $cookieToken : null;
    }

    private function unauthorized(string $message, int $status = 401): Response
    {
        return response()->json(['message' => $message], $status);
    }
}
