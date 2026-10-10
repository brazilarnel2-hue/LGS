<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('services', AdminServiceController::class);
        Route::resource('users', AdminUserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::get('/settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/reset', [AdminSettingController::class, 'reset'])->name('settings.reset');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('orders', OrderController::class);
    Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');

    Route::post('/orders/{order}/payment', [PaymentController::class, 'store'])->name('orders.payment.store');
    Route::post('/orders/{order}/payment/verify', [PaymentController::class, 'verifyGcash'])->name('orders.payment.verify');
    Route::get('/orders/{order}/pay-gcash', [PaymentController::class, 'showGcashCheckout'])->name('orders.gcash.checkout');
    Route::post('/orders/{order}/pay-gcash', [PaymentController::class, 'confirmGcashPayment'])->name('orders.gcash.confirm');
    Route::post('/orders/{order}/pay-cod', [PaymentController::class, 'selectCod'])->name('orders.cod.select');
    Route::post('/orders/{order}/pay-personal', [PaymentController::class, 'selectPersonal'])->name('orders.personal.select');
});

require __DIR__.'/auth.php';