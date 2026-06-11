<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\HomeService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use App\Traits\Paginator;
use Exception;
use Illuminate\Http\JsonResponse;

class ListSellerHomeController extends Controller
{
    use Paginator;

    /**
     * Handle the incoming request.
     */
    public function __construct(
        protected HomeService $service
    ) {}

    /**
     * @throws Exception
     */
    public function __invoke(string $slug): JsonResponse
    {
        try {
            $seller = $this->service->getSellerData($slug);

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'pagination' => $this->paginateResource($seller),
                    'vendor' => UserResource::collection($seller)->resolve(),
                ]
            );
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
