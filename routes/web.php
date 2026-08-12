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
require __DIR__.'/backend/admin/staff.php';
require __DIR__.'/backend/admin/council.php';
require __DIR__.'/backend/admin/settings.php';
require __DIR__.'/backend/admin/audit-logs.php';

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
