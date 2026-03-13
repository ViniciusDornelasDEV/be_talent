<?php

declare(strict_types=1);

namespace Modules\Product\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsRole(string $role): User
    {
        $user = User::factory()->create([
            'role' => $role,
        ]);

        Sanctum::actingAs($user);

        return $user;
    }

    public function test_admin_can_list_products(): void
    {
        Product::factory()->count(3)->create();

        $this->actingAsRole('ADMIN');

        $response = $this->getJson('/api/v1/products');

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_user_cannot_list_products(): void
    {
        Product::factory()->count(3)->create();

        $this->actingAsRole('USER');

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => [
                    'type' => 'authorization_error',
                ],
            ]);
    }
}

