<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use App\Traits\Paginator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListCategoriesController extends Controller
{
    use Paginator;

    /**
     * Handle the incoming request.
     */
    public function __invoke(CategoryService $service): JsonResponse
    {
        try {
            $categories = $service->paginate(100);

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'pagination' => $this->paginateResource($categories),
                    'categories' => CategoryResource::collection($categories)->resolve(),
                ]
            );
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
