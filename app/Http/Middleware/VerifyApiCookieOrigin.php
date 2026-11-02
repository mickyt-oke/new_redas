<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiCookieOrigin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $next($request);
        }

        if (! $request->cookies->has('access_token') && ! $request->cookies->has('refresh_token')) {
            return $next($request);
        }

        $origin = $request->headers->get('Origin');

        // Non-browser API clients may omit Origin; browser requests include it.
        if ($origin !== null && ! $this->isSameOrigin($origin, $request->getSchemeAndHttpHost())) {
            return response()->json(['message' => 'Cross-origin cookie authentication is not allowed.'], 403);
        }

        return $next($request);
    }

    private function isSameOrigin(string $origin, string $requestOrigin): bool
    {
        $originParts = parse_url($origin);
        $requestParts = parse_url($requestOrigin);

        if (! is_array($originParts) || ! is_array($requestParts)) {
            return false;
        }

        foreach (['user', 'pass', 'path', 'query', 'fragment'] as $component) {
            if (array_key_exists($component, $originParts)) {
                return false;
            }
        }

        $originScheme = strtolower((string) ($originParts['scheme'] ?? ''));
        $requestScheme = strtolower((string) ($requestParts['scheme'] ?? ''));
        $originPort = $originParts['port'] ?? ($originScheme === 'https' ? 443 : 80);
        $requestPort = $requestParts['port'] ?? ($requestScheme === 'https' ? 443 : 80);

        return $originScheme === $requestScheme
            && strtolower((string) ($originParts['host'] ?? '')) === strtolower((string) ($requestParts['host'] ?? ''))
            && $originPort === $requestPort;
    }
}
