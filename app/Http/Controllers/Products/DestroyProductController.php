<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Service\ProductService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DestroyProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Product $product, ProductService $service)
    {
        $this->authorize('delete', $product);

        $holder = $service->destroy($product);
        if ($holder) {
            return ApiResponse::success(ApiMessages::PRODUCT_DELETION_SUCCESS);
        }

        return ApiResponse::error(ApiMessages::PRODUCT_DELETION_FAILED);
    }
}
