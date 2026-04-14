<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Service\ProductService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ShowProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, ProductService $service)
    {
        $this->authorize('viewAny', Product::class);

        $products = $service->show();

        return ApiResponse::success(
            ApiMessages::PRODUCT_FETCHED,
            [
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'first_page' => 1,
                    'last_page' => $products->lastPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                    'prev_page' => $products->previousPageUrl() ?? null,
                    'next_page' => $products->nextPageUrl() ?? null,
                ],
                'products' => ProductResource::collection($products)->resolve(),
            ]
        );
    }
}
