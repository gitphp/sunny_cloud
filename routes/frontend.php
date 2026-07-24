<?php

use App\Http\Controllers\frontend\AuthController as FrontendAuthController;
use App\Http\Controllers\frontend\IndexController as FrontendIndexController;
use Illuminate\Support\Facades\Route;

Route::prefix('frontend')->group(function () {
    Route::prefix('api')->group(function () {
        Route::post('auth/login', [FrontendAuthController::class, 'login']);
        Route::post('auth/register', [FrontendAuthController::class, 'register']);
        Route::post('auth/logout', [FrontendAuthController::class, 'logout']);
        Route::get('auth/me', [FrontendAuthController::class, 'me']);
    });

    Route::get('/{any?}', [FrontendIndexController::class, 'index'])
        ->where('any', '.*')
        ->name('frontend.index');
});
