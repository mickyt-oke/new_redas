<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'access' => \App\Http\Middleware\CheckAccess::class,
            'abac.geo' => \App\Http\Middleware\AbacGeolocationMiddleware::class,
            'mfa.pending' => \App\Http\Middleware\RequireMfaPending::class,
        ]);

        $middleware->appendToGroup('web', \App\Http\Middleware\RequirePasswordChange::class);

        // Runs for both web and api requests to flag unusual/blocked traffic.
        $middleware->append(\App\Http\Middleware\LogSuspiciousActivity::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Session/CSRF expiry (419): send the user back to where they were
        // instead of the bare framework error page. Form drafts live in
        // localStorage and reload automatically once the page is revisited.
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            Log::warning('Session/CSRF token expired', [
                'path' => $request->path(),
                'ip' => $request->ip(),
                'user_id' => $request->user()?->id,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your session has expired. Please log in again and resubmit.',
                ], 419);
            }

            return redirect()->back()
                ->with('error', 'Your session expired due to inactivity. Please log in again — any in-progress draft was saved automatically in your browser.');
        });

        // Log every unhandled exception on API routes with request context,
        // in addition to Laravel's normal error reporting. Guarded because
        // no HTTP request is bound when exceptions are reported from the
        // console (artisan commands, queue workers, etc).
        $exceptions->report(function (\Throwable $e) {
            if (! app()->bound('request') || app()->runningInConsole()) {
                return;
            }

            $request = request();
            if ($request->is('api/*')) {
                Log::error('API exception', [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'path' => $request->path(),
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                    'user_id' => $request->user()?->id,
                ]);
            }
        });
    })->create();
