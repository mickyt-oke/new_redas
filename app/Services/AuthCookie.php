<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Cookie;

/**
 * Centralised builder for authentication cookies.
 *
 * All auth cookies are Secure, HttpOnly and SameSite=Strict by default to
 * minimise XSS and CSRF exposure for a government data repository.
 */
final class AuthCookie
{
    private const DEFAULT_PATH = '/';

    private const SAME_SITE_STRICT = 'Strict';

    private const SAME_SITE_LAX = 'Lax';

    /**
     * Build a hardened authentication cookie.
     *
     * @param  string  $name  Cookie name
     * @param  string  $value  Cookie value
     * @param  int  $ttlMinutes  Lifetime in minutes; 0 creates a session cookie
     * @param  bool  $strictSameSite  Use Strict SameSite (Lax for CSRF/session cookie compatibility)
     */
    public static function make(
        string $name,
        string $value,
        int $ttlMinutes,
        bool $strictSameSite = true
    ): Cookie {
        return cookie(
            name: $name,
            value: $value,
            minutes: $ttlMinutes,
            path: self::DEFAULT_PATH,
            domain: null,
            secure: self::secure(),
            httpOnly: true,
            raw: false,
            sameSite: $strictSameSite ? self::SAME_SITE_STRICT : self::SAME_SITE_LAX
        );
    }

    /**
     * Build a cookie-clearing response cookie.
     */
    public static function forget(string $name, bool $strictSameSite = true): Cookie
    {
        return cookie(
            name: $name,
            value: '',
            minutes: -1,
            path: self::DEFAULT_PATH,
            domain: null,
            secure: self::secure(),
            httpOnly: true,
            raw: false,
            sameSite: $strictSameSite ? self::SAME_SITE_STRICT : self::SAME_SITE_LAX
        );
    }

    /**
     * Whether auth cookies should be marked Secure.
     *
     * Defaults to true outside the local environment so production/staging
     * always require HTTPS. Local dev may use plain HTTP.
     */
    private static function secure(): bool
    {
        return (bool) env('AUTH_COOKIE_SECURE', ! app()->environment('local'));
    }
}
