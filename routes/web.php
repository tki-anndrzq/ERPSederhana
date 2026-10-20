<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
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
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/ui/datatables', fn () => Inertia::render('Ui/DataTables'))->name('ui.datatables');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    Route::redirect('/dashboard-ecommerce', '/dashboard');
});
