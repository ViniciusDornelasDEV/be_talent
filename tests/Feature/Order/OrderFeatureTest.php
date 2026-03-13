<?php

declare(strict_types=1);

namespace Tests\Feature\Order;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Order\Models\Transaction;
use Modules\Order\Models\TransactionProduct;
use Modules\Product\Models\Product;
use Tests\TestCase;

class OrderFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function createProduct(array $overrides = []): Product
    {
        return Product::query()->create(array_merge([
            'name'   => 'Test Product',
            'amount' => 1000,
        ], $overrides));
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
}

