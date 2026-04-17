<?php

namespace App\Http\Controllers\User;

use App\Enums\AccountType;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use App\Traits\Paginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class ListUsersController extends Controller
{
    use AuthorizesRequests, Paginator;

    /**
     * Handle the incoming request.
     */
    public function __invoke(User $user)
    {
        $this->authorize('view', $user);

        // fetch all user
        $users = DB::table('users')
            ->whereNot('account_type', AccountType::ADMIN)
            ->paginate();

        if (! $users) {
            return ApiResponse::error();
        }

        return ApiResponse::success(
            ApiMessages::ACTION_COMPLETED,
            [
                'pagination' => $this->paginateResource($users),
                UserResource::collection($users)->resolve(),
            ]
        );
    }
}
