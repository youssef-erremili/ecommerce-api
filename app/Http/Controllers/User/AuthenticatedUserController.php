<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;

class AuthenticatedUserController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke(UserService $service)
    {
        try {
            $user = $service->getAuthUser();

            return ApiResponse::success(
                ApiMessages::USER_FETCHED,
                [
                    'user' => UserResource::make($user),
                ]
            );
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
