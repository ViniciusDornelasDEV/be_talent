<?php

declare(strict_types=1);

namespace Tests\Feature\Order;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Client\Models\Client;
use Modules\Order\Models\Transaction;
use Modules\Order\Models\TransactionProduct;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\TestCase;

class OrderFeatureTest extends TestCase
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

    private function createOrderScenario(): array
    {
        $client = Client::query()->create([
            'name'  => 'Client One',
            'email' => 'client1@example.com',
        ]);

        $product = Product::query()->create([
            'name'   => 'Product 1',
            'amount' => 1000,
        ]);

        $transaction = Transaction::query()->create([
            'client_id'         => $client->id,
            'gateway_id'        => null,
            'external_id'       => null,
            'status'            => 'paid',
            'amount'            => 2000,
            'card_last_numbers' => '1234',
        ]);

        TransactionProduct::query()->create([
            'transaction_id' => $transaction->id,
            'product_id'     => $product->id,
            'quantity'       => 2,
            'amount'         => 1000,
        ]);

        return compact('client', 'product', 'transaction');
    }

    public function test_it_creates_a_purchase_with_multiple_products(): void
    {
        $product1 = $this->createProduct(['name' => 'P1', 'amount' => 1000]);
        $product2 = $this->createProduct(['name' => 'P2', 'amount' => 1500]);

        $payload = [
            'client' => [
                'name'  => 'John Doe',
                'email' => 'john@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product1->id,
                    'quantity'   => 2,
                ],
                [
                    'product_id' => $product2->id,
                    'quantity'   => 1,
                ],
            ],
            'card' => [
                'number' => '5569000000006063',
                'cvv'    => '010',
            ],
        ];

        $response = $this->postJson('/api/v1/orders/purchase', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseCount('transaction_products', 2);
    }

    public function test_it_calculates_total_amount_correctly(): void
    {
        $product1 = $this->createProduct(['amount' => 1000]);
        $product2 = $this->createProduct(['amount' => 1500]);

        $payload = [
            'client' => [
                'name'  => 'Jane Doe',
                'email' => 'jane@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product1->id,
                    'quantity'   => 2,
                ],
                [
                    'product_id' => $product2->id,
                    'quantity'   => 1,
                ],
            ],
            'card' => [
                'number' => '4111111111111111',
                'cvv'    => '123',
            ],
        ];

        $response = $this->postJson('/api/v1/orders/purchase', $payload);

        $response->assertStatus(201);

        $transaction = Transaction::query()->firstOrFail();

        $this->assertSame(3500, (int) $transaction->amount);
    }

    public function test_it_stores_product_price_from_product_module_in_transaction_products(): void
    {
        $product = $this->createProduct(['amount' => 2000]);

        $payload = [
            'client' => [
                'name'  => 'Price Tester',
                'email' => 'price@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity'   => 3,
                ],
            ],
            'card' => [
                'number' => '4000000000000002',
                'cvv'    => '456',
            ],
        ];

        $this->postJson('/api/v1/orders/purchase', $payload)
            ->assertStatus(201);

        $transactionProduct = TransactionProduct::query()->firstOrFail();

        $this->assertSame($product->amount, (int) $transactionProduct->amount);
        $this->assertSame(3, (int) $transactionProduct->quantity);
    }

    public function test_it_fails_when_items_are_invalid(): void
    {
        $payload = [
            'client' => [
                'name'  => 'Invalid Items',
                'email' => 'invalid@example.com',
            ],
            'items' => [
                [
                    'product_id' => null,
                    'quantity'   => 0,
                ],
            ],
            'card' => [
                'number' => '4111111111111111',
                'cvv'    => '123',
            ],
        ];

        $response = $this->postJson('/api/v1/orders/purchase', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'type' => 'validation_error',
                ],
            ]);
    }

    public function test_it_fails_when_product_does_not_exist(): void
    {
        $payload = [
            'client' => [
                'name'  => 'Missing Product',
                'email' => 'missing@example.com',
            ],
            'items' => [
                [
                    'product_id' => 999999,
                    'quantity'   => 1,
                ],
            ],
            'card' => [
                'number' => '4111111111111111',
                'cvv'    => '123',
            ],
        ];

        $response = $this->postJson('/api/v1/orders/purchase', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'type' => 'validation_error',
                ],
            ]);
    }

    public function test_admin_can_list_orders(): void
    {
        $admin = $this->createUser('ADMIN');
        $this->actingAs($admin);

        $this->createOrderScenario();

        $response = $this->getJson('/api/v1/orders');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [
                    [
                        'id',
                        'client',
                        'status',
                        'amount',
                        'gateway',
                        'created_at',
                    ],
                ],
            ]);
    }

    public function test_user_can_list_orders(): void
    {
        $user = $this->createUser('USER');
        $this->actingAs($user);

        $this->createOrderScenario();

        $response = $this->getJson('/api/v1/orders');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_admin_can_view_order_details(): void
    {
        $admin = $this->createUser('ADMIN');
        $this->actingAs($admin);

        ['transaction' => $transaction] = $this->createOrderScenario();

        $response = $this->getJson("/api/v1/orders/{$transaction->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'status',
                    'amount',
                    'card_last_numbers',
                    'gateway',
                    'client' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'items' => [
                        [
                            'product_id',
                            'product_name',
                            'quantity',
                            'amount',
                        ],
                    ],
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_user_can_view_order_details(): void
    {
        $user = $this->createUser('USER');
        $this->actingAs($user);

        ['transaction' => $transaction] = $this->createOrderScenario();

        $response = $this->getJson("/api/v1/orders/{$transaction->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_manager_cannot_access_orders(): void
    {
        $manager = $this->createUser('MANAGER');
        $this->actingAs($manager);

        ['transaction' => $transaction] = $this->createOrderScenario();

        $index = $this->getJson('/api/v1/orders');
        $show  = $this->getJson("/api/v1/orders/{$transaction->id}");

        $index->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => ['type' => 'authorization_error'],
            ]);

        $show->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => ['type' => 'authorization_error'],
            ]);
    }

    public function test_finance_cannot_access_orders(): void
    {
        $finance = $this->createUser('FINANCE');
        $this->actingAs($finance);

        ['transaction' => $transaction] = $this->createOrderScenario();

        $index = $this->getJson('/api/v1/orders');
        $show  = $this->getJson("/api/v1/orders/{$transaction->id}");

        $index->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => ['type' => 'authorization_error'],
            ]);

        $show->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => ['type' => 'authorization_error'],
            ]);
    }
}

