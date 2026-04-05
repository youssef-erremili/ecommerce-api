<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\ApiMessages;
use App\Support\ApiResponse;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        if (! $user) {
            return ApiResponse::error();
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return ApiResponse::success(
            ApiMessages::USER_CREATED,
            [
                'token' => $token,
                'user' => UserResource::make($user),
            ],
        );
    }
}
