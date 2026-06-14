<?php

use App\Enums\AccountType;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Support\ApiMessages;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->uses(RefreshDatabase::class);

test('create a new category as admin', function () {
    $user = User::factory()->createQuietly([
        'account_type' => AccountType::ADMIN,
    ]);

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/category/create', [
        'category_name' => 'Jewellery',
        'description' => 'A comprehensive collection of on-demand computing',
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'category' => [
                    'category_name',
                    'slug',
                    'description',
                    'sort_order',
                    'is_active',
                ],
            ],
        ])->assertJsonPath('data.category.category_name', 'Jewellery');

    $this->assertDatabaseHas('categories', [
        'category_name' => 'Jewellery',
        'description' => 'A comprehensive collection of on-demand computing',
    ]);

    $this->assertDatabaseCount('categories', 1);
    expect($response->json('data.category.is_active'))->toBeTrue();
});

test('create a new category as any user expect admin', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/category/create', [
        'category_name' => 'Jewellery',
        'description' => 'A comprehensive collection of on-demand computing',
    ]);

    $response->assertForbidden()
        ->assertJsonStructure([
            'message',
            'type',
        ])
        ->assertJsonPaths(
            [
                'message' => 'This action is unauthorized.',
                'type' => ApiMessages::ERROR,
            ]);

    $this->assertDatabaseCount('categories', 0);
    $this->assertDatabaseMissing('categories', [
        'category_name' => 'Jewellery',
        'description' => 'A comprehensive collection of on-demand computing',
    ]);
});

test('create a new category as guest user', function () {
    $response = $this->postJson('/api/v1/category/create', [
        'category_name' => 'Jewellery',
        'description' => 'A comprehensive collection of on-demand computing',
    ]);

    $response->assertUnauthorized()
        ->assertJsonStructure([
            'message',
            'type',
        ])
        ->assertJsonPaths(
            [
                'message' => ApiMessages::AUTH_UNAUTHORIZED,
                'type' => ApiMessages::ERROR,
            ]);

    $this->assertDatabaseCount('categories', 0);
    $this->assertDatabaseMissing('categories', [
        'category_name' => 'Jewellery',
        'description' => 'A comprehensive collection of on-demand computing',
    ]);
});

test('delete a existing category as admin', function () {
    $user = User::factory()->createQuietly([
        'account_type' => AccountType::ADMIN,
    ]);
    $category = Category::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/category/$category->id/delete");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data',
        ])
        ->assertJsonPaths([
            'message' => ApiMessages::ACTION_COMPLETED,
            'type' => ApiMessages::SUCCESS,
            'data' => [],
        ]);

    expect($response->json('data'))
        ->toBeArray()
        ->toBeEmpty();

    $this->assertDatabaseCount('categories', 0);
    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
        'category_name' => $category->category_name,
        'description' => $category->description,
    ]);
});

test('delete a category as any user expect admin', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/category/$category->id/delete");

    $response->assertForbidden()
        ->assertJsonStructure([
            'message',
            'type',
        ])
        ->assertJsonPaths([
            'message' => 'This action is unauthorized.',
            'type' => ApiMessages::ERROR,
        ]);

    $this->assertDatabaseCount('categories', 1);
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'category_name' => $category->category_name,
    ]);
});

test('delete a category as guest user', function () {
    $category = Category::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->deleteJson("/api/v1/category/$category->id/delete");

    $response->assertUnauthorized()
        ->assertJsonStructure([
            'message',
            'type',
        ])
        ->assertJsonPaths([
            'message' => ApiMessages::AUTH_UNAUTHORIZED,
            'type' => ApiMessages::ERROR,
        ]);

    $this->assertDatabaseCount('categories', 1);
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
    ]);
});

test('delete a category that has products fails', function () {
    $user = User::factory()->createQuietly([
        'account_type' => AccountType::ADMIN,
    ]);
    $vendor = User::factory()->createQuietly([
        'account_type' => AccountType::VENDOR,
    ]);
    $category = Category::factory()->create([
        'is_active' => true,
    ]);
    Product::factory()->create([
        'category_id' => $category->id,
        'user_id' => $vendor->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/category/$category->id/delete");

    $response->assertUnprocessable()
        ->assertJsonStructure([
            'message',
            'type',
        ])
        ->assertJsonPaths([
            'message' => ApiMessages::CANNOT_DELETE_CATEGORY,
            'type' => ApiMessages::ERROR,
        ]);

    $this->assertDatabaseCount('categories', 1);
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
    ]);
});

test('update a category as admin', function () {
    $user = User::factory()->createQuietly([
        'account_type' => AccountType::ADMIN,
    ]);
    $category = Category::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->actingAs($user, 'sanctum')->patchJson("/api/v1/category/$category->id/update", [
        'category_name' => 'Updated Category',
        'description' => 'Updated description for this category',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'category' => [
                    'category_name',
                    'slug',
                    'description',
                    'sort_order',
                    'is_active',
                ],
            ],
        ])
        ->assertJsonPaths([
            'message' => ApiMessages::ACTION_COMPLETED,
            'type' => ApiMessages::SUCCESS,
        ]);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'category_name' => 'Updated Category',
        'description' => 'Updated description for this category',
    ]);
});

test('update a category as any user expect admin', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->actingAs($user, 'sanctum')->patchJson("/api/v1/category/$category->id/update", [
        'category_name' => 'Updated Category',
        'description' => 'Updated description for this category',
    ]);

    $response->assertForbidden()
        ->assertJsonStructure([
            'message',
            'type',
        ])
        ->assertJsonPaths([
            'message' => 'This action is unauthorized.',
            'type' => ApiMessages::ERROR,
        ]);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'category_name' => $category->category_name,
    ]);
});

