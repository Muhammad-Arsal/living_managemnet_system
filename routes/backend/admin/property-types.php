<?php

use App\Http\Controllers\Backend\Admin\Settings\PropertyTypesController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::resource('property-types', PropertyTypesController::class)->except(['show']);
    });
});
