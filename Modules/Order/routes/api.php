<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\OrderController;

Route::prefix('api/v1')->group(function (): void {
    Route::post('/orders/purchase', [OrderController::class, 'purchase']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{transaction}', [OrderController::class, 'show']);
    });
});

