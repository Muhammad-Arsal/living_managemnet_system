<?php

use App\Http\Controllers\Backend\Admin\TenantController;
use App\Http\Controllers\Backend\Admin\TenantDocumentGenerationController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::post('tenants/{tenant}/tenancies', [TenantController::class, 'storeTenancy'])
        ->name('tenants.tenancies.store');
    Route::put('tenants/{tenant}/tenancies/current', [TenantController::class, 'endTenancy'])
        ->name('tenants.tenancies.end');

    Route::get('tenants/{tenant}/documents/generate', [TenantDocumentGenerationController::class, 'create'])
        ->name('tenants.documents.generate');
    Route::post('tenants/{tenant}/documents/generate/preview', [TenantDocumentGenerationController::class, 'preview'])
        ->name('tenants.documents.generate.preview');
    Route::post('tenants/{tenant}/documents/generate', [TenantDocumentGenerationController::class, 'store'])
        ->name('tenants.documents.generate.store');

    Route::post('tenants/{tenant}/documents', [TenantController::class, 'storeDocuments'])
        ->name('tenants.documents.store');
    Route::get('tenants/{tenant}/documents/{document}/download', [TenantController::class, 'downloadDocument'])
        ->name('tenants.documents.download');
    Route::delete('tenants/{tenant}/documents/{document}', [TenantController::class, 'destroyDocument'])
        ->name('tenants.documents.destroy');

    Route::resource('tenants', TenantController::class)->except(['show']);
});
