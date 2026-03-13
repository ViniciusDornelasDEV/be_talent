<?php

declare(strict_types=1);

namespace Modules\Gateway\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->routes(function (): void {
            Route::middleware(['api', 'auth:sanctum'])
                ->prefix('api/v1')
                ->group(module_path('Gateway', 'routes/api.php'));
        });
    }
}

