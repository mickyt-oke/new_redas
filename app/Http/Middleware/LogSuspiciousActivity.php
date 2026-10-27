<?php

namespace App\Http\Middleware;

use App\Services\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Logs responses with security-relevant status codes (unauthorized, forbidden,
 * expired session/CSRF, rate limited) to the audit log so repeated/unusual
 * patterns from a single IP or account can be detected.
 */
class LogSuspiciousActivity
{
    private const WATCHED_STATUSES = [401, 403, 419, 429];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $status = $response->getStatusCode();

        if (in_array($status, self::WATCHED_STATUSES, true)) {
            $this->logOncePerWindow($request, $status);
        }

        return $response;
    }

    /**
     * Aggregate suspicious responses so the audit log cannot be flooded by
     * an unauthenticated attacker probing random paths. One entry per minute
     * per IP + status + path is enough for detection while keeping cost bounded.
     */
    private function logOncePerWindow(Request $request, int $status): void
    {
        $ip = $request->ip() ?? '0.0.0.0';
        $path = $request->path();
        $key = 'audit:suspicious:'.hash('sha256', $ip.'|'.$status.'|'.$path);

        if (\Illuminate\Support\Facades\Cache::has($key)) {
            return;
        }

        \Illuminate\Support\Facades\Cache::put($key, true, now()->addMinute());

        AuditLogger::log(
            user: $request->user(),
            action: 'security.suspicious_response',
            status: 'blocked',
            entityType: 'http_request',
            details: [
                'status' => $status,
                'path' => $path,
                'method' => $request->method(),
            ],
            ip: $ip,
        );
    }
}
