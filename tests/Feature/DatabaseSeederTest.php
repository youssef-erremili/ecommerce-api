<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->uses(RefreshDatabase::class);

test('run seeder populates correct amounts of data', function () {
    $this->seed();

    $this->assertDatabaseCount('users', 200);
    $this->assertDatabaseCount('categories', 20);
    $this->assertDatabaseCount('products', 1000);
    $this->assertDatabaseCount('carts', 100);
});
