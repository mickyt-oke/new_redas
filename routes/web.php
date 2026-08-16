<?php

use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ApiNotificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthTokenController;
use App\Http\Controllers\MfaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionReviewController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/home', function () {
    return view('welcome');
});

Route::view('/terms-and-conditions', 'legal.terms')->name('terms');
Route::view('/privacy-policy', 'legal.privacy')->name('privacy');
Route::get('/directory', [\App\Http\Controllers\Web\DirectoryController::class, 'index'])->name('directory');

// Authentication Routes (guests only)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');

    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:database')->name('login.submit');

    Route::get('/password/forgot', [AuthTokenController::class, 'showForgotForm'])->name('password.forgot.form');

    Route::get('/register', function () {
        return redirect()->route('login')
            ->with('status', 'User registration is now managed by HQ administrators. Please contact your administrator for access.');
    })->name('register');

    // Magic link verification + password reset (web)
    Route::get('/verify-email/{token}', [AuthTokenController::class, 'verifyEmail'])->name('verify.email');
    Route::post('/password/forgot', [AuthTokenController::class, 'requestPasswordReset'])->middleware('throttle:database')->name('password.forgot');
    Route::get('/password/reset/{token}', [AuthTokenController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/password/reset/{token}', [AuthTokenController::class, 'resetPassword'])->middleware('throttle:database')->name('password.reset');
});

// State user (officer) routes — full access to all user pages
Route::middleware(['auth', 'access:category=state_user|desk_admin,location=state,role=user|officer|admin|state|minLevel=0', 'abac.geo'])->group(function () {
    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })->name('user.dashboard');

    Route::get('/user/returns/create', function () {
        return view('user.states.create-return');
    })->name('user.returns.create');

    Route::get('/user/submissions', function () {
        return view('user.states.submissions');
    })->name('user.submissions');

    Route::get('/user/notifications', function () {
        return view('user.states.notifications');
    })->name('user.notifications');

    // Realtime-friendly notifications endpoints (session auth)
    Route::get('/user/notifications/api', [ApiNotificationController::class, 'index']);
    Route::get('/user/notifications/count', [ApiNotificationController::class, 'count']);
    Route::post('/user/notifications/mark-all-read', [ApiNotificationController::class, 'markAllRead']);
    Route::post('/user/notifications/{id}/read', [ApiNotificationController::class, 'markRead']);

    Route::get('/user/archive', function () {
        return view('user.states.archive');
    })->name('user.archive');

    Route::get('/user/archive/upload', function () {
        return view('user.states.archive');
    })->name('user.archive.upload');

    Route::post('/user/archive/upload', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'doc_type' => ['required', 'string', 'max:50'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:20480'],
            'data_consent' => ['required', 'accepted'],
        ]);

        return redirect()->route('user.archive')
            ->with('status', 'Document(s) uploaded to archive successfully.');
    })->middleware('throttle:database')->name('user.archive.store');

    Route::get('/user/reports', function () {
        return view('user.states.reports');
    })->name('user.reports');

    Route::post('/user/reports/generate', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'report_type' => ['required', 'in:submission_summary,monthly_return,quarterly_return,annual_return,compliance_report,full_export'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'sections' => ['nullable', 'array'],
            'format' => ['required', 'in:pdf,excel,csv'],
        ]);

        return redirect()->route('user.reports')
            ->with('status', 'Your report has been generated and is ready for download.');
    })->middleware('throttle:database')->name('user.reports.generate');

    Route::post('/user/returns', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'command_name' => ['required', 'string', 'max:120'],
            'period' => ['required', 'date_format:Y-m'],
            'return_type' => ['required', 'in:monthly,quarterly,biannual,annual,special'],
            'reporting_officer' => ['required', 'string', 'max:120'],
            'data_consent' => ['required', 'accepted'],
        ]);

        \App\Services\SubmissionWorkflow::create(
            Auth::User(),
            $request->except(['_token', 'data_consent'])
        );

        return redirect()->route('user.submissions')
            ->with('status', 'Return submitted successfully and routed to your supervisor for review.');
    })->middleware('throttle:database')->name('user.returns.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/user/profile', [ProfileController::class, 'edit'])->name('user.profile');
    Route::patch('/user/profile', [ProfileController::class, 'update'])->middleware('throttle:database')->name('user.profile.update');
});

// Zonal commander routes — access only to zonal pages
Route::middleware(['auth', 'access:category=zonal_commander,location=zonal,role=admin|zonal|minLevel=1', 'abac.geo'])->group(function () {
    Route::get('/zonal/dashboard', [SubmissionReviewController::class, 'index'])->name('user.zonal.home');
    Route::patch('/zonal/submissions/{application}/approve', [SubmissionReviewController::class, 'approve'])->middleware('throttle:database')->name('zonal.submissions.approve');
    Route::patch('/zonal/submissions/{application}/reject', [SubmissionReviewController::class, 'reject'])->middleware('throttle:database')->name('zonal.submissions.reject');
});

