<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware([])->name('api.')->group(function () {
    Route::prefix('auth')->group(function () {
        // Login
        Route::post('login', Api\Auth\LoginApiController::class);

        // Logout
        Route::post('logout', Api\Auth\LogoutApiController::class)->middleware('auth:sanctum');
    });

    Route::middleware('auth:sanctum')->prefix('mobile')->name('mobile.')->group(function () {
        // Get weekly schedule
        Route::get('schedule', [Api\ScheduleController::class, 'getWeeklySchedule']);

        // Get Attendance List Today
        Route::get('attendance', [Api\AttendanceController::class, 'getAttendanceListToday']);

        // Get All Attendance List
        Route::get('attendance/all', [Api\AttendanceController::class, 'getAllAttendanceList']);

        // Store Attendance Student
        Route::post('attendance/store', [Api\AttendanceController::class, 'store']);

    });
});
