<?php

namespace App\Http\Controllers;

use App\Models\OtpCode;
use App\Models\User;
use App\Services\Messaging\ResendMailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\JwtService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ApiOtpController extends Controller
{
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

        $ttlSeconds = (int) env('OTP_TTL_SECONDS', 300); // 5 minutes default
        $maxAttempts = (int) env('OTP_MAX_ATTEMPTS', 5);

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

        $mailer = new ResendMailService();
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

            return response()->json(['message' => 'OTP_INVALID'], 401);
        }

        $maxAttempts = (int) env('OTP_MAX_ATTEMPTS', 5);
        if ((int) $otp->attempts >= $maxAttempts) {
            return response()->json(['message' => 'OTP_MAX_ATTEMPTS_EXCEEDED'], 403);
        }

        $otp->consumed_at = now();
        $otp->attempts = (int) $otp->attempts + 1;
        $otp->save();

        if ($purpose === 'login') {
            // Issue JWTs only after 2FA OTP verification
            $jti = (string) \Illuminate\Support\Str::uuid();

            $refreshToken = new \App\Models\RefreshToken([
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
                jti: (string) \Illuminate\Support\Str::uuid()
            );

            return response()->json([
                'message' => 'OTP verified successfully.',
                'user_id' => $user->id,
                'access_token' => $accessJwt,
                'refresh_token' => $refreshJwt,
                'token_type' => 'bearer',
                'expires_in' => (int) env('JWT_ACCESS_TTL', 900),
            ], 200);
        }

        // verify_email (and other purposes) currently just consume OTP
        return response()->json([
            'message' => 'OTP verified successfully.',
            'user_id' => $user->id,
        ], 200);
    }
}
