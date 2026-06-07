<?php

use App\Models\User;
use App\Support\ApiMessages;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function credentials(): array
{
    return [
        'first_name' => 'developer',
        'last_name' => 'full-stuck',
        'phone_number' => '0090809475',
        'email' => 'youssef@gmail.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];
}

// Login
test('log in with valid credentials', function () {

    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => ApiMessages::AUTH_SUCCESSFUL_LOGIN,
            'type' => ApiMessages::SUCCESS,
        ])->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'token',
                'user',
            ],
        ]);
    expect($response->json('data.user.id'))
        ->toBe($user->id)
        ->and($response->json('data.user.email'))
        ->toBe($user->email)
        ->and($response->json('data.user.profile'))
        ->toBeUrl()
        ->and($response->json('data.token'))
        ->not->toBeEmpty();

    $this->assertAuthenticated();
});

test('failed login with wrong password', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password1234',
    ]);

    $response->assertJsonStructure([
        'message',
        'type',
    ])->assertBadRequest()
        ->assertJson([
            'message' => ApiMessages::AN_ERROR_OCCURRED,
            'type' => ApiMessages::ERROR,
        ])->assertJsonMissingPaths([
            'data',
            'data.token',
            'data.user',
        ]);

    expect($response->json('data'))->toBeEmpty();

    $this->assertDatabaseMissing('users', [
        'password' => bcrypt('password1234'),
    ]);

    $this->assertGuest();
});

test('failed login with wrong email address', function () {
    User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'yousseferremili.dev@gmail.com',
        'password' => 'password123',
    ]);

    $response->assertJsonStructure([
        'message',
        'type',
    ])->assertJson([
        'type' => ApiMessages::ERROR,
    ])->assertJsonMissingPaths([
        'data',
        'data.token',
        'data.user',
    ]);

    expect($response->json('data'))->toBeEmpty();

    $this->assertDatabaseMissing('users', [
        'email' => 'yousseferremili.dev@gmail.com',
    ]);

    $this->assertGuest();
});

// Register
test('a guest can register a new account', function () {

    $response = $this->postJson('/api/v1/auth/register', credentials());

    $response
        ->assertCreated()
        ->assertJsonStructure([
            'message',
            'type',
            'data' => [
                'token',
                'user',
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'email' => credentials()['email'],
        'phone_number' => credentials()['phone_number'],
        'account_type' => 'customer',
    ]);

    expect($response->json('data.token'))
        ->not()
        ->toBeEmpty()
        ->and($response->json('data.user'))
        ->not()
        ->toBeEmpty();
});

test('a user cannot register with an existing email', function () {
    $user = User::factory()->create([
        'email' => 'youssef@gmail.com',
        'password' => bcrypt('password'),
    ]);

    $credentials = credentials();
    $credentials['email'] = $user->email;

    $response = $this->postJson('/api/v1/auth/register', $credentials);

    $response->assertUnprocessable()
        ->assertJsonStructure([
            'message',
            'type',
        ]);

    $this->assertDatabaseHas('users', [
        'email' => $user->email,
        'password' => $user->password,
    ]);

    $this->assertDatabaseCount('users', 1);
});

// log-out
test('check if user can log-out successfully', function () {
    $user = User::factory()->create();
    $token = $user->createToken('authToken')->plainTextToken;

    $response = $this->postJson('/api/v1/auth/logout', [], [
        'Authorization' => 'Bearer '.$token,
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'type',
            'data',
        ])
        ->assertJsonPath('message', ApiMessages::AUTH_SUCCESSFUL_LOGOUT)
        ->assertJsonPath('type', ApiMessages::SUCCESS)
        ->assertJsonPath('data', []);

});

test('user cannot logout without token', function () {

    $response = $this->postJson('/api/v1/auth/logout');

    $response->assertUnauthorized();
});

test('user cannot logout with invalid token', function () {

    $response = $this->postJson('/api/v1/auth/logout', [], [
        'Authorization' => 'Bearer invalid-token',
    ]);

    $response->assertUnauthorized();
});
