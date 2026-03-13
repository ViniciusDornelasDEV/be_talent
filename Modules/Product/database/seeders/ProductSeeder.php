<?php

declare(strict_types=1);

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Use factory if available; otherwise fall back to static examples.
        if (method_exists(Product::class, 'factory')) {
            Product::factory()
                ->count(15)
                ->create();

            return;
        }

        $products = [];

        for ($i = 1; $i <= 15; $i++) {
            $products[] = [
                'name'   => "Product {$i}",
                'amount' => 1000 * $i,
            ];
        }

        foreach ($products as $data) {
            Product::query()->firstOrCreate(
                ['name' => $data['name']],
                ['amount' => $data['amount']],
            );
        }
    }
}

