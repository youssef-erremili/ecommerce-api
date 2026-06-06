<?php

use App\Models\User;
use App\Support\ApiMessages;

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
