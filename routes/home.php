<?php

use App\Http\Controllers\Home\ListProductHomeController;
use App\Http\Controllers\Home\ListSellerHomeController;
use App\Http\Controllers\Home\ListsProductsHomeController;
use Illuminate\Support\Facades\Route;

// home page route
Route::prefix('home')->group(function () {
    Route::get('products', ListsProductsHomeController::class);
    Route::get('/product/{slug}', ListProductHomeController::class);
    Route::get('/seller/{slug}', ListSellerHomeController::class);
});
