<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DestroyCategoryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Category $category, CategoryService $service): JsonResponse
    {
        try {
            $service->delete($category);

            return ApiResponse::success(ApiMessages::ACTION_COMPLETED);

        } catch (HttpException $exception) {
            return ApiResponse::error($exception->getMessage(), $exception->getStatusCode());
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
