<?php

use App\Http\Controllers\Backend\Staff\Auth\EmailVerificationController;
use App\Http\Controllers\Backend\Staff\Auth\ForgotPasswordController;
use App\Http\Controllers\Backend\Staff\Auth\LoginController;
use App\Http\Controllers\Backend\Staff\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('staff/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('staff.verification.verify');

Route::prefix('staff')->name('staff.')->group(function () {
    Route::middleware('guest:staff')->group(function () {
        Route::get('login', [LoginController::class, 'show'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->name('login.post');

        Route::prefix('password')->name('password.')->group(function () {
            Route::get('reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('request');
            Route::post('email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('email');
            Route::get('reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('reset');
            Route::post('reset', [ResetPasswordController::class, 'reset'])->name('update');
        });
    });

    Route::middleware('auth:staff')->group(function () {
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    });
});
