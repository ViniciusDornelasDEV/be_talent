<?php

declare(strict_types=1);

namespace Tests\Feature\Refund;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Modules\Client\Models\Client;
use Modules\Gateway\Models\Gateway;
use Modules\Order\Models\Transaction;
use Modules\User\Models\User;
use Tests\TestCase;

class RefundTransactionTest extends TestCase
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

    private function createPaidTransactionWithGateway(array $overrides = []): Transaction
    {
        $client = Client::query()->create([
            'name'  => 'Refund Client',
            'email' => 'refund@example.com',
        ]);

        $gateway = Gateway::query()->create([
            'name'      => 'gateway2',
            'is_active' => true,
            'priority'  => 1,
        ]);

        return Transaction::query()->create(array_merge([
            'client_id'         => $client->id,
            'gateway_id'        => $gateway->id,
            'external_id'       => 'ext-789',
            'status'            => 'paid',
            'amount'            => 5000,
            'card_last_numbers' => '9999',
        ], $overrides));
    }

    public function test_admin_can_refund_paid_transaction(): void
    {
        Http::fake([
            '*' => Http::response([], 200),
        ]);

        $admin = $this->createUser('ADMIN');
        $transaction = $this->createPaidTransactionWithGateway();

        $this->actingAs($admin);

        $response = $this->postJson("/api/v1/orders/{$transaction->id}/refund");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $transaction->refresh();
        $this->assertSame('refunded', $transaction->status);
    }

    public function test_finance_can_refund_paid_transaction(): void
    {
        Http::fake([
            '*' => Http::response([], 200),
        ]);

        $finance = $this->createUser('FINANCE');
        $transaction = $this->createPaidTransactionWithGateway();

        $this->actingAs($finance);

        $response = $this->postJson("/api/v1/orders/{$transaction->id}/refund");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $transaction->refresh();
        $this->assertSame('refunded', $transaction->status);
    }

    public function test_manager_cannot_refund_returns_403(): void
    {
        $manager = $this->createUser('MANAGER');
        $transaction = $this->createPaidTransactionWithGateway();

        $this->actingAs($manager);

        $response = $this->postJson("/api/v1/orders/{$transaction->id}/refund");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error'   => [
                    'type' => 'authorization_error',
                ],
            ]);
    }

    public function test_user_cannot_refund_returns_403(): void
    {
        $user = $this->createUser('USER');
        $transaction = $this->createPaidTransactionWithGateway();

        $this->actingAs($user);

        $response = $this->postJson("/api/v1/orders/{$transaction->id}/refund");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error'   => [
                    'type' => 'authorization_error',
                ],
            ]);
    }

    public function test_refund_idempotent_when_already_refunded(): void
    {
        Http::fake();

        $admin = $this->createUser('ADMIN');
        $transaction = $this->createPaidTransactionWithGateway(['status' => 'refunded']);

        $this->actingAs($admin);

        $response = $this->postJson("/api/v1/orders/{$transaction->id}/refund");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertCount(0, Http::recorded());
    }

    public function test_refund_returns_404_for_missing_transaction(): void
    {
        $admin = $this->createUser('ADMIN');
        $this->actingAs($admin);

        $response = $this->postJson('/api/v1/orders/99999/refund');

        $response->assertStatus(404);
    }
}
