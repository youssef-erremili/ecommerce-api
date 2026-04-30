<?php

namespace App\Http\Controllers\User\RegularUsers;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Http\Request;

class UpdateUserProfileController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke(Request $request, UserService $service)
    {
        try {
            $profileImage = $request->validate([
                'profile_image' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/jpg', 'max:2048'],
            ]);
            [$processUploadImage] = $service->updateUserProfileImage($profileImage);

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
