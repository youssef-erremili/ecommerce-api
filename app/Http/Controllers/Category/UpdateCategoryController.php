<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UpdateCategoryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CreateCategoryRequest $request, CategoryService $service, Category $category)
    {
        try {
            $service->update($category, $request->validated());

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'category' => CategoryResource::make($category),
                ]
            );
        } catch (HttpException $exception) {
            return ApiResponse::error($exception->getMessage(), $exception->getStatusCode());
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
