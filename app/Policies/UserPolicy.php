<?php

namespace App\Policies;

use App\Enums\AccountType;
use App\Models\User;
use App\Support\ApiMessages;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can update the account type of users.
     */
    public function update(User $user): Response
    {
        return $user->account_type === AccountType::ADMIN
            ? Response::allow()
            : Response::deny(ApiMessages::ADMIN_ACTION_AllOWED, 403);
    }

    public function view(User $user): bool
    {
        return $user->account_type === AccountType::ADMIN;
    }
}
