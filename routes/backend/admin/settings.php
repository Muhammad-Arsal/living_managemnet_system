<?php

use App\Http\Controllers\Backend\Admin\Settings\AdminsController;
use App\Http\Controllers\Backend\Admin\Settings\EmailTemplatesController;
use App\Http\Controllers\Backend\Admin\Settings\SiteSettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::post('admins/{admin}/send-verification-email', [AdminsController::class, 'sendVerificationEmail'])
            ->name('admins.send-verification-email');
        Route::post('admins/{admin}/send-password-reset-email', [AdminsController::class, 'sendPasswordResetEmail'])
            ->name('admins.send-password-reset-email');
        Route::post('admins/{admin}/send-welcome-email', [AdminsController::class, 'sendWelcomeEmail'])
            ->name('admins.send-welcome-email');
        Route::resource('admins', AdminsController::class)->except(['show']);

        Route::resource('email-templates', EmailTemplatesController::class)->except(['show']);

        Route::get('site-settings', [SiteSettingsController::class, 'index'])->name('site-settings.index');
        Route::put('site-settings', [SiteSettingsController::class, 'update'])->name('site-settings.update');
    });
});
