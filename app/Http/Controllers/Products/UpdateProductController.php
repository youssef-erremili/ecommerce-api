<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Service\ProductService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class UpdateProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdateProductRequest $request, Product $product, ProductService $service)
    {
        $this->authorize('update', $product);

        $data = $request->validated();
        try {
            $service->update($product, $data);
        } catch (\Exception $exception) {
            Log::error(ApiMessages::AN_ERROR_OCCURRED.$exception->getMessage());
        }

        return ApiResponse::success(
            ApiMessages::PRODUCT_UPDATED,
            [ProductResource::make($product)]
        );
    }
}
