<?php

use App\Http\Controllers\backend\AuthController;
use App\Http\Controllers\backend\CategoryController;
use App\Http\Controllers\backend\IndexController;
use App\Http\Controllers\backend\UserAccountController;
use App\Http\Middleware\EnsureBackendAuthenticated;
use Illuminate\Support\Facades\Route;

Route::prefix('backend')->group(function () {
    Route::prefix('api')->group(function () {
        // 公开接口
        Route::post('auth/login', [AuthController::class, 'login']);
        Route::post('auth/register', [AuthController::class, 'register']);

        // 需登录
        Route::middleware(EnsureBackendAuthenticated::class)->group(function () {
            Route::post('auth/logout', [AuthController::class, 'logout']);
            Route::get('auth/me', [AuthController::class, 'me']);

            Route::get('categories', [CategoryController::class, 'index']);
            Route::post('categories', [CategoryController::class, 'store']);
            Route::put('categories/{category}', [CategoryController::class, 'update']);
            Route::patch('categories/{category}/sort', [CategoryController::class, 'updateSort']);
            Route::delete('categories/{category}', [CategoryController::class, 'destroy']);

            Route::get('users', [UserAccountController::class, 'index']);
            Route::post('users', [UserAccountController::class, 'store']);
            Route::get('users/{userAccount}', [UserAccountController::class, 'show']);
            Route::put('users/{userAccount}', [UserAccountController::class, 'update']);
            Route::patch('users/{userAccount}/status', [UserAccountController::class, 'updateStatus']);
            Route::delete('users/{userAccount}', [UserAccountController::class, 'destroy']);
        });
    });

    Route::get('/{any?}', [IndexController::class, 'index'])
        ->where('any', '.*')
        ->name('backend.index');
});
