<?php

use App\Http\Controllers\backend\AuthController;
use App\Http\Controllers\backend\AuthMenuController;
use App\Http\Controllers\backend\AuthPermissionController;
use App\Http\Controllers\backend\AuthRoleController;
use App\Http\Controllers\backend\CategoryController;
use App\Http\Controllers\backend\HrDepartmentController;
use App\Http\Controllers\backend\HrPostController;
use App\Http\Controllers\backend\HrUserDeptPostController;
use App\Http\Controllers\backend\IndexController;
use App\Http\Controllers\backend\ProductBrandController;
use App\Http\Controllers\backend\ProductCategoryController;
use App\Http\Controllers\backend\ProductController;
use App\Http\Controllers\backend\ProductSpecificationController;
use App\Http\Controllers\backend\UserAccountController;
use App\Http\Controllers\backend\WfFlowApplyController;
use App\Http\Controllers\backend\WfFlowDefinitionController;
use App\Http\Controllers\backend\WfFlowTypeController;
use App\Http\Middleware\EnsureBackendAuthenticated;
use Illuminate\Support\Facades\Route;

Route::prefix('backend')->group(function () {
    Route::prefix('api')->group(function () {
        // 公开接口（登录限流：每分钟 10 次）
        Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
        Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:5,1');

        // 需登录
        Route::middleware(EnsureBackendAuthenticated::class)->group(function () {
            Route::post('auth/logout', [AuthController::class, 'logout']);
            Route::get('auth/me', [AuthController::class, 'me']);

            Route::get('categories', [CategoryController::class, 'index']);
            Route::post('categories', [CategoryController::class, 'store']);
            Route::put('categories/{category}', [CategoryController::class, 'update']);
            Route::patch('categories/{category}/sort', [CategoryController::class, 'updateSort']);
            Route::patch('categories/{category}/status', [CategoryController::class, 'updateStatus']);
            Route::delete('categories/{category}', [CategoryController::class, 'destroy']);

            Route::get('hr/departments', [HrDepartmentController::class, 'index']);
            Route::post('hr/departments', [HrDepartmentController::class, 'store']);
            Route::put('hr/departments/{hrDepartment}', [HrDepartmentController::class, 'update']);
            Route::patch('hr/departments/{hrDepartment}/sort', [HrDepartmentController::class, 'updateSort']);
            Route::patch('hr/departments/{hrDepartment}/status', [HrDepartmentController::class, 'updateStatus']);
            Route::get('hr/departments/{hrDepartment}/leaders', [HrDepartmentController::class, 'leaders']);
            Route::put('hr/departments/{hrDepartment}/leaders', [HrDepartmentController::class, 'syncLeaders']);
            Route::delete('hr/departments/{hrDepartment}', [HrDepartmentController::class, 'destroy']);

            Route::get('hr/posts', [HrPostController::class, 'index']);
            Route::post('hr/posts', [HrPostController::class, 'store']);
            Route::put('hr/posts/{hrPost}', [HrPostController::class, 'update']);
            Route::patch('hr/posts/{hrPost}/sort', [HrPostController::class, 'updateSort']);
            Route::patch('hr/posts/{hrPost}/status', [HrPostController::class, 'updateStatus']);
            Route::delete('hr/posts/{hrPost}', [HrPostController::class, 'destroy']);

            Route::get('hr/user-dept-posts', [HrUserDeptPostController::class, 'index']);
            Route::post('hr/user-dept-posts', [HrUserDeptPostController::class, 'store']);
            Route::put('hr/user-dept-posts/{hrUserDeptPost}', [HrUserDeptPostController::class, 'update']);
            Route::delete('hr/user-dept-posts/{hrUserDeptPost}', [HrUserDeptPostController::class, 'destroy']);

            Route::get('product/brands', [ProductBrandController::class, 'index']);
            Route::post('product/brands', [ProductBrandController::class, 'store']);
            Route::put('product/brands/{productBrand}', [ProductBrandController::class, 'update']);
            Route::patch('product/brands/{productBrand}/sort', [ProductBrandController::class, 'updateSort']);
            Route::patch('product/brands/{productBrand}/status', [ProductBrandController::class, 'updateStatus']);
            Route::delete('product/brands/{productBrand}', [ProductBrandController::class, 'destroy']);

            Route::get('product/categories', [ProductCategoryController::class, 'index']);
            Route::post('product/categories', [ProductCategoryController::class, 'store']);
            Route::put('product/categories/{productCategory}', [ProductCategoryController::class, 'update']);
            Route::patch('product/categories/{productCategory}/sort', [ProductCategoryController::class, 'updateSort']);
            Route::patch('product/categories/{productCategory}/status', [ProductCategoryController::class, 'updateStatus']);
            Route::delete('product/categories/{productCategory}', [ProductCategoryController::class, 'destroy']);

            Route::get('product/specifications', [ProductSpecificationController::class, 'index']);
            Route::post('product/specifications', [ProductSpecificationController::class, 'store']);
            Route::put('product/specifications/{productSpecification}', [ProductSpecificationController::class, 'update']);
            Route::patch('product/specifications/{productSpecification}/sort', [ProductSpecificationController::class, 'updateSort']);
            Route::patch('product/specifications/{productSpecification}/status', [ProductSpecificationController::class, 'updateStatus']);
            Route::delete('product/specifications/{productSpecification}', [ProductSpecificationController::class, 'destroy']);
            Route::get('product/specifications/{productSpecification}/values', [ProductSpecificationController::class, 'values']);
            Route::post('product/specifications/{productSpecification}/values', [ProductSpecificationController::class, 'storeValue']);
            Route::put('product/specification-values/{productSpecificationValue}', [ProductSpecificationController::class, 'updateValue']);
            Route::patch('product/specification-values/{productSpecificationValue}/sort', [ProductSpecificationController::class, 'updateValueSort']);
            Route::patch('product/specification-values/{productSpecificationValue}/status', [ProductSpecificationController::class, 'updateValueStatus']);
            Route::delete('product/specification-values/{productSpecificationValue}', [ProductSpecificationController::class, 'destroyValue']);

            Route::post('product/upload', [ProductController::class, 'upload']);
            Route::get('product/products', [ProductController::class, 'index']);
            Route::post('product/products', [ProductController::class, 'store']);
            Route::get('product/products/{product}', [ProductController::class, 'show']);
            Route::put('product/products/{product}', [ProductController::class, 'update']);
            Route::patch('product/products/{product}/status', [ProductController::class, 'updateStatus']);
            Route::delete('product/products/{product}', [ProductController::class, 'destroy']);

            Route::get('wf/flow-types', [WfFlowTypeController::class, 'index']);
            Route::get('wf/flow-types/options', [WfFlowTypeController::class, 'options']);
            Route::post('wf/flow-types', [WfFlowTypeController::class, 'store']);
            Route::put('wf/flow-types/{wfFlowType}', [WfFlowTypeController::class, 'update']);
            Route::patch('wf/flow-types/{wfFlowType}/sort', [WfFlowTypeController::class, 'updateSort']);
            Route::patch('wf/flow-types/{wfFlowType}/status', [WfFlowTypeController::class, 'updateStatus']);
            Route::delete('wf/flow-types/{wfFlowType}', [WfFlowTypeController::class, 'destroy']);

            Route::get('wf/flow-definitions', [WfFlowDefinitionController::class, 'index']);
            Route::post('wf/flow-definitions', [WfFlowDefinitionController::class, 'store']);
            Route::get('wf/flow-definitions/{wfFlowDefinition}', [WfFlowDefinitionController::class, 'show']);
            Route::put('wf/flow-definitions/{wfFlowDefinition}', [WfFlowDefinitionController::class, 'update']);
            Route::post('wf/flow-definitions/{wfFlowDefinition}/publish', [WfFlowDefinitionController::class, 'publish']);
            Route::post('wf/flow-definitions/{wfFlowDefinition}/unpublish', [WfFlowDefinitionController::class, 'unpublish']);
            Route::delete('wf/flow-definitions/{wfFlowDefinition}', [WfFlowDefinitionController::class, 'destroy']);

            Route::get('wf/applies/mine', [WfFlowApplyController::class, 'mine']);
            Route::get('wf/applies/todo', [WfFlowApplyController::class, 'todo']);
            Route::get('wf/applies/cc', [WfFlowApplyController::class, 'ccList']);
            Route::get('wf/applies/published-definitions', [WfFlowApplyController::class, 'publishedDefinitions']);
            Route::get('wf/applies/published-definitions/{wfFlowDefinition}', [WfFlowApplyController::class, 'definitionDetail']);
            Route::post('wf/applies', [WfFlowApplyController::class, 'store']);
            Route::get('wf/applies/{wfFlowApply}', [WfFlowApplyController::class, 'show']);
            Route::put('wf/applies/{wfFlowApply}', [WfFlowApplyController::class, 'update']);
            Route::post('wf/applies/{wfFlowApply}/submit', [WfFlowApplyController::class, 'submit']);
            Route::post('wf/applies/{wfFlowApply}/withdraw', [WfFlowApplyController::class, 'withdraw']);
            Route::post('wf/applies/{wfFlowApply}/void', [WfFlowApplyController::class, 'void']);
            Route::delete('wf/applies/{wfFlowApply}', [WfFlowApplyController::class, 'destroy']);
            Route::post('wf/applies/{wfFlowApply}/agree', [WfFlowApplyController::class, 'agree']);
            Route::post('wf/applies/{wfFlowApply}/reject', [WfFlowApplyController::class, 'reject']);
            Route::post('wf/applies/{wfFlowApply}/transfer', [WfFlowApplyController::class, 'transfer']);
            Route::post('wf/applies/{wfFlowApply}/add-sign', [WfFlowApplyController::class, 'addSign']);
            Route::post('wf/cc/{wfFlowCcUser}/read', [WfFlowApplyController::class, 'markCcRead']);

            Route::get('users', [UserAccountController::class, 'index']);
            Route::post('users', [UserAccountController::class, 'store']);
            Route::get('users/{userAccount}', [UserAccountController::class, 'show']);
            Route::put('users/{userAccount}', [UserAccountController::class, 'update']);
            Route::patch('users/{userAccount}/status', [UserAccountController::class, 'updateStatus']);
            Route::get('users/{userAccount}/roles', [UserAccountController::class, 'roles']);
            Route::put('users/{userAccount}/roles', [UserAccountController::class, 'syncRoles']);
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
            Route::get('roles/{authRole}/grant', [AuthRoleController::class, 'grant']);
            Route::put('roles/{authRole}/grant', [AuthRoleController::class, 'syncGrant']);
            Route::put('roles/{authRole}/menus', [AuthRoleController::class, 'syncMenus']);
            Route::put('roles/{authRole}/permissions', [AuthRoleController::class, 'syncPermissions']);
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
