<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AuthenticatedUserController;
use App\Http\Controllers\Products\DestroyProductController;
use App\Http\Controllers\Products\ShowProductController;
use App\Http\Controllers\Products\StoreProductController;
use App\Http\Controllers\Products\UpdateProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // auth api route
    Route::prefix('auth')->group(function () {
        Route::post('register', RegisterController::class);
        Route::post('login', LoginController::class);
        Route::post('logout', LogoutController::class)->middleware('auth:sanctum');
    });

    // get authenticated user
    Route::get('/me', AuthenticatedUserController::class)->middleware('auth:sanctum');

    // products routes api
    Route::middleware('auth:sanctum')->prefix('products')->group(function () {
        Route::post('store', StoreProductController::class);
        Route::get('show', ShowProductController::class);
        Route::post('update/{product}', UpdateProductController::class);
        Route::post('delete/{product}', DestroyProductController::class);
    });

});
