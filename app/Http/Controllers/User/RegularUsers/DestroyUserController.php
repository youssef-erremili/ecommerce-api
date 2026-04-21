<?php

namespace App\Http\Controllers\User\RegularUsers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DestroyUserController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Request $request, User $user, UserService $service): JsonResponse
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
