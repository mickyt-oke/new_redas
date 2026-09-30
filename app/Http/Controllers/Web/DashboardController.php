<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationComment;
use App\Models\NisDirectory;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\GeolocationService;
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
            'name' => 'Finance and Accounts (F/A)',
            'icon' => 'fas fa-coins',
        ],
        'investigation' => [
            'name' => 'Investigation and Compliance (I/C)',
            'icon' => 'fas fa-search',
        ],
        'passport' => [
            'name' => 'Passport and Other Travel Documents (P/OTD)',
            'icon' => 'fas fa-passport',
        ],
        'visa' => [
            'name' => 'Visa and Residency (V/R)',
            'icon' => 'fas fa-stamp',
        ],
        'migration' => [
            'name' => 'Migration Directorate',
            'icon' => 'fas fa-globe-africa',
        ],
        'border' => [
            'name' => 'Border Management (BMD)',
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
     * Metadata for CGIS unit return views. Fields are defined explicitly in
     * resources/views/user/cgis-units/{slug}.blade.php.
     */
    private const CGIS_UNITS = [
        'actu' => [
            'name' => 'Anti-Corruption and Transparency Unit (ACTU)',
            'icon' => 'fas fa-shield-halved',
        ],
        'epms' => [
            'name' => 'Electronic Passport Management System (EPMS)',
            'icon' => 'fas fa-chart-line',
        ],
        'hostmanship' => [
            'name' => 'Hostmanship Unit',
            'icon' => 'fas fa-people-arrows',
        ],
        'pro-media' => [
            'name' => 'Public Relations and Media Unit',
            'icon' => 'fas fa-bullhorn',
        ],
        'protocol' => [
            'name' => 'Protocol Unit',
            'icon' => 'fas fa-handshake-angle',
        ],
        'provost' => [
            'name' => 'Provost Unit',
            'icon' => 'fas fa-user-shield',
        ],
        'servicom' => [
            'name' => 'SERVICOM Unit',
            'icon' => 'fas fa-handshake',
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
				'formChannel' => 'directorate',
				'directorateName' => self::DIRECTORATES[$slug]['name'],
				'directorateIcon' => self::DIRECTORATES[$slug]['icon'],
				'directorate' => self::DIRECTORATES[$slug],
				'allDirectorates' => self::DIRECTORATES,
			], $this->passportViewData($slug)));
		}

		return view('user.directorates.' . $slug, array_merge([
			'slug' => $slug,
			'formChannel' => 'directorate',
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
     * Display the CGIS unit user landing page.
     * Shows only data relevant to the logged-in CGIS unit user.
     */
    public function cgisUnitDashboard(): View
    {
        $user = Auth::user();
        $slug = $user?->cgisUnitSlug();
        $unit = ($slug !== null && isset(self::CGIS_UNITS[$slug]))
            ? self::CGIS_UNITS[$slug]
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

        return view('user.cgis-units.dashboard', [
            'slug' => $slug,
            'unit' => $unit,
            'allUnits' => self::CGIS_UNITS,
            'submissions' => $submissions,
            'totalSubmissions' => $totalSubmissions,
            'pendingSubmissions' => $pendingSubmissions,
            'approvedSubmissions' => $approvedSubmissions,
            'queriedSubmissions' => $queriedSubmissions,
            'unreadNotifications' => $unreadNotifications,
        ]);
    }

    /**
     * Display the CGIS unit return form for the given unit slug.
     * CGIS unit users may only view and submit their own unit form.
     */
    public function showCgisUnit(string $slug): View|RedirectResponse
    {
        $unit = self::CGIS_UNITS[$slug] ?? null;
        abort_if($unit === null, 404);

        $user = Auth::user();

        if ($user && $user->user_category === 'cgis_unit_user') {
            $userSlug = $user->cgisUnitSlug();
            $validUserSlug = $userSlug !== null && isset(self::CGIS_UNITS[$userSlug]);

            if ($validUserSlug && $userSlug !== $slug) {
                return redirect()->to('/user/cgis-units/' . $userSlug);
            }

            if (! $validUserSlug) {
                return redirect()
                    ->to('/user/cgis-units')
                    ->with('status', 'Your account is not assigned to a valid CGIS unit.');
            }
        }

        return view('user.cgis-units.' . $slug, [
            'slug' => $slug,
            'formChannel' => 'cgis',
            'unitName' => $unit['name'],
            'unitIcon' => $unit['icon'],
            'unit' => $unit,
            'allUnits' => self::CGIS_UNITS,
        ]);
    }

    /**
     * Handle the submission of a CGIS unit return form.
     */
    public function storeCgisUnit(Request $request, string $slug): RedirectResponse
    {
        $unit = self::CGIS_UNITS[$slug] ?? null;
        abort_if($unit === null, 404);

        $validated = $request->validate($this->returnValidationRules(), $this->returnValidationMessages());

        $user = Auth::user();

        // Ensure the slug being submitted belongs to the logged-in CGIS unit user.
        if ($user && $user->user_category === 'cgis_unit_user') {
            $userSlug = $user->cgisUnitSlug();

            if ($userSlug === null || $userSlug !== $slug) {
                abort(403, 'You are not authorised to submit this CGIS unit return.');
            }
        }

        try {
            SubmissionWorkflow::create($user, array_merge(
                $request->except(['_token', 'data_consent', 'supporting_documents', 'attachments']),
                [
                    'cgis_unit_slug' => $slug,
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
            ->route('user.cgis-units.show', $slug)
            ->with('status', $unit['name'] . ' return submitted successfully.');
    }

    /**
     * Show the combined state return form (all ten directorate sections).
     */
    public function createStateReturn(): View
    {
        return view('user.states.create-return', $this->passportDirectoryData());
    }

    /**
     * Display the state user dashboard with live submission metrics.
     */
    public function stateDashboard(): View
    {
        $user = Auth::user();

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

        $notifications = Schema::hasTable('user_notifications')
            ? UserNotification::query()
                ->where('user_id', $user?->id)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
            : collect();

        $trendLabels = [];
        $trendValues = [];
        if ($baseQuery !== null) {
            $countsByMonth = (clone $baseQuery)
                ->where('created_at', '>=', now()->startOfMonth()->subMonths(5))
                ->get(['created_at'])
                ->groupBy(fn ($application) => $application->created_at?->format('Y-m'))
                ->map->count();

            for ($i = 5; $i >= 0; $i--) {
                $month = now()->startOfMonth()->subMonths($i);
                $trendLabels[] = $month->format('M');
                $trendValues[] = (int) ($countsByMonth[$month->format('Y-m')] ?? 0);
            }
        }

        return view('user.dashboard', compact(
            'submissions',
            'totalSubmissions',
            'pendingSubmissions',
            'approvedSubmissions',
            'queriedSubmissions',
            'unreadNotifications',
            'notifications',
            'trendLabels',
            'trendValues'
        ));
    }

    /**
     * Render a read-only preview of the combined state return (no persistence).
     */
    public function previewStateReturn(Request $request): View
    {
        $previewData = $request->except(['_token', 'data_consent', 'attachments', 'supporting_documents', 'workflow_path', 'status']);

        $user = Auth::user();

        return view('user.states.preview', [
            'previewData' => $previewData,
            'user' => $user,
            'commandName' => $this->commandNameForUser($user),
        ]);
    }

    /**
     * Directorate metadata for views that render all ten state sections.
     */
    public static function directorates(): array
    {
        return self::DIRECTORATES;
    }

    /**
     * Resolve the human command name for a state user's state code.
     */
    private function commandNameForUser(?User $user): ?string
    {
        $code = $user?->primary_location_code ?: $user?->assigned_state_code;

        if ($code === null || $code === '') {
            return null;
        }

        return GeolocationService::stateNameFromCode((string) $code) ?? strtoupper((string) $code);
    }

    /**
     * List the logged-in state user's own submissions.
     */
    public function stateSubmissions(): View
    {
        $baseQuery = Application::query()->where('user_id', Auth::id());

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'returned' => (clone $baseQuery)->where('status', 'returned')->count(),
        ];

        $submissions = (clone $baseQuery)->latest()->paginate(15);

        return view('user.states.submissions', [
            'submissions' => $submissions,
            'stats' => $stats,
        ]);
    }

    /**
     * Display a previously submitted return for preview (screen).
     */
    public function showSubmission(Request $request, Application $application): View
    {
        abort_unless($application->user_id === $request->user()->id, 403);

        $application->load('reviewComments.user');

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

        $user = Auth::user();
        $isStateUser = $user?->user_category === 'state_user';
        $homeUrl = $isStateUser ? route('user.submissions') : $this->formHomeUrl($user);

        if (strtolower((string) $application->status) === 'approved') {
            return redirect()
                ->to($homeUrl)
                ->with('status', 'Approved submissions cannot be edited.');
        }

        if ($isStateUser) {
            return view('user.states.create-return', array_merge([
                'editing' => $application,
            ], $this->passportDirectoryData()));
        }

        $slug = $this->formSlugForUser($user);
        $metadata = $slug !== null ? $this->formMetadata($slug) : null;

        if ($slug === null || $metadata === null) {
            return redirect()
                ->to($homeUrl)
                ->with('status', 'Your account is not assigned to a valid directorate or CGIS unit.');
        }

        return view($this->formViewName($slug), array_merge([
            'slug' => $slug,
            'formChannel' => isset(self::CGIS_UNITS[$slug]) ? 'cgis' : 'directorate',
            'directorateName' => $metadata['name'],
            'directorateIcon' => $metadata['icon'],
            'unitName' => $metadata['name'],
            'unitIcon' => $metadata['icon'],
            'unit' => $metadata,
            'allDirectorates' => self::DIRECTORATES,
            'allUnits' => self::CGIS_UNITS,
            'editing' => $application,
        ], $this->passportViewData($slug)));
    }

    /**
     * Extra view data for the passport directorate form: passport centre and
     * foreign mission names used by its executive-summary dropdowns.
     */
    private function passportViewData(string $slug): array
    {
        if ($slug !== 'passport') {
            return ['processingCenters' => [], 'foreignMissions' => []];
        }

        return $this->passportDirectoryData();
    }

    /**
     * Passport centre and foreign mission name lists, unconditionally — the
     * combined state form embeds the passport section alongside all others.
     */
    private function passportDirectoryData(): array
    {
        if (! Schema::hasTable('nis_directories')) {
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

        $user = Auth::user();
        $isStateUser = $user?->user_category === 'state_user';

        if ($isStateUser) {
            $request->validate([
                'command_name' => ['required', 'string', 'max:120'],
                'period' => ['required', 'date_format:Y-m'],
                'return_type' => ['required', 'in:monthly,quarterly,biannual,annual,special'],
                'reporting_officer' => ['required', 'string', 'max:120'],
                'data_consent' => ['required', 'accepted'],
                'attachments' => ['nullable', 'array'],
                'attachments.*' => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:20480'],
            ]);

            $returnData = array_merge(
                $request->except(['_token', '_method', 'data_consent', 'attachments', 'workflow_path', 'status']),
                [
                    'report_period' => $request->input('period'),
                    'attachments' => $this->storeUploadedFiles($request, 'attachments', 'state'),
                ]
            );
        } else {
            $validated = $request->validate($this->returnValidationRules(), $this->returnValidationMessages());

            $slug = $this->formSlugForUser($user);
            abort_if($slug === null || $this->formMetadata($slug) === null, 403);

            $slugKey = isset(self::CGIS_UNITS[$slug]) ? 'cgis_unit_slug' : 'directorate_slug';

            $returnData = array_merge(
                $request->except(['_token', '_method', 'data_consent', 'supporting_documents', 'attachments']),
                [
                    $slugKey => $slug,
                    'report_period' => $validated['report_period'],
                    'supporting_documents' => $this->storeUploadedFiles($request, 'supporting_documents', $slug),
                    'attachments' => $this->storeUploadedFiles($request, 'attachments', $slug),
                ]
            );
        }

        // Mirror the initial-stage logic of SubmissionWorkflow::create() so the
        // updated return re-enters the workflow where a fresh submission would.
        $initialStage = SubmissionWorkflow::initialStageForUser($user);

        $path = $application->workflow_path ?? [];
        $path[] = [
            'stage' => SubmissionWorkflow::STAGE_SUBMITTED,
            'by' => $user->id,
            'at' => now()->toDateTimeString(),
            'action' => 'resubmitted',
        ];

        try {
            $application->update([
                'return_data' => $returnData,
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

        SubmissionWorkflow::recordComment(
            $application,
            $user,
            SubmissionWorkflow::STAGE_SUBMITTED,
            ApplicationComment::ACTION_RESUBMITTED
        );

        SubmissionWorkflow::notifyPendingReviewers($application);

        $redirect = $isStateUser
            ? redirect()->route('user.submissions')
            : redirect()->to($this->formHomeUrl($user) . '/dashboard');

        return $redirect->with('status', 'Return updated and resubmitted successfully.');
    }

    /**
     * Delete a submission owned by the user, together with its uploaded files.
     * Only allowed while the return has not been finally approved.
     */
    public function destroySubmission(Application $application): RedirectResponse
    {
        $user = Auth::user();

        abort_unless($user !== null && SubmissionWorkflow::deletableBy($application, $user), 403);

        $data = is_array($application->return_data) ? $application->return_data : [];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk();

        foreach (['supporting_documents', 'attachments'] as $key) {
            foreach ((array) ($data[$key] ?? []) as $path) {
                if (! is_string($path) || $path === '') {
                    continue;
                }

                try {
                    if ($disk->exists($path)) {
                        $disk->delete($path);
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }

        $application->delete($user);

        $redirect = match ($user->user_category) {
            'cgis_unit_user' => redirect()->route('user.cgis-units.dashboard'),
            'directorate_user' => redirect()->route('user.directorates.dashboard'),
            default => redirect()->route('user.submissions'),
        };

        return $redirect->with('status', 'Return deleted successfully.');
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

        $slug = $application->return_data['directorate_slug']
            ?? $application->return_data['cgis_unit_slug']
            ?? null;
        $directorateName = ($slug !== null && $this->formMetadata($slug) !== null)
            ? $this->formMetadata($slug)['name']
            : null;

        return ReportPdfService::downloadSubmission($application, $directorateName);
    }

    /**
     * Resolve the return-form slug (directorate or CGIS unit) for a user.
     */
    private function formSlugForUser(?User $user): ?string
    {
        return $user?->directorateSlug() ?? $user?->cgisUnitSlug();
    }

    /**
     * Look up display metadata for a form slug across directorates and CGIS units.
     */
    private function formMetadata(string $slug): ?array
    {
        return self::DIRECTORATES[$slug] ?? self::CGIS_UNITS[$slug] ?? null;
    }

    /**
     * Resolve the blade view for a form slug.
     */
    private function formViewName(string $slug): string
    {
        return isset(self::DIRECTORATES[$slug])
            ? 'user.directorates.' . $slug
            : 'user.cgis-units.' . $slug;
    }

    /**
     * Resolve the index URL of the user's return-form area.
     */
    private function formHomeUrl(?User $user): string
    {
        return $user?->user_category === 'cgis_unit_user'
            ? '/user/cgis-units'
            : '/user/directorates';
    }

    /**
     * Build the view data shared by the preview and print pages.
     */
    private function submissionViewData(Application $application, bool $isPrint): array
    {
        $user = Auth::user();
        $slug = $application->return_data['directorate_slug']
            ?? $application->return_data['cgis_unit_slug']
            ?? $this->formSlugForUser($user);
        $metadata = ($slug !== null) ? $this->formMetadata($slug) : null;

        return [
            'application' => $application,
            'slug' => $slug,
            'directorate' => $metadata,
            'unit' => $metadata,
            'isPrint' => $isPrint,
        ];
    }
}
