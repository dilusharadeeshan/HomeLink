<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProviderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('home');
Route::get('/services/{listing:slug}', [CatalogController::class, 'show'])->name('services.show');
Route::get('/services/{listing:slug}/slots', [CatalogController::class, 'slots'])->middleware('throttle:60,1')->name('services.slots');
Route::view('/how-it-works', 'how-it-works')->name('how');
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
});
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::view('/profile', 'profile')->name('profile');
    Route::put('/profile', [AuthController::class, 'profile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'password'])->middleware('throttle:6,1')->name('profile.password');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings', [BookingController::class, 'index'])->middleware('role:customer,provider')->name('bookings.index');
    Route::post('/services/{listing:slug}/book', [BookingController::class, 'store'])->middleware(['role:customer', 'throttle:20,1'])->name('bookings.store');
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'update'])->middleware('role:provider')->name('bookings.update');
    Route::post('/bookings/{booking}/review', [BookingController::class, 'review'])->middleware('role:customer')->name('bookings.review');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->middleware('role:customer')->name('bookings.cancel');
    Route::middleware('role:provider')->prefix('provider')->name('provider.')->group(function () {
        Route::get('/services', [ProviderController::class, 'index'])->name('services');
        Route::get('/services/create', [ProviderController::class, 'create'])->name('services.create');
        Route::post('/services', [ProviderController::class, 'store'])->name('services.store');
        Route::get('/services/{listing}/edit', [ProviderController::class, 'edit'])->name('services.edit');
        Route::put('/services/{listing}', [ProviderController::class, 'update'])->name('services.update');
        Route::delete('/services/{listing}', [ProviderController::class, 'destroy'])->name('services.destroy');
    });
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::patch('/users/{user}/status', [AdminController::class, 'toggle'])->name('users.toggle');
    });
});
