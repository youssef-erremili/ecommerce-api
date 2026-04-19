<?php

namespace App\Contracts\Services;

use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Pagination\LengthAwarePaginator;

interface WishlistServiceInterface
{
    public function getUserWishlist(): LengthAwarePaginator;

    public function add(User $user, int $productId): bool;

    public function remove(Wishlist $wishlist): bool;

    public function clear(array $ids): bool;
}
