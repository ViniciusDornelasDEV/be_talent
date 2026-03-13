<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Gateway\Http\Controllers\GatewayController;

Route::patch('/gateways/{gateway}/toggle-active', [GatewayController::class, 'toggleActive']);
Route::patch('/gateways/{gateway}/priority', [GatewayController::class, 'updatePriority']);

