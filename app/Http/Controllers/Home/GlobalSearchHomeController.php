<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\Home\GlobalSearchHomeRequest;
use App\Http\Resources\ProductResource;
use App\Services\HomeService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use App\Traits\Paginator;
use Exception;

class GlobalSearchHomeController extends Controller
{
    use Paginator;

    /**
     * Handle the incoming request.
     */
    public function __construct(
        protected HomeService $service
    ) {}

    public function __invoke(GlobalSearchHomeRequest $request)
    {
        $query = $request->query('query');
        try {
            $search = $this->service->search($query);

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'pagination' => $this->paginateResource($search),
                    'result' => ProductResource::collection($search)->resolve(),
                ]
            );

        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage(), $exception->getCode());
        }
    }
}
