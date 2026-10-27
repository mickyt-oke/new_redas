<?php

namespace App\Models;

use App\Services\SubmissionWorkflow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

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
        'retention_expires_at',
    ];

    protected $casts = [
        'return_data' => 'array',
        'workflow_path' => 'array',
        'retention_expires_at' => 'datetime',
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

    public function reviewComments(): HasMany
    {
        return $this->hasMany(ApplicationComment::class)->oldest();
    }

    /**
     * Scope submissions that are awaiting review at the given workflow stage.
     */
    public function scopeAwaiting($query, string $stage)
    {
        return $query->where('workflow_stage', $stage)
            ->where('status', SubmissionWorkflow::STATUS_PENDING);
    }

    /**
     * Scope submissions that are still open (not finally approved or returned).
     */
    public function scopeOpen($query)
    {
        return $query->where('status', SubmissionWorkflow::STATUS_PENDING);
    }
}