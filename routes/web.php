<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AppointmentController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/services', [ServiceController::class, 'publicIndex'])->name('services');

Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

// Route::get('/book-appointment', function () {
//     return Inertia::render('BookAppointment');
// })->name('book-appointment');

Route::get('/book-appointment', [AppointmentController::class, 'showBookingForm'])->name('booking.form');
Route::post('/book-appointment', [AppointmentController::class, 'storeFromPublic'])->name('booking.store');


require __DIR__.'/admin.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
