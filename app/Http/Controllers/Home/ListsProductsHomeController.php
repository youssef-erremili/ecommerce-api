<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\HomeService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use App\Traits\Paginator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListsProductsHomeController extends Controller
{
    use Paginator;

    /**
     * Handle the incoming request.
     */
    public function __construct(
        protected HomeService $service
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $products = $this->service->index();

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'pagination' => $this->paginateResource($products),
                    'products' => ProductResource::collection($products)->resolve(),
                ]
            );
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage(), 500);
        }
    }
}
