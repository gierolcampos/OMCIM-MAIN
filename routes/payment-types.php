<?php

use App\Http\Controllers\CashPaymentController;
use App\Http\Controllers\GcashPaymentController;
use Illuminate\Support\Facades\Route;

// Admin Payment Type Routes
Route::prefix('admin/payment-types')->middleware(['auth', 'permission:manage-payments'])->group(function () {
    // Cash Payments
    Route::prefix('cash')->group(function () {
        Route::get('/', [CashPaymentController::class, 'index'])->name('admin.cash-payments.index');
        Route::get('/create', [CashPaymentController::class, 'create'])->name('admin.cash-payments.create');
        Route::post('/', [CashPaymentController::class, 'store'])->name('admin.cash-payments.store');
        Route::get('/{id}', [CashPaymentController::class, 'show'])->name('admin.cash-payments.show');
        Route::get('/{id}/edit', [CashPaymentController::class, 'edit'])->name('admin.cash-payments.edit');
        Route::put('/{id}', [CashPaymentController::class, 'update'])->name('admin.cash-payments.update');
        Route::delete('/{id}', [CashPaymentController::class, 'destroy'])->name('admin.cash-payments.destroy');
        Route::post('/{id}/approve', [CashPaymentController::class, 'approve'])->name('payment.types.cash.approve');
        Route::post('/{id}/admin-approve', [CashPaymentController::class, 'approve'])->name('admin.cash-payments.approve');
        // Reject route removed as requested
    });

    // GCash Payments
    Route::prefix('gcash')->group(function () {
        Route::get('/', [GcashPaymentController::class, 'index'])->name('admin.gcash-payments.index');
        Route::get('/create', [GcashPaymentController::class, 'create'])->name('admin.gcash-payments.create');
        Route::post('/', [GcashPaymentController::class, 'store'])->name('admin.gcash-payments.store');
        Route::get('/{id}', [GcashPaymentController::class, 'show'])->name('admin.gcash-payments.show');
        Route::get('/{id}/edit', [GcashPaymentController::class, 'edit'])->name('admin.gcash-payments.edit');
        Route::put('/{id}', [GcashPaymentController::class, 'update'])->name('admin.gcash-payments.update');
        Route::delete('/{id}', [GcashPaymentController::class, 'destroy'])->name('admin.gcash-payments.destroy');
        Route::post('/{id}/approve', [GcashPaymentController::class, 'approve'])->name('payment.types.gcash.approve');
        Route::post('/{id}/admin-approve', [GcashPaymentController::class, 'approve'])->name('admin.gcash-payments.approve');
        // Reject route removed as requested
    });
});

// Client Payment Type Routes
Route::prefix('omcms/payment-types')->middleware(['auth', 'verified'])->group(function () {
    // Cash Payments
    Route::prefix('cash')->group(function () {
        Route::get('/', [CashPaymentController::class, 'clientIndex'])->name('client.cash-payments.index');
        Route::get('/create', [CashPaymentController::class, 'memberCreate'])->name('client.cash-payments.create');
        Route::post('/', [CashPaymentController::class, 'memberStore'])->name('client.cash-payments.store');
        Route::get('/{id}', [CashPaymentController::class, 'show'])->name('client.cash-payments.show');
        Route::get('/{id}/edit', [CashPaymentController::class, 'memberEdit'])->name('client.cash-payments.edit');
        Route::put('/{id}', [CashPaymentController::class, 'memberUpdate'])->name('client.cash-payments.update');
    });

    // GCash Payments
    Route::prefix('gcash')->group(function () {
        Route::get('/', [GcashPaymentController::class, 'clientIndex'])->name('client.gcash-payments.index');
        Route::get('/create', [GcashPaymentController::class, 'memberCreate'])->name('client.gcash-payments.create');
        Route::post('/', [GcashPaymentController::class, 'memberStore'])->name('client.gcash-payments.store');
        Route::get('/{id}', [GcashPaymentController::class, 'show'])->name('client.gcash-payments.show');
        Route::get('/{id}/edit', [GcashPaymentController::class, 'memberEdit'])->name('client.gcash-payments.edit');
        Route::put('/{id}', [GcashPaymentController::class, 'memberUpdate'])->name('client.gcash-payments.update');
    });
});
