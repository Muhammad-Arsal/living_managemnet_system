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

/*
|--------------------------------------------------------------------------
| Backend — Staff
|--------------------------------------------------------------------------
*/
require __DIR__.'/backend/staff/auth.php';
require __DIR__.'/backend/staff/dashboard.php';

/*
|--------------------------------------------------------------------------
| Backend — Council
|--------------------------------------------------------------------------
*/
require __DIR__.'/backend/council/auth.php';
require __DIR__.'/backend/council/dashboard.php';
