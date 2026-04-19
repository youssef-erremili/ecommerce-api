<?php

namespace App\Http\Controllers\WishLists;

use App\Http\Controllers\Controller;
use App\Http\Resources\WishListResource;
use App\Services\WishlistService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use App\Traits\Paginator;

class ListsWishListsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    use Paginator;

    public function __invoke(WishlistService $service)
    {
        try {
            $wishlist = $service->getUserWishlist();

            return ApiResponse::success(
                ApiMessages::ACTION_COMPLETED,
                [
                    'pagination' => $this->paginateResource($wishlist),
                    'wishlists' => WishListResource::collection($wishlist)->resolve(),
                ]
            );
        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
