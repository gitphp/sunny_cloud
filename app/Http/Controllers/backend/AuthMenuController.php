<?php

namespace App\Http\Controllers\backend;

use App\Enums\MenuStatus;
use App\Exceptions\BusinessException;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\backend\AuthMenuRequest;
use App\Http\Resources\backend\AuthMenuResource;
use App\Models\AuthMenu;
use App\Service\AuthMenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AuthMenuController extends Controller
{
    public function __construct(
        private readonly AuthMenuService $authMenuService
    ) {
    }

    /** 菜单管理树（含禁用） */
    public function index(Request $request): JsonResponse
    {
        $tree = $this->authMenuService->getTree($request->query('keyword'));

        return ApiResponseHelper::success([
            'list' => $tree,
            'options' => [
                'menu_status' => MenuStatus::labels(),
            ],
        ]);
    }

    /** 侧栏导航（仅启用） */
    public function nav(): JsonResponse
    {
        $tree = $this->authMenuService->getTree(null, true);

        return ApiResponseHelper::success($tree);
    }

    public function store(AuthMenuRequest $request): JsonResponse
    {
        try {
            $menu = $this->authMenuService->create($request->validated());

            return ApiResponseHelper::success(
                (new AuthMenuResource($menu))->resolve(),
                '添加成功'
            );
        } catch (BusinessException $e) {
            return ApiResponseHelper::error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return ApiResponseHelper::error(800099, '添加失败');
        }
    }

    public function update(AuthMenuRequest $request, AuthMenu $authMenu): JsonResponse
    {
        try {
            $menu = $this->authMenuService->update($authMenu, $request->validated());

            return ApiResponseHelper::success(
                (new AuthMenuResource($menu))->resolve(),
                '修改成功'
            );
        } catch (BusinessException $e) {
            return ApiResponseHelper::error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return ApiResponseHelper::error(800099, '修改失败');
        }
    }

    public function updateSort(AuthMenuRequest $request, AuthMenu $authMenu): JsonResponse
    {
        $menu = $this->authMenuService->updateSort(
            $authMenu,
            (int) $request->validated('menu_sort')
        );

        return ApiResponseHelper::success(
            (new AuthMenuResource($menu))->resolve(),
            '排序更新成功'
        );
    }

    public function updateStatus(AuthMenuRequest $request, AuthMenu $authMenu): JsonResponse
    {
        $menu = $this->authMenuService->updateStatus(
            $authMenu,
            (int) $request->validated('menu_status')
        );

        return ApiResponseHelper::success(
            (new AuthMenuResource($menu))->resolve(),
            '状态更新成功'
        );
    }

    public function destroy(AuthMenu $authMenu): JsonResponse
    {
        try {
            $this->authMenuService->delete($authMenu);

            return ApiResponseHelper::success(null, '删除成功');
        } catch (BusinessException $e) {
            return ApiResponseHelper::error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return ApiResponseHelper::error(800099, '删除失败');
        }
    }
}
