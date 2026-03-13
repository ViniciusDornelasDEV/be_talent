<?php

declare(strict_types=1);

namespace Modules\Order\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\OrderController;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->routes(function (): void {
            Route::middleware('api')
                ->group(module_path('Order', 'routes/api.php'));
        });
    }
}

