<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\HomeService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListProductHomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __construct(
        protected HomeService $service
    ) {}

    public function __invoke(string $slug): JsonResponse
    {
        try {
            $product = $this->service->getProduct($slug);

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'product' => ProductResource::make($product)->resolve(),
                ]
            );
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage(), 500);
        }
    }
}
