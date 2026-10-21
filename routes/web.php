<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Dashboard\DevicesController;
use App\Http\Controllers\Dashboard\RentalPackageController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;



Route::get('/', fn (Request $request) => redirect()->route(
    $request->user() ? 'dashboard' : 'login'
));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    // Package Routes
    Route::get('/packages', [RentalPackageController::class, 'index'])->name('packages.dashboard');
    Route::post('/packages', [RentalPackageController::class, 'store'])->name('packages.store');
    Route::put('/packages/{id}', [RentalPackageController::class, 'update'])->whereNumber('id')->name('packages.update');
    Route::delete('/packages/{id}', [RentalPackageController::class, 'destroy'])->whereNumber('id')->name('packages.destroy');

    //Device Routes
    Route::get('/devices', [DevicesController::class, 'index'])->name('devices.dashboard');
    Route::post('/devices', [DevicesController::class, 'store'])->name('devices.store');
    Route::put('/devices/{id}', [DevicesController::class, 'update'])->whereNumber('id')->name('devices.update');
    Route::delete('/devices/{id}', [DevicesController::class, 'destroy'])->whereNumber('id')->name('devices.destroy');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/ui/datatables', fn () => Inertia::render('Ui/DataTables'))->name('ui.datatables');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    Route::redirect('/dashboard-ecommerce', '/dashboard');
});