// Desk / directorate admin review routes
Route::middleware(['auth', 'access:category=desk_admin|directorate_admin,location=state|directorate,role=admin|state|directorate|minLevel=1', 'abac.geo'])->group(function () {
    Route::get('/desk-admin/dashboard', [SubmissionReviewController::class, 'index'])->name('user.desk.home');
    Route::patch('/desk-admin/submissions/{application}/approve', [SubmissionReviewController::class, 'approve'])->middleware('throttle:database')->name('desk.admin.submissions.approve');
    Route::patch('/desk-admin/submissions/{application}/reject', [SubmissionReviewController::class, 'reject'])->middleware('throttle:database')->name('desk.admin.submissions.reject');
    Route::get('/desk-admin/submissions/{application}', [SubmissionReviewController::class, 'show'])->name('desk.admin.submissions.show');
});

// CGIS Units

// Directorate user routes — access only to directorate pages
// Note: Each Directorate user has a unique slug (e.g., hrm, prs, finance) that is used to access their specific directorate form.
Route::middleware(['auth', 'access:category=directorate_user|directorate_admin,location=directorate,role=user|admin|directorate|minLevel=0', 'abac.geo'])->group(function () {
    Route::get('/user/directorates', function () {
        return redirect()->route('user.directorates.dashboard');
    })->name('user.directorates.home');

    Route::get('/user/directorates/dashboard', [DashboardController::class, 'directorateDashboard'])->name('user.directorates.dashboard');

    Route::get('/user/directorates/submissions/{application}', [DashboardController::class, 'showSubmission'])->name('user.directorates.submissions.show');
    Route::get('/user/directorates/submissions/{application}/print', [DashboardController::class, 'printSubmission'])->name('user.directorates.submissions.print');
    Route::get('/user/directorates/submissions/{application}/edit', [DashboardController::class, 'editSubmission'])->name('user.directorates.submissions.edit');
    Route::put('/user/directorates/submissions/{application}', [DashboardController::class, 'updateSubmission'])->middleware('throttle:database')->name('user.directorates.submissions.update');

    Route::get('/user/directorates/{slug}', [DashboardController::class, 'showDirectorate'])->name('user.directorates.show');
    Route::post('/user/directorates/{slug}', [DashboardController::class, 'storeDirectorate'])->middleware('throttle:database')->name('user.directorates.store');
});

// Shared directorate form routes — accessible by both state (officer) and directorate users

Route::middleware(['auth', 'access:category=state_user|directorate_user|directorate_admin,location=state|directorate,role=user|admin|officer|directorate|minLevel=0', 'abac.geo'])->group(function () {
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
Route::middleware(['auth', 'access:category=super_admin,location=headquarters,role=super_admin|minLevel=6', 'abac.geo'])->group(function () {
    Route::get('/superadmin/dashboard', function () {
        // Redirect to the superadmin users list until the dashboard view is available
        return redirect()->route('superadmin.users');
    })->name('superadmin.dashboard');
});
Route::middleware(['auth', 'access:category=super_admin,location=headquarters,role=super_admin|minLevel=6', 'abac.geo'])->group(function () {
    Route::get('/superadmin/submissions', [SubmissionReviewController::class, 'index'])->name('superadmin.submissions');
    Route::patch('/superadmin/submissions/{application}/approve', [SubmissionReviewController::class, 'approve'])->middleware('throttle:database')->name('superadmin.submissions.approve');
    Route::patch('/superadmin/submissions/{application}/reject', [SubmissionReviewController::class, 'reject'])->middleware('throttle:database')->name('superadmin.submissions.reject');

    Route::get('/superadmin/users', function () {
        return redirect()->route('superadmin.users.create');
    })->name('superadmin.users');

    Route::get('/superadmin/users/create', function () {
        return redirect()->route('superadmin.dashboard');
    })->name('superadmin.users.create');

    Route::post('/superadmin/users', function (\Illuminate\Http\Request $request) {
        return redirect()->route('superadmin.users')
            ->with('status', 'User created successfully.');
    })->middleware('throttle:database')->name('superadmin.users.store');
});

// Supervisor dashboards (state and zonal share the same view)
Route::middleware(['auth', 'access:category=desk_admin|zonal_commander,location=state|zonal,role=admin|state|zonal|minLevel=1', 'abac.geo'])->group(function () {
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

// Admin dashboard
Route::middleware(['auth', 'access:category=admin,location=headquarters,role=admin|minLevel=5', 'abac.geo'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/submissions', [SubmissionReviewController::class, 'index'])->name('admin.submissions');
    Route::patch('/admin/submissions/{application}/approve', [SubmissionReviewController::class, 'approve'])->middleware('throttle:database')->name('admin.submissions.approve');
    Route::patch('/admin/submissions/{application}/reject', [SubmissionReviewController::class, 'reject'])->middleware('throttle:database')->name('admin.submissions.reject');

    Route::get('/admin/settings', [AdminSettingController::class, 'index'])->name('admin.settings.index');
    Route::put('/admin/settings', [AdminSettingController::class, 'update'])->name('admin.settings.update');

    Route::get('/admin/audit-log', [AdminAuditLogController::class, 'index'])->name('admin.audit-log.index');

    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users');
    Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->middleware('throttle:database')->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::patch('/admin/users/{user}', [UserManagementController::class, 'update'])->middleware('throttle:database')->name('admin.users.update');
    Route::patch('/admin/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->middleware('throttle:database')->name('admin.users.toggle_status');
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
