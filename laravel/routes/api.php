<?php

use Src\Auth\Infrastructure\Controllers\RegisterUserController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::prefix('auth')->group(function () {
    Route::post('/register', RegisterUserController::class);
    
});