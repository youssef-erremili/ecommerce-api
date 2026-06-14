<?php

use App\Enums\AccountType;
use App\Models\User;
use App\Support\ApiMessages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function makeRegularUser(AccountType $accountType = AccountType::CUSTOMER): User
{
    return User::factory()->create([
        'account_type' => $accountType,
    ]);
}

test('authenticated user can retrieve their own profile', function () {
    $user = makeRegularUser();

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/account/me');

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'user' => [
                    'id',
                    'full_name',
                    'email',
                    'slug',
                    'profile',
                    'phone_number',
                ],
            ],
        ])
        ->assertJsonPaths([
            'type' => ApiMessages::SUCCESS,
            'data.user.id' => $user->id,
            'data.user.email' => $user->email,
        ]);

    expect($response->json('data.user.profile'))->toBeUrl();
});

test('guest cannot access their profile', function () {
    $response = $this->getJson('/api/v1/account/me');

    $response->assertUnauthorized();
});

test('user can update their profile image (supabase storage)', function () {
    Storage::fake('supabase');

    $user = makeRegularUser();

    $response = $this->actingAs($user, 'sanctum')
        ->patchJson('/api/v1/account/profile-image', [
            'profile_image' => UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg'),
        ]);

    $response->assertOk()
        ->assertJsonPaths([
            'type' => ApiMessages::SUCCESS,
        ])
        ->assertJsonStructure([
            'message',
            'type',
            'data',
        ]);
});

test('profile image update fails when no file is provided', function () {
    $user = makeRegularUser();

    $response = $this->actingAs($user, 'sanctum')->patchJson('/api/v1/account/profile-image', []);

    $response->assertUnprocessable()
        ->assertJsonPath('type', ApiMessages::ERROR);
});

test('profile image update fails with non-image mime type', function () {
    $user = makeRegularUser();

    $response = $this->actingAs($user, 'sanctum')->patchJson('/api/v1/account/profile-image', [
        'profile_image' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('type', ApiMessages::ERROR);
});

test('guest cannot update profile image', function () {
    $response = $this->patchJson('/api/v1/account/profile-image', [
        'profile_image' => UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg'),
    ]);

    $response->assertUnauthorized();
});

test('user can edit their own profile details', function () {
    $user = makeRegularUser();

    $response = $this->actingAs($user, 'sanctum')->patchJson("/api/v1/account/edit/$user->id", [
        'first_name' => 'Updated',
        'last_name' => 'Name',
        'email' => 'updated@example.com',
        'physical_address' => '456 New Street',
        'phone_number' => '0123456789',
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
        'first_name' => 'Updated',
        'last_name' => 'Name',
        'email' => 'updated@example.com',
    ]);
});

test('user cannot edit another user profile — validation fails before policy (422)', function () {
    $user = makeRegularUser();
    $another = makeRegularUser();

    $response = $this->actingAs($user, 'sanctum')->patchJson("/api/v1/account/edit/$another->id", [
        'first_name' => 'Hacker',
    ]);

    $response->assertUnprocessable();

    $this->assertDatabaseMissing('users', [
        'id' => $another->id,
        'first_name' => 'Hacker',
    ]);
});

test('user sending a full valid payload to edit another account is denied by policy', function () {
    $user = makeRegularUser();
    $another = makeRegularUser();

    $response = $this->actingAs($user, 'sanctum')->patchJson("/api/v1/account/edit/$another->id", [
        'first_name' => 'Hacker',
        'last_name' => 'Attack',
        'email' => 'unique-hacker@example.com',
        'physical_address' => '1 Evil Lane',
        'phone_number' => '0000000001',
    ]);

    $response->assertStatus(500)
        ->assertJsonPath('type', ApiMessages::ERROR);

    $this->assertDatabaseMissing('users', [
        'id' => $another->id,
        'first_name' => 'Hacker',
    ]);
});

test('edit user fails with missing required fields', function () {
    $user = makeRegularUser();

    $response = $this->actingAs($user, 'sanctum')->patchJson("/api/v1/account/edit/$user->id", []);

    $response->assertUnprocessable()
        ->assertJsonPath('type', ApiMessages::ERROR);
});

test('edit user fails with a duplicate email', function () {
    $user = makeRegularUser();
    $another = makeRegularUser();

    $response = $this->actingAs($user, 'sanctum')->patchJson("/api/v1/account/edit/$user->id", [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => $another->email,
        'physical_address' => '123 St',
        'phone_number' => '0000000000',
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('type', ApiMessages::ERROR);
});

test('guest cannot edit user details', function () {
    $user = makeRegularUser();

    $response = $this->patchJson("/api/v1/account/edit/$user->id", [
        'first_name' => 'Ghost',
    ]);

    $response->assertUnauthorized();
});

test('user can reset their own password', function () {
    $user = makeRegularUser();

    $response = $this->actingAs($user, 'sanctum')->patchJson('/api/v1/account/reset-password', [
        'current_password' => 'password123',
        'password' => 'newSecurePass!99',
        'password_confirmation' => 'newSecurePass!99',
    ]);

    $response->assertOk()
        ->assertJsonPaths([
            'message' => ApiMessages::ACTION_COMPLETED,
            'type' => ApiMessages::SUCCESS,
        ]);
});

test('reset password fails when confirmation does not match', function () {
    $user = makeRegularUser();

    $response = $this->actingAs($user, 'sanctum')->patchJson('/api/v1/account/reset-password', [
        'current_password' => 'password123',
        'password' => 'newPassword!1',
        'password_confirmation' => 'doesNotMatch!',
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('type', ApiMessages::ERROR);
});

test('reset password fails when new password is missing', function () {
    $user = makeRegularUser();

    $response = $this->actingAs($user, 'sanctum')->patchJson('/api/v1/account/reset-password', []);

    $response->assertUnprocessable()
        ->assertJsonPath('type', ApiMessages::ERROR);
});

test('guest cannot reset password', function () {
    $response = $this->patchJson('/api/v1/account/reset-password', [
        'current_password' => 'password123',
        'password' => 'new',
        'password_confirmation' => 'new',
    ]);

    $response->assertUnauthorized();
});

test('user cannot delete themselves — returns 400 (admin action restricted)', function () {
    $user = makeRegularUser();

    $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/account/delete/$user->id");

    $response->assertStatus(400);

    $this->assertDatabaseHas('users', ['id' => $user->id]);
});

test('user cannot delete another user account — returns 400 (exception caught)', function () {
    $user = makeRegularUser();
    $another = makeRegularUser();

    $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/account/delete/$another->id");

    $response->assertStatus(400);

    $this->assertDatabaseHas('users', [
        'id' => $another->id,
    ]);
});

test('guest cannot delete any user account', function () {
    $user = makeRegularUser();

    $response = $this->deleteJson("/api/v1/account/delete/$user->id");

    $response->assertUnauthorized();
});
