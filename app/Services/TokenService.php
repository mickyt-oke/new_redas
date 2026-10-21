<?php

namespace App\Services;

use App\Models\RefreshToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Sanctum\NewAccessToken;

/**
 * Token lifecycle manager for the JSON API.
 *
 * - Access tokens are short-lived Laravel Sanctum personal access tokens
 *   (hashed at rest, instantly revocable, scoped to the `access` ability).
 * - Refresh tokens are long-lived opaque tokens stored in the `refresh_tokens`
 *   table with rotation, reuse detection, IP/UA metadata and revocation.
 */
class TokenService
{
    private const ACCESS_ABILITY = 'access';

    public function __construct(
        private readonly int $accessTtlSeconds = 900,
        private readonly int $refreshTtlSeconds = 2592000,
    ) {
    }

    /**
     * Build a service instance from application configuration.
     */
    public static function fromConfig(): self
    {
        return new self(
            accessTtlSeconds: (int) config('sanctum.access_ttl', env('SANCTUM_ACCESS_TTL', 900)),
            refreshTtlSeconds: (int) config('sanctum.refresh_ttl', env('SANCTUM_REFRESH_TTL', 2592000)),
        );
    }

    /**
     * Issue a short-lived Sanctum access token for a user.
     *
     * @return array{plainTextToken: string, token: \Laravel\Sanctum\PersonalAccessToken, expiresAt: Carbon}
     */
    public function issueAccessToken(User $user): array
    {
        $expiresAt = Carbon::now()->addSeconds($this->accessTtlSeconds);

        /** @var NewAccessToken $newToken */
        $newToken = $user->createToken(
            name: 'api-access',
            abilities: [self::ACCESS_ABILITY],
            expiresAt: $expiresAt
        );

        return [
            'plainTextToken' => $newToken->plainTextToken,
            'token' => $newToken->accessToken,
            'expiresAt' => $expiresAt,
        ];
    }

    /**
     * Revoke all API access tokens for a user.
     */
    public function revokeAccessTokens(User $user): void
    {
        $user->tokens()
            ->whereJsonContains('abilities', self::ACCESS_ABILITY)
            ->delete();
    }

    /**
     * Revoke a single access token by its plain text value.
     */
    public function revokeAccessTokenByPlainText(User $user, string $plainTextToken): void
    {
        [$id, $token] = explode('|', $plainTextToken, 2) + [null, null];

        if (! is_numeric($id) || ! $token) {
            return;
        }

        $accessToken = $user->tokens()->find($id);

        if ($accessToken && hash_equals($accessToken->token, hash('sha256', $token))) {
            $accessToken->delete();
        }
    }

    /**
     * Issue a long-lived refresh token and persist its hash/metadata.
     *
     * @return array{plainTextToken: string, refreshToken: RefreshToken, expiresAt: Carbon}
     */
    public function issueRefreshToken(User $user, Request $request): array
    {
        $plainToken = $this->generateOpaqueToken();
        $tokenHash = hash('sha256', $plainToken);
        $expiresAt = Carbon::now()->addSeconds($this->refreshTtlSeconds);

        $refreshToken = RefreshToken::create([
            'user_id' => $user->id,
            'jti' => (string) Str::uuid(),
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
            'revoked_at' => null,
            'created_ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        return [
            'plainTextToken' => $plainToken,
            'refreshToken' => $refreshToken,
            'expiresAt' => $expiresAt,
        ];
    }

    /**
     * Rotate a refresh token: revoke the old one and issue a new one.
     *
     * @return array{plainTextToken: string, refreshToken: RefreshToken, expiresAt: Carbon}
     */
    public function rotateRefreshToken(RefreshToken $oldToken, Request $request): array
    {
        $oldToken->revoked_at = Carbon::now();
        $oldToken->save();

        return $this->issueRefreshToken($oldToken->user, $request);
    }

    /**
     * Revoke every refresh token for a user (security breach response).
     */
    public function revokeAllRefreshTokens(User $user): void
    {
        RefreshToken::query()
            ->where('user_id', $user->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => Carbon::now()]);
    }

    /**
     * Look up a refresh token by its plain text value.
     */
    public function findRefreshToken(string $plainToken): ?RefreshToken
    {
        $hash = hash('sha256', $plainToken);

        return RefreshToken::query()
            ->where('token_hash', $hash)
            ->first();
    }

    /**
     * Generate a cryptographically secure opaque token.
     */
    private function generateOpaqueToken(): string
    {
        return Str::random(64);
    }
}
