<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public login route
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {

    // me route
    Route::get('auth/me', [AuthController::class, 'me']);

    Route::get('profile', [AuthController::class, 'profile']);
    Route::post('update-profile', [AuthController::class, 'updateProfile']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
    // roles and permissions
    // Roles
    Route::apiResource('roles', RoleController::class)->except(['create', 'edit']);

    // Permissions
    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::post('/permissions', [PermissionController::class, 'store']);
});
