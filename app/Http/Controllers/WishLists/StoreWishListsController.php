<?php

namespace App\Http\Controllers\WishLists;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\WishlistService;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class StoreWishListsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    use AuthorizesRequests;

    public function __invoke(Request $request, User $user, WishlistService $service)
    {
        try {
            $this->authorize('create', Wishlist::class);

            $id = $request->integer('product_id');
            $service->add(auth()->user(), $id);

            return ApiResponse::success(ApiMessages::ACTION_COMPLETED);
        } catch (\Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }
    }
}
