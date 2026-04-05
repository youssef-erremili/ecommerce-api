<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Create a new class instance.
     */
    public static function success(string $message, $data = []): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'type' => ApiMessages::SUCCESS,
            'data' => $data,
        ]);
    }

    public static function error(string $message = ApiMessages::AN_ERROR_OCCURRED): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'type' => ApiMessages::ERROR,
        ]);
    }
}
