<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use App\Traits\Paginator;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class ShowProductController extends Controller
{
    use AuthorizesRequests;
    use Paginator;

    /**
     * Handle the incoming request.
     */
    public function __invoke(ProductService $service): JsonResponse
    {
        try {
            $this->authorize('viewAny', Product::class);
            $products = $service->paginate();

            return ApiResponse::success(
                ApiMessages::PRODUCT_FETCHED,
                [
                    'pagination' => $this->paginateResource($products),
                    'products' => ProductResource::collection($products)->resolve(),
                ]
            );
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
