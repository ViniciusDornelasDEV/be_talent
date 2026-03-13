<?php

declare(strict_types=1);

namespace Tests\Feature\Gateway;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Gateway\Models\Gateway;
use Modules\User\Models\User;
use Tests\TestCase;

class GatewayFeatureTest extends TestCase
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

    private function createGateway(array $overrides = []): Gateway
    {
        return Gateway::query()->create(array_merge([
            'name'      => 'Test Gateway',
            'is_active' => true,
            'priority'  => 1,
        ], $overrides));
    }

    public function test_admin_can_toggle_gateway_status(): void
    {
        $admin = $this->createUser('ADMIN');
        $this->actingAs($admin);

        $gateway = $this->createGateway(['is_active' => true]);

        $response = $this->patchJson("/api/v1/gateways/{$gateway->id}/toggle-active");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('gateways', [
            'id'        => $gateway->id,
            'is_active' => false,
        ]);
    }

    public function test_user_can_toggle_gateway_status(): void
    {
        $user = $this->createUser('USER');
        $this->actingAs($user);

        $gateway = $this->createGateway(['is_active' => true]);

        $response = $this->patchJson("/api/v1/gateways/{$gateway->id}/toggle-active");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('gateways', [
            'id'        => $gateway->id,
            'is_active' => false,
        ]);
    }

    public function test_manager_cannot_toggle_gateway_status(): void
    {
        $manager = $this->createUser('MANAGER');
        $this->actingAs($manager);

        $gateway = $this->createGateway();

        $response = $this->patchJson("/api/v1/gateways/{$gateway->id}/toggle-active");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => [
                    'type' => 'authorization_error',
                ],
            ]);
    }

    public function test_finance_cannot_toggle_gateway_status(): void
    {
        $finance = $this->createUser('FINANCE');
        $this->actingAs($finance);

        $gateway = $this->createGateway();

        $response = $this->patchJson("/api/v1/gateways/{$gateway->id}/toggle-active");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => [
                    'type' => 'authorization_error',
                ],
            ]);
    }

    public function test_admin_can_change_gateway_priority(): void
    {
        $admin = $this->createUser('ADMIN');
        $this->actingAs($admin);

        $gateway = $this->createGateway(['priority' => 1]);

        $response = $this->patchJson("/api/v1/gateways/{$gateway->id}/priority", [
            'priority' => 10,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('gateways', [
            'id'       => $gateway->id,
            'priority' => 10,
        ]);
    }

    public function test_user_can_change_gateway_priority(): void
    {
        $user = $this->createUser('USER');
        $this->actingAs($user);

        $gateway = $this->createGateway(['priority' => 2]);

        $response = $this->patchJson("/api/v1/gateways/{$gateway->id}/priority", [
            'priority' => 3,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('gateways', [
            'id'       => $gateway->id,
            'priority' => 3,
        ]);
    }

    public function test_manager_cannot_change_gateway_priority(): void
    {
        $manager = $this->createUser('MANAGER');
        $this->actingAs($manager);

        $gateway = $this->createGateway(['priority' => 2]);

        $response = $this->patchJson("/api/v1/gateways/{$gateway->id}/priority", [
            'priority' => 3,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => [
                    'type' => 'authorization_error',
                ],
            ]);
    }

    public function test_finance_cannot_change_gateway_priority(): void
    {
        $finance = $this->createUser('FINANCE');
        $this->actingAs($finance);

        $gateway = $this->createGateway(['priority' => 2]);

        $response = $this->patchJson("/api/v1/gateways/{$gateway->id}/priority", [
            'priority' => 3,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => [
                    'type' => 'authorization_error',
                ],
            ]);
    }

    public function test_priority_validation_errors(): void
    {
        $admin = $this->createUser('ADMIN');
        $this->actingAs($admin);

        $gateway = $this->createGateway();

        $response = $this->patchJson("/api/v1/gateways/{$gateway->id}/priority", [
            'priority' => 'not-an-integer',
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

