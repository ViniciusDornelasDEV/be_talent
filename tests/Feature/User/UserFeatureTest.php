<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;
use Tests\TestCase;

class UserFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $role, array $overrides = []): User
    {
        return User::query()->create(array_merge([
            'email'    => strtolower($role) . '@example.com',
            'password' => 'password',
            'role'     => $role,
        ], $overrides));
    }

    public function test_admin_can_list_users(): void
    {
        $admin = $this->createUser('ADMIN', ['email' => 'admin@example.com']);
        $this->actingAs($admin);

        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    public function test_admin_can_create_users(): void
    {
        $admin = $this->createUser('ADMIN', ['email' => 'admin@example.com']);
        $this->actingAs($admin);

        $payload = [
            'email'    => 'new.user@example.com',
            'password' => 'password',
            'role'     => 'USER',
        ];

        $response = $this->postJson('/api/v1/users', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'new.user@example.com',
            'role'  => 'USER',
        ]);
    }

    public function test_admin_can_update_users(): void
    {
        $admin = $this->createUser('ADMIN', ['email' => 'admin@example.com']);
        $this->actingAs($admin);

        $user = $this->createUser('USER', ['email' => 'old@example.com']);

        $payload = [
            'email'    => 'updated@example.com',
            'password' => 'new-password',
            'role'     => 'MANAGER',
        ];

        $response = $this->putJson("/api/v1/users/{$user->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'email' => 'updated@example.com',
            'role'  => 'MANAGER',
        ]);
    }

    public function test_non_admin_users_cannot_manage_users(): void
    {
        $user = $this->createUser('USER', ['email' => 'user@example.com']);
        $this->actingAs($user);

        $responseIndex = $this->getJson('/api/v1/users');
        $responseCreate = $this->postJson('/api/v1/users', [
            'email'    => 'another@example.com',
            'password' => 'password',
            'role'     => 'USER',
        ]);

        $responseIndex->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => [
                    'type' => 'authorization_error',
                ],
            ]);

        $responseCreate->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => [
                    'type' => 'authorization_error',
                ],
            ]);
    }

    public function test_login_works_correctly(): void
    {
        $user = $this->createUser('ADMIN', ['email' => 'login@example.com']);

        $response = $this->postJson('/api/v1/login', [
            'email'    => 'login@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'user' => [
                        'id',
                        'email',
                        'role',
                    ],
                ],
            ]);
    }

    public function test_login_validation_errors_are_returned_for_invalid_data(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email'    => 'not-an-email',
            'password' => '',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'type' => 'validation_error',
                ],
            ]);
    }

    public function test_user_creation_validation_errors_are_returned_for_invalid_data(): void
    {
        $admin = $this->createUser('ADMIN', ['email' => 'admin@example.com']);
        $this->actingAs($admin);

        $response = $this->postJson('/api/v1/users', [
            'email'    => 'invalid-email',
            'password' => '',
            'role'     => 'INVALID_ROLE',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'type' => 'validation_error',
                ],
            ]);
    }
}

