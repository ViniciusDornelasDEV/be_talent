<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'email'      => 'admin@example.com',
                'password'   => Hash::make('password'),
                'role'       => 'ADMIN',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email'      => 'manager@example.com',
                'password'   => Hash::make('password'),
                'role'       => 'MANAGER',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email'      => 'finance@example.com',
                'password'   => Hash::make('password'),
                'role'       => 'FINANCE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email'      => 'user@example.com',
                'password'   => Hash::make('password'),
                'role'       => 'USER',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
