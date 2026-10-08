<?php

use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\ConsolidationController;
use App\Http\Controllers\Admin\HqAdminController;
use App\Http\Controllers\Admin\SuperAdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ApiNotificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthTokenController;
use App\Http\Controllers\LockscreenController;
use App\Http\Controllers\MfaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionReviewController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DirectoryController;
use App\Http\Controllers\Web\SpecialCommandController;
use App\Http\Controllers\Web\ZonalUserController;
use App\Http\Middleware\EnsureSpecialCommand;
use App\Models\Application;
use App\Services\SubmissionWorkflow;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

$homeView = 'welcome';

Route::get('/', fn () => view()->exists($homeView)
    ? view($homeView)
    : redirect('/login'))->name('home');

Route::get('/home', fn () => view()->exists($homeView)
    ? view($homeView)
    : redirect('/login'));

Route::get('/terms-and-conditions', fn () => view()->exists('legal.terms')
    ? view('legal.terms')
    : redirect('/login'))->name('terms');
Route::get('/privacy-policy', fn () => view()->exists('legal.privacy')
    ? view('legal.privacy')
    : redirect('/login'))->name('privacy');
Route::get('/directory', [DirectoryController::class, 'index'])->name('directory');

// Pinged periodically by long-running forms to keep the session alive and
// avoid a 419 (session expired) error while a user is actively filling a form.
Route::middleware(Authenticate::class)->post('/session/keep-alive', function () {
    return response()->noContent();
})->name('session.keep-alive');

// Authentication Routes (guests only)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');

    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.submit');

    Route::get('/password/forgot', [AuthTokenController::class, 'showForgotForm'])->name('password.forgot.form');

    Route::get('/register', function () {
        return redirect()->route('login')
            ->with('status', 'User registration is now managed by HQ administrators. Please contact your administrator for access.');
    })->name('register');

    // Magic link verification + password reset (web)
    Route::get('/verify-email/{token}', [AuthTokenController::class, 'verifyEmail'])->name('verify.email');
    Route::post('/verify-email/resend', [AuthTokenController::class, 'resendVerificationEmail'])->middleware('throttle:5,1')->name('verify.email.resend');
    Route::post('/password/forgot', [AuthTokenController::class, 'requestPasswordReset'])->middleware('throttle:10,1')->name('password.forgot');
    Route::get('/password/reset/{token}', [AuthTokenController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/password/reset/{token}', [AuthTokenController::class, 'resetPassword'])->middleware('throttle:10,1')->name('password.reset');
});

// Lockscreen Routes
Route::middleware([Authenticate::class])->group(function () {
    Route::get('/lockscreen', [LockscreenController::class, 'show'])->name('lockscreen.show');
    Route::post('/lockscreen/unlock', [LockscreenController::class, 'unlock'])->name('lockscreen.unlock');
    Route::post('/lockscreen/lock', [LockscreenController::class, 'lock'])->name('lockscreen.lock');
});

