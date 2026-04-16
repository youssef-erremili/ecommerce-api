<?php

namespace App\Http\Controllers\User;

use App\Enums\AccountType;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AccountTypeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    use AuthorizesRequests;

    public function __invoke(User $user, int|string $id)
    {
        $this->authorize('update', $user);

        // prevent admin from execute action on its account
        if (auth()->user()->id === (int) $id) {
            return ApiResponse::error(ApiMessages::ADMIN_ACTION_RESTRICTED);
        }

        $account = User::find($id);

        if (! $account) {
            return ApiResponse::error(ApiMessages::USER_NOT_FOUND);
        }

        // check if user is already a vendor
        if ($account->account_type === AccountType::VENDOR) {
            return ApiResponse::error(ApiMessages::ACCOUNT_ALREADY_VENDOR);
        }

        $account->update([
            'account_type' => AccountType::VENDOR,
        ]);

        return ApiResponse::success(
            ApiMessages::ACTION_COMPLETED,
            [UserResource::make($account)]
        );
    }
}
