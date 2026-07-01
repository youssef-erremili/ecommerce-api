<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Mail\OnBoardingMail;
use App\Models\User;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        if (! $user) {
            return ApiResponse::error();
        }

        $token = $user->createToken('authToken')->plainTextToken;

        Mail::to($user->email)->queue(new OnBoardingMail(
            $user->first_name,
            $user->last_name,
            $user->email,
        ));

        return ApiResponse::success(
            ApiMessages::AUTH_SUCCESSFUL_REGISTRATION,
            [
                'token' => $token,
                'user' => UserResource::make($user),
            ],
            Response::HTTP_CREATED
        );
    }
}
