<?php

namespace App\Http\Controllers\backend;

use App\Enums\PermissionStatus;
use App\Enums\PermissionType;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\AuthPermissionRequest;
use App\Http\Resources\backend\AuthPermissionResource;
use App\Models\AuthPermission;
use App\Service\AuthPermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AuthPermissionController extends AbstractController
{
    public function __construct(
        private readonly AuthPermissionService $authPermissionService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $tree = $this->authPermissionService->getTree(
            $request->query('keyword'),
            $request->query('per_type')
        );

        return $this->success([
            'list' => $tree,
            'options' => [
                'per_type' => PermissionType::labels(),
                'per_status' => PermissionStatus::labels(),
                'per_method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
            ],
        ]);
    }

    /** 启用权限树（供角色授权勾选） */
    public function tree(): JsonResponse
    {
        $tree = $this->authPermissionService->getTree(null, null, true);

        return $this->success($tree);
    }

    public function store(AuthPermissionRequest $request): JsonResponse
    {
        try {
            $permission = $this->authPermissionService->create($request->validated());

            return $this->success(
                (new AuthPermissionResource($permission))->resolve(),
                '添加成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(820099, '添加失败');
        }
    }

    public function update(AuthPermissionRequest $request, AuthPermission $authPermission): JsonResponse
    {
        try {
            $permission = $this->authPermissionService->update($authPermission, $request->validated());

            return $this->success(
                (new AuthPermissionResource($permission))->resolve(),
                '修改成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(820099, '修改失败');
        }
    }

    public function updateSort(AuthPermissionRequest $request, AuthPermission $authPermission): JsonResponse
    {
        $permission = $this->authPermissionService->updateSort(
            $authPermission,
            (int) $request->validated('per_sort')
        );

        return $this->success(
            (new AuthPermissionResource($permission))->resolve(),
            '排序更新成功'
        );
    }

    public function updateStatus(AuthPermissionRequest $request, AuthPermission $authPermission): JsonResponse
    {
        $permission = $this->authPermissionService->updateStatus(
            $authPermission,
            (int) $request->validated('per_status')
        );

        return $this->success(
            (new AuthPermissionResource($permission))->resolve(),
            '状态更新成功'
        );
    }

    public function destroy(AuthPermission $authPermission): JsonResponse
    {
        try {
            $this->authPermissionService->delete($authPermission);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(820099, '删除失败');
        }
    }
}
