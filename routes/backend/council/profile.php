<?php

use App\Http\Controllers\Backend\Council\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('council')->name('council.')->middleware('auth:council')->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
});
