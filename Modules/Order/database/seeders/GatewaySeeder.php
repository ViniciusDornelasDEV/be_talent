<?php

declare(strict_types=1);

namespace Modules\Order\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Order\Models\Gateway;

class GatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            [
                'name'      => 'Gateway 1',
                'is_active' => true,
                'priority'  => 1,
            ],
            [
                'name'      => 'Gateway 2',
                'is_active' => true,
                'priority'  => 2,
            ],
        ];

        foreach ($gateways as $data) {
            Gateway::query()->firstOrCreate(
                ['name' => $data['name']],
                [
                    'is_active' => $data['is_active'],
                    'priority'  => $data['priority'],
                ],
            );
        }
    }
}

