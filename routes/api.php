<?php

use App\Http\Controllers\StateController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/states', [StateController::class, 'index']);
    Route::get('/states/{state_id}/cities', [StateController::class, 'cities']);
});
