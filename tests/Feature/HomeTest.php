<?php

use App\Enums\AccountType;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Support\ApiMessages;
use App\Traits\GenerateSlug;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->uses(RefreshDatabase::class);
pest()->uses(GenerateSlug::class);

test('test user can get single product as guest', function () {
    $user = User::factory()->createQuietly([
        'account_type' => AccountType::VENDOR,
        'is_active' => true,
    ]);
    $category = Category::factory()->create([
        'is_active' => true,
    ]);
    $product = Product::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'is_active' => true,
    ]);

    $response = $this->getJson("/api/v1/home/product/$product->slug");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'product' => [
                    'id',
                    'product_name',
                    'description',
                    'price',
                    'slug',
                    'discount',
                    'quantity',
                    'product_images',
                    'vendor' => [
                        'full_name',
                        'slug',
                        'email_address',
                    ],
                    'category' => [
                        'category',
                        'category_slug',
                    ],
                ],
            ],
        ]);

    expect($product->slug)
        ->toBeString()
        ->not()
        ->toBeEmpty()
        ->and($response->json('data.product'))
        ->toBeArray()
        ->not
        ->toBeEmpty()
        ->toHaveKeys([
            'id',
            'product_name',
            'description',
            'price',
            'slug',
            'discount',
            'quantity',
            'product_images',
            'vendor',
            'category',
        ])
        ->and($response->json('data.product.product_images'))
        ->toBeArray()
        ->not->toBeEmpty()
        ->and($response->json('data.product.vendor'))
        ->toBeArray()
        ->toHaveKeys([
            'full_name',
            'slug',
            'email_address',
        ])
        ->and($response->json('data.product.category'))
        ->toBeArray()
        ->toHaveKeys([
            'category',
            'category_slug',
        ]);

    $this->assertDatabaseHas('products', [
        'slug' => $product->slug,
    ]);
});

test('test if there is no product exist in Database', function () {
    $response = $this->getJson('/api/v1/home/product/1');

    $response->assertStatus(500)
        ->assertJsonStructure([
            'message',
            'type',
        ])->assertJsonPaths([
            'message' => ApiMessages::PRODUCT_NOT_FOUND,
            'type' => ApiMessages::ERROR,
        ]);

    $this->assertDatabaseMissing('products', [
        'id' => 1,
    ]);

    expect($response->assertJsonMissingPath('data'));

    $this->assertDatabaseCount('products', 0);
});

test('guest can list home products', function () {
    $vendor = User::factory()->create([
        'account_type' => AccountType::VENDOR,
        'is_active' => true,
    ]);
    $category = Category::factory()->create([
        'is_active' => true,
    ]);
    Product::factory()->count(3)->create([
        'user_id' => $vendor->id,
        'category_id' => $category->id,
        'is_active' => true,
    ]);

    $response = $this->getJson('/api/v1/home/products');

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'products',
                'pagination',
            ],
        ]);
});

test('guest can view a seller products', function () {
    $seller = User::factory()->create([
        'account_type' => AccountType::VENDOR,
        'is_active' => true,
    ]);
    $category = Category::factory()->create([
        'is_active' => true,
    ]);
    Product::factory()->count(2)->create([
        'user_id' => $seller->id,
        'category_id' => $category->id,
        'is_active' => true,
    ]);

    $response = $this->getJson("/api/v1/home/seller/$seller->slug");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'pagination',
                'vendor',
            ],
        ]);
});
