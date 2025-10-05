<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClassRoomController;
use App\Http\Controllers\Api\CriteriaController;
use App\Http\Controllers\Api\PeriodController;
use App\Http\Controllers\Api\ScoreController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route Login
Route::post('/login', [AuthController::class, 'login']);

// Route with Sanctum Authentication
Route::middleware('auth:sanctum')->group(function () {

    // Route Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    // Route Manage Periods
    Route::apiResource('periods', PeriodController::class)->middleware('permission:manage periods');
    // Route Manage Classes
    Route::apiResource('classes', ClassRoomController::class)->middleware('permission:manage classes');
    // Route Manage Students
    Route::apiResource('students', StudentController::class)->middleware('permission:manage students');
    // Route Manage Scores
    Route::apiResource('scores', ScoreController::class)->middleware('permission:manage scores');
    // Route Manage Criterias
    Route::apiResource('criterias', CriteriaController::class)->middleware('permission:manage criterias');
    // Route Manage Users
    Route::apiResource('users', UserController::class)->middleware('permission:manage users');

});
