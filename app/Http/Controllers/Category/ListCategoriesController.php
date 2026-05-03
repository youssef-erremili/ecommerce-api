<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Http\Request;

class ListCategoriesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CategoryService $service)
    {
        try {
            $categories = $service->getCategories();

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'categories count' => $categories->count(),
                    'categories' => CategoryResource::collection($categories)->resolve(),
                ]
            );
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
