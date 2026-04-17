<?php

namespace App\Http\Controllers\Products;

use App\Enums\AccountType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;

class StoreProductController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke(StoreProductRequest $request, ProductService $service)
    {
        try {
            $user = $request->user();
            $data = $request->validated();

            // first check is user is logged in
            if (! $user) {
                return ApiResponse::error(ApiMessages::PRODUCT_UNAUTHORIZED_ACTION);
            }

            // second check is user is seller not customer
            if ($user->account_type === AccountType::CUSTOMER) {
                return ApiResponse::error(ApiMessages::USER_NOT_VENDOR);
            }

            $product = $service->create($user, $data);

            return ApiResponse::success(
                ApiMessages::PRODUCT_CREATED,
                [
                    'product' => ProductResource::make($product),
                ]
            );
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
