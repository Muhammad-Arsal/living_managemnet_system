<?php

use App\Http\Controllers\Backend\Council\Auth\EmailVerificationController;
use App\Http\Controllers\Backend\Council\Auth\ForgotPasswordController;
use App\Http\Controllers\Backend\Council\Auth\LoginController;
use App\Http\Controllers\Backend\Council\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('council/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('council.verification.verify');

Route::prefix('council')->name('council.')->group(function () {
    Route::middleware('guest:council')->group(function () {
        Route::get('login', [LoginController::class, 'show'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->name('login.post');

        Route::prefix('password')->name('password.')->group(function () {
            Route::get('reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('request');
            Route::post('email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('email');
            Route::get('reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('reset');
            Route::post('reset', [ResetPasswordController::class, 'reset'])->name('update');
        });
    });

    Route::middleware('auth:council')->group(function () {
        Route::get('email/verify', [EmailVerificationController::class, 'notice'])
            ->name('verification.notice');
        Route::post('email/verification-notification', [EmailVerificationController::class, 'send'])
            ->middleware('throttle:6,1')
            ->name('verification.send');
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    });
});
