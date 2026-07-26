<?php

namespace App\Http\Controllers\backend;

use App\Constants\Code\UserError;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\AuthRequest;
use App\Http\Resources\backend\UserAccountResource;
use App\Service\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AuthController extends AbstractController
{
    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    public function register(AuthRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->register(
                $request->validated(),
                $request->ip() ?? '',
                (string) $request->input('register_device', ''),
                (string) $request->input('register_channel', 'web'),
            );

            return $this->success(
                (new UserAccountResource($user))->resolve(),
                '注册成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(UserError::AUTH_REGISTER_FAILED, '注册失败');
        }
    }

    public function login(AuthRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = $this->authService->login(
                $data['account'],
                $data['password'],
                $request->ip() ?? '',
                ''
            );

            $request->session()->regenerate();

            $user->load('roles');

            return $this->success(
                (new UserAccountResource($user))->resolve(),
                '登录成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(UserError::AUTH_LOGIN_FAILED, '登录失败');
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout();

        return $this->success(null, '已退出登录');
    }

    public function me(): JsonResponse
    {
        $user = $this->authService->currentUser();
        if (! $user) {
            return $this->error(UserError::AUTH_NOT_LOGGED_IN, '未登录');
        }

        $user->load(['roles.permissions']);

        $permissionCodes = $user->roles
            ->flatMap(fn ($role) => $role->permissions->pluck('per_code'))
            ->unique()
            ->values()
            ->all();

        $data = (new UserAccountResource($user))->resolve();
        $data['permission_codes'] = $user->isSuperAdmin() ? ['*'] : $permissionCodes;
        $data['is_super_admin'] = $user->isSuperAdmin();

        return $this->success($data);
    }
}
