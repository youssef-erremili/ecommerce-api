<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DestroyProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke(ProductService $service, Product $product)
    {
        try {
            $this->authorize('delete', $product);
            $service->destroy($product);

            return ApiResponse::success(ApiMessages::PRODUCT_DELETED);

        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
