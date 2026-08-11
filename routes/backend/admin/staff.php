<?php

use App\Http\Controllers\Backend\Admin\StaffController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::post('staff/{staff}/send-verification-email', [StaffController::class, 'sendVerificationEmail'])
        ->name('staff.send-verification-email');
    Route::post('staff/{staff}/send-password-reset-email', [StaffController::class, 'sendPasswordResetEmail'])
        ->name('staff.send-password-reset-email');
    Route::post('staff/{staff}/send-welcome-email', [StaffController::class, 'sendWelcomeEmail'])
        ->name('staff.send-welcome-email');
    Route::resource('staff', StaffController::class)->except(['show']);
});
