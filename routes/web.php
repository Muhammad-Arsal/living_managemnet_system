<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'frontend::home')->name('home');

/*
|--------------------------------------------------------------------------
| Backend — Admin
|--------------------------------------------------------------------------
*/
require __DIR__.'/backend/admin/auth.php';
require __DIR__.'/backend/admin/dashboard.php';
require __DIR__.'/backend/admin/profile.php';

/*
|--------------------------------------------------------------------------
| Backend — Staff
|--------------------------------------------------------------------------
*/
require __DIR__.'/backend/staff/auth.php';
require __DIR__.'/backend/staff/dashboard.php';
require __DIR__.'/backend/staff/profile.php';

/*
|--------------------------------------------------------------------------
| Backend — Council
|--------------------------------------------------------------------------
*/
require __DIR__.'/backend/council/auth.php';
require __DIR__.'/backend/council/dashboard.php';
require __DIR__.'/backend/council/profile.php';
