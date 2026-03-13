<?php

declare(strict_types=1);

namespace Modules\Client\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Client\Models\Client;
use Modules\Client\Policies\ClientPolicy;

class ClientServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path('Client', 'database/migrations'));
        $this->loadTranslationsFrom(module_path('Client', 'lang'), 'client');
        $this->loadViewsFrom(module_path('Client', 'resources/views'), 'client');

        Gate::policy(Client::class, ClientPolicy::class);
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }
}

