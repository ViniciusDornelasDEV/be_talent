<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Client\Http\Controllers\ClientController;

Route::middleware(['auth:sanctum'])
    ->prefix('api/v1')
    ->group(function (): void {
        Route::get('/clients', [ClientController::class, 'index']);
        Route::get('/clients/{client}', [ClientController::class, 'show']);
    });

