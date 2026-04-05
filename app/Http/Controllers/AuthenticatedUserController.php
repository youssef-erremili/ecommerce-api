<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class AuthenticatedUserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponse::error();
        }

        return ApiResponse::success(
            ApiMessages::USER_FETCHED,
            new UserResource($user)
        );
    }
}
