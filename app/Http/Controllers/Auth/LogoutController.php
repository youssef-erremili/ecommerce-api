<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponse::error(ApiMessages::USER_NOT_FOUND);
        }

        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(
            ApiMessages::USER_LOGGED_OUT
        );
    }
}
