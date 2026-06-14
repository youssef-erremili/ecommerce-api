<?php

use App\Enums\AccountType;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Support\ApiMessages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function makeVendor(): User
{
    return User::factory()->createQuietly([
        'account_type' => AccountType::VENDOR,
        'is_active' => true,
    ]);
}

function makeCategory(): Category
{
    return Category::factory()->create(['is_active' => true]);
}

function makeProduct(User $vendor, Category $category, array $overrides = []): Product
{
    return Product::factory()->create(array_merge([
        'user_id' => $vendor->id,
        'category_id' => $category->id,
    ], $overrides));
}

function fakeProductPayload(Category $category): array
{
    return [
        'category_id' => $category->id,
        'product_name' => 'Awesome Product',
        'description' => 'A well-written description that is long enough.',
        'price' => 199.99,
        'quantity' => 10,
        'product_images' => [
            UploadedFile::fake()->create('product.jpg', 100, 'image/jpeg'),
        ],
    ];
}

test('vendor can list their own products', function () {
    $vendor = makeVendor();
    $category = makeCategory();
    Product::factory()->count(3)->create(['user_id' => $vendor->id, 'category_id' => $category->id]);

    $response = $this->actingAs($vendor, 'sanctum')->getJson('/api/v1/products/lists');

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'products',
                'pagination',
            ],
        ])
        ->assertJsonPaths([
            'message' => ApiMessages::PRODUCT_FETCHED,
            'type' => ApiMessages::SUCCESS,
        ]);

    expect($response->json('data.products'))->toHaveCount(3);
});

test('admin can list products — policy allows admin', function () {
    $admin = User::factory()->createQuietly(['account_type' => 'admin', 'is_active' => true]);
    $vendor = makeVendor();
    $category = makeCategory();
    Product::factory()->count(2)->create(['user_id' => $vendor->id, 'category_id' => $category->id]);

    $response = $this->actingAs($admin, 'sanctum')->getJson('/api/v1/products/lists');

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

test('customer cannot list products — exception caught, returns 500', function () {
    $customer = User::factory()->createQuietly([
        'account_type' => AccountType::CUSTOMER,
    ]);

    $response = $this->actingAs($customer, 'sanctum')->getJson('/api/v1/products/lists');

    $response->assertStatus(500)
        ->assertJsonPaths([
            'type' => ApiMessages::ERROR,
        ]);
});

test('guest cannot list products', function () {
    $response = $this->getJson('/api/v1/products/lists');

    $response->assertUnauthorized();
});

test('authenticated user can view a single product', function () {
    $vendor = makeVendor();
    $category = makeCategory();
    $product = makeProduct($vendor, $category);

    $response = $this->actingAs($vendor, 'sanctum')->getJson("/api/v1/products/show/$product->id");

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
                    'quantity',
                ],
            ],
        ])
        ->assertJsonPath('data.product.id', $product->id);
});

test('guest cannot view a single product', function () {
    $vendor = makeVendor();
    $category = makeCategory();
    $product = makeProduct($vendor, $category);

    $response = $this->getJson("/api/v1/products/show/$product->id");

    $response->assertUnauthorized();
});

test('viewing a non-existent product returns 404', function () {
    $vendor = makeVendor();

    $response = $this->actingAs($vendor, 'sanctum')->getJson('/api/v1/products/show/646');

    $response->assertNotFound();
});

test('vendor can create a product with image upload', function () {
    Storage::fake('supabase');

    $vendor = makeVendor();
    $category = makeCategory();

    $response = $this->actingAs($vendor, 'sanctum')->postJson('/api/v1/products/store', fakeProductPayload($category));

    $response->assertCreated()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'product' => [
                    'id',
                    'product_name',
                    'description',
                    'price',
                    'quantity',
                ],
            ],
        ])
        ->assertJsonPaths([
            'message' => ApiMessages::PRODUCT_CREATED,
            'type' => ApiMessages::SUCCESS,
            'data.product.product_name' => 'Awesome Product',
        ]);

    $this->assertDatabaseHas('products', [
        'user_id' => $vendor->id,
        'category_id' => $category->id,
        'product_name' => 'Awesome Product',
    ]);
});

test('customer cannot create a product', function () {
    Storage::fake('supabase');

    $customer = User::factory()->create([
        'account_type' => AccountType::CUSTOMER,
    ]);
    $category = makeCategory();

    $response = $this->actingAs($customer, 'sanctum')
        ->postJson('/api/v1/products/store', fakeProductPayload($category));

    $response->assertBadRequest()
        ->assertJsonPaths([
            'message' => ApiMessages::USER_NOT_VENDOR,
            'type' => ApiMessages::ERROR,
        ]);

    $this->assertDatabaseCount('products', 0);
});

test('guest cannot create a product', function () {
    $response = $this->postJson('/api/v1/products/store', []);

    $response->assertUnauthorized();
});

test('create product fails with missing required fields', function () {
    $vendor = makeVendor();

    $response = $this->actingAs($vendor, 'sanctum')
        ->postJson('/api/v1/products/store', []);

    $response->assertUnprocessable()
        ->assertJsonPath('type', ApiMessages::ERROR);

    $this->assertDatabaseCount('products', 0);
});

test('create product fails with invalid price (non-numeric)', function () {
    $vendor = makeVendor();
    $category = makeCategory();

    $payload = fakeProductPayload($category);
    $payload['price'] = 'not-a-number';

    $response = $this->actingAs($vendor, 'sanctum')->postJson('/api/v1/products/store', $payload);

    $response->assertUnprocessable()
        ->assertJsonPath('type', ApiMessages::ERROR);
});

