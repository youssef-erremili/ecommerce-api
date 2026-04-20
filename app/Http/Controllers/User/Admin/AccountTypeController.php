<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class AccountTypeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    use AuthorizesRequests;

    /**
     * @throws Exception
     */
    public function __invoke(UserService $service, int|string $id): JsonResponse
    {
        try {
            $this->authorize('update', User::class);
            $account = $service->upgradeUserAccountType($id);

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                ['user' => UserResource::make($account)]
            );

        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
