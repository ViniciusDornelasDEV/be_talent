<?php

declare(strict_types=1);

namespace Tests\Feature\Product;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\TestCase;

class ProductFeatureTest extends TestCase
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

    private function createProduct(array $overrides = []): Product
    {
        return Product::query()->create(array_merge([
            'name'   => 'Test Product',
            'amount' => 1000,
        ], $overrides));
    }

    public function test_admin_can_create_products(): void
    {
        $admin = $this->createUser('ADMIN');
        $this->actingAs($admin);

        $payload = [
            'name'   => 'Admin Product',
            'amount' => 1000,
        ];

        $response = $this->postJson('/api/v1/products', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('products', [
            'name'   => 'Admin Product',
            'amount' => 1000,
        ]);
    }

    public function test_manager_can_create_products(): void
    {
        $manager = $this->createUser('MANAGER');
        $this->actingAs($manager);

        $payload = [
            'name'   => 'Manager Product',
            'amount' => 1000,
        ];

        $response = $this->postJson('/api/v1/products', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_finance_can_create_products(): void
    {
        $finance = $this->createUser('FINANCE');
        $this->actingAs($finance);

        $payload = [
            'name'   => 'Finance Product',
            'amount' => 1000,
        ];

        $response = $this->postJson('/api/v1/products', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_regular_user_cannot_manage_products(): void
    {
        $user = $this->createUser('USER');
        $this->actingAs($user);

        $product = $this->createProduct();

        $responseCreate = $this->postJson('/api/v1/products', [
            'name'   => 'Forbidden',
            'amount' => 1000,
        ]);

        $responseUpdate = $this->putJson("/api/v1/products/{$product->id}", [
            'name'   => 'Updated Name',
            'amount' => 1000,
        ]);

        $responseCreate->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => [
                    'type' => 'authorization_error',
                ],
            ]);

        $responseUpdate->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => [
                    'type' => 'authorization_error',
                ],
            ]);
    }

    public function test_products_can_be_listed(): void
    {
        $admin = $this->createUser('ADMIN');
        $this->actingAs($admin);

        $this->createProduct(['name' => 'P1']);
        $this->createProduct(['name' => 'P2']);

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    public function test_products_can_be_updated(): void
    {
        $admin = $this->createUser('ADMIN');
        $this->actingAs($admin);

        $product = $this->createProduct(['name' => 'Old Name', 'amount' => 10.00]);

        $response = $this->putJson("/api/v1/products/{$product->id}", [
            'name'   => 'New Name',
            'amount' => 1000,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('products', [
            'id'     => $product->id,
            'name'   => 'New Name',
            'amount' => 1000,
        ]);
    }

    public function test_validation_errors_occur_when_invalid_product_data_is_sent(): void
    {
        $admin = $this->createUser('ADMIN');
        $this->actingAs($admin);

        $response = $this->postJson('/api/v1/products', [
            'name'   => '',
            'amount' => -10,
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

