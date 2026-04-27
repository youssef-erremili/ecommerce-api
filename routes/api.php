<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Carts\CreateCartController;
use App\Http\Controllers\Products\DestroyProductController;
use App\Http\Controllers\Products\ShowProductController;
use App\Http\Controllers\Products\SingleProductController;
use App\Http\Controllers\Products\StoreProductController;
use App\Http\Controllers\Products\UpdateProductController;
use App\Http\Controllers\Products\UpdateProductImageController;
use App\Http\Controllers\User\Admin\AccountTypeController;
use App\Http\Controllers\User\Admin\DestroyUserAdminController;
use App\Http\Controllers\User\Admin\EditUserAdminController;
use App\Http\Controllers\User\Admin\ListUsersController;
use App\Http\Controllers\User\RegularUsers\AuthenticatedUserController;
use App\Http\Controllers\User\RegularUsers\DestroyUserController;
use App\Http\Controllers\User\RegularUsers\EditUserController;
use App\Http\Controllers\User\RegularUsers\ResetPasswordController;
use App\Http\Controllers\WishLists\BulkDestroyWishListsController;
use App\Http\Controllers\WishLists\DestroyWishListController;
use App\Http\Controllers\WishLists\ListsWishListsController;
use App\Http\Controllers\WishLists\StoreWishListsController;
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
        Route::get('show/{product}', SingleProductController::class);
        Route::patch('update/{product}', UpdateProductController::class);
        Route::put('update/{product}/images', UpdateProductImageController::class);
        Route::delete('delete/{product}', DestroyProductController::class);
    });

    Route::middleware('auth:sanctum')->prefix('account')->group(function () {
        // for Admins
        Route::middleware('can:admin-access')->prefix('admin')->group(function () {
            Route::patch('/upgrade/{id}', AccountTypeController::class);
            Route::get('/users', ListUsersController::class);
            Route::delete('/delete/{user}', DestroyUserAdminController::class);
            Route::patch('/edit/{user}', EditUserAdminController::class);
        });

        // for Regular
        Route::get('/me', AuthenticatedUserController::class);
        Route::patch('/edit/{user}', EditUserController::class);
        Route::delete('/delete/{user}', DestroyUserController::class);
        Route::patch('/reset-password', ResetPasswordController::class);
    });

    Route::middleware('auth:sanctum')->prefix('wishlist')->group(function () {
        Route::post('store', StoreWishListsController::class);
        Route::get('lists', ListsWishListsController::class);
        Route::delete('delete/{wishlist}', DestroyWishListController::class);
        Route::delete('/bulk-delete', BulkDestroyWishListsController::class);
    });

    // add to cart routes
    Route::middleware('auth:sanctum')->prefix('carts')->group(function () {
        Route::post('/{product}/create', CreateCartController::class);
    });

});
