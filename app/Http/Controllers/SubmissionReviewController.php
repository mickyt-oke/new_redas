<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use App\Services\ReportPdfService;
use App\Services\SubmissionWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        $approvedSubmissions = Application::query()
            ->with('user')
            ->where('status', 'approved')
            ->when($this->scopeValue($user) !== null, function ($query) use ($user) {
                $query->where($this->scopeColumn($user), $this->scopeValue($user));
            })
            ->latest('updated_at')
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
            'approvedSubmissions' => $approvedSubmissions,
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

        $this->authorizeView($application, $user);

        return view('desk-admin.preview', [
            'application' => $application,
            'canReview' => $this->canReview($application, $user),
            'approveRoute' => $this->reviewRoute($user, 'approve'),
            'rejectRoute' => $this->reviewRoute($user, 'reject'),
        ]);
    }

    /**
     * Report generation page (and CSV export) filtered by submission date.
     */
    public function reports(Request $request): View|StreamedResponse|\Illuminate\Http\Response
    {
        $user = Auth::user();

        if (! $user instanceof User || SubmissionWorkflow::stageForApprover($user) === null) {
            abort(403, 'Unauthorized.');
        }

        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'status' => ['nullable', 'in:pending,approved,returned'],
        ]);

        $query = $this->reportQuery($user, $filters);

        if ($request->query('format') === 'csv') {
            return $this->streamReportCsv($query->get());
        }

        if ($request->query('format') === 'pdf') {
            return ReportPdfService::downloadReport($query->get(), $filters, $this->dashboardTitle($user));
        }

        $submissions = $request->filled('date_from') || $request->filled('date_to') || $request->filled('status')
            ? $query->limit(200)->get()
            : collect();

        return view('desk-admin.reports', [
            'submissions' => $submissions,
            'filters' => $filters,
            'dashboardTitle' => $this->dashboardTitle($user),
        ]);
    }

    /**
     * Download a single submission's return data as CSV (default) or PDF.
     */
    public function download(Request $request, Application $application)
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403, 'Unauthorized.');
        }

        $this->authorizeView($application, $user);

        if ($request->query('format') === 'pdf') {
            return ReportPdfService::downloadSubmission($application);
        }

        $filename = 'submission-' . $application->id . '.csv';

        return response()->streamDownload(function () use ($application) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Section', 'Field', 'Value']);

            $data = is_array($application->return_data) ? $application->return_data : [];
            foreach ($data as $section => $values) {
                $this->flattenForCsv($out, (string) $section, '', $values);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Stream an uploaded supporting document / attachment for in-browser viewing.
     */
    public function document(Request $request, Application $application, string $collection, int $index)
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403, 'Unauthorized.');
        }

        $this->authorizeView($application, $user);

        abort_unless(in_array($collection, ['supporting', 'attachments'], true), 404);

        $key = $collection === 'supporting' ? 'supporting_documents' : 'attachments';
        $files = array_values(array_filter(
            (array) ($application->return_data[$key] ?? []),
            'is_string'
        ));

        $path = $files[$index] ?? null;
        abort_unless(is_string($path) && $path !== '', 404);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk();
        abort_unless($disk->exists($path), 404);

        if ($request->boolean('download')) {
            return $disk->download($path, basename($path));
        }

        return $disk->response($path, basename($path));
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

        $this->authorizeScope($application, $user);
    }

    /**
     * Ensure the logged-in user can view this submission (any stage).
     */
    private function authorizeView(Application $application, User $user): void
    {
        if (SubmissionWorkflow::stageForApprover($user) === null) {
            abort(403, 'Unauthorized.');
        }

        $this->authorizeScope($application, $user);
    }

    /**
     * Scope check shared by view and action authorization.
     */
    private function authorizeScope(Application $application, User $user): void
    {
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

    /**
     * Whether this submission currently awaits action from the given approver.
     */
    private function canReview(Application $application, User $user): bool
    {
        $stage = SubmissionWorkflow::stageForApprover($user);

        return $stage !== null
            && $application->workflow_stage === $stage
            && ! in_array(strtolower((string) $application->status), ['approved', 'rejected'], true);
    }

    /**
     * Base query for report generation, scoped to the approver.
     */
    private function reportQuery(User $user, array $filters)
    {
        return Application::query()
            ->with('user')
            ->when($this->scopeValue($user) !== null, function ($query) use ($user) {
                $query->where($this->scopeColumn($user), $this->scopeValue($user));
            })
            ->when($filters['date_from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['date_to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest();
    }

    /**
     * Stream the report result set as CSV.
     */
    private function streamReportCsv($submissions): StreamedResponse
    {
        return response()->streamDownload(function () use ($submissions) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Officer', 'Scope', 'Category', 'Report Period', 'Status', 'Submitted At']);

            foreach ($submissions as $submission) {
                fputcsv($out, [
                    $submission->id,
                    optional($submission->user)->name ?? 'N/A',
                    $submission->scope_code,
                    $submission->category,
                    $submission->return_data['report_period'] ?? '',
                    $submission->status,
                    optional($submission->created_at)->toDateTimeString(),
                ]);
            }

            fclose($out);
        }, 'submissions-report-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * Write a nested return_data structure as flat CSV rows.
     */
    private function flattenForCsv(mixed $out, string $section, string $prefix, mixed $value): void
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $field = $prefix === '' ? (string) $key : $prefix . '.' . $key;
                $this->flattenForCsv($out, $section, $field, $item);
            }

            return;
        }

        if (is_bool($value)) {
            $value = $value ? 'Yes' : 'No';
        }

        fputcsv($out, [$section, $prefix, $value ?? '']);
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
