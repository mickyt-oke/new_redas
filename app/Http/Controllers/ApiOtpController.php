<?php

namespace App\Http\Controllers;

use App\Models\OtpCode;
use App\Models\RefreshToken;
use App\Models\User;
use App\Services\JwtService;
use App\Services\Messaging\ResendMailService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ApiOtpController extends Controller
{
    /**
     * Build a Secure/HttpOnly/Strict SameSite auth cookie.
     */
    private function authCookie(string $name, string $value, int $ttlMinutes): \Symfony\Component\HttpFoundation\Cookie
    {
        return cookie($name, $value, $ttlMinutes, '/', null, true, true, false, 'Strict');
    }

    public function request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'in:login,verify_email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $loginInput = trim((string) $request->input('login'));
        $purpose = (string) $request->input('purpose');

        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'service_number';
        $normalizedLogin = $field === 'email' ? strtolower($loginInput) : $loginInput;

        /** @var User|null $user */
        $user = User::query()
            ->where([$field => $normalizedLogin])
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'login' => ['Account not found.'],
            ]);
        }

        if ($this->otpLockedUntil($user)?->isFuture()) {
            return $this->otpLockedResponse($user);
        }

        $ttlSeconds = (int) env('OTP_TTL_SECONDS', 300); // 5 minutes default

        // Exactly 4 digits, including leading zeros (e.g. 0007)
        $codeInt = random_int(0, 9999);
        $code = str_pad((string) $codeInt, 4, '0', STR_PAD_LEFT);
        $codeHash = hash('sha256', $code);

        // Create a fresh OTP row (simpler than rotation logic)
        $otp = OtpCode::create([
            'user_id' => $user->id,
            'purpose' => $purpose,
            'code_hash' => $codeHash,
            'expires_at' => Carbon::now()->addSeconds($ttlSeconds),
            'attempts' => 0,
            'consumed_at' => null,
        ]);

        // Send OTP email (template keys are configurable)
        $templateKey = $purpose === 'verify_email'
            ? 'otp_verify_email'
            : 'otp_login';

        try {
            $mailer = new ResendMailService;
            $mailer->sendFromTemplate(
                $templateKey,
                [
                    'name' => $user->name,
                    'otp' => $code,
                    'expires_in_minutes' => (int) ceil($ttlSeconds / 60),
                ],
                $user->email,
                $user->name
            );
        } catch (\Throwable $e) {
            Log::error('OTP email delivery failed', [
                'user_id' => $user->id,
                'purpose' => $purpose,
                'template' => $templateKey,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'We could not send your verification code right now. Please try again shortly.',
            ], 502);
        }

        return response()->json([
            'message' => 'OTP sent successfully.',
            'expires_at' => $otp->expires_at?->toIso8601String(),
        ], 200);
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'in:login,verify_email'],
            'otp' => ['required', 'string', 'regex:/^\d{4}$/'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $loginInput = trim((string) $request->input('login'));
        $purpose = (string) $request->input('purpose');
        $otpInput = (string) $request->input('otp');

        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'service_number';
        $normalizedLogin = $field === 'email' ? strtolower($loginInput) : $loginInput;

        /** @var User|null $user */
        $user = User::query()->where([$field => $normalizedLogin])->first();
        if (! $user) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        if ($user->is_enabled === false) {
            return response()->json(['message' => 'ACCOUNT_DISABLED'], 403);
        }

        if ($this->otpLockedUntil($user)?->isFuture()) {
            return $this->otpLockedResponse($user);
        }

        $codeHash = hash('sha256', $otpInput);

        /** @var OtpCode|null $otp */
        $otp = OtpCode::query()
            ->where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->where('code_hash', $codeHash)
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->orderByDesc('created_at')
            ->first();

        if (! $otp) {
            // Best-effort attempt increment (for the most recent active OTP for this purpose)
            $latest = OtpCode::query()
                ->where('user_id', $user->id)
                ->where('purpose', $purpose)
                ->whereNull('consumed_at')
                ->where('expires_at', '>', now())
                ->orderByDesc('created_at')
                ->first();

            if ($latest) {
                $latest->increment('attempts');
            }

            $this->recordFailedOtpAttempt($user, $purpose, $request->ip());

            if ($this->otpLockedUntil($user)?->isFuture()) {
                return $this->otpLockedResponse($user);
            }

            return response()->json(['message' => 'OTP_INVALID'], 401);
        }

        $maxAttempts = (int) env('OTP_MAX_ATTEMPTS', 5);
        if ((int) $otp->attempts >= $maxAttempts) {
            Log::warning('OTP max attempts exceeded', [
                'user_id' => $user->id,
                'purpose' => $purpose,
                'ip' => $request->ip(),
            ]);

            return response()->json(['message' => 'OTP_MAX_ATTEMPTS_EXCEEDED'], 403);
        }

        $otp->consumed_at = now();
        $otp->attempts = (int) $otp->attempts + 1;
        $otp->save();

        $this->resetOtpLockout($user);

        if ($purpose === 'login') {
            // No API password-change flow exists yet, so a pending forced
            // change must be completed on the web portal before a mobile/API
            // session can be issued — otherwise it would bypass the policy.
            if ($user->must_change_password) {
                return response()->json([
                    'message' => 'PASSWORD_CHANGE_REQUIRED',
                ], 403);
            }

            // Issue JWTs only after 2FA OTP verification
            $jti = (string) Str::uuid();

            $refreshToken = new RefreshToken([
                'user_id' => $user->id,
                'jti' => $jti,
                'token_hash' => '', // set below
                'expires_at' => now()->addSeconds((int) env('JWT_REFRESH_TTL', 2592000)),
                'revoked_at' => null,
                'created_ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
            ]);

            $refreshJwt = JwtService::fromEnv()->issueRefreshToken(
                userId: $user->id,
                role: $user->role,
                jti: $jti
            );

            $refreshToken->token_hash = hash('sha256', $refreshJwt);
            $refreshToken->save();

            $accessJwt = JwtService::fromEnv()->issueAccessToken(
                userId: $user->id,
                role: $user->role,
                jti: (string) Str::uuid()
            );

            $accessTtlMinutes = (int) ceil((int) env('JWT_ACCESS_TTL', 900) / 60);
            $refreshTtlMinutes = (int) ceil((int) env('JWT_REFRESH_TTL', 2592000) / 60);
            $refreshExpiresAt = now()->addSeconds((int) env('JWT_REFRESH_TTL', 2592000));

            return response()->json([
                'message' => 'OTP verified successfully.',
                'user_id' => $user->id,
                'access_token' => $accessJwt,
                'token_type' => 'bearer',
                'expires_in' => (int) env('JWT_ACCESS_TTL', 900),
                'refresh_expires_in' => (int) env('JWT_REFRESH_TTL', 2592000),
                'refresh_expires_at' => $refreshExpiresAt->toDateTimeString(),
            ], 200)
                ->withCookie($this->authCookie('access_token', $accessJwt, $accessTtlMinutes))
                ->withCookie($this->authCookie('refresh_token', $refreshJwt, $refreshTtlMinutes));
        }

        // verify_email (and other purposes) currently just consume OTP
        return response()->json([
            'message' => 'OTP verified successfully.',
            'user_id' => $user->id,
        ], 200);
    }

    /**
     * Return the user's OTP lockout timestamp, if any.
     */
    private function otpLockedUntil(User $user): ?Carbon
    {
        return $user->otp_locked_until;
    }

    /**
     * Build the standardized OTP lockout JSON response.
     */
    private function otpLockedResponse(User $user): JsonResponse
    {
        $seconds = (int) now()->diffInSeconds($user->otp_locked_until);

        return response()->json([
            'message' => 'Account temporarily locked due to too many failed verification attempts.',
            'retry_after_seconds' => max(0, $seconds),
        ], 403);
    }

    /**
     * Record a failed OTP verification attempt and lock the account if the
     * threshold has been reached.
     */
    private function recordFailedOtpAttempt(User $user, string $purpose, ?string $ip): void
    {
        $maxAttempts = (int) env('OTP_MAX_ATTEMPTS', 5);
        $lockoutMinutes = (int) env('OTP_LOCKOUT_MINUTES', 15);

        $attempts = (int) $user->otp_failed_attempts + 1;

        if ($attempts >= $maxAttempts) {
            $user->forceFill([
                'otp_failed_attempts' => 0,
                'otp_locked_until' => Carbon::now()->addMinutes($lockoutMinutes),
            ])->save();

            Log::warning('OTP account locked after max attempts', [
                'user_id' => $user->id,
                'purpose' => $purpose,
                'ip' => $ip,
                'otp_locked_until' => $user->otp_locked_until,
            ]);

            return;
        }

        $user->forceFill([
            'otp_failed_attempts' => $attempts,
        ])->save();
    }

    /**
     * Clear any OTP lockout state after a successful verification.
     */
    private function resetOtpLockout(User $user): void
    {
        if ($user->otp_failed_attempts !== 0 || $user->otp_locked_until !== null) {
            $user->forceFill([
                'otp_failed_attempts' => 0,
                'otp_locked_until' => null,
            ])->save();
        }
    }
}
