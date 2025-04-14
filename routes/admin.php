<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ServiceController;

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::prefix('admin')->group(function () {
    Route::get('services', [ServiceController::class, 'index'])->middleware(['auth', 'verified'])->name('services.index');
    Route::post('services', [ServiceController::class, 'store'])->middleware(['auth', 'verified'])->name('services.store');
    Route::get('services/{service}', [ServiceController::class, 'show'])->middleware(['auth', 'verified'])->name('services.show');
    Route::put('services/{service}', [ServiceController::class, 'update'])->middleware(['auth', 'verified'])->name('services.update');
    Route::delete('services/{service}', [ServiceController::class, 'destroy'])->middleware(['auth', 'verified'])->name('services.destroy');
});