<?php

declare(strict_types=1);

namespace Modules\Gateway\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Gateway\Models\Gateway;
use Modules\Gateway\Policies\GatewayPolicy;

class GatewayServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path('Gateway', 'database/migrations'));
        $this->loadTranslationsFrom(module_path('Gateway', 'lang'), 'gateway');
        $this->loadViewsFrom(module_path('Gateway', 'resources/views'), 'gateway');

        Gate::policy(Gateway::class, GatewayPolicy::class);
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }
}

