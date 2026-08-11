<?php

use App\Http\Controllers\Backend\Staff\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('staff')->name('staff.')->middleware('auth:staff')->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
});
