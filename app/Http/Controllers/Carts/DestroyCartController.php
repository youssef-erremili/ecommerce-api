<?php

namespace App\Http\Controllers\Carts;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Services\CartService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use LogicException;

class DestroyCartController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Cart $cart, CartService $service): JsonResponse
    {
        try {
            $this->authorize('delete', $cart);

            $service->remove($cart);

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
            );
        } catch (LogicException $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
