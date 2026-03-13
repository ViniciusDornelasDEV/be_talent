<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return new \Illuminate\Http\JsonResponse([
        'success' => false,
        'error' => [
            'type' => 'authentication_error',
            'message' => 'Unauthenticated.',
        ],
    ], 401);
})->name('login');