test('update a category as guest user', function () {
    $category = Category::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->patchJson("/api/v1/category/$category->id/update", [
        'category_name' => 'Updated Category',
        'description' => 'Updated description for this category',
    ]);

    $response->assertUnauthorized()
        ->assertJsonStructure([
            'message',
            'type',
        ])
        ->assertJsonPaths([
            'message' => ApiMessages::AUTH_UNAUTHORIZED,
            'type' => ApiMessages::ERROR,
        ]);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'category_name' => $category->category_name,
    ]);
});

test('update a category that has products fails', function () {
    $user = User::factory()->createQuietly([
        'account_type' => AccountType::ADMIN,
    ]);
    $vendor = User::factory()->createQuietly([
        'account_type' => AccountType::ADMIN,
    ]);
    $category = Category::factory()->create([
        'is_active' => true,
    ]);
    Product::factory()->create([
        'category_id' => $category->id,
        'user_id' => $vendor->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')->patchJson("/api/v1/category/$category->id/update", [
        'category_name' => 'Updated Category',
        'description' => 'Updated description for this category',
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure([
            'message',
            'type',
        ])
        ->assertJsonPaths([
            'message' => ApiMessages::CANNOT_UPDATE_CATEGORY,
            'type' => ApiMessages::ERROR,
        ]);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'category_name' => $category->category_name,
    ]);
});

test('toggle category status as admin', function () {
    $user = User::factory()->createQuietly([
        'account_type' => AccountType::ADMIN,
    ]);
    $category = Category::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->actingAs($user, 'sanctum')->patchJson("/api/v1/category/$category->id/toggle-status", [
        'is_active' => false,
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'category' => [
                    'category_name',
                    'slug',
                    'description',
                    'sort_order',
                    'is_active',
                ],
            ],
        ])
        ->assertJsonPaths([
            'message' => ApiMessages::ACTION_COMPLETED,
            'type' => ApiMessages::SUCCESS,
        ]);

    expect($response->json('data.category.is_active'))->toBeFalse();

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'is_active' => false,
    ]);
});

test('toggle category status with same status fails', function () {
    $user = User::factory()->createQuietly([
        'account_type' => AccountType::ADMIN,
    ]);
    $category = Category::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->actingAs($user, 'sanctum')->patchJson("/api/v1/category/$category->id/toggle-status", [
        'is_active' => true,
    ]);

    $response->assertBadRequest()
        ->assertJsonStructure([
            'message',
            'type',
        ])
        ->assertJsonPaths([
            'message' => ApiMessages::CANNOT_REUSE_STATUS,
            'type' => ApiMessages::ERROR,
        ]);
});

test('toggle category status as any user expect admin', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->actingAs($user, 'sanctum')->patchJson("/api/v1/category/$category->id/toggle-status", [
        'is_active' => false,
    ]);

    $response->assertForbidden()
        ->assertJsonStructure([
            'message',
            'type',
        ])
        ->assertJsonPaths([
            'message' => 'This action is unauthorized.',
            'type' => ApiMessages::ERROR,
        ]);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'is_active' => true,
    ]);
});

test('toggle category status as guest user', function () {
    $category = Category::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->patchJson("/api/v1/category/$category->id/toggle-status", [
        'is_active' => false,
    ]);

    $response->assertUnauthorized()
        ->assertJsonStructure([
            'message',
            'type',
        ])
        ->assertJsonPaths([
            'message' => ApiMessages::AUTH_UNAUTHORIZED,
            'type' => ApiMessages::ERROR,
        ]);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'is_active' => true,
    ]);
});

test('list categories as admin', function () {
    $user = User::factory()->createQuietly([
        'account_type' => AccountType::ADMIN,
    ]);
    Category::factory()->count(3)->create();

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/category/lists');

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'pagination',
                'categories',
            ],
        ])
        ->assertJsonPaths([
            'message' => ApiMessages::ACTION_COMPLETED,
            'type' => ApiMessages::SUCCESS,
        ]);

    expect($response->json('data.categories'))->toHaveCount(3);
});

test('list categories as any user expect admin', function () {
    $user = User::factory()->create();
    Category::factory()->count(3)->create();

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/category/lists');

    $response->assertForbidden()
        ->assertJsonStructure([
            'message',
            'type',
        ])
        ->assertJsonPaths([
            'message' => 'This action is unauthorized.',
            'type' => ApiMessages::ERROR,
        ]);
});

test('list categories as guest user', function () {
    Category::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/category/lists');

    $response->assertUnauthorized()
        ->assertJsonStructure([
            'message',
            'type',
        ])
        ->assertJsonPaths([
            'message' => ApiMessages::AUTH_UNAUTHORIZED,
            'type' => ApiMessages::ERROR,
        ]);
});

test('create category validation fails with missing fields', function () {
    $user = User::factory()->createQuietly([
        'account_type' => AccountType::ADMIN,
    ]);

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/category/create', []);

    $response->assertUnprocessable()
        ->assertJsonStructure([
            'message',
            'type',
        ])
        ->assertJsonPath('type', ApiMessages::ERROR);

    $this->assertDatabaseCount('categories', 0);
});

test('create category validation fails with duplicate name', function () {
    $user = User::factory()->createQuietly([
        'account_type' => AccountType::ADMIN,
    ]);
    Category::factory()->create([
        'category_name' => 'Jewellery',
    ]);

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/category/create', [
        'category_name' => 'Jewellery',
        'description' => 'A comprehensive collection of on-demand computing',
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure([
            'message',
            'type',
        ])
        ->assertJsonPath('type', ApiMessages::ERROR);

    $this->assertDatabaseCount('categories', 1);
});
