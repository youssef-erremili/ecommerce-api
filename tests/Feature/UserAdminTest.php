<?php

use App\Enums\AccountType;
use App\Models\User;
use App\Support\ApiMessages;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function admin(): User
{
    return User::factory()->createQuietly([
        'account_type' => AccountType::ADMIN,
        'is_active' => true,
    ]);
}

function nonAdmin(AccountType $type = AccountType::CUSTOMER): User
{
    return User::factory()->createQuietly([
        'account_type' => $type,
    ]);
}

test('admin can list all non-admin users', function () {
    $admin = admin();
    User::factory()->count(3)->create([
        'account_type' => AccountType::CUSTOMER,
    ]);

    $response = $this->actingAs($admin, 'sanctum')->getJson('/api/v1/account/admin/users');

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'users',
                'pagination',
            ],
        ])
        ->assertJsonPaths([
            'message' => ApiMessages::ACTION_COMPLETED,
            'type' => ApiMessages::SUCCESS,
        ]);

    expect($response->json('data.users'))->toHaveCount(3);
});

test('admin list users returns error when no non-admin users exist', function () {
    $admin = admin();

    $response = $this->actingAs($admin, 'sanctum')->getJson('/api/v1/account/admin/users');

    $response->assertStatus(500)
        ->assertJsonPaths([
            'message' => ApiMessages::AN_ERROR_OCCURRED,
            'type' => ApiMessages::ERROR,
        ]);
});

test('customer cannot access the admin users list', function () {
    $customer = nonAdmin();

    $response = $this->actingAs($customer, 'sanctum')
        ->getJson('/api/v1/account/admin/users');

    $response->assertForbidden()
        ->assertJsonPaths([
            'type' => ApiMessages::ERROR,
        ]);
});

test('vendor cannot access the admin users list', function () {
    $vendor = nonAdmin(AccountType::VENDOR);

    $response = $this->actingAs($vendor, 'sanctum')->getJson('/api/v1/account/admin/users');

    $response->assertForbidden();
});

test('guest cannot access the admin users list', function () {
    $response = $this->getJson('/api/v1/account/admin/users');

    $response->assertUnauthorized();
});

test('admin can edit any user', function () {
    $admin = admin();
    $user = nonAdmin();

    $response = $this->actingAs($admin, 'sanctum')
        ->patchJson("/api/v1/account/admin/edit/$user->id", [
            'first_name' => 'AdminEdited',
            'last_name' => 'LastName',
            'email' => 'adminedited@example.com',
            'physical_address' => '99 Admin Ave',
            'phone_number' => '0987654321',
        ]);

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'user',
            ],
        ])
        ->assertJsonPaths([
            'message' => ApiMessages::ACTION_COMPLETED,
            'type' => ApiMessages::SUCCESS,
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'first_name' => 'AdminEdited',
        'email' => 'adminedited@example.com',
    ]);
});

test('customer cannot use admin edit endpoint', function () {
    $customer = nonAdmin();
    $user = nonAdmin();

    $response = $this->actingAs($customer, 'sanctum')->patchJson("/api/v1/account/admin/edit/$user->id", [
        'first_name' => 'Hacker',
        'last_name' => 'Attack',
        'email' => 'unique-hacker@example.com',
        'physical_address' => '1 Evil Lane',
        'phone_number' => '0000000001',
    ]);

    $response->assertForbidden();

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
        'first_name' => 'Hacker',
    ]);
});

test('admin edit user fails with missing required fields', function () {
    $admin = admin();
    $user = nonAdmin();

    $response = $this->actingAs($admin, 'sanctum')->patchJson("/api/v1/account/admin/edit/$user->id", []);

    $response->assertUnprocessable()
        ->assertJsonPath('type', ApiMessages::ERROR);
});

