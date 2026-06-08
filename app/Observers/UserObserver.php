<?php

namespace App\Observers;

use App\Enums\AccountType;
use App\Models\User;
use App\Traits\GenerateSlug;

class UserObserver
{
    use GenerateSlug;

    public function creating(User $user): void
    {
        $fullName = $user->first_name.'-'.$user->last_name;
        $user->account_type = AccountType::CUSTOMER->value;
        $user->slug = $this->slug($fullName, User::class);
        $user->profile = asset('images/default-user.png');
    }
}
