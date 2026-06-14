<?php

use App\Enums\AccountType;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use App\Support\ApiMessages;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function wishlistCustomer(): User
{
    return User::factory()->create([
        'account_type' => AccountType::CUSTOMER,
    ]);
}

function wishlistVendor(): User
{
    return User::factory()->createQuietly([
        'account_type' => AccountType::VENDOR,
    ]);
}

function wishlistAdmin(): User
{
    return User::factory()->createQuietly([
        'account_type' => AccountType::ADMIN,
    ]);
}

function wishlistProduct(User $vendor, Category $category): Product
{
    return Product::factory()->create([
        'user_id' => $vendor->id,
        'category_id' => $category->id,
    ]);
}

function wishlistCategory(): Category
{
    return Category::factory()->create([
        'is_active' => true,
    ]);
}

function createWishlistEntry(User $user, Product $product): Wishlist
{
    return Wishlist::forceCreate([
        'user_id' => $user->id,
        'product_id' => $product->id,
    ]);
}

test('user can list their wishlists', function () {
    $user = wishlistCustomer();
    $vendor = wishlistVendor();
    $category = wishlistCategory();
    $product = wishlistProduct($vendor, $category);

    createWishlistEntry($user, $product);

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/wishlist/lists');

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'wishlists',
                'pagination',
            ],
        ])
        ->assertJsonPaths([
            'message' => ApiMessages::ACTION_COMPLETED,
            'type' => ApiMessages::SUCCESS,
        ]);

    expect($response->json('data.wishlists'))->toHaveCount(1);
});

test('listing an empty wishlist returns 500 (WishlistService throws WISHLIST_EMPTY)', function () {
    $user = wishlistCustomer();

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/wishlist/lists');

    $response->assertStatus(500)
        ->assertJsonPaths([
            'message' => ApiMessages::WISHLIST_EMPTY,
            'type' => ApiMessages::ERROR,
        ]);
});

test('guest cannot list wishlists', function () {
    $response = $this->getJson('/api/v1/wishlist/lists');

    $response->assertUnauthorized();
});

test('customer can add a product to their wishlist', function () {
    $user = wishlistCustomer();
    $vendor = wishlistVendor();
    $category = wishlistCategory();
    $product = wishlistProduct($vendor, $category);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/wishlist/store', ['product_id' => $product->id]);

    $response->assertCreated()
        ->assertJsonPaths([
            'message' => ApiMessages::ACTION_COMPLETED,
            'type' => ApiMessages::SUCCESS,
        ]);

    $this->assertDatabaseHas('wishlists', [
        'user_id' => $user->id,
        'product_id' => $product->id,
    ]);
});

test('vendor can add a product to their wishlist', function () {
    $vendor1 = wishlistVendor();
    $vendor2 = wishlistVendor();
    $category = wishlistCategory();
    $product = wishlistProduct($vendor1, $category);

    $response = $this->actingAs($vendor2, 'sanctum')->postJson('/api/v1/wishlist/store', [
        'product_id' => $product->id,
    ]);

    $response->assertCreated();

    $this->assertDatabaseHas('wishlists', [
        'user_id' => $vendor2->id,
        'product_id' => $product->id,
    ]);
});

test('admin cannot add a product to a wishlist — returns 500 (exception from policy denial)', function () {
    $admin = wishlistAdmin();
    $vendor = wishlistVendor();
    $category = wishlistCategory();
    $product = wishlistProduct($vendor, $category);

    $response = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/wishlist/store', [
        'product_id' => $product->id,
    ]);

    $response->assertStatus(500);

    $this->assertDatabaseCount('wishlists', 0);
});

test('adding the same product twice to the wishlist returns 500 (WISH_ALREADY_EXISTS)', function () {
    $user = wishlistCustomer();
    $vendor = wishlistVendor();
    $category = wishlistCategory();
    $product = wishlistProduct($vendor, $category);

    createWishlistEntry($user, $product);

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/wishlist/store', [
        'product_id' => $product->id,
    ]);

    $response->assertStatus(500)
        ->assertJsonPaths([
            'message' => ApiMessages::WISH_ALREADY_EXISTS,
            'type' => ApiMessages::ERROR,
        ]);

    $this->assertDatabaseCount('wishlists', 1);
});