test('vendor can update their own product', function () {
    $vendor = makeVendor();
    $category = makeCategory();
    $product = makeProduct($vendor, $category, ['product_name' => 'Old Name']);

    $response = $this->actingAs($vendor, 'sanctum')
        ->patchJson("/api/v1/products/update/$product->id", [
            'product_name' => 'New Name',
            'category_id' => $category->id,
            'description' => 'Updated description that is long enough.',
            'price' => 299.99,
            'quantity' => 5,
        ]);

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => ['product'],
        ])
        ->assertJsonPath('data.product.product_name', 'New Name');

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'product_name' => 'New Name',
    ]);
});

test('vendor cannot update a product belonging to another vendor', function () {
    $vendor1 = makeVendor();
    $vendor2 = makeVendor();
    $category = makeCategory();
    $product = makeProduct($vendor1, $category);

    $response = $this->actingAs($vendor2, 'sanctum')
        ->patchJson("/api/v1/products/update/$product->id", [
            'product_name' => 'Hijacked Name',
            'category_id' => $category->id,
            'description' => "Trying to update someone else's product.",
            'price' => 10.00,
            'quantity' => 1,
        ]);

    $response->assertForbidden();

    $this->assertDatabaseMissing('products', [
        'id' => $product->id,
        'product_name' => 'Hijacked Name',
    ]);
});

test('guest cannot update a product', function () {
    $vendor = makeVendor();
    $category = makeCategory();
    $product = makeProduct($vendor, $category);

    $response = $this->patchJson("/api/v1/products/update/$product->id", [
        'product_name' => 'Should Fail',
    ]);

    $response->assertUnauthorized();
});

test('vendor can update their product images', function () {
    Storage::fake('supabase');

    $vendor = makeVendor();
    $category = makeCategory();
    $product = makeProduct($vendor, $category, ['product_images' => []]);

    $response = $this->actingAs($vendor, 'sanctum')->putJson("/api/v1/products/update/$product->id/images", [
        'product_images' => [
            UploadedFile::fake()->create('image-1.jpg', 100, 'image/jpeg'),
            UploadedFile::fake()->create('image-2.jpg', 100, 'image/jpeg'),
        ],
    ]);

    $response->assertOk()
        ->assertJsonPaths([
            'message' => ApiMessages::ACTION_COMPLETED,
            'type' => ApiMessages::SUCCESS,
        ]);
});

test('update images validation fails with fewer than 2 images', function () {
    $vendor = makeVendor();
    $category = makeCategory();
    $product = makeProduct($vendor, $category);

    $response = $this->actingAs($vendor, 'sanctum')
        ->putJson("/api/v1/products/update/$product->id/images", [
            'product_images' => [
                UploadedFile::fake()->create('only-one.jpg', 100, 'image/jpeg'),
            ],
        ]);

    $response->assertUnprocessable()
        ->assertJsonPath('type', ApiMessages::ERROR);
});

test('vendor cannot update images of another vendor product — exception caught, returns 500', function () {
    Storage::fake('supabase');

    $vendor1 = makeVendor();
    $vendor2 = makeVendor();
    $category = makeCategory();
    $product = makeProduct($vendor1, $category);

    $response = $this->actingAs($vendor2, 'sanctum')
        ->putJson("/api/v1/products/update/$product->id/images", [
            'product_images' => [
                UploadedFile::fake()->create('hack-1.jpg', 100, 'image/jpeg'),
                UploadedFile::fake()->create('hack-2.jpg', 100, 'image/jpeg'),
            ],
        ]);

    $response->assertStatus(500)
        ->assertJsonPath('type', ApiMessages::ERROR);
});

test('vendor can soft-delete their own product', function () {
    $vendor = makeVendor();
    $category = makeCategory();
    $product = makeProduct($vendor, $category);

    $response = $this->actingAs($vendor, 'sanctum')->deleteJson("/api/v1/products/delete/$product->id");

    $response->assertOk()
        ->assertJsonPaths([
            'message' => ApiMessages::PRODUCT_DELETED,
            'type' => ApiMessages::SUCCESS,
        ]);

    $this->assertSoftDeleted('products', ['id' => $product->id]);
    expect($product->fresh()->deleted_at)->not->toBeNull();
});

test('vendor cannot delete a product belonging to another vendor — exception caught, returns 500', function () {
    $vendor1 = makeVendor();
    $vendor2 = makeVendor();
    $category = makeCategory();
    $product = makeProduct($vendor1, $category);

    $response = $this->actingAs($vendor2, 'sanctum')->deleteJson("/api/v1/products/delete/$product->id");

    $response->assertStatus(500)
        ->assertJsonPath('type', ApiMessages::ERROR);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
    ]);
    expect($product->fresh()->deleted_at)->toBeNull();
});

test('guest cannot delete a product', function () {
    $vendor = makeVendor();
    $category = makeCategory();
    $product = makeProduct($vendor, $category);

    $response = $this->deleteJson("/api/v1/products/delete/$product->id");

    $response->assertUnauthorized();
});

test('deleting a non-existent product returns 404', function () {
    $vendor = makeVendor();

    $response = $this->actingAs($vendor, 'sanctum')->deleteJson('/api/v1/products/delete/999999');

    $response->assertNotFound();
});
