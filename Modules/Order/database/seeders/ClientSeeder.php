<?php

declare(strict_types=1);

namespace Modules\Order\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Order\Models\Client;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['name' => 'Alice Johnson', 'email' => 'alice@example.com'],
            ['name' => 'Bob Smith', 'email' => 'bob@example.com'],
            ['name' => 'Carol Davis', 'email' => 'carol@example.com'],
        ];

        foreach ($clients as $data) {
            Client::query()->firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name']],
            );
        }
    }
}

