<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => Hash::make('password123'),
        ]); // Buat pengguna
        $this->actingAs($user); // Login sebagai pengguna
    }

    public function test_index_returns_paginated_products()
    {
        $this->authenticate(); // Tambahkan autentikasi
        Product::factory()->count(15)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_index_filters_products_by_name()
    {
        $this->authenticate(); // Tambahkan autentikasi
        Product::factory()->create(['name' => 'Test Product']);
        Product::factory()->create(['name' => 'Another Product']);

        $response = $this->getJson('/api/products?search=Test');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_store_creates_a_new_product()
    {
        $this->authenticate(); // Tambahkan autentikasi
        $payload = [
            'name' => 'New Product',
            'description' => 'A great product',
            'quantity' => 10,
            'price' => 99.99,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'New Product']);

        $this->assertDatabaseHas('products', ['name' => 'New Product']);
    }

    // Lakukan hal serupa untuk semua pengujian lainnya...
    public function test_show_returns_a_product()
    {
        $this->authenticate();
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $product->id]);
    }

    public function test_update_updates_a_product()
    {
        $this->authenticate();
        $product = Product::factory()->create();

        $payload = [
            'name' => 'Updated Product',
        ];

        $response = $this->putJson("/api/products/{$product->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Product']);

        $this->assertDatabaseHas('products', ['name' => 'Updated Product']);
    }

    public function test_destroy_deletes_a_product()
    {
        $this->authenticate();
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
