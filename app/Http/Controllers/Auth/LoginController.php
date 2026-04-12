<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $isUserLoggedIn = Auth::attempt($credentials);

        if (! $isUserLoggedIn) {
            return ApiResponse::error();
        }

        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;

        return ApiResponse::success(
            ApiMessages::AUTH_SUCCESSFUL_LOGIN,
            [
                'token' => $token,
                UserResource::collection($user)->resolve(),
            ],
        );

    }
}
