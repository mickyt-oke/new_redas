<?php

namespace App\Services;

use App\Events\SubmissionCreated;
use App\Events\SubmissionRejected;
use App\Events\SubmissionStageAdvanced;
use App\Models\Application;
use App\Models\ApplicationComment;
use App\Models\User;
use App\Models\UserNotification;

class SubmissionWorkflow
{
    public const CATEGORY_STATE = 'state';
    public const CATEGORY_DIRECTORATE = 'directorate';
    public const CATEGORY_CGIS = 'cgis';

    public const STAGE_SUBMITTED = 'submitted';
    public const STAGE_DESK_REVIEW = 'desk_review';
    public const STAGE_ZONAL_REVIEW = 'zonal_review';
    public const STAGE_DIRECTORATE_REVIEW = 'directorate_review';
    public const STAGE_CGIS_DESK_REVIEW = 'cgis_desk_review';
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

        $application = Application::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'workflow_stage' => self::initialStageForUser($user),
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

        self::recordComment($application, $user, self::STAGE_SUBMITTED, ApplicationComment::ACTION_SUBMITTED);
        self::notifyPendingReviewers($application);
        SubmissionCreated::dispatch($application);

        return $application;
    }

    /**
     * Resolve the first review stage a submission from this user enters.
     */
    public static function initialStageForUser(User $user): string
    {
        return match (self::categoryForUser($user)) {
            self::CATEGORY_DIRECTORATE => self::STAGE_DIRECTORATE_REVIEW,
            self::CATEGORY_CGIS => self::STAGE_CGIS_DESK_REVIEW,
            default => self::STAGE_DESK_REVIEW,
        };
    }

    /**
     * Resolve the submission category for a user account.
     */
    public static function categoryForUser(User $user): string
    {
        return match ($user->user_category) {
            'directorate_user' => self::CATEGORY_DIRECTORATE,
            'cgis_unit_user' => self::CATEGORY_CGIS,
            default => self::CATEGORY_STATE,
        };
    }

    /**
     * Resolve the scope code used to tag a submission to the right reviewer.
     * For state users this is the state code; for directorate users it is the
     * directorate slug; for CGIS unit users it is the unit slug.
     */
    public static function scopeCodeForUser(User $user): ?string
    {
        if ($user->user_category === 'directorate_user') {
            return $user->directorateSlug();
        }

        if ($user->user_category === 'cgis_unit_user') {
            return $user->cgisUnitSlug();
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
            $user->user_category === 'cgis_desk_admin' => self::STAGE_CGIS_DESK_REVIEW,
            $user->user_category === 'zonal_commander' => self::STAGE_ZONAL_REVIEW,
            $user->user_category === 'hq_admin' => self::STAGE_HQ_REVIEW,
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
            $user->user_category === 'cgis_desk_admin' => $user->cgisUnitSlug(),
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
    public static function approve(Application $application, User $actor, ?string $comment = null): void
    {
        $currentStage = $application->workflow_stage;
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

        self::recordComment($application, $actor, $currentStage, ApplicationComment::ACTION_APPROVED, $comment);

        if ($nextStage === self::STAGE_APPROVED) {
            self::notifySubmitter(
                $application,
                'Return approved',
                'Your return RET-' . str_pad((string) $application->id, 5, '0', STR_PAD_LEFT) . ' has completed the review workflow and is approved.'
            );
        } else {
            self::notifySubmitter(
                $application,
                'Return advanced',
                'Your return RET-' . str_pad((string) $application->id, 5, '0', STR_PAD_LEFT) . ' was approved at ' . str_replace('_', ' ', $currentStage) . ' and moved to ' . str_replace('_', ' ', $nextStage) . '.'
            );
            self::notifyPendingReviewers($application);
        }

        SubmissionStageAdvanced::dispatch($application, $nextStage);
    }

    /**
     * Reject / return a submission to the originating officer for correction.
     */
    public static function reject(Application $application, User $actor, ?string $comment = null): void
    {
        $currentStage = $application->workflow_stage;

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

        self::recordComment($application, $actor, $currentStage, ApplicationComment::ACTION_REJECTED, $comment);

        self::notifySubmitter(
            $application,
            'Return returned for correction',
            'Your return RET-' . str_pad((string) $application->id, 5, '0', STR_PAD_LEFT) . ' was sent back at ' . str_replace('_', ' ', $currentStage) . '.' . ($comment ? ' Reason: ' . $comment : '')
        );

        SubmissionRejected::dispatch($application, $comment);
    }

    /**
     * Whether the given user may delete this submission: the owner, while the
     * return has not been finally approved (pending or returned for correction).
     */
    public static function deletableBy(Application $application, User $user): bool
    {
        return $application->user_id === $user->id
            && $application->status !== 'approved';
    }

    /**
     * Persist a workflow comment to the submission's review history.
     */
    public static function recordComment(Application $application, User $actor, string $stage, string $action, ?string $comment = null): void
    {
        if ($comment === null && in_array($action, [ApplicationComment::ACTION_APPROVED, ApplicationComment::ACTION_NOTE], true)) {
            return;
        }

        ApplicationComment::create([
            'application_id' => $application->id,
            'user_id' => $actor->id,
            'stage' => $stage,
            'action' => $action,
            'comment' => $comment,
        ]);
    }

    /**
     * Notify every enabled reviewer responsible for the submission's current stage.
     */
    public static function notifyPendingReviewers(Application $application): void
    {
        $stage = $application->workflow_stage;

        $query = User::query()->where('is_enabled', true);

        switch ($stage) {
            case self::STAGE_DESK_REVIEW:
                $query->where('user_category', 'desk_admin')
                    ->where(fn ($q) => $q->where('primary_location_code', $application->scope_code)
                        ->orWhere('assigned_state_code', $application->scope_code));
                break;
            case self::STAGE_ZONAL_REVIEW:
                $zone = $application->zonal_code ?: null;
                if ($zone === null) {
                    return;
                }
                $query->where('user_category', 'zonal_commander')
                    ->where(fn ($q) => $q->where('primary_location_code', $zone)
                        ->orWhere('assigned_zonal_command_code', $zone));
                break;
            case self::STAGE_DIRECTORATE_REVIEW:
                $query->where('user_category', 'directorate_admin');
                break;
            case self::STAGE_CGIS_DESK_REVIEW:
                $query->where('user_category', 'cgis_desk_admin');
                break;
            case self::STAGE_HQ_REVIEW:
                $query->where('user_category', 'hq_admin');
                break;
            default:
                return;
        }

        $ref = 'RET-' . str_pad((string) $application->id, 5, '0', STR_PAD_LEFT);

        foreach ($query->get() as $approver) {
            if (self::scopeCodeForApprover($approver) !== null
                && self::scopeCodeForApprover($approver) !== ($approver->user_category === 'zonal_commander' ? ($application->zonal_code ?: $application->scope_code) : $application->scope_code)) {
                continue;
            }

            UserNotification::create([
                'user_id' => $approver->id,
                'type' => 'workflow',
                'title' => 'Return awaiting your review',
                'description' => "Return {$ref} is awaiting " . str_replace('_', ' ', $stage) . '.',
                'tag' => 'review',
                'action_url' => self::reviewUrlForApprover($approver, $application),
                'payload_json' => ['application_id' => $application->id, 'stage' => $stage],
            ]);
        }
    }

    /**
     * Notify the submission owner about a workflow event on their return.
     */
    public static function notifySubmitter(Application $application, string $title, string $description): void
    {
        $owner = $application->user;
        if ($owner === null) {
            return;
        }

        UserNotification::create([
            'user_id' => $owner->id,
            'type' => 'workflow',
            'title' => $title,
            'description' => $description,
            'tag' => 'return',
            'action_url' => self::submitterUrl($owner, $application),
            'payload_json' => ['application_id' => $application->id, 'status' => $application->status],
        ]);
    }

    /**
     * Best-effort landing page for an approver opening a notification.
     */
    private static function reviewUrlForApprover(User $approver, Application $application): ?string
    {
        try {
            return match ($approver->user_category) {
                'desk_admin', 'directorate_admin', 'cgis_desk_admin' => route('desk.admin.submissions.show', $application),
                'hq_admin' => route('admin.submissions'),
                'zonal_commander' => route('user.zonal.home'),
                default => null,
            };
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Best-effort landing page for a submitter opening a notification.
     */
    private static function submitterUrl(User $owner, Application $application): ?string
    {
        try {
            return match ($owner->user_category) {
                'directorate_user' => route('user.directorates.submissions.show', $application),
                'cgis_unit_user' => route('user.cgis-units.submissions.show', $application),
                default => route('user.returns.show', $application),
            };
        } catch (\Throwable) {
            return null;
        }
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
            self::STAGE_CGIS_DESK_REVIEW => self::STAGE_HQ_REVIEW,
            self::STAGE_HQ_REVIEW => self::STAGE_APPROVED,
            self::STAGE_ADMIN_REVIEW => self::STAGE_APPROVED,
            default => self::STAGE_APPROVED,
        };
    }
}
