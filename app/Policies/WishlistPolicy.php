<?php

namespace App\Policies;

use App\Enums\AccountType;
use App\Models\User;
use App\Models\Wishlist;

class WishlistPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->account_type !== AccountType::ADMIN;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Wishlist $wishlist): bool
    {
        return $user->id === $wishlist->user_id;
    }

    public function deleteAny(User $user): bool
    {
        return auth()->check();
    }
}
