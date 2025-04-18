<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CommissionController;

Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::prefix('admin')->group(function () {
    // Services routes
    Route::get('services', [ServiceController::class, 'index'])->middleware(['auth', 'verified'])->name('admin.services.index');
    Route::post('services', [ServiceController::class, 'store'])->middleware(['auth', 'verified'])->name('admin.services.store');
    Route::get('services/{service}', [ServiceController::class, 'show'])->middleware(['auth', 'verified'])->name('admin.services.show');
    Route::put('services/{service}', [ServiceController::class, 'update'])->middleware(['auth', 'verified'])->name('admin.services.update');
    Route::delete('services/{service}', [ServiceController::class, 'destroy'])->middleware(['auth', 'verified'])->name('admin.services.destroy');

    // Appointment route
    Route::get('appointment', function () {
        return Inertia::render('admin/Appointment');
    })->middleware(['auth', 'verified'])->name('admin.appointment');

    Route::get('invoice', [App\Http\Controllers\InvoiceController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('admin.invoice.index');

    // New invoice routes with InvoiceController
    // Route::get('invoice/generate', [InvoiceController::class, 'create'])->middleware(['auth', 'verified'])->name('admin.invoice.generate');
    // Route::post('invoice/store', [InvoiceController::class, 'store'])->middleware(['auth', 'verified'])->name('admin.invoice.store');
    // Route::get('invoice/{invoice}', [InvoiceController::class, 'show'])->middleware(['auth', 'verified'])->name('admin.invoice.show');
    // Route::get('invoice/{invoice}/edit', [InvoiceController::class, 'edit'])->middleware(['auth', 'verified'])->name('admin.invoice.edit');
    // Route::put('invoice/{invoice}', [InvoiceController::class, 'update'])->middleware(['auth', 'verified'])->name('admin.invoice.update');
    // Route::delete('invoice/{invoice}', [InvoiceController::class, 'destroy'])->middleware(['auth', 'verified'])->name('admin.invoice.destroy');


    Route::get('/invoice/generate', [InvoiceController::class, 'create'])->name('admin.invoice.generate');
    Route::post('/invoice/store', [InvoiceController::class, 'store'])->name('admin.invoice.store');
    Route::get('/invoice/{invoice}', [InvoiceController::class, 'show'])->name('admin.invoice.show');
    Route::put('/invoice/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('admin.invoice.mark-paid');
    Route::delete('/invoice/{invoice}', [InvoiceController::class, 'destroy'])->name('admin.invoice.destroy');

    Route::prefix('commission')->name('commission.')->group(function () {
        Route::get('/', [CommissionController::class, 'index'])->name('index');
        Route::get('/statistics', [CommissionController::class, 'statistics'])->name('statistics');
        Route::get('/staff/{staff}', [CommissionController::class, 'staffSummary'])->name('staff.summary');
        Route::put('/{commission}/status', [CommissionController::class, 'updateStatus'])->name('update.status');
        Route::post('/batch-update', [CommissionController::class, 'batchUpdate'])->name('batch.update');
        Route::post('/record', [CommissionController::class, 'recordManual'])->name('record.manual');
    });

    // Staff routes
    Route::get('staff', [StaffController::class, 'index'])->middleware(['auth', 'verified'])->name('admin.staff.index');
    Route::post('staff', [StaffController::class, 'store'])->middleware(['auth', 'verified'])->name('admin.staff.store');
    Route::get('staff/{staff}', [StaffController::class, 'show'])->middleware(['auth', 'verified'])->name('admin.staff.show');
    Route::put('staff/{staff}', [StaffController::class, 'update'])->middleware(['auth', 'verified'])->name('admin.staff.update');
    Route::delete('staff/{staff}', [StaffController::class, 'destroy'])->middleware(['auth', 'verified'])->name('admin.staff.destroy');
});
