<?php

use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes - No authentication required
Route::post('/register', [UserController::class, 'store']);   // Register new user
Route::post('/login', [UserController::class, 'login']);      // Login and get token

// Protected routes - Require valid Bearer token (Passport)
Route::middleware('auth:api')->group(function () {
    
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/user', [UserController::class, 'user']); 
    Route::apiResource('users', UserController::class);
    Route::apiResource('permissions', PermissionController::class);
    Route::apiResource('roles', RoleController::class);
    Route::get('permissions-grouped', [PermissionController::class, 'getGroupedPermissions']); 
   
});