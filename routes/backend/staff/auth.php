<?php

use App\Http\Controllers\Backend\Staff\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::prefix('staff')->name('staff.')->group(function () {
    Route::middleware('guest:staff')->group(function () {
        Route::get('login', [LoginController::class, 'show'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->name('login.post');
    });

    Route::middleware('auth:staff')->group(function () {
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    });
});
