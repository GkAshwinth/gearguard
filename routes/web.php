<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| Web Routes — GearGuard
|--------------------------------------------------------------------------
|
| Public routes: catalog browsing, individual product pages.
| Client routes: protected by auth middleware.
| Owner routes: protected by auth + CheckRole:owner middleware.
|
| SECURITY: Laravel's auth middleware uses encrypted, signed session cookies
| (HttpOnly + SameSite=Lax by default) preventing XSS token theft and CSRF.
| The CheckRole middleware prevents URL-hacking (clients accessing /owner/*).
|
*/

// ── Public Routes ──────────────────────────────────────────────────────────

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');

Route::get('/browse', [EquipmentController::class, 'index'])->name('equipment.index');
Route::get('/browse/{equipment}', [EquipmentController::class, 'show'])->name('equipment.show');

// ── Authenticated Client Routes ────────────────────────────────────────────

Route::middleware(['auth'])->group(function () {

    // Client dashboard
    Route::get('/dashboard', [BookingController::class, 'clientDashboard'])->name('dashboard');

    // Booking flow
    Route::get('/checkout/{equipment}', [BookingController::class, 'checkout'])->name('booking.checkout');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');

    // Payment Processing
    Route::get('/payment/success', [\App\Http\Controllers\PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [\App\Http\Controllers\PaymentController::class, 'cancel'])->name('payment.cancel');
});

// ── Owner / Admin Routes ───────────────────────────────────────────────────
// SECURITY: double-protected — requires authentication AND the 'owner' role.

Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':owner'])->prefix('owner')->name('owner.')->group(function () {

    // Owner dashboard (bookings overview + metrics)
    Route::get('/dashboard', [BookingController::class, 'ownerDashboard'])->name('dashboard');

    // Booking management
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('booking.status');

    // Inventory management (full CRUD)
    Route::get('/inventory', [EquipmentController::class, 'adminIndex'])->name('inventory');
    Route::get('/inventory/create', [EquipmentController::class, 'create'])->name('equipment.create');
    Route::post('/inventory', [EquipmentController::class, 'store'])->name('equipment.store');
    Route::get('/inventory/{equipment}/edit', [EquipmentController::class, 'edit'])->name('equipment.edit');
    Route::put('/inventory/{equipment}', [EquipmentController::class, 'update'])->name('equipment.update');
    Route::delete('/inventory/{equipment}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');
});


