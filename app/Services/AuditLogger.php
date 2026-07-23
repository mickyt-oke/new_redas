<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Request as RequestFacade;

class AuditLogger
{
    /**
     * Log a generic event.
     */
    public static function log(
        ?User $user,
        string $action,
        string $status = 'success',
        ?string $entityType = null,
        string|int|null $entityId = null,
        array $details = [],
        ?string $ip = null,
        ?string $location = null,
        ?string $userAgent = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId !== null ? (string) $entityId : null,
            'details' => $details,
            'ip_address' => $ip ?? self::ip(),
            'location' => $location,
            'user_agent' => $userAgent ?? self::userAgent(),
            'status' => $status,
            'created_at' => now(),
        ]);
    }

    /**
     * Log an authentication attempt.
     */
    public static function logAuthAttempt(
        ?User $user,
        string $status,
        array $details = [],
        ?string $ip = null,
        ?string $location = null
    ): AuditLog {
        return self::log(
            user: $user,
            action: 'auth.attempt',
            status: $status,
            entityType: 'user',
            entityId: $user?->id,
            details: $details,
            ip: $ip,
            location: $location
        );
    }

    /**
     * Log a blocked authentication or request due to ABAC geolocation policy.
     */
    public static function logAuthBlocked(
        ?User $user,
        string $reason,
        array $details = [],
        ?string $ip = null,
        ?string $location = null
    ): AuditLog {
        return self::log(
            user: $user,
            action: 'auth.blocked',
            status: 'blocked',
            entityType: 'user',
            entityId: $user?->id,
            details: array_merge(['reason' => $reason], $details),
            ip: $ip,
            location: $location
        );
    }

    /**
     * Log a successful logout.
     */
    public static function logLogout(User $user, ?string $ip = null): AuditLog
    {
        return self::log(
            user: $user,
            action: 'auth.logout',
            status: 'success',
            entityType: 'user',
            entityId: $user->id,
            ip: $ip
        );
    }

    /**
     * Log a CRUD operation.
     */
    public static function logCrud(
        User $user,
        string $action,
        string $entityType,
        string|int $entityId,
        array $details = []
    ): AuditLog {
        return self::log(
            user: $user,
            action: $action,
            status: 'success',
            entityType: $entityType,
            entityId: $entityId,
            details: $details
        );
    }

    /**
     * Log a system setting change.
     */
    public static function logSettingChange(
        User $user,
        string $key,
        mixed $oldValue,
        mixed $newValue
    ): AuditLog {
        return self::log(
            user: $user,
            action: 'settings.update',
            status: 'success',
            entityType: 'setting',
            entityId: $key,
            details: [
                'key' => $key,
                'old_value' => $oldValue,
                'new_value' => $newValue,
            ]
        );
    }

    private static function ip(): ?string
    {
        try {
            return RequestFacade::instance()?->ip() ?? ($_SERVER['REMOTE_ADDR'] ?? null);
        } catch (\Throwable) {
            return $_SERVER['REMOTE_ADDR'] ?? null;
        }
    }

    private static function userAgent(): ?string
    {
        try {
            return RequestFacade::instance()?->userAgent() ?? ($_SERVER['HTTP_USER_AGENT'] ?? null);
        } catch (\Throwable) {
            return $_SERVER['HTTP_USER_AGENT'] ?? null;
        }
    }
}
