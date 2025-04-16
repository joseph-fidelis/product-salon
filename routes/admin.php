<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;

Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});



Route::prefix('admin')->group(function () {

    Route::get('services', [ServiceController::class, 'index'])->middleware(['auth', 'verified'])->name('admin.services.index');
    Route::post('services', [ServiceController::class, 'store'])->middleware(['auth', 'verified'])->name('admin.services.store');
    Route::get('services/{service}', [ServiceController::class, 'show'])->middleware(['auth', 'verified'])->name('admin.services.show');
    Route::put('services/{service}', [ServiceController::class, 'update'])->middleware(['auth', 'verified'])->name('admin.services.update');
    Route::delete('services/{service}', [ServiceController::class, 'destroy'])->middleware(['auth', 'verified'])->name('admin.services.destroy');


    Route::get('appointment', function () {
        return Inertia::render('admin/Appointment');
    })->middleware(['auth', 'verified'])->name('admin.appointment');


    Route::get('invoice', function () {
        return Inertia::render('admin/Invoice');
    })->middleware(['auth', 'verified'])->name('admin.invoice');

    Route::get('staff', [StaffController::class, 'index'])->middleware(['auth', 'verified'])->name('admin.staff.index');
    Route::post('staff', [StaffController::class, 'store'])->middleware(['auth', 'verified'])->name('admin.staff.store');
    Route::get('staff/{staff}', [StaffController::class, 'show'])->middleware(['auth', 'verified'])->name('admin.staff.show');
    Route::put('staff/{staff}', [StaffController::class, 'update'])->middleware(['auth', 'verified'])->name('admin.staff.update');
    Route::delete('staff/{staff}', [StaffController::class, 'destroy'])->middleware(['auth', 'verified'])->name('admin.staff.destroy');


});

