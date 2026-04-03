<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request)
    {
        $user = User::create($request->validated());
        if (! $user) {

            return response()->json([
                'error' => 'Something went wrong',
                'type' => 'error',
            ], 201);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'type' => 'success',
            [
                'token' => $token,
                'user' => UserResource::make($user),
            ],
        ], 201);
    }
}
