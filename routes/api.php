<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', 'App\Http\Controllers\API\RegisterController@register');
Route::post('login', 'App\Http\Controllers\API\RegisterController@login');
Route::post('category', 'App\Http\Controllers\API\ExamController@getCategory');
Route::post('exam','App\Http\Controllers\API\ExamController@getExam');
Route::post('questionanswer','App\Http\Controllers\API\ExamController@getQuestion');
Route::post('getprofile','App\Http\Controllers\API\ExamController@getProfile');