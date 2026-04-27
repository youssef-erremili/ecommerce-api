<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    /**
     * Create a new class instance.
     */
    public static function success(
        string $message, array $data = [],
        $status = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'message' => $message,
            'type' => ApiMessages::SUCCESS,
            'data' => $data,
        ], $status);
    }

    public static function error(
        string $message = ApiMessages::AN_ERROR_OCCURRED,
        $status = Response::HTTP_BAD_REQUEST
    ): JsonResponse {
        return response()->json([
            'message' => $message,
            'type' => ApiMessages::ERROR,
        ], $status);
    }
}
