<?php

namespace App\Contracts\Services;

use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Pagination\LengthAwarePaginator;

interface WishlistServiceInterface
{
    public function getUserWishlist(): LengthAwarePaginator;

    public function add(User $user, int $productId): bool;

    public function remove(User $user, int $productId): bool;

    public function clear(User $user): bool;
}
