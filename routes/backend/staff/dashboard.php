<?php

use App\Http\Controllers\Backend\Staff\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('staff')->name('staff.')->middleware('auth:staff')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
});
