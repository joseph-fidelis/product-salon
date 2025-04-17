<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CommissionController;

// Admin routes - all protected with authentication middleware
Route::middleware(['auth'])->prefix('admin')->group(function () {
    // Your existing admin routes would be here

    // Invoice management routes
    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('/invoice/generate', [InvoiceController::class, 'create'])->name('invoice.create');
    Route::post('/invoice/store', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::get('/invoice/{invoice}', [InvoiceController::class, 'show'])->name('invoice.show');
    Route::get('/invoice/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::put('/invoice/{invoice}', [InvoiceController::class, 'update'])->name('invoice.update');
    Route::delete('/invoice/{invoice}', [InvoiceController::class, 'destroy'])->name('invoice.destroy');

    // API endpoints for the invoice form
    Route::get('/api/services', [ServiceController::class, 'getServices'])->name('api.services');
    Route::get('/api/staff', [StaffController::class, 'getStaff'])->name('api.staff');

    // Commission routes related to invoices
    Route::get('/commission', [CommissionController::class, 'index'])->name('commission.index');
    Route::get('/commission/{staff}', [CommissionController::class, 'show'])->name('commission.show');
});

require __DIR__.'/admin.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
