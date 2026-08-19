<?php

use App\Http\Controllers\ApiNotificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VisaController;
use App\Http\Controllers\IctController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\AuthTokenController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/home', function () {
    return view('welcome');
});

Route::view('/terms-and-conditions', 'legal.terms')->name('terms');
Route::view('/privacy-policy', 'legal.privacy')->name('privacy');

// Authentication Routes (open to all so multiple logins are supported)
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::middleware('guest')->group(function () {
    Route::get('/register', function () {
        return view('register');
    })->name('register');

    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    // Magic link verification + password reset (web)
    Route::get('/verify-email/{token}', [AuthTokenController::class, 'verifyEmail'])->name('verify.email');
    Route::post('/password/forgot', [AuthTokenController::class, 'requestPasswordReset'])->name('password.forgot');
    Route::get('/password/reset/{token}', [AuthTokenController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/password/reset/{token}', [AuthTokenController::class, 'resetPassword'])->name('password.reset');
});

// General Authenticated Routes (Accessible by all user categories/locations)
Route::middleware(['auth'])->group(function () {
    Route::get('/user/profile', [AuthController::class, 'profile'])->name('user.profile');
    Route::post('/user/profile', [AuthController::class, 'profileUpdate'])->name('user.profile.update');
});

// State user (officer) routes — full access to all user pages
Route::middleware(['auth', 'access:category=state_user|desk_admin,location=state,role=user|officer|admin|state|minLevel=0'])->group(function () {
    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })->name('user.dashboard');

    Route::get('/user/returns/create', function () {
        return view('user.create-return');
    })->name('user.returns.create');

    Route::get('/user/submissions', function () {
        return view('user.submissions');
    })->name('user.submissions');

    Route::get('/user/notifications', function () {
        return view('user.notifications');
    })->name('user.notifications');

    // Realtime-friendly notifications endpoints (session auth)
    Route::get('/user/notifications/api', [ApiNotificationController::class, 'index']);
    Route::get('/user/notifications/count', [ApiNotificationController::class, 'count']);
    Route::post('/user/notifications/mark-all-read', [ApiNotificationController::class, 'markAllRead']);
    Route::post('/user/notifications/{id}/read', [ApiNotificationController::class, 'markRead']);

    Route::get('/user/archive', function () {
        return view('user.archive');
    })->name('user.archive');

    Route::get('/user/archive/upload', function () {
        return view('user.archive');
    })->name('user.archive.upload');

    Route::post('/user/archive/upload', function (\Illuminate\Http\Request $request) {
        return redirect()->route('user.archive')
            ->with('status', 'Document(s) uploaded to archive successfully.');
    })->name('user.archive.store');

    Route::get('/user/reports', function () {
        return view('user.reports');
    })->name('user.reports');

    Route::post('/user/reports/generate', function (\Illuminate\Http\Request $request) {
        return redirect()->route('user.reports')
            ->with('status', 'Your report has been generated and is ready for download.');
    })->name('user.reports.generate');


    Route::post('/user/returns', function (\Illuminate\Http\Request $request) {
        return redirect()->route('user.submissions')
            ->with('status', 'Return submitted successfully and routed to your supervisor for review.');
    })->name('user.returns.store');
});

// Directorate user routes — access only to directorate pages
Route::middleware(['auth', 'access:category=directorate_user|directorate_admin,location=directorate,role=user|admin|directorate|minLevel=2'])->group(function () {
    Route::get('/user/directorate', function () {
        return view('user.directorate');
    })->name('user.directorate.home');
});

//Visa Directorate Routes
// Contributor: ASI Adamma Blessing Eze
// Date: July 2026

Route::middleware([
    'auth',
    'access:category=directorate_user|directorate_admin,location=directorate,role=user|admin|directorate|minLevel=2'
])
->prefix('user/directorate/visa')
->name('visa.')
->group(function () {

    // Visa Dashboard
    Route::get('/', [VisaController::class, 'dashboard'])
        ->name('dashboard');

    // Display Monthly Visa & Residence Return Form
    Route::get('/report', [VisaController::class, 'create'])
        ->name('report');

    // Save Monthly Return
    Route::post('/report', [VisaController::class, 'store'])
        ->name('store');

    // View Generated Reports
    Route::get('/reports', [VisaController::class, 'reports'])
        ->name('reports');

    // View Submitted Returns
    Route::get('/submissions', [VisaController::class, 'submissions'])
        ->name('submissions');

    // Approve Return
    Route::post('/submissions/{id}/approve', [VisaController::class, 'approve'])
        ->name('approve');

    // Query Return
    Route::post('/submissions/{id}/query', [VisaController::class, 'query'])
        ->name('query');

    // Submit Approved Return
    Route::post('/submissions/{id}/submit', [VisaController::class, 'submit'])
        ->name('submit');

});

