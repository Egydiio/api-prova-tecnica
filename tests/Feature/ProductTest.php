<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_product(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Produto Teste',
            'description' => 'Esse é um teste',
            'price' => 19.99,
            'stock_quantity' => 100,
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Produto Teste']);
    }

    public function test_can_list_products(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_update_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $product->name]);
    }

    public function test_can_delete_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_can_filter_products_by_name(): void
    {
        Product::factory()->create(['name' => 'Mouse Gamer']);
        Product::factory()->create(['name' => 'Teclado Gamer']);

        $response = $this->getJson('/api/products?name=Mouse');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_filter_products_by_price_range(): void
    {
        Product::factory()->create(['price' => 50.00]);
        Product::factory()->create(['price' => 150.00]);
        Product::factory()->create(['price' => 300.00]);

        $response = $this->getJson('/api/products?min_price=100&max_price=200');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_validation_requires_name(): void
    {
        $response = $this->postJson('/api/products', [
            'description'    => 'Sem nome',
            'price'          => 100.00,
            'stock_quantity' => 5,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}
