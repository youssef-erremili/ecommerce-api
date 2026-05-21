<?php

use App\Http\Controllers\Home\ListsProductsHomeController;
use Illuminate\Support\Facades\Route;

// home page route
Route::prefix('home')->group(function () {
    Route::get('products', ListsProductsHomeController::class);
});