test('adding a non-existent product to the wishlist returns 500 (PRODUCT_NOT_FOUND)', function () {
    $user = wishlistCustomer();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/wishlist/store', [
        'product_id' => 536,
    ]);

    $response->assertStatus(500)
        ->assertJsonPaths([
            'message' => ApiMessages::PRODUCT_NOT_FOUND,
            'type' => ApiMessages::ERROR,
        ]);
});

test('guest cannot add a product to the wishlist', function () {
    $response = $this->postJson('/api/v1/wishlist/store', [
        'product_id' => 1,
    ]);

    $response->assertUnauthorized();
});

test('user can remove an item from their wishlist', function () {
    $user = wishlistCustomer();
    $vendor = wishlistVendor();
    $category = wishlistCategory();
    $product = wishlistProduct($vendor, $category);
    $wishlist = createWishlistEntry($user, $product);

    $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/wishlist/delete/$wishlist->id");

    $response->assertOk()
        ->assertJsonPaths([
            'message' => ApiMessages::ACTION_COMPLETED,
            'type' => ApiMessages::SUCCESS,
        ]);

    $this->assertDatabaseMissing('wishlists', [
        'id' => $wishlist->id,
    ]);
});

test('user cannot remove an item from another user wishlist — returns 500 (exception caught)', function () {
    $user1 = wishlistCustomer();
    $user2 = wishlistCustomer();
    $vendor = wishlistVendor();
    $category = wishlistCategory();
    $product = wishlistProduct($vendor, $category);
    $wishlist = createWishlistEntry($user1, $product);

    $response = $this->actingAs($user2, 'sanctum')->deleteJson("/api/v1/wishlist/delete/$wishlist->id");

    $response->assertStatus(500);

    $this->assertDatabaseHas('wishlists', [
        'id' => $wishlist->id,
    ]);
});

test('deleting a non-existent wishlist item returns 404', function () {
    $user = wishlistCustomer();

    $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/v1/wishlist/delete/7778');

    $response->assertNotFound();
});

test('guest cannot remove an item from a wishlist', function () {
    $user = wishlistCustomer();
    $vendor = wishlistVendor();
    $category = wishlistCategory();
    $product = wishlistProduct($vendor, $category);
    $wishlist = createWishlistEntry($user, $product);

    $response = $this->deleteJson("/api/v1/wishlist/delete/$wishlist->id");

    $response->assertUnauthorized();
});

test('user can clear specific wishlist items by ids', function () {
    $user = wishlistCustomer();
    $vendor = wishlistVendor();
    $category = wishlistCategory();

    $product1 = wishlistProduct($vendor, $category);
    $product2 = wishlistProduct($vendor, $category);

    $wishlist1 = createWishlistEntry($user, $product1);
    $wishlist2 = createWishlistEntry($user, $product2);

    $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/v1/wishlist/bulk-delete', [
        'ids' => [$wishlist1->id, $wishlist2->id],
    ]);

    $response->assertOk()
        ->assertJsonPaths([
            'message' => ApiMessages::ACTION_COMPLETED,
            'type' => ApiMessages::SUCCESS,
        ]);

    $this->assertDatabaseMissing('wishlists', ['user_id' => $user->id]);
});

test('user cannot clear another user wishlists — returns 500 (0 rows deleted)', function () {
    $user1 = wishlistCustomer();
    $user2 = wishlistCustomer();
    $vendor = wishlistVendor();
    $category = wishlistCategory();
    $product = wishlistProduct($vendor, $category);
    $wishlist = createWishlistEntry($user1, $product);

    $response = $this->actingAs($user2, 'sanctum')->deleteJson('/api/v1/wishlist/bulk-delete', [
        'ids' => [$wishlist->id],
    ]);

    $response->assertStatus(500)
        ->assertJsonPaths([
            'message' => ApiMessages::AN_ERROR_OCCURRED,
            'type' => ApiMessages::ERROR,
        ]);

    $this->assertDatabaseHas('wishlists', ['id' => $wishlist->id]);
});

test('bulk delete with empty ids array returns 500 (WishlistService rejects empty array)', function () {
    $user = wishlistCustomer();

    $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/v1/wishlist/bulk-delete', [
        'ids' => [],
    ]);

    $response->assertStatus(500);
});

test('guest cannot bulk delete wishlists', function () {
    $response = $this->deleteJson('/api/v1/wishlist/bulk-delete', [
        'ids' => [1, 2],
    ]);

    $response->assertUnauthorized();
});
