<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Create a new class instance.
     */
    public static function success($message, $data = []): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'type' => ApiMessages::SUCCESS,
            'data' => $data,
        ]);
    }

    public static function error(): JsonResponse
    {
        return response()->json([
            'message' => ApiMessages::AN_ERROR_OCCURRED,
            'type' => ApiMessages::ERROR,
        ]);
    }
}
