<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;
use Modules\User\Http\Controllers\AuthController;

Route::prefix('api/v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/me', function () {
            return auth()->user();
        });
        Route::post('/logout', [AuthController::class, 'logout']);
        
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'create']);
        Route::put('/users/{user}', [UserController::class, 'update']);
    });
});
