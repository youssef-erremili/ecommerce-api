<?php

namespace App\Observers;

use App\Enums\AccountType;
use App\Models\User;

class UserObserver
{
    public function creating(User $user): void
    {
        $user->account_type = AccountType::CUSTOMER->value;
    }
}
