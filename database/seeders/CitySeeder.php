<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $mgId = DB::table('states')->where('uf', 'MG')->value('id');

        if ($mgId === null) {
            $this->command->warn('Minas Gerais (MG) state not found. Run StateSeeder first.');

            return;
        }

        $now = now();

        $cities = [
            ['name' => 'Coronel Fabriciano'],
            ['name' => 'Ipatinga'],
            ['name' => 'Timóteo'],
        ];

        foreach ($cities as $city) {
            DB::table('cities')->insert([
                'name'       => $city['name'],
                'state_id'   => $mgId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
