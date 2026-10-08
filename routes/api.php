<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ApiNotificationController;
use App\Http\Controllers\ApiOtpController;
use App\Http\Middleware\JwtAccessTokenMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:api')->group(function () {
    // Public routes
    Route::post('/login', [ApiAuthController::class, 'login'])->middleware('throttle:10,1');

    // OTP (public; no JWT required) — tightly throttled, a 4-digit code only
    // has 10,000 possibilities so brute force must be rate-limited here too.
    Route::post('/otp/request', [ApiOtpController::class, 'request'])->middleware('throttle:5,1');
    Route::post('/otp/verify', [ApiOtpController::class, 'verify'])->middleware('throttle:10,1');

    // JWT auth routes
    Route::post('/refresh', [ApiAuthController::class, 'refresh'])->middleware('throttle:10,1');

    Route::middleware(JwtAccessTokenMiddleware::class)->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Notifications
        Route::get('/notifications', [ApiNotificationController::class, 'index']);
        Route::get('/notifications/count', [ApiNotificationController::class, 'count']);
        Route::post('/notifications/mark-all-read', [ApiNotificationController::class, 'markAllRead']);
        Route::post('/notifications/{id}/read', [ApiNotificationController::class, 'markRead']);

        Route::post('/logout', [ApiAuthController::class, 'logout']);
    });
});
