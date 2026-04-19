<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Products\DestroyProductController;
use App\Http\Controllers\Products\ShowProductController;
use App\Http\Controllers\Products\SingleProductController;
use App\Http\Controllers\Products\StoreProductController;
use App\Http\Controllers\Products\UpdateProductController;
use App\Http\Controllers\User\AccountTypeController;
use App\Http\Controllers\User\AuthenticatedUserController;
use App\Http\Controllers\User\ListUsersController;
use App\Http\Controllers\WishLists\BulkDestroyWishListsController;
use App\Http\Controllers\WishLists\DestroyWishListController;
use App\Http\Controllers\WishLists\ListsWishListsController;
use App\Http\Controllers\WishLists\StoreWishListsController;
use App\Support\ApiMessages;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // auth api route
    Route::prefix('auth')->group(function () {
        Route::post('register', RegisterController::class);
        Route::post('login', LoginController::class);
        Route::post('logout', LogoutController::class)->middleware('auth:sanctum');
    });

    // products routes api
    Route::middleware('auth:sanctum')->prefix('products')->group(function () {
        Route::get('lists', ShowProductController::class);
        Route::post('store', StoreProductController::class);
        Route::get('show/{product}', SingleProductController::class)->missing(function () {
            return ApiResponse::error(ApiMessages::PRODUCT_NOT_FOUND);
        });
        Route::patch('update/{product}', UpdateProductController::class);
        Route::delete('delete/{product}', DestroyProductController::class)
            ->missing(function () {
                return ApiResponse::error(ApiMessages::PRODUCT_NOT_FOUND);
            });
    });

    Route::middleware('auth:sanctum')->prefix('account')->group(function () {
        Route::get('/me', AuthenticatedUserController::class);
        Route::patch('/upgrade/{id}', AccountTypeController::class);
        Route::get('/users', ListUsersController::class);
    });

    Route::middleware('auth:sanctum')->prefix('wishlist')->group(function () {
        Route::post('store', StoreWishListsController::class);
        Route::get('lists', ListsWishListsController::class);
        Route::delete('delete/{wishlist}', DestroyWishListController::class)
            ->missing(function () {
                return ApiResponse::error(ApiMessages::PRODUCT_NOT_FOUND);
            });
        Route::delete('/bulk-delete', BulkDestroyWishListsController::class);
    });

});
