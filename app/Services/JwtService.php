<?php

namespace App\Services;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    public function __construct(
        private readonly string $secret,
        private readonly string $issuer = 'nis-redas',
        private readonly int $accessTtlSeconds = 900, // 15 minutes
        private readonly int $refreshTtlSeconds = 2592000 // 30 days
    ) {
    }

    public static function fromEnv(): self
    {
        return new self(
            secret: (string) env('JWT_SECRET', ''),
            issuer: (string) env('JWT_ISSUER', 'nis-redas'),
            accessTtlSeconds: (int) env('JWT_ACCESS_TTL', 900),
            refreshTtlSeconds: (int) env('JWT_REFRESH_TTL', 2592000),
        );
    }

    public function issueAccessToken(int $userId, string $role, string $jti): string
    {
        $now = time();

        $payload = [
            'iss' => $this->issuer,
            'sub' => (string) $userId,
            'role' => $role,
            'type' => 'access',
            'jti' => $jti,
            'iat' => $now,
            'exp' => $now + $this->accessTtlSeconds,
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function issueRefreshToken(int $userId, string $role, string $jti): string
    {
        $now = time();

        $payload = [
            'iss' => $this->issuer,
            'sub' => (string) $userId,
            'role' => $role,
            'type' => 'refresh',
            'jti' => $jti,
            'iat' => $now,
            'exp' => $now + $this->refreshTtlSeconds,
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    /**
     * @return object{iss:string,sub:string,role:string,type:string,jti:string,iat:int,exp:int}
     */
    public function decode(string $jwt): object
    {
        return JWT::decode($jwt, new Key($this->secret, 'HS256'));
    }

    public function isExpired(string $jwt): bool
    {
        try {
            $this->decode($jwt);

            return false;
        } catch (ExpiredException) {
            return true;
        }
    }

    public function getSecret(): string
    {
        return $this->secret;
    }
}
