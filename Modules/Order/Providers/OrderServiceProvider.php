<?php

declare(strict_types=1);

namespace Modules\Order\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Order\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Gate;
use Modules\Order\Models\Transaction;
use Modules\Order\Policies\OrderPolicy;

class OrderServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path('Order', 'database/migrations'));
        $this->loadTranslationsFrom(module_path('Order', 'lang'), 'order');
        $this->loadViewsFrom(module_path('Order', 'resources/views'), 'order');

        Gate::policy(Transaction::class, OrderPolicy::class);
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }
}

