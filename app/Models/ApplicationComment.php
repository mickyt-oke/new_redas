<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationComment extends Model
{
    public const ACTION_SUBMITTED = 'submitted';
    public const ACTION_RESUBMITTED = 'resubmitted';
    public const ACTION_APPROVED = 'approved';
    public const ACTION_REJECTED = 'rejected';
    public const ACTION_NOTE = 'note';

    protected $fillable = [
        'application_id',
        'user_id',
        'stage',
        'action',
        'comment',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
