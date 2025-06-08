<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;


Route::post('/register', [AuthController::class,'register'])->name('register_user');
Route::post('/login', [AuthController::class, 'login'])->name('login_user');
Route::post('/refresh',[AuthController::class, 'refresh'])->name('refresh_token');
Route::middleware(['auth:api'])->group(function () {

    // logout route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout_user');

    // permissions routes
    Route::get('/permissions', [PermissionController::class, 'index'])->middleware('permission:read-permissions');
    Route::get('/permissions/{id}', [PermissionController::class, 'show'])->middleware('permission:read-permissions');

    // roles routes
    Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:read-roles');
    Route::get('/roles/{id}', [RoleController::class, 'show'])->middleware('permission:read-roles');
});