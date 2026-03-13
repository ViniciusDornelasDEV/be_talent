<?php

declare(strict_types=1);

namespace Modules\Product\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Modules\Product\Models\Product;
use Modules\Product\Policies\ProductPolicy;
use Modules\Product\Providers\RouteServiceProvider;

class ProductServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path('Product', 'database/migrations'));
        $this->loadTranslationsFrom(module_path('Product', 'lang'), 'product');
        $this->loadViewsFrom(module_path('Product', 'resources/views'), 'product');

        Gate::policy(Product::class, ProductPolicy::class);
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }
}

