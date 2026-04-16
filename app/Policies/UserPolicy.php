<?php

namespace App\Policies;

use App\Enums\AccountType;
use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can update the account type of users.
     */
    public function update(User $user): bool
    {
        return $user->account_type === AccountType::ADMIN;
    }
}
