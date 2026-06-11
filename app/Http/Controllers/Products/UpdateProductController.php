<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class UpdateProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke(UpdateProductRequest $request, Product $product, ProductService $service): JsonResponse
    {
        $this->authorize('update', $product);

        $data = $request->validated();
        $service->update($product, $data);

        return ApiResponse::success(
            ApiMessages::PRODUCT_UPDATED,
            [
                'product' => ProductResource::make($product),
            ]
        );
    }
}
