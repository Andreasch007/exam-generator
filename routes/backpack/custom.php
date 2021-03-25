<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('question', 'QuestionCrudController');
    Route::crud('category', 'CategoryCrudController');
    Route::crud('answer', 'AnswerCrudController');
    Route::crud('exam', 'ExamCrudController');
    Route::crud('company', 'CompanyCrudController');
}); // this should be the absolute last line of this file

Route::group(
    [
        'namespace'  => 'App\Http\Controllers',
        'middleware' => config('backpack.base.web_middleware', 'web'),
        'prefix'     => config('backpack.base.route_prefix'),
    ],
function () {
    // Registration Routes...
    if (config('backpack.base.setup_auth_routes')) {
        Route::post('register', [RegisterController::class, 'register']);
        Route::post('login', [RegisterController::class, 'login']);
        Route::post('category', [ExamController::class, 'getCategory']);
        Route::post('exam', [ExamController::class, 'getExam']);
    }
});