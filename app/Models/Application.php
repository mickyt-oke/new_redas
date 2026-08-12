<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'workflow_stage',
        'workflow_path',
        'category',
        'scope_code',
        'zonal_code',
        'return_data',
        'comments',
        'supervisor_id',
        'last_action_by',
    ];

    protected $casts = [
        'return_data' => 'array',
        'workflow_path' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function lastActionBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_action_by');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope submissions that are awaiting review at the given workflow stage.
     */
    public function scopeAwaiting($query, string $stage)
    {
        return $query->where('workflow_stage', $stage)
            ->whereNotIn('status', ['approved', 'rejected']);
    }
}