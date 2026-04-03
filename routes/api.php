<?php

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // register api route
    Route::prefix('auth')->group(function () {
        Route::post('register', RegisterController::class);
    });

});
