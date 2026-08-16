<?php

use App\Http\Controllers\Backend\Admin\PropertyController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::post('properties/{property}/tenancies', [PropertyController::class, 'storeTenancy'])
        ->name('properties.tenancies.store');
    Route::put('properties/{property}/tenancies/current', [PropertyController::class, 'endTenancy'])
        ->name('properties.tenancies.end');
    Route::delete('properties/{property}/images/{property_image}', [PropertyController::class, 'destroyImage'])
        ->name('properties.images.destroy');

    Route::resource('properties', PropertyController::class)->except(['show']);
});
