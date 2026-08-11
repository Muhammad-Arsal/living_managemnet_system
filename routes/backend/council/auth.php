<?php

use App\Http\Controllers\Backend\Council\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::prefix('council')->name('council.')->group(function () {
    Route::middleware('guest:council')->group(function () {
        Route::get('login', [LoginController::class, 'show'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->name('login.post');
    });

    Route::middleware('auth:council')->group(function () {
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    });
});
