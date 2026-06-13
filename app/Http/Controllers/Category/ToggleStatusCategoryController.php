<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\ToggleStatusCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class ToggleStatusCategoryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ToggleStatusCategoryRequest $request, CategoryService $service, Category $category): JsonResponse
    {
        try {
            $category = $service->toggleStatus($category, $request->boolean('is_active'));

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'category' => CategoryResource::make($category),
                ]
            );
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage(), 400);
        }
    }
}
