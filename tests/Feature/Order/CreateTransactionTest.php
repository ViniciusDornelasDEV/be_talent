<?php

declare(strict_types=1);

namespace Tests\Feature\Order;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Modules\Order\Jobs\ProcessPaymentJob;
use Modules\Order\Models\Transaction;
use Modules\Order\Models\TransactionProduct;
use Modules\Product\Models\Product;
use Tests\TestCase;

class CreateTransactionTest extends TestCase
{
    use RefreshDatabase;

    private function createProduct(int $amount = 1000, array $overrides = []): Product
    {
        return Product::query()->create(array_merge([
            'name'   => 'Test Product',
            'amount' => $amount,
        ], $overrides));
    }

    public function test_purchase_creates_transaction_with_pending_status(): void
    {
        Queue::fake();

        $product = $this->createProduct(1000);

        $payload = [
            'client' => [
                'name'  => 'John Doe',
                'email' => 'john@example.com',
            ],
            'items'  => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
            'card'   => [
                'number' => '4111111111111111',
                'cvv'    => '123',
            ],
        ];

        $response = $this->postJson('/api/v1/orders/purchase', $payload);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('transactions', [
            'status' => 'pending',
        ]);

        $transaction = Transaction::query()->firstOrFail();
        $this->assertSame('pending', $transaction->status);
    }

    public function test_purchase_persists_transaction_products(): void
    {
        Queue::fake();

        $product1 = $this->createProduct(1000);
        $product2 = $this->createProduct(1500);

        $payload = [
            'client' => [
                'name'  => 'Jane Doe',
                'email' => 'jane@example.com',
            ],
            'items'  => [
                ['product_id' => $product1->id, 'quantity' => 2],
                ['product_id' => $product2->id, 'quantity' => 1],
            ],
            'card'   => [
                'number' => '5569000000006063',
                'cvv'    => '010',
            ],
        ];

        $this->postJson('/api/v1/orders/purchase', $payload)->assertStatus(201);

        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseCount('transaction_products', 2);

        $transaction = Transaction::query()->firstOrFail();
        $this->assertSame(2, $transaction->transactionProducts()->count());
    }

    public function test_purchase_calculates_correct_total_amount(): void
    {
        Queue::fake();

        $product1 = $this->createProduct(1000);
        $product2 = $this->createProduct(1500);

        $payload = [
            'client' => [
                'name'  => 'Amount Tester',
                'email' => 'amount@example.com',
            ],
            'items'  => [
                ['product_id' => $product1->id, 'quantity' => 2],
                ['product_id' => $product2->id, 'quantity' => 1],
            ],
            'card'   => [
                'number' => '4111111111111111',
                'cvv'    => '123',
            ],
        ];

        $this->postJson('/api/v1/orders/purchase', $payload)->assertStatus(201);

        $transaction = Transaction::query()->firstOrFail();
        $this->assertSame(3500, (int) $transaction->amount);
    }

    public function test_purchase_dispatches_process_payment_job(): void
    {
        Queue::fake();

        $product = $this->createProduct(1000);

        $payload = [
            'client' => [
                'name'  => 'Job Tester',
                'email' => 'job@example.com',
            ],
            'items'  => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
            'card'   => [
                'number' => '4111111111111111',
                'cvv'    => '123',
            ],
        ];

        $this->postJson('/api/v1/orders/purchase', $payload)->assertStatus(201);

        Queue::assertPushed(ProcessPaymentJob::class);
        $this->assertDatabaseCount('transactions', 1);
    }
}
