<?php

use App\Http\Controllers\Backend\Admin\CouncilController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::post('council/{council}/send-verification-email', [CouncilController::class, 'sendVerificationEmail'])
        ->name('council.send-verification-email');
    Route::post('council/{council}/send-password-reset-email', [CouncilController::class, 'sendPasswordResetEmail'])
        ->name('council.send-password-reset-email');
    Route::post('council/{council}/send-welcome-email', [CouncilController::class, 'sendWelcomeEmail'])
        ->name('council.send-welcome-email');
    Route::resource('council', CouncilController::class)->except(['show']);
});
