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
            AuditLogger::log(
                user: $request->user(),
                action: 'security.suspicious_response',
                status: 'blocked',
                entityType: 'http_request',
                details: [
                    'status' => $status,
                    'path' => $request->path(),
                    'method' => $request->method(),
                ],
                ip: $request->ip() ?? '0.0.0.0',
            );
        }

        return $response;
    }
}
