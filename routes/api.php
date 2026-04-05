<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AuthenticatedUserController;
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

});
