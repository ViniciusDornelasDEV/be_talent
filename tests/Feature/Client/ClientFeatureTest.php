<?php

declare(strict_types=1);

namespace Tests\Feature\Client;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Client\Models\Client;
use Modules\Order\Models\Transaction;
use Modules\Order\Models\TransactionProduct;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\TestCase;

class ClientFeatureTest extends TestCase
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

    private function createClient(array $overrides = []): Client
    {
        return Client::query()->create(array_merge([
            'name'  => 'Client Name',
            'email' => 'client@example.com',
        ], $overrides));
    }

    public function test_admin_can_list_clients(): void
    {
        $admin = $this->createUser('ADMIN');
        $this->actingAs($admin);

        $this->createClient(['email' => 'c1@example.com']);
        $this->createClient(['email' => 'c2@example.com']);

        $response = $this->getJson('/api/v1/clients');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    public function test_user_can_list_clients(): void
    {
        $user = $this->createUser('USER');
        $this->actingAs($user);

        $this->createClient(['email' => 'c1@example.com']);

        $response = $this->getJson('/api/v1/clients');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_manager_cannot_access_clients(): void
    {
        $manager = $this->createUser('MANAGER');
        $this->actingAs($manager);

        $client = $this->createClient();

        $indexResponse = $this->getJson('/api/v1/clients');
        $showResponse = $this->getJson("/api/v1/clients/{$client->id}");

        $indexResponse->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => ['type' => 'authorization_error'],
            ]);

        $showResponse->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => ['type' => 'authorization_error'],
            ]);
    }

    public function test_finance_cannot_access_clients(): void
    {
        $finance = $this->createUser('FINANCE');
        $this->actingAs($finance);

        $client = $this->createClient();

        $indexResponse = $this->getJson('/api/v1/clients');
        $showResponse = $this->getJson("/api/v1/clients/{$client->id}");

        $indexResponse->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => ['type' => 'authorization_error'],
            ]);

        $showResponse->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => ['type' => 'authorization_error'],
            ]);
    }

    public function test_admin_can_view_client_purchase_history(): void
    {
        $admin = $this->createUser('ADMIN');
        $this->actingAs($admin);

        $client = $this->createClient(['email' => 'history@example.com']);
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

        $response = $this->getJson("/api/v1/clients/{$client->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'client' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'transactions' => [
                        [
                            'id',
                            'status',
                            'amount',
                            'card_last_numbers',
                            'gateway_id',
                            'external_id',
                            'created_at',
                            'updated_at',
                            'items' => [
                                [
                                    'product_id',
                                    'product_name',
                                    'quantity',
                                    'amount',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
    }

    public function test_user_can_view_client_purchase_history(): void
    {
        $user = $this->createUser('USER');
        $this->actingAs($user);

        $client = $this->createClient(['email' => 'history2@example.com']);

        $response = $this->getJson("/api/v1/clients/{$client->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}

