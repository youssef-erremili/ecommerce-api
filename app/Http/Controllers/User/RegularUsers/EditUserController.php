<?php

namespace App\Http\Controllers\User\RegularUsers;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegularUsers\EditUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class EditUserController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(EditUserRequest $request, User $user, UserService $service): JsonResponse
    {
        try {
            $this->authorize('updateRegular', $user);

            $data = $request->validated();
            $updatedUser = $service->update($user, $data);

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'user' => UserResource::make($updatedUser),
                ]
            );
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
