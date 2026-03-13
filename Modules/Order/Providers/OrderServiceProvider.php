<?php

declare(strict_types=1);

namespace Modules\Order\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Order\Providers\RouteServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path('Order', 'database/migrations'));
        $this->loadTranslationsFrom(module_path('Order', 'lang'), 'order');
        $this->loadViewsFrom(module_path('Order', 'resources/views'), 'order');
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }
}

