<?php

use App\Http\Controllers\Backend\Council\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('council')->name('council.')->middleware('auth:council')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
});
