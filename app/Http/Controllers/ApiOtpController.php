<?php

namespace App\Http\Controllers;

use App\Models\OtpCode;
use App\Models\User;
use App\Services\AuthCookie;
use App\Services\Messaging\ResendMailService;
use App\Services\MfaService;
use App\Services\TokenService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ApiOtpController extends Controller
{
    private TokenService $tokenService;

    public function __construct()
    {
        $this->tokenService = TokenService::fromConfig();
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

        $codeInt = random_int(0, 9999);
        $code = str_pad((string) $codeInt, 4, '0', STR_PAD_LEFT);
        $codeHash = hash('sha256', $code);

        OtpCode::create([
            'user_id' => $user->id,
            'purpose' => $purpose,
            'code_hash' => $codeHash,
            'expires_at' => Carbon::now()->addSeconds($ttlSeconds),
            'attempts' => 0,
            'consumed_at' => null,
        ]);

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
            'expires_at' => Carbon::now()->addSeconds($ttlSeconds)->toIso8601String(),
        ], 200);
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'in:login,verify_email'],
            'otp' => ['required', 'string', 'regex:/^\d{4}$/'],
            'totp' => ['nullable', 'string', 'max:32'],
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
            if ($user->must_change_password) {
                return response()->json([
                    'message' => 'PASSWORD_CHANGE_REQUIRED',
                ], 403);
            }

            $mfaResponse = $this->ensureMfaSatisfied($user, $request);
            if ($mfaResponse !== null) {
                return $mfaResponse;
            }

            $access = $this->tokenService->issueAccessToken($user);
            $refresh = $this->tokenService->issueRefreshToken($user, $request);

            $accessTtlMinutes = (int) ceil($access['expiresAt']->diffInSeconds(now()) / 60);
            $refreshTtlMinutes = (int) ceil($refresh['expiresAt']->diffInSeconds(now()) / 60);

            return response()->json([
                'message' => 'OTP verified successfully.',
                'user_id' => $user->id,
                'access_token' => $access['plainTextToken'],
                'token_type' => 'bearer',
                'expires_in' => max(0, $access['expiresAt']->diffInSeconds(now())),
                'refresh_expires_in' => max(0, $refresh['expiresAt']->diffInSeconds(now())),
                'refresh_expires_at' => $refresh['expiresAt']->toDateTimeString(),
            ], 200)
                ->withCookie(AuthCookie::make('access_token', $access['plainTextToken'], max(1, $accessTtlMinutes)))
                ->withCookie(AuthCookie::make('refresh_token', $refresh['plainTextToken'], max(1, $refreshTtlMinutes)));
        }

        return response()->json([
            'message' => 'OTP verified successfully.',
            'user_id' => $user->id,
        ], 200);
    }

    private function otpLockedUntil(User $user): ?Carbon
    {
        return $user->otp_locked_until;
    }

    private function otpLockedResponse(User $user): JsonResponse
    {
        $seconds = (int) now()->diffInSeconds($user->otp_locked_until);

        return response()->json([
            'message' => 'Account temporarily locked due to too many failed verification attempts.',
            'retry_after_seconds' => max(0, $seconds),
        ], 403);
    }

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

    private function resetOtpLockout(User $user): void
    {
        if ($user->otp_failed_attempts !== 0 || $user->otp_locked_until !== null) {
            $user->forceFill([
                'otp_failed_attempts' => 0,
                'otp_locked_until' => null,
            ])->save();
        }
    }

    /**
     * Enforce the enrolled second factor before issuing API tokens.
     *
     * If the user has MFA enabled, a valid TOTP code or single-use backup code
     * must be supplied in the same request. Missing/invalid codes return a
     * 401 challenge so the client can prompt for the second factor.
     */
    private function ensureMfaSatisfied(User $user, Request $request): ?JsonResponse
    {
        if (! $user->isMfaEnabled()) {
            return null;
        }

        $totp = (string) $request->input('totp', '');

        if ($totp === '') {
            return response()->json([
                'message' => 'MFA_REQUIRED',
                'methods' => ['totp', 'backup_code'],
            ], 401);
        }

        if (! app(MfaService::class)->verifyCode($user, $totp)) {
            return response()->json([
                'message' => 'MFA_INVALID',
                'methods' => ['totp', 'backup_code'],
            ], 401);
        }

        return null;
    }
}
