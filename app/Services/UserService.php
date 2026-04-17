<?php

namespace App\Services;

use App\Contracts\Services\UserServiceInterface;
use App\Enums\AccountType;
use App\Models\User;
use App\Support\ApiMessages;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserService implements UserServiceInterface
{
    /**
     * Create a new class instance.
     *
     * @throws Exception
     */
    private Request $request;

    public function __construct(Request $request)
    {
        return $this->request = $request;
    }

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        $holder = DB::table('users')
            ->whereNot('account_type', AccountType::ADMIN)
            ->paginate($perPage);

        if ($holder->isEmpty()) {
            throw new Exception(ApiMessages::AN_ERROR_OCCURRED);
        }

        return $holder;
    }

    /**
     * @throws Exception
     */
    public function getAuthUser(): User|JsonResponse|Authenticatable
    {
        $user = $this->request->user();

        if (! $user) {
            throw new Exception(ApiMessages::AUTH_UNAUTHENTICATED);
        }

        return $user;
    }

    /**
     * @throws Exception
     */
    public function upgradeUserAccountType(int|string $id): User
    {
        // prevent admin from execute action on its account
        if (auth()->user()->id === (int) $id) {
            throw new Exception(ApiMessages::ADMIN_ACTION_RESTRICTED);
        }

        $holder = User::find($id);

        if (! $holder) {
            throw new Exception(ApiMessages::USER_NOT_FOUND);
        }

        // check if user is already a vendor
        if ($holder->account_type === AccountType::VENDOR) {
            throw new Exception(ApiMessages::ACCOUNT_ALREADY_VENDOR);
        }

        $holder->update([
            'account_type' => AccountType::VENDOR,
        ]);

        return $holder;
    }
}
