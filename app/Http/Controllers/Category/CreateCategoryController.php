<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Http\Request;

class CreateCategoryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CreateCategoryRequest $request, CategoryService $service)
    {
        try {
            $category = $service->create($request->validated());

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'category' => CategoryResource::make($category),
                ]
            );

        } catch (Exception $exception) {
            return ApiResponse::error(
                $exception->getMessage()
            );
        }
    }
}
