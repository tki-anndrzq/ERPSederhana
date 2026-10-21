<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Dashboard\BookingController;
use App\Http\Controllers\Dashboard\InvoiceController;
use App\Http\Controllers\Dashboard\PaymentController;
use App\Http\Controllers\Dashboard\DevicesController;
use App\Http\Controllers\Dashboard\RentalPackageController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (Request $request) => redirect()->route(
    $request->user() ? 'dashboard' : 'login'
));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.store');
});

// Pelanggan membuka link token pribadi tanpa login ke dashboard.
Route::get('/invoices/{token}', [InvoiceController::class, 'show'])
    ->where('token', '[A-Za-z0-9]{64}')->middleware('throttle:60,1')->name('invoices.show');
Route::get('/invoices/{token}/pay', [PaymentController::class, 'show'])
    ->where('token', '[A-Za-z0-9]{64}')->middleware('throttle:60,1')->name('payments.show');
Route::post('/invoices/{token}/pay', [PaymentController::class, 'store'])
    ->where('token', '[A-Za-z0-9]{64}')->middleware('throttle:20,1')->name('payments.store');
Route::post('/invoices/{token}/check', [PaymentController::class, 'check'])
    ->where('token', '[A-Za-z0-9]{64}')->middleware('throttle:20,1')->name('payments.check');

// Midtrans mengirim notifikasi tanpa login pelanggan atau admin.
Route::post('/midtrans/notification', [PaymentController::class, 'notification'])
    ->name('midtrans.notification');

Route::middleware('auth')->group(function () {
    // Package Routes
    Route::get('/packages', [RentalPackageController::class, 'index'])->name('packages.dashboard');
    Route::get('/packages/create', [RentalPackageController::class, 'create'])->name('packages.create');
    Route::get('/packages/{id}/edit', [RentalPackageController::class, 'edit'])->whereNumber('id')->name('packages.edit');
    Route::post('/packages', [RentalPackageController::class, 'store'])->name('packages.store');
    Route::put('/packages/{id}', [RentalPackageController::class, 'update'])->whereNumber('id')->name('packages.update');
    Route::delete('/packages/{id}', [RentalPackageController::class, 'destroy'])->whereNumber('id')->name('packages.destroy');

    //Device Routes
    Route::get('/devices', [DevicesController::class, 'index'])->name('devices.dashboard');
    Route::get('/devices/create', [DevicesController::class, 'create'])->name('devices.create');
    Route::get('/devices/{id}/edit', [DevicesController::class, 'edit'])->whereNumber('id')->name('devices.edit');
    Route::post('/devices', [DevicesController::class, 'store'])->name('devices.store');
    Route::put('/devices/{id}', [DevicesController::class, 'update'])->whereNumber('id')->name('devices.update');
    Route::delete('/devices/{id}', [DevicesController::class, 'destroy'])->whereNumber('id')->name('devices.destroy');

    // Pemesanan: daftar, form tambah, detail, dan pembatalan.
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->whereNumber('id')->name('bookings.show');
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->whereNumber('id')->name('bookings.cancel');
    Route::post('/bookings/{id}/invoice', [InvoiceController::class, 'store'])->whereNumber('id')->name('bookings.invoice');
    Route::post('/bookings/{id}/handover', [BookingController::class, 'handover'])->whereNumber('id')->name('bookings.handover');
    Route::post('/bookings/{id}/return', [BookingController::class, 'returnDevice'])->whereNumber('id')->name('bookings.return');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
});
