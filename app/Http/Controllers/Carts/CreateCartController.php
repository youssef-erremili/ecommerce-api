<?php

namespace App\Http\Controllers\Carts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\CreateCartRequest;
use App\Http\Resources\CartResource;
use App\Models\Product;
use App\Services\CartService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CreateCartController extends Controller
{
    /**
     * @throws Throwable
     */
    public function __invoke(CreateCartRequest $request, Product $product, CartService $service): JsonResponse
    {
        try {
            $data = $request->validated();
            $cart = $service->addToCart($product, $data);

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'carts' => CartResource::make($cart)->resolve(),
                ],
                Response::HTTP_CREATED
            );
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage(), 500);
        }
    }
}
