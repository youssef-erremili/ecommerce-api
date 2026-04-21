<?php

namespace App\Http\Controllers\User\RegularUsers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\UserService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;

class ResetPasswordController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ResetPasswordRequest $request, UserService $service)
    {
        try {
            $newPassword = $request->validated('password');
            $service->resetPassword(auth()->user(), $newPassword);

            return ApiResponse::success(ApiMessages::ACTION_COMPLETED);
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
