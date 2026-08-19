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
        'access_level',
        'email',
        'password',
        'profile_picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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

    public function applications()
    {
        return $this->hasMany(Application::class, 'user_id');
    }
}
