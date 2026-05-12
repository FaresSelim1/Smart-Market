<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Livewire\Storefront;
use App\Livewire\ShoppingCart;
use App\Livewire\WishlistPage;
use Illuminate\Support\Facades\Route;

// 1. PUBLIC & BRANCH-SCOPED ROUTES
Route::middleware(['web', \App\Http\Middleware\SetBranchContext::class])->group(function () {
    Route::get('/', Storefront::class)->name('home');
    Route::get('/cart', ShoppingCart::class)->name('cart');
    Route::get('/products/{product}', [\App\Http\Controllers\ProductPageController::class, 'show'])->name('products.show');
    Route::get('/checkout', \App\Livewire\Checkout::class)->name('checkout');
});

// 2. AUTHENTICATION
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->name('logout');
});

// 3. PROTECTED ROUTES (Orders, Checkout, Wishlist)
Route::middleware(['auth'])->group(function () {
    Route::get('/wishlist', WishlistPage::class)->name('wishlist');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');
    
    Route::get('/payment/success', [PaymentController::class, 'handleGatewayCallback'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'handleCancel'])->name('payment.cancel');
});

// 4. WEBHOOKS (no CSRF, no auth)
Route::post('/webhooks/payment', [PaymentController::class, 'webhook'])->name('webhooks.payment');