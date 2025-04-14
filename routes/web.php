<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/services', function () {
    return Inertia::render('Services');
})->name('services');

Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

Route::get('/book-appointment', function () {
    return Inertia::render('BookAppointment');
})->name('book-appointment');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('admin-services', function () {
    return Inertia::render('admin/AdminServices');
})->middleware(['auth', 'verified'])->name('admin-services');

Route::get('admin-appointment', function () {
    return Inertia::render('admin/Appointment');
})->middleware(['auth', 'verified'])->name('admin-appointment');

Route::get('admin-staff', function () {
    return Inertia::render('admin/Staff');
})->middleware(['auth', 'verified'])->name('admin-staff');

Route::get('admin-invoice', function () {
    return Inertia::render('admin/Invoice');
})->middleware(['auth', 'verified'])->name('admin-invoice');



require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
