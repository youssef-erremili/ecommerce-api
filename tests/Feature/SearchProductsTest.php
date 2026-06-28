<?php

use App\Enums\AccountType;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->uses(RefreshDatabase::class);

test('it can guest user search', function () {
    $user = User::factory()->createQuietly([
        'account_type' => AccountType::VENDOR,
        'is_active' => true,
    ]);
    $category = Category::factory()->create();

    $product = Product::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'product_name' => 'erremili',
    ]);

    $response = $this->getJson('/api/v1/home/search?'.http_build_query(['query' => $product->product_name]));

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'pagination',
                'result',
            ],
        ]);

    $this->assertDatabaseHas('products', [
        'product_name' => $product->product_name,
    ]);
});

test('it fails validation when query parameter is missing', function () {
    $response = $this->getJson('/api/v1/home/search');

    $response->assertStatus(422);
});

test('it throws a 404 exception when no products match the query', function () {
    $response = $this->getJson('/api/v1/home/search?'.http_build_query(['query' => 'nonexistentproduct']));

    $response->assertStatus(404);
});

test('it excludes inactive products from search results', function () {
    $user = User::factory()->createQuietly([
        'account_type' => AccountType::VENDOR,
        'is_active' => true,
    ]);
    $category = Category::factory()->create();

    Product::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'product_name' => 'erremili',
        'is_active' => false,
    ]);

    $response = $this->getJson('/api/v1/home/search?'.http_build_query(['query' => 'erremili']));

    $response->assertStatus(404);
});

test('it applies active logic when category parameter is present', function () {
    $user = User::factory()->createQuietly([
        'account_type' => AccountType::VENDOR,
        'is_active' => true,
    ]);
    $category = Category::factory()->create();

    $product = Product::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'product_name' => 'erremili category collection',
        'is_active' => true,
    ]);

    $response = $this->getJson('/api/v1/home/search?'.http_build_query([
        'query' => 'erremili',
        'category' => 'true',
    ]));

    $response->assertOk();
});
