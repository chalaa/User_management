<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;

Route::post('/register', [AuthController::class, 'register'])->name('register.user');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh.token');

Route::middleware(['auth:api'])->group(function () {
    // Logout route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout.    user');

    // Permissions routes
    Route::get('/permissions', [PermissionController::class, 'index'])->middleware('permission:read-permissions');
    Route::get('/permissions/{id}', [PermissionController::class, 'show'])->middleware('permission:read-permissions');

    // Roles routes
    Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:read-roles');
    Route::get('/roles/{id}', [RoleController::class, 'show'])->middleware('permission:read-roles');
    Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:create-roles');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->middleware('permission:update-roles');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->middleware('permission:delete-roles');

    // Assign and revoke permissions to roles
    Route::post('/roles/{id}/assign', [RoleController::class, 'assignPermission'])->middleware('permission:assign-permissions');
    Route::post('/roles/{id}/revoke', [RoleController::class, 'revokePermission'])->middleware('permission:assign-permissions');
});