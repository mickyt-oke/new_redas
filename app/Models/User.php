<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'service_number',
        'role',
        'user_category',
        'primary_location_type',
        'primary_location_code',
        'assigned_state_code',
        'assigned_directorate_code',
        'assigned_cgis_unit_code',
        'assigned_desk_admin_code',
        'assigned_zonal_command_code',
        'geo_state',
        'access_level',
        'email',
        'password',
        'is_enabled',
        'mfa_secret',
        'mfa_enabled',
        'mfa_verified_at',
        'mfa_backup_codes',
        'mfa_last_used_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'mfa_secret',
        'mfa_backup_codes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'access_level' => 'integer',
        'is_enabled' => 'boolean',
        'mfa_enabled' => 'boolean',
        'mfa_verified_at' => 'datetime',
        'mfa_last_used_at' => 'datetime',
        'mfa_secret' => 'encrypted',
        'mfa_backup_codes' => 'encrypted:array',
    ];

    public const ACCESS_CATEGORIES = [
        'state_user',
        'desk_admin',
        'directorate_user',
        'directorate_admin',
        'zonal_commander',
        'admin',
        'super_admin',
    ];

    public const LOCATION_TYPES = [
        'state',
        'zonal',
        'directorate',
        'headquarters',
    ];

    public const ROLE_TYPES = [
        'user',
        'officer',
        'admin',
        'state',
        'zonal',
        'directorate',
        'super_admin',
    ];

    public function hasCategory(string ...$categories): bool
    {
        return in_array($this->user_category, $categories, true);
    }

    public function hasLocationType(string ...$locationTypes): bool
    {
        return in_array($this->primary_location_type, $locationTypes, true);
    }

    public function hasRoleType(string ...$roleTypes): bool
    {
        return in_array($this->role, $roleTypes, true);
    }

    public function hasLegacyRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function hasMinimumAccessLevel(int $level): bool
    {
        return (int) $this->access_level >= $level;
    }

    /**
     * Return the geographic state this user is expected to authenticate from.
     * Falls back to the primary location code for state users when no explicit geo_state is set.
     */
    public function requiredGeoState(): ?string
    {
        if ($this->primary_location_type === 'directorate') {
            return null;
        }

        if (filled($this->geo_state)) {
            return strtoupper($this->geo_state);
        }

        if ($this->primary_location_type === 'state' && filled($this->primary_location_code)) {
            return strtoupper($this->primary_location_code);
        }

        return null;
    }

    /**
     * Whether this user is a headquarters / national-level user that can optionally bypass
     * geolocation enforcement when the HQ bypass setting is enabled.
     */
    public function isHeadquartersUser(): bool
    {
        return in_array($this->primary_location_type, ['headquarters'], true)
            || in_array($this->user_category, ['admin', 'super_admin'], true);
    }

    /**
     * Whether the user's geographic state is explicitly enforced.
     * Enforced when a required state exists and the user is not an HQ-only user.
     */
    public function isGeoStateEnforced(): bool
    {
        return $this->requiredGeoState() !== null
            && $this->primary_location_type !== 'headquarters';
    }

    /**
     * Whether the user has configured a TOTP secret.
     */
    public function hasMfaSecret(): bool
    {
        return filled($this->mfa_secret);
    }

    /**
     * Whether MFA is fully enabled and verified for this user.
     */
    public function isMfaEnabled(): bool
    {
        return $this->mfa_enabled === true && $this->hasMfaSecret();
    }

    /**
     * Whether the user is partway through MFA setup (has a secret but not verified).
     */
    public function isMfaSetupPending(): bool
    {
        return $this->hasMfaSecret() && ! $this->mfa_enabled;
    }

    /**
     * Whether this account is a directorate-level user (user or admin).
     */
    public function isDirectorateAccount(): bool
    {
        return $this->primary_location_type === 'directorate'
            || in_array($this->user_category, ['directorate_user', 'directorate_admin'], true);
    }

    /**
     * Return the canonical directorate slug assigned to this account.
     * Falls back through assigned_directorate_code and primary_location_code.
     * Handles both the legacy 3-letter codes and the full view slugs.
     */
    public function directorateSlug(): ?string
    {
        $code = $this->assigned_directorate_code ?: $this->primary_location_code;

        if (! filled($code)) {
            return null;
        }

        $normalised = strtolower((string) $code);

        $map = [
            'hrm' => 'hrm',
            'prs' => 'prs',
            'fin' => 'finance',
            'finance' => 'finance',
            'inv' => 'investigation',
            'investigation' => 'investigation',
            'pas' => 'passport',
            'passport' => 'passport',
            'vis' => 'visa',
            'visa' => 'visa',
            'mig' => 'migration',
            'migration' => 'migration',
            'bor' => 'border',
            'border' => 'border',
            'ict' => 'ict',
            'wks' => 'works-logistics',
            'works-logistics' => 'works-logistics',
            'works' => 'works-logistics',
        ];

        return $map[$normalised] ?? null;
    }
}
