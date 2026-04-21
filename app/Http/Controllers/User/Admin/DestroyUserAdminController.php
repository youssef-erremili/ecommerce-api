<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class DestroyUserAdminController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(User $user, UserService $service): JsonResponse
    {
        try {
            $this->authorize('delete', $user);
            $service->destroy($user);

            return ApiResponse::success(ApiMessages::ACTION_COMPLETED);

        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
