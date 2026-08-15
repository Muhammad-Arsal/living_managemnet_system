<?php

use App\Http\Controllers\Backend\Admin\Settings\TicketPrioritiesController;
use App\Http\Controllers\Backend\Admin\Settings\TicketTypesController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::resource('ticket-types', TicketTypesController::class)->except(['show']);
        Route::resource('ticket-priorities', TicketPrioritiesController::class)->except(['show']);
    });
});
