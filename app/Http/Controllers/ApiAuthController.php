<?php

namespace App\Http\Controllers;

use App\Models\OtpCode;
use App\Models\RefreshToken;
use App\Models\User;
use App\Services\AuthCookie;
use App\Services\Messaging\ResendMailService;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAuthController extends Controller
{
    private TokenService $tokenService;

    public function __construct()
    {
        $this->tokenService = TokenService::fromConfig();
    }

    public function login(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $loginInput = trim((string) $request->input('login'));
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'service_number';
        $normalizedLogin = $field === 'email' ? Str::lower($loginInput) : $loginInput;

        /** @var User|null $user */
        $user = User::query()
            ->where([$field => $normalizedLogin])
            ->first();

        if (! $user || ! Hash::check((string) $request->input('password'), $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        if ($user->is_enabled === false) {
            return response()->json([
                'message' => 'ACCOUNT_DISABLED',
            ], 403);
        }

        if (! $user->email_verified_at) {
            return response()->json([
                'message' => 'EMAIL_NOT_VERIFIED',
            ], 403);
        }

        $ttlSeconds = config('auth.otp.ttl_seconds');

        $otp = (string) random_int(100000, 999999);
        $codeHash = Hash::make($otp);

        OtpCode::create([
            'user_id' => $user->id,
            'purpose' => 'login',
            'code_hash' => $codeHash,
            'expires_at' => now()->addSeconds($ttlSeconds),
            'attempts' => 0,
            'consumed_at' => null,
        ]);

        $mailer = new ResendMailService;

        try {
            $mailer->sendFromTemplate(
                'otp_login',
                [
                    'name' => $user->name,
                    'otp' => $otp,
                    'expires_in_minutes' => (int) ceil($ttlSeconds / 60),
                ],
                $user->email,
                $user->name
            );
        } catch (Throwable $e) {
            Log::error('Login OTP email delivery failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'We could not send your verification code right now. Please try again shortly.',
            ], 502);
        }

        return response()->json([
            'message' => 'OTP_REQUIRED',
            'expires_at' => now()->addSeconds($ttlSeconds)->toIso8601String(),
            'purpose' => 'login',
        ], 200);
    }

    public function refresh(Request $request): Response
    {
        $refreshTokenPlain = $request->cookie('refresh_token');

        if (! $refreshTokenPlain) {
            return response()->json([
                'message' => 'SESSION_EXPIRED',
            ], 401);
        }

        $rt = $this->tokenService->findRefreshToken($refreshTokenPlain);

        if (! $rt) {
            return response()->json([
                'message' => 'TOKEN_INVALID',
            ], 401);
        }

        if ($rt->revoked_at) {
            $this->tokenService->revokeAllRefreshTokens($rt->user);

            return response()->json([
                'message' => 'SECURITY_BREACH_DETECTED',
            ], 401);
        }

        if ($rt->expires_at->isPast()) {
            return response()->json([
                'message' => 'TOKEN_EXPIRED',
            ], 401);
        }

        $user = $rt->user;

        if (! $user instanceof User) {
            return response()->json(['message' => 'TOKEN_INVALID'], 401);
        }

        if ($user->is_enabled === false) {
            return response()->json(['message' => 'ACCOUNT_DISABLED'], 401);
        }

        if (! $user->email_verified_at) {
            return response()->json(['message' => 'EMAIL_NOT_VERIFIED'], 401);
        }

        // Rotation: revoke the old refresh token and issue a new one.
        $newRefresh = $this->tokenService->rotateRefreshToken($rt, $request);
        $access = $this->tokenService->issueAccessToken($user);

        return $this->tokenResponse(
            $user,
            $access['plainTextToken'],
            $newRefresh['plainTextToken'],
            $access['expiresAt'],
            $newRefresh['expiresAt']
        );
    }

    public function logout(Request $request): Response
    {
        $refreshTokenPlain = (string) $request->cookie('refresh_token');

        if ($refreshTokenPlain) {
            $rt = $this->tokenService->findRefreshToken($refreshTokenPlain);

            if ($rt && ! $rt->revoked_at && $rt->user_id) {
                $rt->revoked_at = now();
                $rt->save();
            }
        }

        // Revoke the access token if it was resolved by the API middleware.
        $accessToken = $request->attributes->get('api_access_token');
        if ($accessToken) {
            $accessToken->delete();
        }

        return response()->json(['message' => 'Logged out'], 200)
            ->withCookie(AuthCookie::forget('access_token'))
            ->withCookie(AuthCookie::forget('refresh_token'));
    }

    /**
     * Build a consistent token-issuing JSON response with Secure/HttpOnly cookies.
     */
    private function tokenResponse(
        User $user,
        string $accessToken,
        string $refreshToken,
        \DateTimeInterface $accessExpiresAt,
        \DateTimeInterface $refreshExpiresAt
    ): Response {
        $accessTtlMinutes = (int) ceil($accessExpiresAt->getTimestamp() - now()->getTimestamp()) / 60;
        $refreshTtlMinutes = (int) ceil($refreshExpiresAt->getTimestamp() - now()->getTimestamp()) / 60;

        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'bearer',
            'expires_in' => max(0, $accessExpiresAt->getTimestamp() - now()->getTimestamp()),
            'refresh_expires_in' => max(0, $refreshExpiresAt->getTimestamp() - now()->getTimestamp()),
            'refresh_expires_at' => $refreshExpiresAt->format('Y-m-d H:i:s'),
        ])
            ->withCookie(AuthCookie::make('access_token', $accessToken, max(1, $accessTtlMinutes)))
            ->withCookie(AuthCookie::make('refresh_token', $refreshToken, max(1, $refreshTtlMinutes)));
    }
}
