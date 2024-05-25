<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return 'you are view the web version';
   // return view('welcome');
});

Route::get('/clear', function () {
    // Clear application cache:
    $exitCode = Artisan::call('cache:clear');
    // Clear route cache:
    $exitCode = Artisan::call('route:clear');
    // Clear config cache:
    $exitCode = Artisan::call('config:clear');
    // Clear compiled views:
    $exitCode = Artisan::call('view:clear');

    return 'Cache is cleared';
});