// State user (officer) routes — restricted to state_user accounts
Route::middleware([Authenticate::class, 'access:category=state_user,location=state,role=user|officer|minLevel=0', 'abac.geo'])->group(function () {
    Route::get('/user/dashboard', [DashboardController::class, 'stateDashboard'])->name('user.dashboard');

    Route::get('/user/returns/create', [DashboardController::class, 'createStateReturn'])->name('user.returns.create');

    Route::post('/user/returns/preview', [DashboardController::class, 'previewStateReturn'])->middleware('throttle:60,1')->name('user.returns.preview');

    Route::get('/user/submissions', [DashboardController::class, 'stateSubmissions'])->name('user.submissions');

    Route::get('/user/returns/{applicationHash}', [DashboardController::class, 'showSubmission'])->name('user.returns.show');
    Route::get('/user/returns/{applicationHash}/print', [DashboardController::class, 'printSubmission'])->name('user.returns.print');
    Route::get('/user/returns/{applicationHash}/documents/{collection}/{index}', [DashboardController::class, 'submissionDocument'])->name('user.returns.document');
    Route::get('/user/returns/{applicationHash}/edit', [DashboardController::class, 'editSubmission'])->name('user.returns.edit');
    Route::put('/user/returns/{applicationHash}', [DashboardController::class, 'updateSubmission'])->middleware('throttle:database')->name('user.returns.update');
    Route::delete('/user/returns/{applicationHash}', [DashboardController::class, 'destroySubmission'])->middleware(['signed', 'throttle:database'])->name('user.returns.destroy');

    Route::post('/user/returns', function (Request $request) {
        $request->validate([
            'command_name' => ['required', 'string', 'max:120'],
            'period' => ['required', 'date_format:Y-m'],
            'return_type' => ['required', 'in:monthly,quarterly,biannual,annual,special'],
            'reporting_officer' => ['required', 'string', 'max:120'],
            'data_consent' => ['required', 'accepted'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:20480'],
        ]);

        $user = Auth::user();

        // Prevent duplicate returns for the same period.
        if (SubmissionWorkflow::existingSubmissionForPeriod($user, $request->input('period'))) {
            throw ValidationException::withMessages([
                'period' => ['A return for this period has already been submitted. You can edit the existing submission instead.'],
            ]);
        }

        $returnData = (new DashboardController)->sanitizeReturnData(
            $request->except(['_token', 'data_consent', 'attachments', 'workflow_path', 'status'])
        );
        $returnData['report_period'] = $request->input('period');
        // Command/officer identity is server-derived, never trusted from the
        // (readonly, but client-editable) POST body.
        $returnData['command_name'] = DashboardController::commandNameForUser($user) ?? $request->input('command_name');
        $returnData['reporting_officer'] = $user?->name ?? $request->input('reporting_officer');
        $returnData['data_consent'] = $request->boolean('data_consent');

        $paths = [];
        foreach ((array) $request->file('attachments', []) as $file) {
            if ($file && $file->isValid()) {
                $paths[] = $file->store('supporting-documents/state');
            }
        }
        $returnData['attachments'] = $paths;

        SubmissionWorkflow::create(Auth::User(), $returnData);

        return redirect('/user/submissions')
            ->with('status', 'Return submitted successfully and routed to your supervisor for review.');
    })->middleware('throttle:60,1')->name('user.returns.store');
});

// Shared authenticated user pages (all roles)
Route::middleware([Authenticate::class, 'abac.geo'])->group(function () {
    Route::get('/user/notifications', function () {
        return view('user.states.notifications');
    })->name('user.notifications');

    // Realtime-friendly notifications endpoints (session auth)
    Route::get('/user/notifications/api', [ApiNotificationController::class, 'index']);
    Route::get('/user/notifications/count', [ApiNotificationController::class, 'count']);
    Route::post('/user/notifications/mark-all-read', [ApiNotificationController::class, 'markAllRead']);
    Route::post('/user/notifications/{id}/read', [ApiNotificationController::class, 'markRead']);

    Route::get('/user/archive', function () {
        $user = Auth::user();
        $archiveView = view()->exists('user.archive.index') ? 'user.archive.index' : 'user.states.archive';

        $completedQuery = Application::query()
            ->with('user')
            ->where('status', 'approved')
            ->latest('updated_at')
            ->limit(50);

        // Approvers see completed returns in their scope; officers see their own.
        if (SubmissionWorkflow::stageForApprover($user) !== null) {
            $scopeCode = SubmissionWorkflow::scopeCodeForApprover($user);
            if ($scopeCode !== null) {
                $completedQuery->where('scope_code', $scopeCode);
            }
        } else {
            $completedQuery->where('user_id', $user?->id);
        }

        return view($archiveView, ['completedReturns' => $completedQuery->get()]);
    })->name('user.archive');

    Route::get('/user/archive/upload', function () {
        $archiveView = view()->exists('user.archive.upload') ? 'user.archive.upload' : 'user.states.archive';

        return view($archiveView);
    })->name('user.archive.upload');

    Route::post('/user/archive/upload', function (Request $request) {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'doc_type' => ['required', 'string', 'max:50'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:20480'],
            'data_consent' => ['required', 'accepted'],
        ]);

        return redirect('/user/archive')
            ->with('status', 'Document(s) uploaded to archive successfully.');
    })->middleware('throttle:60,1')->name('user.archive.store');

    Route::get('/user/reports', function () {
        return view('user.states.reports');
    })->name('user.reports');

    Route::post('/user/reports/generate', function (Request $request) {
        $request->validate([
            'report_type' => ['required', 'in:submission_summary,monthly_return,quarterly_return,annual_return,compliance_report,full_export'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'sections' => ['nullable', 'array'],
            'format' => ['required', 'in:pdf,excel,csv'],
        ]);

        return redirect()->route('user.reports')
            ->with('status', 'Your report has been generated and is ready for download.');
    })->middleware('throttle:60,1')->name('user.reports.generate');
});

Route::middleware([Authenticate::class])->group(function () {
    Route::get('/user/profile', [ProfileController::class, 'edit'])->name('user.profile');
    Route::patch('/user/profile', [ProfileController::class, 'update'])->middleware([ThrottleRequests::class.':60,1'])->name('user.profile.update');

    // PDF export of a submitted return — owner or in-scope approver only (checked in controller).
    Route::get('/user/submissions/{applicationHash}/pdf', [DashboardController::class, 'downloadSubmissionPdf'])->middleware('signed')->name('user.submissions.pdf');
});

// Zonal user (officer) routes — restricted to zonal_user accounts
Route::middleware([Authenticate::class, 'access:category=zonal_user,location=zonal,role=user|officer|minLevel=0', 'abac.geo'])->group(function () {
    Route::get('/user/zones/dashboard', [ZonalUserController::class, 'dashboard'])->name('user.zones.dashboard');
    Route::get('/user/zones/returns/create', [ZonalUserController::class, 'createReturn'])->name('user.zones.returns.create');
    Route::post('/user/zones/returns', [ZonalUserController::class, 'storeReturn'])->middleware('throttle:60,1')->name('user.zones.returns.store');
    Route::get('/user/zones/returns', [ZonalUserController::class, 'submissions'])->name('user.zones.returns.index');
    Route::get('/user/zones/returns/{applicationHash}', [ZonalUserController::class, 'showSubmission'])->name('user.zones.returns.show');
    Route::get('/user/zones/returns/{applicationHash}/edit', [ZonalUserController::class, 'editSubmission'])->name('user.zones.returns.edit');
    Route::put('/user/zones/returns/{applicationHash}', [ZonalUserController::class, 'updateSubmission'])->middleware('throttle:database')->name('user.zones.returns.update');
    Route::delete('/user/zones/returns/{applicationHash}', [ZonalUserController::class, 'destroySubmission'])->middleware(['signed', 'throttle:database'])->name('user.zones.returns.destroy');
    Route::get('/user/zones/returns/{applicationHash}/documents/{collection}/{index}', [ZonalUserController::class, 'submissionDocument'])->middleware('signed')->name('user.zones.returns.document');
});

// Special command routes — restricted to state_user accounts provisioned to a special command
Route::middleware([Authenticate::class, 'access:category=state_user,location=state,role=user|officer|minLevel=0', 'abac.geo'])->group(function () {
    Route::middleware([EnsureSpecialCommand::class])->group(function () {
        Route::get('/special-commands/dashboard', [SpecialCommandController::class, 'dashboard'])->name('special-commands.dashboard');
        Route::get('/special-commands/returns/create', [SpecialCommandController::class, 'createReturn'])->name('special-commands.returns.create');
        Route::post('/special-commands/returns', [SpecialCommandController::class, 'storeReturn'])->middleware('throttle:60,1')->name('special-commands.returns.store');
        Route::get('/special-commands/returns', [SpecialCommandController::class, 'submissions'])->name('special-commands.returns.index');
        Route::get('/special-commands/returns/{applicationHash}', [SpecialCommandController::class, 'showSubmission'])->name('special-commands.returns.show');
        Route::get('/special-commands/returns/{applicationHash}/edit', [SpecialCommandController::class, 'editSubmission'])->name('special-commands.returns.edit');
        Route::put('/special-commands/returns/{applicationHash}', [SpecialCommandController::class, 'updateSubmission'])->middleware('throttle:database')->name('special-commands.returns.update');
        Route::delete('/special-commands/returns/{applicationHash}', [SpecialCommandController::class, 'destroySubmission'])->middleware(['signed', 'throttle:database'])->name('special-commands.returns.destroy');
        Route::get('/special-commands/returns/{applicationHash}/report', [SpecialCommandController::class, 'report'])->name('special-commands.returns.report');
        Route::get('/special-commands/returns/{applicationHash}/report/pdf', [SpecialCommandController::class, 'downloadReport'])->middleware('signed')->name('special-commands.returns.report.pdf');
        Route::get('/special-commands/returns/{applicationHash}/documents/{collection}/{index}', [SpecialCommandController::class, 'submissionDocument'])->middleware('signed')->name('special-commands.returns.document');
    });
});

// Zonal commander routes — access only to zonal pages
Route::middleware([Authenticate::class, 'access:category=zonal_commander,location=zonal,role=admin|zonal|minLevel=1', 'abac.geo'])->group(function () {
    Route::get('/zonal/dashboard', [SubmissionReviewController::class, 'index'])->name('user.zonal.home');
    Route::get('/zonal/submissions/{applicationHash}', [SubmissionReviewController::class, 'show'])->name('zonal.submissions.show');
    Route::get('/zonal/submissions/{applicationHash}/documents/{collection}/{index}', [SubmissionReviewController::class, 'document'])->middleware('signed')->name('zonal.submissions.document');
    Route::get('/zonal/submissions/{applicationHash}/download', [SubmissionReviewController::class, 'download'])->middleware('signed')->name('zonal.submissions.download');
    Route::patch('/zonal/submissions/{applicationHash}/approve', [SubmissionReviewController::class, 'approve'])->middleware(['signed', ThrottleRequests::class.':60,1'])->name('zonal.submissions.approve');
    Route::patch('/zonal/submissions/{applicationHash}/reject', [SubmissionReviewController::class, 'reject'])->middleware(['signed', ThrottleRequests::class.':60,1'])->name('zonal.submissions.reject');
});

// Desk / directorate / CGIS desk admin review routes
Route::middleware([Authenticate::class, 'access:category=desk_admin|directorate_admin|cgis_desk_admin|hq_admin,location=state|directorate|unit|headquarters,role=admin|state|directorate|unit_admin|minLevel=1', 'abac.geo'])->group(function () {
    Route::get('/desk-admin/dashboard', [SubmissionReviewController::class, 'index'])->name('user.desk.home');
    Route::get('/desk-admin/reports', [SubmissionReviewController::class, 'reports'])->name('desk.admin.reports');
    Route::patch('/desk-admin/submissions/{applicationHash}/approve', [SubmissionReviewController::class, 'approve'])->middleware(['signed', ThrottleRequests::class.':60,1'])->name('desk.admin.submissions.approve');
    Route::patch('/desk-admin/submissions/{applicationHash}/reject', [SubmissionReviewController::class, 'reject'])->middleware(['signed', ThrottleRequests::class.':60,1'])->name('desk.admin.submissions.reject');
    Route::get('/desk-admin/submissions/{applicationHash}', [SubmissionReviewController::class, 'show'])->name('desk.admin.submissions.show');
    Route::get('/desk-admin/submissions/{applicationHash}/download', [SubmissionReviewController::class, 'download'])->middleware('signed')->name('desk.admin.submissions.download');
    Route::get('/desk-admin/submissions/{applicationHash}/documents/{collection}/{index}', [SubmissionReviewController::class, 'document'])->middleware('signed')->name('desk.admin.submissions.document');
});

// CGIS unit user routes — access only to CGIS unit pages
// Note: Each CGIS unit user has a unique slug (e.g., actu, provost, servicom) that is used to access their specific unit form.
Route::middleware([Authenticate::class, 'access:category=cgis_unit_user,location=unit|headquarters,role=user|officer|unit_officer|minLevel=0', 'abac.geo'])->group(function () {
    Route::get('/user/cgis-units', function () {
        return redirect()->route('user.cgis-units.dashboard');
    })->name('user.cgis-units.home');

    Route::get('/user/cgis-units/dashboard', [DashboardController::class, 'cgisUnitDashboard'])->name('user.cgis-units.dashboard');

    Route::get('/user/cgis-units/submissions/{applicationHash}', [DashboardController::class, 'showSubmission'])->name('user.cgis-units.submissions.show');
    Route::get('/user/cgis-units/submissions/{applicationHash}/print', [DashboardController::class, 'printSubmission'])->name('user.cgis-units.submissions.print');
    Route::get('/user/cgis-units/submissions/{applicationHash}/edit', [DashboardController::class, 'editSubmission'])->name('user.cgis-units.submissions.edit');
    Route::put('/user/cgis-units/submissions/{applicationHash}', [DashboardController::class, 'updateSubmission'])->middleware('throttle:database')->name('user.cgis-units.submissions.update');
    Route::delete('/user/cgis-units/submissions/{applicationHash}', [DashboardController::class, 'destroySubmission'])->middleware(['signed', 'throttle:database'])->name('user.cgis-units.submissions.destroy');
    Route::get('/user/cgis-units/submissions/{applicationHash}/documents/{collection}/{index}', [DashboardController::class, 'submissionDocument'])->middleware('signed')->name('user.cgis-units.submissions.document');

    Route::get('/user/cgis-units/{slug}', [DashboardController::class, 'showCgisUnit'])->name('user.cgis-units.show');
    Route::post('/user/cgis-units/{slug}', [DashboardController::class, 'storeCgisUnit'])->middleware('throttle:database')->name('user.cgis-units.store');
});
// Directorate user routes — access only to directorate pages
// Note: Each Directorate user has a unique slug (e.g., hrm, prs, finance) that is used to access their specific directorate form.
Route::middleware([Authenticate::class, 'access:category=directorate_user|directorate_admin,location=directorate,role=user|admin|directorate|minLevel=0', 'abac.geo'])->group(function () {
    Route::get('/user/directorates', function () {
        return redirect()->route('user.directorates.dashboard');
    })->name('user.directorates.home');

    Route::get('/user/directorates/dashboard', [DashboardController::class, 'directorateDashboard'])->name('user.directorates.dashboard');

    Route::get('/user/directorates/submissions/{applicationHash}', [DashboardController::class, 'showSubmission'])->name('user.directorates.submissions.show');
    Route::get('/user/directorates/submissions/{applicationHash}/print', [DashboardController::class, 'printSubmission'])->name('user.directorates.submissions.print');
    Route::get('/user/directorates/submissions/{applicationHash}/edit', [DashboardController::class, 'editSubmission'])->name('user.directorates.submissions.edit');
    Route::put('/user/directorates/submissions/{applicationHash}', [DashboardController::class, 'updateSubmission'])->middleware('throttle:database')->name('user.directorates.submissions.update');
    Route::delete('/user/directorates/submissions/{applicationHash}', [DashboardController::class, 'destroySubmission'])->middleware(['signed', 'throttle:database'])->name('user.directorates.submissions.destroy');
    Route::get('/user/directorates/submissions/{applicationHash}/documents/{collection}/{index}', [DashboardController::class, 'submissionDocument'])->middleware('signed')->name('user.directorates.submissions.document');

    Route::get('/user/directorates/{slug}', [DashboardController::class, 'showDirectorate'])->name('user.directorates.show');
    Route::post('/user/directorates/{slug}', [DashboardController::class, 'storeDirectorate'])->middleware('throttle:database')->name('user.directorates.store');
});

// Shared directorate form routes — accessible by both state (officer) and directorate users

Route::middleware([Authenticate::class, 'access:category=state_user|directorate_user|directorate_admin,location=state|directorate,role=user|admin|officer|directorate|minLevel=0', 'abac.geo'])->group(function () {
    Route::get('/user/directorates/{slug}', [DashboardController::class, 'showDirectorate'])->name('user.directorates.show');
    Route::post('/user/directorates/{slug}', [DashboardController::class, 'storeDirectorate'])->middleware('throttle:database')->name('user.directorates.store');

    Route::get('/user/directorate/{id}', function ($id) {
        $legacyMap = [
            '1' => 'hrm',
            '2' => 'prs',
            '3' => 'finance',
            '4' => 'investigation',
            '5' => 'passport',
            '6' => 'visa',
            '7' => 'migration',
            '8' => 'border',
            '9' => 'ict',
            '10' => 'works-logistics',
        ];

        return redirect()->route('user.directorates.show', $legacyMap[$id] ?? 'hrm');
    })->name('user.directorate');
});
// Super-admin (executive) — view-only dashboard and consolidated returns
Route::middleware([Authenticate::class, 'access:category=super_admin,location=headquarters,role=super_admin|minLevel=6', 'abac.geo'])->group(function () {
    Route::get('/superadmin/dashboard', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/superadmin/returns', [SuperAdminController::class, 'returns'])->name('superadmin.returns');
    Route::get('/superadmin/returns/{applicationHash}', [SuperAdminController::class, 'show'])->name('superadmin.returns.show');
    Route::get('/superadmin/returns/{applicationHash}/documents/{collection}/{index}', [SubmissionReviewController::class, 'document'])->middleware('signed')->name('superadmin.returns.document');
    Route::get('/superadmin/returns/{applicationHash}/download', [SubmissionReviewController::class, 'download'])->middleware('signed')->name('superadmin.returns.download');
});

// Supervisor dashboards (state and zonal share the same view)
Route::middleware([Authenticate::class, 'access:category=desk_admin|zonal_commander,location=state|zonal,role=admin|state|zonal|minLevel=1', 'abac.geo'])->group(function () {
    Route::get('/dashboard/state', function () {
        return view('supervisor.dashboard');
    })->name('supervisor.dashboard.state');

    Route::get('/dashboard/zonal', function () {
        return view('supervisor.dashboard');
    })->name('supervisor.dashboard.zonal');

    // Generic named route used in blade links
    Route::get('/supervisor/dashboard', function () {
        $url = Auth::user()->user_category === 'zonal_commander'
            ? '/dashboard/zonal'
            : '/dashboard/state';

        return redirect($url);
    })->name('supervisor.dashboard');
});

// Admin area — read-only routes shared by HQ admins (approvers) and general admins (view-only)
Route::middleware([Authenticate::class, 'access:category=hq_admin|admin,location=headquarters,role=admin|minLevel=5', 'abac.geo'])->group(function () {
    Route::get('/admin/dashboard', [HqAdminController::class, 'index'])->name('admin.dashboard');

    Route::get('/admin/hq/returns', [HqAdminController::class, 'returns'])->name('admin.hq.returns');
    Route::get('/admin/hq/returns/{applicationHash}', [HqAdminController::class, 'show'])->name('admin.hq.returns.show');
    Route::get('/admin/hq/returns/{applicationHash}/documents/{collection}/{index}', [SubmissionReviewController::class, 'document'])->middleware('signed')->name('admin.hq.returns.document');
    Route::get('/admin/hq/returns/{applicationHash}/download', [SubmissionReviewController::class, 'download'])->middleware('signed')->name('admin.hq.returns.download');
    Route::get('/admin/hq/archive', [HqAdminController::class, 'archive'])->name('admin.hq.archive');
    Route::get('/admin/hq/analytics', [HqAdminController::class, 'analytics'])->name('admin.hq.analytics');
    Route::get('/admin/hq/reports', [HqAdminController::class, 'reports'])->name('admin.hq.reports');
    Route::post('/admin/hq/reports/generate', [HqAdminController::class, 'generateReport'])->middleware('throttle:database')->name('admin.hq.reports.generate');

    Route::get('/admin/consolidation', [ConsolidationController::class, 'index'])->name('admin.consolidation');
});

// HQ admin only — final review actions and platform administration
Route::middleware([Authenticate::class, 'access:category=hq_admin,location=headquarters,role=admin|minLevel=5', 'abac.geo'])->group(function () {
    Route::get('/admin/submissions', [SubmissionReviewController::class, 'index'])->name('admin.submissions');
    Route::patch('/admin/submissions/{applicationHash}/approve', [SubmissionReviewController::class, 'approve'])->middleware(['signed', 'throttle:database'])->name('admin.submissions.approve');
    Route::patch('/admin/submissions/{applicationHash}/reject', [SubmissionReviewController::class, 'reject'])->middleware(['signed', 'throttle:database'])->name('admin.submissions.reject');

    Route::get('/admin/settings', [AdminSettingController::class, 'index'])->name('admin.settings.index');
    Route::put('/admin/settings', [AdminSettingController::class, 'update'])->name('admin.settings.update');

    Route::get('/admin/audit-log', [AdminAuditLogController::class, 'index'])->name('admin.audit-log.index');

    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users');
    Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->middleware('throttle:database')->name('admin.users.store');
    Route::get('/admin/users/{userHash}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::patch('/admin/users/{userHash}', [UserManagementController::class, 'update'])->middleware(['signed', 'throttle:database'])->name('admin.users.update');
    Route::patch('/admin/users/{userHash}/toggle-status', [UserManagementController::class, 'toggleStatus'])->middleware(['signed', 'throttle:database'])->name('admin.users.toggle_status');
});

// MFA challenge routes (pending login stage)
Route::middleware(['mfa.pending'])->group(function () {
    Route::get('/mfa/setup', [MfaController::class, 'setup'])->name('mfa.setup');
    Route::post('/mfa/setup', [MfaController::class, 'verifySetup'])->name('mfa.setup.verify');
    Route::get('/mfa/challenge', [MfaController::class, 'challenge'])->name('mfa.challenge');
    Route::post('/mfa/challenge', [MfaController::class, 'verifyChallenge'])->name('mfa.challenge.verify');
    Route::post('/mfa/complete', [MfaController::class, 'complete'])->name('mfa.complete');
    Route::post('/mfa/cancel', [MfaController::class, 'cancel'])->name('mfa.cancel');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->middleware('throttle:database')->name('logout');
