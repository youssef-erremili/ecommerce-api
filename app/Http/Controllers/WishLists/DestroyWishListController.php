<?php

namespace App\Http\Controllers\WishLists;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Services\WishlistService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DestroyWishListController extends Controller
{
    /**
     * Handle the incoming request.
     */
    use AuthorizesRequests;

    public function __invoke(WishlistService $service, Wishlist $wishlist)
    {
        try {
            $this->authorize('delete', $wishlist);

            $service->remove($wishlist);

            return ApiResponse::success(ApiMessages::ACTION_COMPLETED);
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
