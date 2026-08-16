<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\UserNotification;
use App\Services\SubmissionWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Metadata for directorate return views. Fields are now defined explicitly in
     * resources/views/user/directorates/{slug}.blade.php for easier reference and design.
     */
    private const DIRECTORATES = [
        'hrm' => [
            'name' => 'Human Resources Management (HRM)',
            'icon' => 'fas fa-users',
        ],
        'prs' => [
            'name' => 'Planning, Research and Statistics (PRS)',
            'icon' => 'fas fa-chart-bar',
        ],
        'finance' => [
            'name' => 'Finance and Accounts',
            'icon' => 'fas fa-coins',
        ],
        'investigation' => [
            'name' => 'Investigation and Compliance',
            'icon' => 'fas fa-search',
        ],
        'passport' => [
            'name' => 'Passport and Other Travel Documents',
            'icon' => 'fas fa-passport',
        ],
        'visa' => [
            'name' => 'Visa and Residency',
            'icon' => 'fas fa-stamp',
        ],
        'migration' => [
            'name' => 'Migration Directorate',
            'icon' => 'fas fa-globe-africa',
        ],
        'border' => [
            'name' => 'Border Management',
            'icon' => 'fas fa-border-all',
        ],
        'ict' => [
            'name' => 'ICT Directorate',
            'icon' => 'fas fa-laptop-code',
        ],
        'works-logistics' => [
            'name' => 'Works and Logistics',
            'icon' => 'fas fa-truck',
        ],
    ];

    /**
     * Directorate Index Page: Displays a list of all directorates for users with the 'directorate_admin' role.
     */
    public function index(): View
    {
        // Support multiple possible view names to avoid "View not found" errors
        $viewNames = [
            'user.directorates.home',
            'user.directorates.index',
            'directorates.home',
            'directorates.index',
        ];

        foreach ($viewNames as $viewName) {
            if (view()->exists($viewName)) {
                return view($viewName, [
                    'allDirectorates' => self::DIRECTORATES,
                ]);
            }
        }

        abort(500, 'Dashboard view not found.');
    }

      /**
     * Display the directorate index page based on the provided slug.
     * Display only user-specific directorate pages for users with the 'directorate_user' role.
     * @param string $slug The slug representing the directorate (e.g., 'hrm', 'prs', 'finance').
     * @return View The view for the specified directorate
     * @throws \Illuminate\Http\Exceptions\HttpResponseException If the slug is invalid or the user is unauthorized.
     */

    public function showDirectorate(string $slug): View|RedirectResponse
    {
        $directorate = self::DIRECTORATES[$slug] ?? null;
        abort_if($directorate === null, 404);

        $user = Auth::user();

        // Directorate users may only view and submit their own directorate form.
        if ($user && $user->user_category === 'directorate_user') {
            $userSlug = $user->directorateSlug();
            $validUserSlug = $userSlug !== null && isset(self::DIRECTORATES[$userSlug]);

            if ($validUserSlug && $userSlug !== $slug) {
                return redirect()->route('user.directorates.show', $userSlug);
            }

            if (! $validUserSlug) {
                return redirect()
                    ->route('user.directorates.dashboard')
                    ->with('status', 'Your account is not assigned to a valid directorate.');
            }
        }

        return view('user.directorates.' . $slug, [
            'slug' => $slug,
            'directorateName' => $directorate['name'],
            'directorateIcon' => $directorate['icon'],
            'allDirectorates' => self::DIRECTORATES,
        ]);
    }

    /**
     * Display the directorate user landing page.
     * Shows only data relevant to the logged-in directorate user.
     */
    public function directorateDashboard(): View
    {
        $user = Auth::user();
        $slug = $user?->directorateSlug();
        $directorate = ($slug !== null && isset(self::DIRECTORATES[$slug]))
            ? self::DIRECTORATES[$slug]
            : null;

        $baseQuery = Schema::hasTable('applications')
            ? Application::query()->where('user_id', $user?->id)
            : null;

        $submissions = $baseQuery !== null
            ? (clone $baseQuery)->orderByDesc('created_at')->limit(5)->get()
            : collect();

        $totalSubmissions = $baseQuery !== null ? (clone $baseQuery)->count() : 0;
        $pendingSubmissions = $baseQuery !== null
            ? (clone $baseQuery)->whereIn('status', ['pending', 'Pending Review', 'submitted'])->count()
            : 0;
        $approvedSubmissions = $baseQuery !== null
            ? (clone $baseQuery)->where('status', 'approved')->count()
            : 0;
        $queriedSubmissions = $baseQuery !== null
            ? (clone $baseQuery)->whereIn('status', ['queried', 'rejected', 'returned'])->count()
            : 0;

        $unreadNotifications = Schema::hasTable('user_notifications')
            ? UserNotification::query()
                ->where('user_id', $user?->id)
                ->where('is_read', false)
                ->count()
            : 0;

        return view('user.directorates.dashboard', [
            'slug' => $slug,
            'directorate' => $directorate,
            'allDirectorates' => self::DIRECTORATES,
            'submissions' => $submissions,
            'totalSubmissions' => $totalSubmissions,
            'pendingSubmissions' => $pendingSubmissions,
            'approvedSubmissions' => $approvedSubmissions,
            'queriedSubmissions' => $queriedSubmissions,
            'unreadNotifications' => $unreadNotifications,
        ]);
    }

    /**
     * Handle the submission of a directorate return form.
     * @param Request $request The incoming HTTP request containing form data.
     * @param string $slug The slug representing the directorate (e.g., 'hrm', 'prs', 'finance').
     * @return RedirectResponse A redirect response to the directorate page with a success message.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException If the slug is invalid or validation fails.
     */

    public function storeDirectorate(Request $request, string $slug): RedirectResponse
    {
        $directorate = self::DIRECTORATES[$slug] ?? null;
        abort_if($directorate === null, 404);

        $validated = $request->validate([
            'report_period' => ['required', 'date_format:Y-m'],
            'reporting_officer' => ['required', 'string', 'max:120'],
            'data_consent' => ['required', 'accepted'],
        ]);

        $user = Auth::user();

        // Ensure the slug being submitted belongs to the logged-in directorate user.
        if ($user && $user->user_category === 'directorate_user') {
            $userSlug = $user->directorateSlug();

            if ($userSlug === null || $userSlug !== $slug) {
                abort(403, 'You are not authorised to submit this directorate return.');
            }
        }

        SubmissionWorkflow::create($user, array_merge(
            $request->except(['_token', 'data_consent']),
            ['directorate_slug' => $slug, 'report_period' => $validated['report_period']]
        ));

        return redirect()
            ->route('user.directorates.show', $slug)
            ->with('status', $directorate['name'] . ' return submitted successfully.');
    }

    /**
     * Display a previously submitted return for preview (screen).
     */
    public function showSubmission(Request $request, Application $application): View
    {
        abort_unless($application->user_id === $request->user()->id, 403);

        return view('user.directorates.submission', $this->submissionViewData($application, false));
    }

    /**
     * Display a previously submitted return in print mode (auto-prints).
     */
    public function printSubmission(Request $request, Application $application): View
    {
        abort_unless($application->user_id === $request->user()->id, 403);

        return view('user.directorates.submission', $this->submissionViewData($application, true));
    }

    /**
     * Show the directorate form prefilled with an existing submission for editing.
     */
    public function editSubmission(Request $request, Application $application): View|RedirectResponse
    {
        abort_unless($application->user_id === $request->user()->id, 403);

        if (strtolower((string) $application->status) === 'approved') {
            return redirect()
                ->route('user.directorates.dashboard')
                ->with('status', 'Approved submissions cannot be edited.');
        }

        $user = Auth::user();
        $slug = $user?->directorateSlug();

        if ($slug === null || ! isset(self::DIRECTORATES[$slug])) {
            return redirect()
                ->route('user.directorates.dashboard')
                ->with('status', 'Your account is not assigned to a valid directorate.');
        }

        $directorate = self::DIRECTORATES[$slug];

        return view('user.directorates.' . $slug, [
            'slug' => $slug,
            'directorateName' => $directorate['name'],
            'directorateIcon' => $directorate['icon'],
            'allDirectorates' => self::DIRECTORATES,
            'editing' => $application,
        ]);
    }

    /**
     * Update an existing submission and re-enter it into the review workflow.
     */
    public function updateSubmission(Request $request, Application $application): RedirectResponse
    {
        abort_unless($application->user_id === $request->user()->id, 403);
        abort_if(strtolower((string) $application->status) === 'approved', 403, 'Approved submissions cannot be edited.');

        $validated = $request->validate([
            'report_period' => ['required', 'date_format:Y-m'],
            'reporting_officer' => ['required', 'string', 'max:120'],
            'data_consent' => ['required', 'accepted'],
        ]);

        $user = Auth::user();
        $slug = $user?->directorateSlug();
        abort_if($slug === null || ! isset(self::DIRECTORATES[$slug]), 403);

        // Mirror the initial-stage logic of SubmissionWorkflow::create() so the
        // updated return re-enters the workflow where a fresh submission would.
        $initialStage = SubmissionWorkflow::categoryForUser($user) === SubmissionWorkflow::CATEGORY_DIRECTORATE
            ? SubmissionWorkflow::STAGE_DIRECTORATE_REVIEW
            : SubmissionWorkflow::STAGE_DESK_REVIEW;

        $path = $application->workflow_path ?? [];
        $path[] = [
            'stage' => SubmissionWorkflow::STAGE_SUBMITTED,
            'by' => $user->id,
            'at' => now()->toDateTimeString(),
            'action' => 'resubmitted',
        ];

        $application->update([
            'return_data' => array_merge(
                $request->except(['_token', '_method', 'data_consent']),
                ['directorate_slug' => $slug, 'report_period' => $validated['report_period']]
            ),
            'status' => 'pending',
            'workflow_stage' => $initialStage,
            'workflow_path' => $path,
        ]);

        return redirect()
            ->route('user.directorates.dashboard')
            ->with('status', 'Return updated and resubmitted successfully.');
    }

    /**
     * Build the view data shared by the preview and print pages.
     */
    private function submissionViewData(Application $application, bool $isPrint): array
    {
        $user = Auth::user();
        $slug = $application->return_data['directorate_slug'] ?? $user?->directorateSlug();
        $directorate = ($slug !== null && isset(self::DIRECTORATES[$slug]))
            ? self::DIRECTORATES[$slug]
            : null;

        return [
            'application' => $application,
            'slug' => $slug,
            'directorate' => $directorate,
            'isPrint' => $isPrint,
        ];
    }
}
