<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('welcome');
});
// Authentication Routes with middleware to prevent access to login/register pages if already authenticated
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');
    
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Registration Routes
    Route::get('/register', function () {
        return view('register');
    })->name('register');
    
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

//User Dashboard Route (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/user/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

//Admin Dashboard Route (Protected)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin_dashboard');
    })->name('admin.dashboard');
});

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
