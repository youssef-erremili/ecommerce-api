<?php

namespace App\Http\Controllers\Products;

use App\Enums\AccountType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Products\StoreProductRequest;
use App\Service\ProductService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;

class StoreProductController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreProductRequest $request, ProductService $service)
    {
        $user = $request->user();
        $product = $request->validated();

        // first check is user is logged in
        if (! $user) {
            return ApiResponse::error(ApiMessages::UNAUTHORIZED_PRODUCT_CREATION);
        }

        // second check is user is seller not customer
        if ($user->account_type === AccountType::CUSTOMER) {
            return ApiResponse::error(ApiMessages::UNAUTHORIZED_PRODUCT_CREATION);
        }

        $savedProduct = $service->create($user, $product);

        if ($savedProduct) {
            return response()->json(
                $savedProduct
            );
        }

    }
}
