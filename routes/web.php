<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ServiceController;

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

Route::get('/book-appointment', function () {
    return Inertia::render('BookAppointment');
})->name('book-appointment');



require __DIR__.'/admin.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
