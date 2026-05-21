<?php

use App\Http\Controllers\Admin\AdminTransactionController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\CustomerAccountController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationCampaignController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SessionsController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CustomerProductController;
use App\Http\Controllers\Customer\NotificationController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Customer\TransactionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MidtransCallbackController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. PUBLIC ROUTES
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/midtrans/callback', MidtransCallbackController::class)->name('midtrans.callback');


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
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::patch('/notifications/{notification}/read', [AdminNotificationController::class, 'read'])->name('notifications.read');
        Route::patch('/notifications/read-all', [AdminNotificationController::class, 'readAll'])->name('notifications.read-all');
        Route::delete('/notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/notifications', [AdminNotificationController::class, 'destroyAll'])->name('notifications.clear');

        Route::get('/customers', [CustomerAccountController::class, 'index'])->name('customers.index');
        Route::get('/customers/{id}', [CustomerAccountController::class, 'show'])->name('customers.show');
        Route::patch('/customers/{user}/status', [CustomerAccountController::class, 'updateStatus'])->name('customers.status');

        Route::get('/products/archives', [ProductController::class, 'archives'])->name('products.archives');
        Route::patch('/products/archives/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::resource('/products', ProductController::class)->except(['create', 'edit'])->names('products');

        Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/history', [AdminTransactionController::class, 'history'])->name('transactions.history');
        Route::get('/transactions/{transaction}', [AdminTransactionController::class, 'show'])->name('transactions.show');
        Route::patch('/transactions/{transaction}/status', [AdminTransactionController::class, 'updateStatus'])->name('transactions.status');
        Route::patch('/transactions/{transaction}/shipping', [AdminTransactionController::class, 'updateOngkir'])->name('transactions.shipping');
        Route::patch('/transactions/{transaction}/refund', [AdminTransactionController::class, 'updateRefund'])->name('transactions.refund');
        Route::patch('/transactions/{transaction}/warranty', [AdminTransactionController::class, 'processWarranty'])->name('transactions.warranty');

        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/{review}/reply/create', [AdminReviewController::class, 'create'])->name('reviews.create');
        Route::put('/reviews/{review}/reply', [AdminReviewController::class, 'reply'])->name('reviews.reply');

        Route::get('/notification-campaigns', [NotificationCampaignController::class, 'index'])->name('campaigns.index');
        Route::post('/notification-campaigns', [NotificationCampaignController::class, 'store'])->name('campaigns.store');
        Route::put('/notification-campaigns/{notificationCampaign}', [NotificationCampaignController::class, 'update'])->name('campaigns.update');
        Route::delete('/notification-campaigns/{notificationCampaign}', [NotificationCampaignController::class, 'destroy'])->name('campaigns.destroy');
        Route::patch('/notification-campaigns/{notificationCampaign}/publish', [NotificationCampaignController::class, 'publish'])->name('campaigns.publish');
        Route::patch('/notification-campaigns/{notificationCampaign}/unpublish', [NotificationCampaignController::class, 'unpublish'])->name('campaigns.unpublish');

        Route::get('/profile', [ProfileController::class, 'adminIndex'])->name('profile');
        Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });

    // -- Customer Routes --
    Route::middleware('role:customer')->group(function () {
        Route::get('/dashboard', [HomeController::class, 'customerDashboard'])->name('dashboard');

        Route::get('/products', [CustomerProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product}/reviews', [CustomerProductController::class, 'reviews'])->name('products.reviews');
        Route::get('/products/{product}', [CustomerProductController::class, 'show'])->name('products.show');

        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.items.update');
        Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy'])->name('cart.items.destroy');

        Route::get('/checkout', [TransactionController::class, 'checkoutForm'])->name('checkout.index');
        Route::post('/checkout', [TransactionController::class, 'checkoutProcess'])->name('checkout.store');
        Route::get('/addresses/create', [AddressController::class, 'create'])->name('addresses.create');
        Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
        Route::get('/addresses/cities', [AddressController::class, 'kotas'])->name('addresses.cities');
        Route::get('/addresses/districts', [AddressController::class, 'kecamatans'])->name('addresses.districts');
        Route::get('/products/{product}/buy-now', [TransactionController::class, 'buyNowForm'])->name('products.buy-now.index');
        Route::post('/products/{product}/buy-now', [TransactionController::class, 'buyNowProcess'])->name('products.buy-now.store');
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/history', [TransactionController::class, 'history'])->name('transactions.history');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::post('/transactions/{transaction}/pay', [TransactionController::class, 'pay'])->name('transactions.pay');
        Route::patch('/transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
        Route::patch('/transactions/{transaction}/complete', [TransactionController::class, 'complete'])->name('transactions.complete');
        Route::patch('/transactions/{transaction}/warranty', [TransactionController::class, 'claimWarranty'])->name('transactions.warranty');
        Route::get('/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
        Route::get('/reviews/history', [ReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/history/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
        Route::put('/reviews/history/{review}', [ReviewController::class, 'update'])->name('reviews.update');
        Route::get('/reviews/{transactionDetail}', [ReviewController::class, 'show'])->whereNumber('transactionDetail')->name('reviews.show');
        Route::post('/transactions/{transaction}/details/{transactionDetail}/reviews', [ReviewController::class, 'store'])->name('transactions.details.reviews.store');

        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
        Route::patch('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('notifications.clear');

        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

        Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    });
});
