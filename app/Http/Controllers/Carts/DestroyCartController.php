<?php

namespace App\Http\Controllers\Carts;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Services\CartService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use LogicException;

class DestroyCartController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Cart $cart, CartService $service)
    {
        try {

            $this->authorize('delete', $cart);

            $service->destroy($cart);

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'cart' => CartResource::make($cart),
                ]
            );
        } catch (LogicException $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
