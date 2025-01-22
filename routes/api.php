<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\JwtMiddleware;


Route::post('/register', [AuthController::class,'register'])->name('register_user');
Route::post('/login', [AuthController::class, 'login'])->name('login_user');
Route::post('/refresh',[AuthController::class, 'refresh'])->name('refresh_token');
Route::middleware([JwtMiddleware::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout_user');
});