<?php

use App\Http\Controllers\Backend\Admin\Settings\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('audit-logs/{audit}', [AuditLogController::class, 'show'])
            ->name('audit-logs.show');
        Route::get('audit-logs', [AuditLogController::class, 'index'])
            ->name('audit-logs.index');
    });
});
