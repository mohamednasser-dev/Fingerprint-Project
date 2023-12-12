<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\TransactionsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReportsController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [UserController::class, 'login']);
Route::post('/update-profile', [UserController::class, 'updateProfile']);
Route::post('/update-password', [UserController::class, 'updatePassword']);


Route::group(['middleware' => ['admin'], 'prefix' => "admin"], function () {

    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/store', [UserController::class, 'store']);
        Route::post('/update', [UserController::class, 'update']);
        Route::post('/reset-device-id', [UserController::class, 'resetDeviceId']);
        Route::post('/delete', [UserController::class, 'delete']);

    });

    Route::group(['prefix' => 'reports'], function () {
        Route::post('/by-user', [AttendanceController::class, 'AdminAttendanceByUserReport']);
        Route::post('/all-users', [AttendanceController::class, 'AdminAttendanceAllUsersReport']);

    });
});

Route::group(['prefix' => 'attendance'], function () {
    Route::post('/store', [AttendanceController::class, 'storeAttendance']);
    Route::post('/report', [AttendanceController::class, 'attendanceReport']);

});
