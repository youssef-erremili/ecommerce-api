<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CreateCategoryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CreateCategoryRequest $request, CategoryService $service): JsonResponse
    {
        try {
            $category = $service->create($request->validated());

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'category' => CategoryResource::make($category),
                ],
                Response::HTTP_CREATED
            );

        } catch (Exception $exception) {
            return ApiResponse::error(
                $exception->getMessage()
            );
        }
    }
}
