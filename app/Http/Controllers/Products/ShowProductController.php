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
        $products = $service->show($request->user()->id);

        return ApiResponse::success(
            ApiMessages::PRODUCT_FETCHED,
            ProductResource::collection($products)->resolve()
        );
    }
}
