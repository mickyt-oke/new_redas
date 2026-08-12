<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use App\Services\SubmissionWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubmissionReviewController extends Controller
{
    /**
     * Display the review queue for the authenticated approver.
     */
    public function index(): View
    {
        $user = Auth::user();

        $accessRole = $this->accessRole($user);
        $stageName = $this->stageName($user);
        $dashboardTitle = $this->dashboardTitle($user);
        $nextStage = SubmissionWorkflow::nextStageFromStage(
            SubmissionWorkflow::stageForApprover($user)
        );

        $pendingQuery = SubmissionWorkflow::pendingQueryForApprover($user);

        $pendingSubmissions = (clone $pendingQuery)
            ->latest()
            ->limit(50)
            ->get();

        $approvedCount = Application::query()
            ->where('status', 'approved')
            ->when($this->scopeValue($user) !== null, function ($query) use ($user) {
                $query->where($this->scopeColumn($user), $this->scopeValue($user));
            })
            ->count();

        $rejectedCount = Application::query()
            ->where('status', 'returned')
            ->when($this->scopeValue($user) !== null, function ($query) use ($user) {
                $query->where($this->scopeColumn($user), $this->scopeValue($user));
            })
            ->count();

        return view('desk-admin.dashboard', [
            'accessRole' => $accessRole,
            'stageName' => $stageName,
            'dashboardTitle' => $dashboardTitle,
            'nextStage' => $nextStage,
            'pendingSubmissions' => $pendingSubmissions,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'approveRoute' => $this->reviewRoute($user, 'approve'),
            'rejectRoute' => $this->reviewRoute($user, 'reject'),
        ]);
    }

    /**
     * Show submission preview details for the authenticated approver.
     */
    public function show(Application $application): View
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403, 'Unauthorized.');
        }

        $this->authorizeAction($application, $user);

        return view('desk-admin.preview', [
            'application' => $application,
        ]);
    }

    /**
     * Approve a submission and advance it to the next workflow stage.
     */
    public function approve(Request $request, Application $application): RedirectResponse
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403, 'Unauthorized.');
        }

        $this->authorizeAction($application, $user);

        SubmissionWorkflow::approve($application, $user);

        return back()->with('status', 'Submission approved and routed to the next stage.');
    }

    /**
     * Reject / return a submission to the originating officer for correction.
     */
    public function reject(Request $request, Application $application): RedirectResponse
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403, 'Unauthorized.');
        }

        $this->authorizeAction($application, $user);

        $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        SubmissionWorkflow::reject($application, $user, $request->input('comment'));

        return back()->with('status', 'Submission returned to the officer for correction.');
    }

    /**
     * Ensure the logged-in user can act on this submission.
     */
    private function authorizeAction(Application $application, User $user): void
    {
        $requiredStage = SubmissionWorkflow::stageForApprover($user);

        if ($requiredStage === null) {
            abort(403, 'Unauthorized action.');
        }

        if ($application->workflow_stage !== $requiredStage) {
            abort(403, 'This submission is not awaiting your review.');
        }

        $scopeCode = SubmissionWorkflow::scopeCodeForApprover($user);

        if ($scopeCode !== null) {
            $applicationScope = $application->scope_code;

            if ($user->user_category === 'zonal_commander') {
                $applicationScope = $application->zonal_code ?: $application->scope_code;
            }

            if ($applicationScope !== $scopeCode) {
                abort(403, 'This submission is outside your provisioned scope.');
            }
        }
    }

    private function accessRole(?User $user): string
    {
        return match ($user?->user_category) {
            'directorate_user' => 'directorate',
            'directorate_admin' => 'directorate',
            'zonal_commander' => 'zonal',
            'admin', 'super_admin' => 'hq',
            default => 'state',
        };
    }

    private function stageName(?User $user): string
    {
        return match ($user?->user_category) {
            'desk_admin' => 'Desk Admin Review',
            'directorate_admin' => 'Directorate Admin Review',
            'zonal_commander' => 'Zonal Command Review',
            'admin' => 'HQ Admin Review',
            'super_admin' => 'Final Admin Review',
            default => 'Review Queue',
        };
    }

    private function scopeColumn(?User $user): ?string
    {
        return match ($user?->user_category) {
            'zonal_commander' => 'zonal_code',
            default => 'scope_code',
        };
    }

    private function scopeValue(?User $user): ?string
    {
        return SubmissionWorkflow::scopeCodeForApprover($user);
    }

    private function dashboardTitle(?User $user): string
    {
        return match ($user?->user_category) {
            'desk_admin' => 'Desk Admin Dashboard',
            'directorate_admin' => 'Directorate Admin Dashboard',
            'zonal_commander' => 'Zonal Command Dashboard',
            'admin' => 'HQ Admin Review Dashboard',
            'super_admin' => 'Final Admin Review Dashboard',
            default => 'Review Dashboard',
        };
    }

    private function reviewRoute(?User $user, string $action): string
    {
        return match ($user?->user_category) {
            'desk_admin', 'directorate_admin' => "desk.admin.submissions.{$action}",
            'zonal_commander' => "zonal.submissions.{$action}",
            'admin' => "admin.submissions.{$action}",
            'super_admin' => "superadmin.submissions.{$action}",
            default => 'user.dashboard',
        };
    }
}