test('admin edit fails with duplicate email', function () {
    $admin = admin();
    $user = nonAdmin();
    $another = nonAdmin();

    $response = $this->actingAs($admin, 'sanctum')->patchJson("/api/v1/account/admin/edit/$user->id", [
        'first_name' => 'Test',
        'last_name' => 'Name',
        'email' => $another->email,
        'physical_address' => '1 Test St',
        'phone_number' => '1111111111',
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('type', ApiMessages::ERROR);
});

test('guest cannot use admin edit endpoint', function () {
    $user = nonAdmin();

    $response = $this->patchJson("/api/v1/account/admin/edit/$user->id", [
        'first_name' => 'Ghost',
    ]);

    $response->assertUnauthorized();
});

test('admin can delete another user', function () {
    $admin = admin();
    $user = nonAdmin();

    $response = $this->actingAs($admin, 'sanctum')->deleteJson("/api/v1/account/admin/delete/$user->id");

    $response->assertOk()
        ->assertJsonPaths([
            'message' => ApiMessages::ACTION_COMPLETED,
            'type' => ApiMessages::SUCCESS,
        ]);

    $this->assertSoftDeleted('users', [
        'id' => $user->id,
    ]);
});

test('admin cannot delete their own account — returns 500', function () {
    $admin = admin();

    $response = $this->actingAs($admin, 'sanctum')->deleteJson("/api/v1/account/admin/delete/$admin->id");

    $response->assertStatus(500)
        ->assertJsonPaths([
            'message' => ApiMessages::ADMIN_ACTION_RESTRICTED,
            'type' => ApiMessages::ERROR,
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $admin->id,
    ]);
});

test('customer cannot delete a user via admin endpoint', function () {
    $customer = nonAdmin();
    $user = nonAdmin();

    $response = $this->actingAs($customer, 'sanctum')->deleteJson("/api/v1/account/admin/delete/$user->id");

    $response->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
    ]);
});

test('vendor cannot delete a user via admin endpoint', function () {
    $vendor = nonAdmin(AccountType::VENDOR);
    $user = nonAdmin();

    $response = $this->actingAs($vendor, 'sanctum')->deleteJson("/api/v1/account/admin/delete/$user->id");

    $response->assertForbidden();
});

test('guest cannot delete a user via admin endpoint', function () {
    $user = nonAdmin();

    $response = $this->deleteJson("/api/v1/account/admin/delete/$user->id");

    $response->assertUnauthorized();
});

test('deleting a non-existent user via admin endpoint returns 404', function () {
    $admin = admin();

    $response = $this->actingAs($admin, 'sanctum')->deleteJson('/api/v1/account/admin/delete/8745');

    $response->assertNotFound();
});

test('admin can upgrade a customer account to vendor', function () {
    $admin = admin();
    $customer = nonAdmin();

    $response = $this->actingAs($admin, 'sanctum')->patchJson("/api/v1/account/admin/upgrade/$customer->id");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'user',
            ],
        ])
        ->assertJsonPaths([
            'message' => ApiMessages::ACTION_COMPLETED,
            'type' => ApiMessages::SUCCESS,
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $customer->id,
        'account_type' => AccountType::VENDOR,
    ]);
});

test('admin cannot upgrade a user who is already a vendor', function () {
    $admin = admin();
    $vendor = nonAdmin(AccountType::VENDOR);

    $response = $this->actingAs($admin, 'sanctum')->patchJson("/api/v1/account/admin/upgrade/$vendor->id");

    $response->assertStatus(500)
        ->assertJsonPaths([
            'message' => ApiMessages::ACCOUNT_ALREADY_VENDOR,
            'type' => ApiMessages::ERROR,
        ]);
});

test('admin cannot upgrade their own account', function () {
    $admin = admin();

    $response = $this->actingAs($admin, 'sanctum')->patchJson("/api/v1/account/admin/upgrade/$admin->id");

    $response->assertStatus(500)
        ->assertJsonPaths([
            'message' => ApiMessages::ADMIN_ACTION_RESTRICTED,
            'type' => ApiMessages::ERROR,
        ]);
});

test('customer cannot upgrade account types', function () {
    $customer = nonAdmin();
    $user = nonAdmin();

    $response = $this->actingAs($customer, 'sanctum')->patchJson("/api/v1/account/admin/upgrade/$user->id");

    $response->assertForbidden();
});

test('guest cannot upgrade account types', function () {
    $user = nonAdmin();

    $response = $this->patchJson("/api/v1/account/admin/upgrade/$user->id");

    $response->assertUnauthorized();
});

test('upgrading a non-existent user returns 500 with user not found message', function () {
    $admin = admin();

    $response = $this->actingAs($admin, 'sanctum')->patchJson('/api/v1/account/admin/upgrade/534');

    $response->assertStatus(500)
        ->assertJsonPaths([
            'message' => ApiMessages::USER_NOT_FOUND,
            'type' => ApiMessages::ERROR,
        ]);
});
