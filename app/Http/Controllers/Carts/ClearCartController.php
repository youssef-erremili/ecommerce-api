<?php

namespace App\Http\Controllers\Carts;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class ClearCartController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CartService $service): JsonResponse
    {
        try {
            $service->clear();

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED
            );
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
