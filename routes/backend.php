<?php

use App\Http\Controllers\backend\AuthController;
use App\Http\Controllers\backend\AuthMenuController;
use App\Http\Controllers\backend\AuthPermissionController;
use App\Http\Controllers\backend\AuthRoleController;
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

            Route::get('menus/nav', [AuthMenuController::class, 'nav']);
            Route::get('menus', [AuthMenuController::class, 'index']);
            Route::post('menus', [AuthMenuController::class, 'store']);
            Route::put('menus/{authMenu}', [AuthMenuController::class, 'update']);
            Route::patch('menus/{authMenu}/sort', [AuthMenuController::class, 'updateSort']);
            Route::patch('menus/{authMenu}/status', [AuthMenuController::class, 'updateStatus']);
            Route::delete('menus/{authMenu}', [AuthMenuController::class, 'destroy']);

            Route::get('roles', [AuthRoleController::class, 'index']);
            Route::post('roles', [AuthRoleController::class, 'store']);
            Route::get('roles/{authRole}', [AuthRoleController::class, 'show']);
            Route::put('roles/{authRole}', [AuthRoleController::class, 'update']);
            Route::patch('roles/{authRole}/sort', [AuthRoleController::class, 'updateSort']);
            Route::patch('roles/{authRole}/status', [AuthRoleController::class, 'updateStatus']);
            Route::delete('roles/{authRole}', [AuthRoleController::class, 'destroy']);

            Route::get('permissions/tree', [AuthPermissionController::class, 'tree']);
            Route::get('permissions', [AuthPermissionController::class, 'index']);
            Route::post('permissions', [AuthPermissionController::class, 'store']);
            Route::put('permissions/{authPermission}', [AuthPermissionController::class, 'update']);
            Route::patch('permissions/{authPermission}/sort', [AuthPermissionController::class, 'updateSort']);
            Route::patch('permissions/{authPermission}/status', [AuthPermissionController::class, 'updateStatus']);
            Route::delete('permissions/{authPermission}', [AuthPermissionController::class, 'destroy']);
        });
    });

    Route::get('/{any?}', [IndexController::class, 'index'])
        ->where('any', '.*')
        ->name('backend.index');
});
