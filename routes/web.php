<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('admin.dashboard');
// });
// Route::namespace('App\Http\Controllers\Admin',)->group(function () {
//     // Controllers Within The "App\Http\Controllers\Admin" Namespace
//     return view('dashboard');
// });
// Route::get('/testdashboard', 'DashboardController@index');
Route::namespace('App\Http\Controllers\Frontend',)->group(function () {
    // Controllers Within The "App\Http\Controllers\Admin" Namespace
    Route::get('/', 'DashboardController@index');
});

