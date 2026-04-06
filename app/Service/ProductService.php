<?php

namespace App\Service;

use App\Models\Product;
use App\Models\User;

class ProductService
{
    /**
     * Create a new class instance.
     */
    public function create(User $user, array $data)
    {
        // 1 handle image upload

        // 2 attach category using name

        // 3 trigger events and jobs in background

        // 4 store product in db
        return $user->products()->create($data)->load(['user', 'category']);
    }
}
