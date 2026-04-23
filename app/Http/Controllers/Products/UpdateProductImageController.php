<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\UpdateProductImagesRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UpdateProductImageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    use AuthorizesRequests;

    public function __invoke(UpdateProductImagesRequest $request, Product $product, ProductService $service)
    {
        try {
            $this->authorize('update', $product);

            $updatedImages = $service->uploadImages($product, $request->validated());

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'product' => ProductResource::make($updatedImages),
                ]
            );

        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }

    }
}
