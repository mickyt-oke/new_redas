<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\NisDirectory;
use App\Models\UserNotification;
use App\Services\ReportPdfService;
use App\Services\SubmissionWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
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
                return redirect()->to('/user/directorates/' . $userSlug);
            }

            if (! $validUserSlug) {
                return redirect()
                    ->to('/user/directorates')
                    ->with('status', 'Your account is not assigned to a valid directorate.');
            }
        }

        if ($slug === 'works-logistics') {
			$commands = [
				'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno',
				'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT', 'Gombe',
				'Imo', 'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara',
				'Lagos', 'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau',
				'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara'
			];
			return view('user.directorates.' . $slug, array_merge([
				'commands' => $commands,
				'slug' => $slug,
				'directorateName' => self::DIRECTORATES[$slug]['name'],
				'directorateIcon' => self::DIRECTORATES[$slug]['icon'],
				'directorate' => self::DIRECTORATES[$slug],
				'allDirectorates' => self::DIRECTORATES,
			], $this->passportViewData($slug)));
		}

		return view('user.directorates.' . $slug, array_merge([
			'slug' => $slug,
			'directorateName' => self::DIRECTORATES[$slug]['name'],
			'directorateIcon' => self::DIRECTORATES[$slug]['icon'],
			'directorate' => self::DIRECTORATES[$slug],
			'allDirectorates' => self::DIRECTORATES,
		], $this->passportViewData($slug)));
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

        $viewNames = [
            'user.directorates.dashboard',
            'user.dashboard',
            'directorates.dashboard',
            'dashboard',
        ];

        foreach ($viewNames as $viewName) {
            if (view()->exists($viewName)) {
                return view($viewName, [
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
        }

        abort(500, 'Directorate dashboard view not found.');
    }

    /**
     * Handle the submission of a directorate return form.
     * @param Request $request The incoming HTTP request containing form data.
     * @param string $slug The slug representing the directorate (e.g., 'hrm', 'prs', 'finance').
     * @return RedirectResponse A redirect response to the directorate page with a success message.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException If the slug is invalid or validation fails.
     */

    /**
     * Validation rules for a directorate return (metadata and uploads only;
     * directorate-specific fields are stored as-is in return_data).
     */
    private function returnValidationRules(): array
    {
        return [
            'report_period' => ['required', 'date_format:Y-m'],
            'reporting_officer' => ['required', 'string', 'max:120'],
            'data_consent' => ['required', 'accepted'],
            'supporting_documents' => ['nullable', 'array'],
            'supporting_documents.*' => ['file', 'mimes:pdf,xls,xlsx,png,jpg,jpeg', 'max:20480'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:20480'],
        ];
    }

    /**
     * Friendly validation messages shown on the form when submission fails.
     */
    private function returnValidationMessages(): array
    {
        return [
            'report_period.required' => 'Please select the report period for this return.',
            'report_period.date_format' => 'The report period must be a valid month (e.g. ' . now()->format('Y-m') . ').',
            'reporting_officer.required' => 'The reporting officer name is required.',
            'reporting_officer.max' => 'The reporting officer name may not exceed 120 characters.',
            'data_consent.required' => 'Please tick the declaration and consent box before submitting.',
            'data_consent.accepted' => 'Please tick the declaration and consent box before submitting.',
            'supporting_documents.*.file' => 'One of the supporting documents could not be uploaded. Please try again.',
            'supporting_documents.*.mimes' => 'Supporting documents must be PDF, Excel (xls/xlsx), or image (png/jpg/jpeg) files.',
            'supporting_documents.*.max' => 'Each supporting document must not be larger than 20 MB.',
            'attachments.*.file' => 'One of the attachments could not be uploaded. Please try again.',
            'attachments.*.mimes' => 'Attachments must be PDF, Word (doc/docx), or image (jpg/jpeg/png) files.',
            'attachments.*.max' => 'Each attachment must not be larger than 20 MB.',
        ];
    }

    public function storeDirectorate(Request $request, string $slug): RedirectResponse
    {
        $directorate = self::DIRECTORATES[$slug] ?? null;
        abort_if($directorate === null, 404);

        $validated = $request->validate($this->returnValidationRules(), $this->returnValidationMessages());

        $user = Auth::user();

        // Ensure the slug being submitted belongs to the logged-in directorate user.
        if ($user && $user->user_category === 'directorate_user') {
            $userSlug = $user->directorateSlug();

            if ($userSlug === null || $userSlug !== $slug) {
                abort(403, 'You are not authorised to submit this directorate return.');
            }
        }

        try {
            SubmissionWorkflow::create($user, array_merge(
                $request->except(['_token', 'data_consent', 'supporting_documents', 'attachments']),
                [
                    'directorate_slug' => $slug,
                    'report_period' => $validated['report_period'],
                    'supporting_documents' => $this->storeUploadedFiles($request, 'supporting_documents', $slug),
                    'attachments' => $this->storeUploadedFiles($request, 'attachments', $slug),
                ]
            ));
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'The return could not be submitted because of a system error. Your entries are preserved below — please try again, or use Save Draft and contact support if the problem persists.');
        }

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
     * Stream one of a submission's uploaded documents to its owner
     * (inline for preview, or as a download with ?download=1).
     */
    public function submissionDocument(Request $request, Application $application, string $collection, int $index)
    {
        abort_unless($application->user_id === $request->user()->id, 403);
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
     * Show the directorate form prefilled with an existing submission for editing.
     */
    public function editSubmission(Request $request, Application $application): View|RedirectResponse
    {
        abort_unless($application->user_id === $request->user()->id, 403);

        if (strtolower((string) $application->status) === 'approved') {
            return redirect()
                ->to('/user/directorates')
                ->with('status', 'Approved submissions cannot be edited.');
        }

        $user = Auth::user();
        $slug = $user?->directorateSlug();

        if ($slug === null || ! isset(self::DIRECTORATES[$slug])) {
            return redirect()
                ->to('/user/directorates')
                ->with('status', 'Your account is not assigned to a valid directorate.');
        }

        $directorate = self::DIRECTORATES[$slug];

        return view('user.directorates.' . $slug, array_merge([
            'slug' => $slug,
            'directorateName' => $directorate['name'],
            'directorateIcon' => $directorate['icon'],
            'allDirectorates' => self::DIRECTORATES,
            'editing' => $application,
        ], $this->passportViewData($slug)));
    }

    /**
     * Extra view data for the passport directorate form: passport centre and
     * foreign mission names used by its executive-summary dropdowns.
     */
    private function passportViewData(string $slug): array
    {
        if ($slug !== 'passport' || ! Schema::hasTable('nis_directories')) {
            return ['processingCenters' => [], 'foreignMissions' => []];
        }

        return [
            'processingCenters' => NisDirectory::where('category', '=', 'passport', 'and')->orderBy('name')->pluck('name')->all(),
            'foreignMissions' => NisDirectory::where('category', '=', 'foreign_mission', 'and')->orderBy('name')->pluck('name')->filter()->values()->all(),
        ];
    }

    /**
     * Store uploaded files for the given input key and return their storage
     * paths, keeping return_data free of UploadedFile objects (it is JSON-cast).
     */
    private function storeUploadedFiles(Request $request, string $key, string $slug): array
    {
        $paths = [];

        foreach ((array) $request->file($key, []) as $file) {
            if ($file && $file->isValid()) {
                $paths[] = $file->store("supporting-documents/{$slug}");
            }
        }

        return $paths;
    }

    /**
     * Update an existing submission and re-enter it into the review workflow.
     */
    public function updateSubmission(Request $request, Application $application): RedirectResponse
    {
        abort_unless($application->user_id === $request->user()->id, 403);
        abort_if(strtolower((string) $application->status) === 'approved', 403, 'Approved submissions cannot be edited.');

        $validated = $request->validate($this->returnValidationRules(), $this->returnValidationMessages());

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

        try {
            $application->update([
                'return_data' => array_merge(
                    $request->except(['_token', '_method', 'data_consent', 'supporting_documents', 'attachments']),
                    [
                        'directorate_slug' => $slug,
                        'report_period' => $validated['report_period'],
                        'supporting_documents' => $this->storeUploadedFiles($request, 'supporting_documents', $slug),
                        'attachments' => $this->storeUploadedFiles($request, 'attachments', $slug),
                    ]
                ),
                'status' => 'pending',
                'workflow_stage' => $initialStage,
                'workflow_path' => $path,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'The return could not be updated because of a system error. Your entries are preserved below — please try again, or use Save Draft and contact support if the problem persists.');
        }

        return redirect()
            ->route('user.directorates.dashboard')
            ->with('status', 'Return updated and resubmitted successfully.');
    }

    /**
     * Download a submission's return as a PDF report (owner or in-scope approver).
     */
    public function downloadSubmissionPdf(Request $request, Application $application)
    {
        $user = Auth::user();

        $isOwner = $user !== null && $application->user_id === $user->id;
        $isApprover = $user !== null && SubmissionWorkflow::stageForApprover($user) !== null;

        abort_unless($isOwner || $isApprover, 403);

        if (! $isOwner) {
            $scopeCode = SubmissionWorkflow::scopeCodeForApprover($user);

            if ($scopeCode !== null) {
                $applicationScope = $user->user_category === 'zonal_commander'
                    ? ($application->zonal_code ?: $application->scope_code)
                    : $application->scope_code;

                abort_if($applicationScope !== $scopeCode, 403, 'This submission is outside your provisioned scope.');
            }
        }

        $slug = $application->return_data['directorate_slug'] ?? null;
        $directorateName = ($slug !== null && isset(self::DIRECTORATES[$slug]))
            ? self::DIRECTORATES[$slug]['name']
            : null;

        return ReportPdfService::downloadSubmission($application, $directorateName);
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