//ICT & Cybersecurity Directorate Routes
Route::middleware([
    'auth',
    'access:category=directorate_user|directorate_admin,location=directorate,role=user|admin|directorate|minLevel=2'
])
->prefix('user/directorate/ict')
->name('ict.')
->group(function () {

    // ICT Dashboard
    Route::get('/', [IctController::class, 'dashboard'])
        ->name('dashboard');

    // Display Monthly ICT & Cybersecurity Return Form
    Route::get('/report', [IctController::class, 'create'])
        ->name('report');

    // Save Monthly Return
    Route::post('/report', [IctController::class, 'store'])
        ->name('store');

    // View Generated Reports
    Route::get('/reports', [IctController::class, 'reports'])
        ->name('reports');

    // View Submitted Returns
    Route::get('/submissions', [IctController::class, 'submissions'])
        ->name('submissions');

    // Approve Return
    Route::post('/submissions/{id}/approve', [IctController::class, 'approve'])
        ->name('approve');

    // Query Return
    Route::post('/submissions/{id}/query', [IctController::class, 'query'])
        ->name('query');

    // Submit Approved Return
    Route::post('/submissions/{id}/submit', [IctController::class, 'submit'])
        ->name('submit');

});

// Shared directorate form routes — accessible by both state (officer) and directorate users

Route::middleware(['auth', 'access:category=state_user|directorate_user|directorate_admin,location=state|directorate,role=user|admin|officer|directorate|minLevel=0'])->group(function () {
    Route::get('/user/directorates/{slug}', [DashboardController::class, 'showDirectorate'])->name('user.directorates.show');
    Route::post('/user/directorates/{slug}', [DashboardController::class, 'storeDirectorate'])->name('user.directorates.store');

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
Route::middleware(['auth', 'access:category=super_admin,location=headquarters,role=super_admin|minLevel=6'])->group(function () {
    Route::get('/superadmin/dashboard', function () {
        // Redirect to the superadmin users list until the dashboard view is available
        return redirect()->route('superadmin.users');
    })->name('superadmin.dashboard');
});
Route::middleware(['auth', 'access:category=super_admin,location=headquarters,role=super_admin|minLevel=6'])->group(function () {
    Route::get('/superadmin/users', function () {
        return redirect()->route('superadmin.users.create');
    })->name('superadmin.users');

    Route::get('/superadmin/users/create', function () {
        return redirect()->route('superadmin.dashboard');
    })->name('superadmin.users.create');

    Route::post('/superadmin/users', function (\Illuminate\Http\Request $request) {
        return redirect()->route('superadmin.users')
            ->with('status', 'User created successfully.');
    })->name('superadmin.users.store');
});

// Supervisor dashboards (state and zonal share the same view)
Route::middleware(['auth', 'access:category=desk_admin|zonal_commander,location=state|zonal,role=admin|state|zonal|minLevel=1'])->group(function () {
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
Route::middleware(['auth', 'access:category=admin,location=headquarters,role=admin|minLevel=5'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

// Admin User Management
Route::middleware(['auth', 'access:category=admin|directorate_admin,role=admin|minLevel=3'])->group(function () {
    Route::get('/admin/users', [App\Http\Controllers\AdminUserController::class, 'index'])->name('admin.users');
    Route::get('/admin/users/create', [App\Http\Controllers\AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [App\Http\Controllers\AdminUserController::class, 'store'])->name('admin.users.store');
    Route::delete('/admin/users/{id}', [App\Http\Controllers\AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});

// User Notifications Mark Read
Route::post('/user/notifications/mark-read', function() {
    \App\Models\UserNotification::where('user_id', auth()->id())->update(['is_read' => true]);
    return response()->json(['success' => true]);
})->middleware('auth')->name('notifications.mark-read');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
