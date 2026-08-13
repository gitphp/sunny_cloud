<?php

use App\Http\Controllers\frontend\AuthController as FrontendAuthController;
use App\Http\Controllers\frontend\FeedbackController as FrontendFeedbackController;
use App\Http\Controllers\frontend\HomeController as FrontendHomeController;
use App\Http\Controllers\frontend\IndexController as FrontendIndexController;
use App\Http\Controllers\frontend\PortalController as FrontendPortalController;
use Illuminate\Support\Facades\Route;

Route::prefix('frontend')->group(function () {
    Route::prefix('api')->group(function () {
        Route::post('auth/login', [FrontendAuthController::class, 'login'])->middleware('throttle:10,1');
        Route::post('auth/register', [FrontendAuthController::class, 'register'])->middleware('throttle:5,1');
        Route::post('auth/logout', [FrontendAuthController::class, 'logout']);
        Route::get('auth/me', [FrontendAuthController::class, 'me']);

        Route::get('portal', [FrontendPortalController::class, 'index']);
        Route::get('home', [FrontendHomeController::class, 'index']);
        Route::post('feedbacks', [FrontendFeedbackController::class, 'store'])->middleware('throttle:5,1');
    });

    Route::get('/{any?}', [FrontendIndexController::class, 'index'])
        ->where('any', '.*')
        ->name('frontend.index');
});
