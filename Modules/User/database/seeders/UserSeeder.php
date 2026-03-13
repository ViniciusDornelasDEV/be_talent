<?php

declare(strict_types=1);

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin@example.com',
                'role'  => 'ADMIN',
            ],
            [
                'email' => 'manager@example.com',
                'role'  => 'MANAGER',
            ],
            [
                'email' => 'finance@example.com',
                'role'  => 'FINANCE',
            ],
            [
                'email' => 'user@example.com',
                'role'  => 'USER',
            ],
        ];

        foreach ($users as $data) {
            User::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'password' => Hash::make('password'),
                    'role'     => $data['role'],
                ],
            );
        }
    }
}

