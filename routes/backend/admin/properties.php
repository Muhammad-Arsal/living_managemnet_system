<?php

use App\Http\Controllers\Backend\Admin\PropertyController;
use App\Http\Controllers\Backend\Admin\PropertyDocumentGenerationController;
use App\Http\Controllers\Backend\Admin\RoomController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::post('properties/{property}/tenancies', [PropertyController::class, 'storeTenancy'])
        ->name('properties.tenancies.store');
    Route::put('properties/{property}/tenancies/current', [PropertyController::class, 'endTenancy'])
        ->name('properties.tenancies.end');

    Route::post('properties/{property}/rooms', [RoomController::class, 'store'])
        ->name('properties.rooms.store');
    Route::put('properties/{property}/rooms/{room}', [RoomController::class, 'update'])
        ->name('properties.rooms.update');
    Route::delete('properties/{property}/rooms/{room}', [RoomController::class, 'destroy'])
        ->name('properties.rooms.destroy');
    Route::post('properties/{property}/rooms/{room}/tenancies', [RoomController::class, 'storeTenancy'])
        ->name('properties.rooms.tenancies.store');
    Route::put('properties/{property}/rooms/{room}/tenancies/current', [RoomController::class, 'endTenancy'])
        ->name('properties.rooms.tenancies.end');

    Route::delete('properties/{property}/images/{property_image}', [PropertyController::class, 'destroyImage'])
        ->name('properties.images.destroy');
    Route::post('properties/{property}/images', [PropertyController::class, 'storeImages'])
        ->name('properties.images.store');
    Route::get('properties/{property}/documents/generate', [PropertyDocumentGenerationController::class, 'create'])
        ->name('properties.documents.generate');
    Route::post('properties/{property}/documents/generate/preview', [PropertyDocumentGenerationController::class, 'preview'])
        ->name('properties.documents.generate.preview');
    Route::post('properties/{property}/documents/generate', [PropertyDocumentGenerationController::class, 'store'])
        ->name('properties.documents.generate.store');

    Route::post('properties/{property}/documents', [PropertyController::class, 'storeDocuments'])
        ->name('properties.documents.store');
    Route::get('properties/{property}/documents/{document}/download', [PropertyController::class, 'downloadDocument'])
        ->name('properties.documents.download');
    Route::delete('properties/{property}/documents/{document}', [PropertyController::class, 'destroyDocument'])
        ->name('properties.documents.destroy');

    Route::resource('properties', PropertyController::class)->except(['show']);
});
