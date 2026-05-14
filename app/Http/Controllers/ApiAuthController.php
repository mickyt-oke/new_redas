<?php

namespace App\Http\Controllers;

use App\Models\RefreshToken;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAuthController extends Controller
{
    private function jwt(): JwtService
    {
        return JwtService::fromEnv();
    }

    private function decodeToken(string $token): object
    {
        return $this->jwt()->decode($token);
    }

    public function login(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'role' => ['required', 'in:admin,zonal,state,officer,directorate'],
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
            ->where('role', $request->input('role'))
            ->first();

        if (! $user || ! Hash::check((string) $request->input('password'), $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

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

        $refreshJwt = $this->jwt()->issueRefreshToken(
            userId: $user->id,
            role: $user->role,
            jti: $jti
        );

        $refreshToken->token_hash = hash('sha256', $refreshJwt);
        $refreshToken->save();

        $accessJwt = $this->jwt()->issueAccessToken(
            userId: $user->id,
            role: $user->role,
            jti: (string) Str::uuid()
        );

        return response()->json([
            'access_token' => $accessJwt,
            'refresh_token' => $refreshJwt,
            'token_type' => 'bearer',
            'expires_in' => (int) env('JWT_ACCESS_TTL', 900),
        ]);
    }

    public function refresh(Request $request): Response
    {
        $refreshTokenJwt = (string) $request->input('refresh_token');

        if (! $refreshTokenJwt) {
            return response()->json([
                'message' => 'refresh_token is required',
            ], 422);
        }

        try {
            $decoded = $this->jwt()->decode($refreshTokenJwt);
        } catch (Throwable) {
            return response()->json([
                'message' => 'TOKEN_INVALID',
            ], 401);
        }

        if (($decoded->type ?? null) !== 'refresh') {
            return response()->json([
                'message' => 'TOKEN_INVALID',
            ], 401);
        }

        $userId = (int) ($decoded->sub ?? 0);
        $jti = (string) ($decoded->jti ?? '');

        if (! $userId || ! $jti) {
            return response()->json([
                'message' => 'TOKEN_INVALID',
            ], 401);
        }

        /** @var RefreshToken|null $rt */
        $rt = RefreshToken::query()
            ->where('jti', $jti)
            ->where('user_id', $userId)
            ->first();

        if (! $rt || $rt->revoked_at || $rt->expires_at->isPast()) {
            return response()->json([
                'message' => 'TOKEN_REVOKED',
            ], 401);
        }

        // Rotation hardening: revoke old refresh token, then issue a new one
        $rt->revoked_at = now();
        $rt->save();

        $newJti = (string) Str::uuid();

        $newRefreshJwt = $this->jwt()->issueRefreshToken(
            userId: $userId,
            role: (string) ($decoded->role ?? ''),
            jti: $newJti
        );

        $newRt = RefreshToken::create([
            'user_id' => $userId,
            'jti' => $newJti,
            'token_hash' => hash('sha256', $newRefreshJwt),
            'expires_at' => now()->addSeconds(env('JWT_REFRESH_TTL', 2592000)),
            'revoked_at' => null,
            'created_ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        $user = User::query()->find($userId);
        if (! $user) {
            return response()->json(['message' => 'TOKEN_INVALID'], 401);
        }

        $accessJwt = $this->jwt()->issueAccessToken(
            userId: $user->id,
            role: $user->role,
            jti: (string) Str::uuid()
        );

        return response()->json([
            'access_token' => $accessJwt,
            'refresh_token' => $newRefreshJwt,
            'token_type' => 'bearer',
            'expires_in' => (int) env('JWT_ACCESS_TTL', 900),
        ]);
    }

    public function logout(Request $request): Response
    {
        $refreshTokenJwt = (string) $request->input('refresh_token');
        if (! $refreshTokenJwt) {
            return response()->json(['message' => 'OK'], 200);
        }

        try {
            $decoded = $this->jwt()->decode($refreshTokenJwt);
        } catch (Throwable) {
            return response()->json(['message' => 'OK'], 200);
        }

        if (($decoded->type ?? null) !== 'refresh') {
            return response()->json(['message' => 'OK'], 200);
        }

        $userId = (int) ($decoded->sub ?? 0);
        $jti = (string) ($decoded->jti ?? '');

        if (! $userId || ! $jti) {
            return response()->json(['message' => 'OK'], 200);
        }

        $hash = hash('sha256', $refreshTokenJwt);

        RefreshToken::query()
            ->where('user_id', $userId)
            ->where('jti', $jti)
            ->where('token_hash', $hash)
            ->update(['revoked_at' => now()]);

        return response()->json(['message' => 'Logged out'], 200);
    }
}

