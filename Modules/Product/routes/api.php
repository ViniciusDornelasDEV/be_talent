<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\ProductController;

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{product}', [ProductController::class, 'update']);

