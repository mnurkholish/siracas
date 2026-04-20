<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\PasswordResetLinkController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionsController;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. PUBLIC ROUTES
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');


// ==========================================
// 2. GUEST ROUTES
// ==========================================
Route::middleware('guest')->group(function () {
    // Registration
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Login
    Route::get('login', [SessionsController::class, 'create'])->name('login');
    Route::post('login', [SessionsController::class, 'store']);


    // Lupa Password
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    // Reset Password
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');

});


// ==========================================
// 3. AUTHENTICATED ROUTES
// ==========================================
Route::middleware('auth')->group(function () {

    Route::post('logout', [SessionsController::class, 'destroy'])->name('logout');

    // -- Admin Routes --
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
    });

    // -- Customer Routes --
    Route::middleware('role:customer')->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', function () {
            return view('customer.dashboard');
        })->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

        Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    });
});
