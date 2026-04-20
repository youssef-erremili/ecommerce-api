<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use App\Traits\Paginator;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ListUsersController extends Controller
{
    use AuthorizesRequests, Paginator;

    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke(UserService $service)
    {
        try {
            $this->authorize('viewAny', User::class);

            $users = $service->paginate();

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'users' => UserResource::collection($users)->resolve(),
                    'pagination' => $this->paginateResource($users),
                ]
            );
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
