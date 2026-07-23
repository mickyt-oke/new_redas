<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\JwtAccessTokenMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiNotificationController;
use App\Http\Controllers\ApiOtpController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);

// OTP (public; no JWT required)
Route::post('/otp/request', [ApiOtpController::class, 'request']);
Route::post('/otp/verify', [ApiOtpController::class, 'verify']);

// JWT auth routes
Route::post('/refresh', [ApiAuthController::class, 'refresh']);

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
