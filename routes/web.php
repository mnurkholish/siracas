<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\CustomerProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\PasswordResetLinkController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
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
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/akun-customer', [CustomerAccountController::class, 'index'])->name('customer.index');
        Route::get('/akun-customer/{id}', [CustomerAccountController::class, 'show'])->name('customer.show');

        Route::get('/product/archives', [ProductController::class, 'archives'])->name('product.archives');
        Route::patch('/product/archives/{product}/restore', [ProductController::class, 'restore'])->name('product.restore');
        Route::resource('/product', ProductController::class)->except(['create', 'edit']);

        Route::get('/profile', [ProfileController::class, 'adminIndex'])->name('profile');
        Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });


    // -- Customer Routes --
    Route::middleware('role:customer')->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.items.update');
        Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy'])->name('cart.items.destroy');
    });

    Route::middleware('role:customer')->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [HomeController::class, 'customerDashboard'])->name('dashboard');
        Route::get('/product', [CustomerProductController::class, 'index'])->name('product.index');
        Route::get('/product/{product}', [CustomerProductController::class, 'show'])->name('product.show');

        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

        Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    });
});
