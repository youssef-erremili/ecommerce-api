<?php

namespace App\Http\Controllers\User\RegularUsers;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileImageRequest;
use App\Models\User;
use App\Services\UserService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;

class UpdateUserProfileController extends Controller
{
    /**
     * @param  User  $user
     *
     * @throws Exception
     */
    public function __invoke(UpdateProfileImageRequest $request, UserService $service, Authenticatable $user): JsonResponse
    {
        try {
            [$processUploadImage] = $service->updateUserProfileImage(
                $request->validated(),
                $user->getProfileImageDirectory()
            );

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'profile-image' => $processUploadImage,
                ]
            );

        } catch (Exception $exception) {
            return ApiResponse::error(
                $exception->getMessage(),
                $exception->getCode()
            );
        }
    }
}
