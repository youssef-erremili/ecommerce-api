<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $isUserLoggedIn = Auth::attempt($credentials);
        if (! $isUserLoggedIn) {
            return response()->json([
                'error' => 'Invalid credentials',
                'type' => 'error',
            ], 200);
        }

        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'logged successfully',
            'type' => 'success',
            [
                'token' => $token,
                'user' => UserResource::make($user),
            ],
        ], 200);

    }
}
