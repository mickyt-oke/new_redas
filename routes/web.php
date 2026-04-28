<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/home', function () {
    return view('welcome');
});

// Authentication Routes (guests only)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');

    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', function () {
        return view('register');
    })->name('register');

    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

// State user (officer) routes — full access to all user pages
Route::middleware(['auth', 'role:officer'])->group(function () {
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

    Route::get('/user/profile', function () {
        return redirect()->route('user.dashboard');
    })->name('user.profile');

    Route::post('/user/returns', function (\Illuminate\Http\Request $request) {
        return redirect()->route('user.submissions')
            ->with('status', 'Return submitted successfully and routed to your supervisor for review.');
    })->name('user.returns.store');
});

// Directorate user routes — access only to directorate pages
Route::middleware(['auth', 'role:directorate'])->group(function () {
    Route::get('/user/directorate', function () {
        return view('user.directorate');
    })->name('user.directorate.home');
});

// Shared directorate form routes — accessible by both state (officer) and directorate users
Route::middleware(['auth', 'role:officer,directorate'])->group(function () {
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

// Supervisor dashboards (state and zonal share the same view)
Route::middleware(['auth', 'role:state,zonal'])->group(function () {
    Route::get('/dashboard/state', function () {
        return view('supervisor.dashboard');
    })->name('supervisor.dashboard.state');

    Route::get('/dashboard/zonal', function () {
        return view('supervisor.dashboard');
    })->name('supervisor.dashboard.zonal');

    // Generic named route used in blade links
    Route::get('/supervisor/dashboard', function () {
        $url = Auth::user()->role === 'zonal'
            ? '/dashboard/zonal'
            : '/dashboard/state';
        return redirect($url);
    })->name('supervisor.dashboard');
});

// Admin dashboard
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
