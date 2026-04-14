<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Service\ProductService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SingleProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Product $product)
    {
        $this->authorize('view', $product);

        if (! $product) {
            return ApiResponse::error();
        }

        return ApiResponse::success(
            ApiMessages::PRODUCT_FETCHED,
            [ProductResource::make($product)]
        );
    }
}
