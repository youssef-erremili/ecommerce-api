<?php

namespace App\Http\Controllers\WishLists;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Services\WishlistService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class BulkDestroyWishListsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    use AuthorizesRequests;

    public function __invoke(Request $request, WishlistService $service)
    {
        try {
            $this->authorize('deleteAny', Wishlist::class);

            $ids = $request->array('ids');
            $service->clear($ids);

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED
            );

        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }

    }
}
