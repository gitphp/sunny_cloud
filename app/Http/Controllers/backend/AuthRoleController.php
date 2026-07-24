<?php

namespace App\Http\Controllers\backend;

use App\Enums\DataScope;
use App\Enums\RoleStatus;
use App\Enums\RoleType;
use App\Exceptions\BusinessException;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\backend\AuthRoleRequest;
use App\Http\Resources\backend\AuthRoleResource;
use App\Models\AuthRole;
use App\Service\AuthRoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AuthRoleController extends Controller
{
    public function __construct(
        private readonly AuthRoleService $authRoleService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->authRoleService->paginate(
            $request->only(['keyword', 'role_status', 'role_type', 'data_scope']),
            (int) $request->query('per_page', 15)
        );

        return ApiResponseHelper::success([
            'list' => AuthRoleResource::collection($paginator->items())->resolve(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'role_type' => RoleType::labels(),
                'role_status' => RoleStatus::labels(),
                'data_scope' => DataScope::labels(),
            ],
        ]);
    }

    public function store(AuthRoleRequest $request): JsonResponse
    {
        try {
            $role = $this->authRoleService->create($request->validated());

            return ApiResponseHelper::success(
                (new AuthRoleResource($role))->resolve(),
                '添加成功'
            );
        } catch (BusinessException $e) {
            return ApiResponseHelper::error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return ApiResponseHelper::error(810099, '添加失败');
        }
    }

    public function show(AuthRole $authRole): JsonResponse
    {
        return ApiResponseHelper::success(
            (new AuthRoleResource($authRole))->resolve()
        );
    }

    public function update(AuthRoleRequest $request, AuthRole $authRole): JsonResponse
    {
        try {
            $role = $this->authRoleService->update($authRole, $request->validated());

            return ApiResponseHelper::success(
                (new AuthRoleResource($role))->resolve(),
                '修改成功'
            );
        } catch (BusinessException $e) {
            return ApiResponseHelper::error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return ApiResponseHelper::error(810099, '修改失败');
        }
    }

    public function updateSort(AuthRoleRequest $request, AuthRole $authRole): JsonResponse
    {
        $role = $this->authRoleService->updateSort(
            $authRole,
            (int) $request->validated('role_sort')
        );

        return ApiResponseHelper::success(
            (new AuthRoleResource($role))->resolve(),
            '排序更新成功'
        );
    }

    public function updateStatus(AuthRoleRequest $request, AuthRole $authRole): JsonResponse
    {
        $role = $this->authRoleService->updateStatus(
            $authRole,
            (int) $request->validated('role_status')
        );

        return ApiResponseHelper::success(
            (new AuthRoleResource($role))->resolve(),
            '状态更新成功'
        );
    }

    public function destroy(AuthRole $authRole): JsonResponse
    {
        try {
            $this->authRoleService->delete($authRole);

            return ApiResponseHelper::success(null, '删除成功');
        } catch (BusinessException $e) {
            return ApiResponseHelper::error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return ApiResponseHelper::error(810099, '删除失败');
        }
    }

    public function grant(AuthRole $authRole): JsonResponse
    {
        return ApiResponseHelper::success($this->authRoleService->getGrant($authRole));
    }

    public function syncGrant(\App\Http\Requests\backend\AuthRoleGrantRequest $request, AuthRole $authRole): JsonResponse
    {
        try {
            $data = $request->validated();
            $grant = $this->authRoleService->syncGrant(
                $authRole,
                $data['menu_ids'] ?? [],
                $data['permission_ids'] ?? []
            );

            return ApiResponseHelper::success($grant, '授权成功');
        } catch (BusinessException $e) {
            return ApiResponseHelper::error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return ApiResponseHelper::error(810098, '授权失败');
        }
    }

    public function syncMenus(\App\Http\Requests\backend\AuthRoleGrantRequest $request, AuthRole $authRole): JsonResponse
    {
        try {
            $grant = $this->authRoleService->syncMenus(
                $authRole,
                $request->validated('menu_ids')
            );

            return ApiResponseHelper::success($grant, '菜单授权成功');
        } catch (BusinessException $e) {
            return ApiResponseHelper::error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return ApiResponseHelper::error(810098, '菜单授权失败');
        }
    }

    public function syncPermissions(\App\Http\Requests\backend\AuthRoleGrantRequest $request, AuthRole $authRole): JsonResponse
    {
        try {
            $grant = $this->authRoleService->syncPermissions(
                $authRole,
                $request->validated('permission_ids')
            );

            return ApiResponseHelper::success($grant, '权限授权成功');
        } catch (BusinessException $e) {
            return ApiResponseHelper::error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return ApiResponseHelper::error(810098, '权限授权失败');
        }
    }
}
