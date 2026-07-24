<?php

namespace App\Http\Controllers\backend;

use App\Enums\RealAuthStatus;
use App\Enums\UserStatus;
use App\Exceptions\BusinessException;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\backend\UserAccountRequest;
use App\Http\Resources\backend\UserAccountResource;
use App\Models\UserAccount;
use App\Service\UserAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class UserAccountController extends Controller
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

        return ApiResponseHelper::success([
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

            return ApiResponseHelper::success(
                (new UserAccountResource($user))->resolve(),
                '添加成功'
            );
        } catch (BusinessException $e) {
            return ApiResponseHelper::error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            return ApiResponseHelper::error(2001097, '添加失败');
        }
    }

    public function show(UserAccount $userAccount): JsonResponse
    {
        return ApiResponseHelper::success(
            (new UserAccountResource($userAccount))->resolve()
        );
    }

    public function update(UserAccountRequest $request, UserAccount $userAccount): JsonResponse
    {
        try {
            $user = $this->userAccountService->update($userAccount, $request->validated());

            return ApiResponseHelper::success(
                (new UserAccountResource($user))->resolve(),
                '修改成功'
            );
        } catch (BusinessException $e) {
            return ApiResponseHelper::error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            return ApiResponseHelper::error(2001096, '修改失败');
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

        return ApiResponseHelper::success(
            (new UserAccountResource($user))->resolve(),
            '状态更新成功'
        );
    }

    public function destroy(UserAccount $userAccount): JsonResponse
    {
        try {
            $this->userAccountService->delete($userAccount);

            return ApiResponseHelper::success(null, '删除成功');
        } catch (Throwable $e) {
            return ApiResponseHelper::error(2001095, '删除失败');
        }
    }
}
