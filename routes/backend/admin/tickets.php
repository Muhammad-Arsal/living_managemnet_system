<?php

use App\Http\Controllers\Backend\Admin\TicketController;
use App\Http\Controllers\Backend\PortalNotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::post('notifications/read-all', [PortalNotificationController::class, 'readAll'])
        ->name('notifications.read-all');
    Route::get('notifications/{notification}/open', [PortalNotificationController::class, 'open'])
        ->name('notifications.open');

    Route::get('tickets/{ticket}/attachments/{attachment}/download', [TicketController::class, 'downloadAttachment'])
        ->name('tickets.attachments.download');
    Route::post('tickets/{ticket}/replies', [TicketController::class, 'reply'])->name('tickets.replies.store');
    Route::resource('tickets', TicketController::class)->only(['index', 'show']);
});
