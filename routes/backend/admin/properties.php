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
    Route::post('properties/{property}/images', [PropertyController::class, 'storeImages'])
        ->name('properties.images.store');
    Route::post('properties/{property}/documents', [PropertyController::class, 'storeDocuments'])
        ->name('properties.documents.store');
    Route::get('properties/{property}/documents/{document}/download', [PropertyController::class, 'downloadDocument'])
        ->name('properties.documents.download');
    Route::delete('properties/{property}/documents/{document}', [PropertyController::class, 'destroyDocument'])
        ->name('properties.documents.destroy');

    Route::resource('properties', PropertyController::class)->except(['show']);
});
