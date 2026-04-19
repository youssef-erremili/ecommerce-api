<?php

namespace App\Services;

use App\Contracts\Services\WishlistServiceInterface;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use App\Support\ApiMessages;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class WishlistService implements WishlistServiceInterface
{
    /**
     * Create a new class instance.
     *
     * @throws Exception
     */
    public function getUserWishlist(): LengthAwarePaginator
    {
        $holder = Wishlist::with('product')
            ->where('user_id', auth()->user()->id)
            ->paginate();

        if (! $holder) {
            throw new Exception(ApiMessages::AN_ERROR_OCCURRED);
        }

        if ($holder->isEmpty()) {
            throw new Exception(ApiMessages::WISHLIST_EMPTY);
        }

        return $holder;
    }

    /**
     * @throws Exception
     */
    public function add(User $user, int $productId): bool
    {
        $holder = Product::find($productId);

        if (! $holder) {
            throw new Exception(ApiMessages::PRODUCT_NOT_FOUND);
        }

        $isWishListAlreadyExists = Wishlist::query()
            ->where('user_id', $user->id)
            ->where('product_id', $holder->id)
            ->exists();

        if ($isWishListAlreadyExists) {
            throw new Exception(ApiMessages::WISH_ALREADY_EXISTS);
        }

        $user->wishlists()->createQuietly(
            [
                'product_id' => $productId,
            ]
        );

        return true;
    }

    public function remove(Wishlist $wishlist): bool
    {
        return $wishlist->delete();
    }

    /**
     * @throws Exception
     */
    public function clear(array $ids): bool
    {
        if (! $ids) {
            throw new Exception('Invalid IDs.');
        }

        $holder = Wishlist::query()
            ->whereIn('id', $ids)
            ->where('user_id', auth()->user()->id)
            ->delete();

        if (! $holder) {
            throw new Exception(ApiMessages::AN_ERROR_OCCURRED);
        }

        return true;
    }
}
