<?php

use App\Http\Controllers\Backend\Admin\Auth\EmailVerificationController;
use App\Http\Controllers\Backend\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Backend\Admin\Auth\LoginController;
use App\Http\Controllers\Backend\Admin\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('admin/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('admin.verification.verify');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [LoginController::class, 'show'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->name('login.post');

        Route::prefix('password')->name('password.')->group(function () {
            Route::get('reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('request');
            Route::post('email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('email');
            Route::get('reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('reset');
            Route::post('reset', [ResetPasswordController::class, 'reset'])->name('update');
        });
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    });
});
