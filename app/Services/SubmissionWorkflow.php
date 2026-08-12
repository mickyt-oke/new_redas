<?php

namespace App\Services;

use App\Models\Application;
use App\Models\User;

class SubmissionWorkflow
{
    public const CATEGORY_STATE = 'state';
    public const CATEGORY_DIRECTORATE = 'directorate';

    public const STAGE_SUBMITTED = 'submitted';
    public const STAGE_DESK_REVIEW = 'desk_review';
    public const STAGE_ZONAL_REVIEW = 'zonal_review';
    public const STAGE_DIRECTORATE_REVIEW = 'directorate_review';
    public const STAGE_HQ_REVIEW = 'hq_review';
    public const STAGE_ADMIN_REVIEW = 'admin_review';
    public const STAGE_APPROVED = 'approved';

    /**
     * Create a new submission and place it in the correct initial review queue.
     */
    public static function create(User $user, array $returnData): Application
    {
        $category = self::categoryForUser($user);
        $scopeCode = self::scopeCodeForUser($user);

        $initialStage = $category === self::CATEGORY_DIRECTORATE
            ? self::STAGE_DIRECTORATE_REVIEW
            : self::STAGE_DESK_REVIEW;

        return Application::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'workflow_stage' => $initialStage,
            'workflow_path' => [
                [
                    'stage' => self::STAGE_SUBMITTED,
                    'by' => $user->id,
                    'at' => now()->toDateTimeString(),
                ],
            ],
            'category' => $category,
            'scope_code' => $scopeCode,
            'return_data' => $returnData,
        ]);
    }

    /**
     * Resolve the submission category for a user account.
     */
    public static function categoryForUser(User $user): string
    {
        return $user->user_category === 'directorate_user'
            ? self::CATEGORY_DIRECTORATE
            : self::CATEGORY_STATE;
    }

    /**
     * Resolve the scope code used to tag a submission to the right reviewer.
     * For state users this is the state code; for directorate users it is the directorate slug.
     */
    public static function scopeCodeForUser(User $user): ?string
    {
        if ($user->user_category === 'directorate_user') {
            return $user->directorateSlug();
        }

        return $user->primary_location_code ?: $user->assigned_state_code ?: null;
    }

    /**
     * Resolve the review stage this approver is responsible for.
     */
    public static function stageForApprover(User $user): ?string
    {
        return match (true) {
            $user->user_category === 'desk_admin' => self::STAGE_DESK_REVIEW,
            $user->user_category === 'directorate_admin' => self::STAGE_DIRECTORATE_REVIEW,
            $user->user_category === 'zonal_commander' => self::STAGE_ZONAL_REVIEW,
            $user->user_category === 'admin' => self::STAGE_HQ_REVIEW,
            $user->user_category === 'super_admin' => self::STAGE_ADMIN_REVIEW,
            default => null,
        };
    }

    /**
     * Resolve the scope code this approver is allowed to review.
     */
    public static function scopeCodeForApprover(User $user): ?string
    {
        return match (true) {
            $user->user_category === 'desk_admin' => $user->primary_location_code ?: $user->assigned_state_code ?: null,
            $user->user_category === 'directorate_admin' => $user->directorateSlug(),
            $user->user_category === 'zonal_commander' => $user->primary_location_code ?: $user->assigned_zonal_command_code ?: null,
            default => null,
        };
    }

    /**
     * Build a query for submissions currently sitting in the approver's queue.
     */
    public static function pendingQueryForApprover(User $user)
    {
        $stage = self::stageForApprover($user);

        if ($stage === null) {
            return Application::query()->whereRaw('1 = 0');
        }

        $query = Application::query()
            ->with('user')
            ->where('workflow_stage', $stage)
            ->whereNotIn('status', ['approved', 'rejected']);

        $scopeCode = self::scopeCodeForApprover($user);

        if ($scopeCode !== null) {
            if ($user->user_category === 'zonal_commander') {
                $query->where(function ($q) use ($scopeCode) {
                    $q->where('zonal_code', $scopeCode)
                        ->orWhere(function ($inner) use ($scopeCode) {
                            $inner->whereNull('zonal_code')
                                ->where('scope_code', $scopeCode);
                        });
                });
            } else {
                $query->where('scope_code', $scopeCode);
            }
        }

        return $query;
    }

    /**
     * Approve a submission and advance it to the next workflow stage.
     */
    public static function approve(Application $application, User $actor): void
    {
        $nextStage = self::nextStage($application);

        $path = $application->workflow_path ?? [];
        $path[] = [
            'stage' => $nextStage,
            'by' => $actor->id,
            'at' => now()->toDateTimeString(),
            'action' => 'approved',
        ];

        $update = [
            'workflow_stage' => $nextStage,
            'status' => $nextStage === self::STAGE_APPROVED ? 'approved' : 'pending',
            'workflow_path' => $path,
            'last_action_by' => $actor->id,
        ];

        // When a desk admin releases a state submission, tag it with the zonal command
        // it should be routed to so the correct zonal commander sees it.
        if ($actor->user_category === 'desk_admin') {
            $zonalCode = $actor->assigned_zonal_command_code ?: null;
            if ($zonalCode !== null) {
                $update['zonal_code'] = $zonalCode;
            }
        }

        $application->update($update);
    }

    /**
     * Reject / return a submission to the originating officer for correction.
     */
    public static function reject(Application $application, User $actor, ?string $comment = null): void
    {
        $path = $application->workflow_path ?? [];
        $path[] = [
            'stage' => self::STAGE_SUBMITTED,
            'by' => $actor->id,
            'at' => now()->toDateTimeString(),
            'action' => 'rejected',
        ];

        $application->update([
            'workflow_stage' => self::STAGE_SUBMITTED,
            'status' => 'returned',
            'comments' => $comment,
            'workflow_path' => $path,
            'last_action_by' => $actor->id,
        ]);
    }

    /**
     * Determine the next stage for a submission based on its current stage.
     */
    public static function nextStage(Application $application): string
    {
        return self::nextStageFromStage($application->workflow_stage);
    }

    /**
     * Determine the next stage for a submission based on a given stage.
     */
    public static function nextStageFromStage(string $stage): string
    {
        return match ($stage) {
            self::STAGE_DESK_REVIEW => self::STAGE_ZONAL_REVIEW,
            self::STAGE_ZONAL_REVIEW => self::STAGE_HQ_REVIEW,
            self::STAGE_DIRECTORATE_REVIEW => self::STAGE_HQ_REVIEW,
            self::STAGE_HQ_REVIEW => self::STAGE_ADMIN_REVIEW,
            self::STAGE_ADMIN_REVIEW => self::STAGE_APPROVED,
            default => self::STAGE_APPROVED,
        };
    }
}
