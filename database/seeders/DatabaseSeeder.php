<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            \Modules\User\database\seeders\UserSeeder::class,
            \Modules\Product\database\seeders\ProductSeeder::class,
        ]);
    }
}
