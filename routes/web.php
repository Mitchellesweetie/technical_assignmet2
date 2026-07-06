<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthenticationController::class, 'showLogin'])->name('login');

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthenticationController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [StockController::class, 'dashboard'])->name('dashboard');
    Route::post('/upload', [StockController::class, 'upload'])->name('stocks.upload');
});
