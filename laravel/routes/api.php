<?php

use Src\Auth\Infrastructure\Controllers\RegisterUserController;
use Src\Auth\Infrastructure\Controllers\LoginUserController;
use Src\Auth\Infrastructure\Controllers\LogoutUserController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::prefix('auth')->group(function () {
    // Public routes
    Route::post('/register', RegisterUserController::class);
    Route::post('/login', LoginUserController::class);

    // Protected routes (require Bearer token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', LogoutUserController::class);
    });
});