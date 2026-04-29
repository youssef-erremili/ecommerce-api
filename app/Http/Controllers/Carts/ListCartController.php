<?php

namespace App\Http\Controllers\Carts;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;

class ListCartController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CartService $service)
    {
        try {
            $cart = $service->getCartItems();

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'cart' => CartResource::collection($cart),
                ]
            );
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
