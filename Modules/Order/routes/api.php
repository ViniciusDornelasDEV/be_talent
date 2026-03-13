<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\OrderController;

Route::post('/orders/purchase', [OrderController::class, 'purchase']);

