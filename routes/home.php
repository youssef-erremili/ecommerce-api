<?php

use App\Http\Controllers\Home\GlobalSearchHomeController;
use App\Http\Controllers\Home\ListProductHomeController;
use App\Http\Controllers\Home\ListSellerHomeController;
use App\Http\Controllers\Home\ListsProductsHomeController;
use Illuminate\Support\Facades\Route;

Route::get('products', ListsProductsHomeController::class);
Route::get('product/{slug}', ListProductHomeController::class);
Route::get('seller/{slug}', ListSellerHomeController::class);
Route::get('/search', GlobalSearchHomeController::class);
