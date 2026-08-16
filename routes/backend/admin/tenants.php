<?php

use App\Http\Controllers\Backend\Admin\TenantController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::post('tenants/{tenant}/tenancies', [TenantController::class, 'storeTenancy'])
        ->name('tenants.tenancies.store');
    Route::put('tenants/{tenant}/tenancies/current', [TenantController::class, 'endTenancy'])
        ->name('tenants.tenancies.end');

    Route::resource('tenants', TenantController::class)->except(['show']);
});
