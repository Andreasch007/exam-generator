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
Route::post('examrule','App\Http\Controllers\API\ExamController@getExamRule');
Route::post('questionanswer','App\Http\Controllers\API\ExamController@getQuestion');
Route::post('updateflagdone','App\Http\Controllers\API\ExamController@updateFlagDone');
Route::post('edit-profile','App\Http\Controllers\API\ExamController@getProfile');
Route::post('company','App\Http\Controllers\API\ExamController@getCompany');
Route::post('sendapproval','App\Http\Controllers\API\ExamController@sendApproval');
Route::post('updatejournal','App\Http\Controllers\API\ExamController@updateResultJournal');
Route::post('save-data','App\Http\Controllers\API\ExamController@updateCompany');
Route::post('forgotpassword','App\Http\Controllers\API\ExamController@forgotPassword');
Route::post('sendapproval','App\Http\Controllers\API\ExamController@sendApproval');
// Route::post('changepassword','App\Http\Controllers\API\ExamController@changePassword');

