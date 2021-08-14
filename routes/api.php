<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Password;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\ResultController;
use App\Http\Controllers\Api\OnlyGpaList;
use App\Http\Controllers\Api\StudentList;
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


Route::group([
    'middleware' => 'api',
    'prefix' => 'v1/auth'
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('my-profile', [AuthController::class, 'userProfile']);
    Route::patch('password-reset', [Password::class, 'resetPassword']);
    Route::patch('password-change', [Password::class, 'changePassword']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'v1'
], function () {

    Route::apiResources([
        'departments' => DepartmentController::class,
        'batches' => BatchController::class,
        'students' => StudentController::class,
        'results' => ResultController::class,
    ], ['middleware' => 'jwt.verify']);

    Route::get('gpa-list', OnlyGpaList::class)->middleware('jwt.verify');
    Route::get('student-list', StudentList::class)->middleware('jwt.verify');
});
