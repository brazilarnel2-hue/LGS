<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Models\Review;

Route::get('/', function () {
    // Pinakamahuhusay/bagong reviews na may comment lang (para may laman) —
    // para sa "What our customers say" sa welcome page, kahit hindi naka-login.
    $testimonials = Review::with('user')
        ->whereNotNull('comment')
        ->where('comment', '!=', '')
        ->latest()
        ->take(6)
        ->get();

    $averageRating = Review::avg('rating');
    $totalReviews = Review::count();

    return view('welcome', compact('testimonials', 'averageRating', 'totalReviews'));
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified', EnsureUserIsActive::class])->name('dashboard');

Route::middleware(['auth', EnsureUserIsActive::class])->group(function () {

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('services', AdminServiceController::class);
        Route::resource('users', AdminUserController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::patch('/users/{user}/deactivate', [AdminUserController::class, 'deactivate'])->name('users.deactivate');
        Route::patch('/users/{user}/activate', [AdminUserController::class, 'activate'])->name('users.activate');
        Route::get('/settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/reset', [AdminSettingController::class, 'reset'])->name('settings.reset');

        Route::get('/reports/sales', [AdminReportController::class, 'sales'])->name('reports.sales');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('orders', OrderController::class);
    Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::post('/orders/{order}/payment', [PaymentController::class, 'store'])->name('orders.payment.store');
    Route::post('/orders/{order}/payment/verify', [PaymentController::class, 'verifyGcash'])->name('orders.payment.verify');
    Route::get('/orders/{order}/pay-gcash', [PaymentController::class, 'showGcashCheckout'])->name('orders.gcash.checkout');
    Route::post('/orders/{order}/pay-gcash', [PaymentController::class, 'confirmGcashPayment'])->name('orders.gcash.confirm');
    Route::post('/orders/{order}/pay-cod', [PaymentController::class, 'selectCod'])->name('orders.cod.select');
    Route::post('/orders/{order}/pay-personal', [PaymentController::class, 'selectPersonal'])->name('orders.personal.select');

    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/orders/{order}/review', [ReviewController::class, 'store'])->name('orders.review.store');
});

require __DIR__.'/auth.php';