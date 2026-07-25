<?php

namespace App\Http\Controllers\backend;

use App\Enums\RealAuthStatus;
use App\Enums\UserStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\UserAccountRequest;
use App\Http\Resources\backend\UserAccountResource;
use App\Models\UserAccount;
use App\Service\UserAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class UserAccountController extends AbstractController
{
    public function __construct(
        private readonly UserAccountService $userAccountService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->userAccountService->paginate(
            $request->only(['keyword', 'user_status', 'real_auth_status']),
            (int) $request->query('per_page', 15)
        );

        $paginator->getCollection()->load('roles');

        return $this->success([
            'list' => UserAccountResource::collection($paginator->items())->resolve(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'user_status' => UserStatus::labels(),
                'real_auth_status' => RealAuthStatus::labels(),
            ],
        ]);
    }

    public function store(UserAccountRequest $request): JsonResponse
    {
        try {
            $user = $this->userAccountService->create($request->validated());

            return $this->success(
                (new UserAccountResource($user))->resolve(),
                '添加成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            return $this->error(2001097, '添加失败');
        }
    }

    public function show(UserAccount $userAccount): JsonResponse
    {
        return $this->success(
            (new UserAccountResource($userAccount))->resolve()
        );
    }

    public function update(UserAccountRequest $request, UserAccount $userAccount): JsonResponse
    {
        try {
            $user = $this->userAccountService->update($userAccount, $request->validated());

            return $this->success(
                (new UserAccountResource($user))->resolve(),
                '修改成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            return $this->error(2001096, '修改失败');
        }
    }

    public function updateStatus(UserAccountRequest $request, UserAccount $userAccount): JsonResponse
    {
        $data = $request->validated();
        $user = $this->userAccountService->updateStatus(
            $userAccount,
            (int) $data['user_status'],
            (string) ($data['lock_reason'] ?? ''),
            $data['lock_expire_time'] ?? null
        );

        return $this->success(
            (new UserAccountResource($user))->resolve(),
            '状态更新成功'
        );
    }

    public function destroy(UserAccount $userAccount): JsonResponse
    {
        try {
            $this->userAccountService->delete($userAccount);

            return $this->success(null, '删除成功');
        } catch (Throwable $e) {
            return $this->error(2001095, '删除失败');
        }
    }

    public function roles(UserAccount $userAccount): JsonResponse
    {
        $userAccount->load('roles');

        return $this->success([
            'role_ids' => $this->userAccountService->getRoleIds($userAccount),
            'roles' => $userAccount->roles->map(fn ($role) => [
                'id' => (string) $role->id,
                'role_name' => $role->role_name,
                'role_code' => $role->role_code,
            ])->values()->all(),
        ]);
    }

    public function syncRoles(\App\Http\Requests\backend\UserRoleRequest $request, UserAccount $userAccount): JsonResponse
    {
        try {
            $roleIds = $this->userAccountService->syncRoles(
                $userAccount,
                $request->validated('role_ids')
            );

            return $this->success(['role_ids' => $roleIds], '角色分配成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(2001094, '角色分配失败');
        }
    }
}
