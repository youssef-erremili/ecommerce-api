<?php

namespace App\Http\Controllers\Carts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\CreateCartRequest;
use App\Http\Resources\CartResource;
use App\Models\Product;
use App\Services\CartService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Throwable;

class CreateCartController extends Controller
{
    public function __invoke(CreateCartRequest $request, Product $product, CartService $service)
    {
        try {
            $data = $request->validated();
            $cart = $service->addToCart($product, $data);

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'cart' => CartResource::make($cart)->resolve(),
                ]
            );
        } catch (Throwable $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
