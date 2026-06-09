<?php

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Support\ApiMessages;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

test('customer or vendor can add to cart', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    $response = actingAs($user, 'sanctum')->postJson("/api/v1/carts/$product->id/create", [
        'quantity' => 2,
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'carts' => [
                    'full_name',
                    'product_name',
                    'quantity',
                    'thumbnail',
                ],
            ],
        ])->assertJsonPath('data.carts.product_name', $product->product_name);

    expect($response->json('type'))
        ->toBeString()
        ->toEqual(ApiMessages::SUCCESS)
        ->and($response->json('data.carts.thumbnail'))
        ->toBeUrl()
        ->not->toBeEmpty();

    $this->assertDatabaseCount('carts', 1);
    $this->assertDatabaseHas('carts', [
        'quantity' => 2,
    ]);
    $this->assertDatabaseHas('products', [
        'user_id' => $user->id,
        'category_id' => $category->id,
        'product_name' => $product->product_name,
    ]);
});

test('customer or vendor can not add to cart if not authenticated', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    $response = $this->postJson("/api/v1/carts/$product->id/create", [
        'quantity' => 2,
    ]);

    $response->assertUnauthorized();
});

test('if customer of vendor can delete to its items', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    $cart = Cart::factory()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/carts/$cart->id/delete");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [],
        ]);

    $this->assertDatabaseMissing('carts', [
        'id' => $cart->id,
        'user_id' => $cart->user_id,
        'product_id' => $cart->product_id,
    ]);

    expect($cart->user_id)
        ->toBe($user->id)
        ->and($cart->product_id)
        ->toBe($product->id);
});

test('if customer of vendor can make bulk delete to its items', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $products = Product::factory()->count(10)->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    $products->map(function ($product) use ($user) {
        return Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    });

    $this->assertDatabaseCount('carts', 10);

    $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/v1/carts/bulk-delete');

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [],
        ]);

    $this->assertDatabaseCount('carts', 0);
});

test('test when user clear its cart it is empty', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/v1/carts/bulk-delete');

    $response->assertBadRequest()
        ->assertJsonStructure([
            'message',
            'type',
        ])
        ->assertJsonPath('message', ApiMessages::CART_IS_EMPTY)
        ->assertJsonPath('type', ApiMessages::ERROR);

    $this->assertDatabaseCount('carts', 0);
});

test('test when user delete item from its cart and it is not exists', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/v1/carts/101/delete');

    $response->assertNotFound()
        ->assertJsonStructure([
            'message',
            'type',
        ])
        ->assertJsonPath('message', ApiMessages::RESOURCE_NOT_FOUND)
        ->assertJsonPath('type', ApiMessages::ERROR);

    $this->assertDatabaseCount('carts', 0);
});

test('user can get cart list when cart has items', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $products = Product::factory()
        ->count(3)
        ->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

    $products->each(function ($product) use ($user) {
        Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    });

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/carts/lists');

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'carts',
            ],
        ]);

    expect($response->json('data.carts'))
        ->toHaveCount(3);
});

test('user gets empty cart list when no items exist', function () {

    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/carts/lists');

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'carts',
            ],
        ]);

    expect($response->json('data.carts'))
        ->toBeEmpty();
});
