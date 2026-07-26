<?php

namespace App\Http\Controllers\frontend;

use App\Constants\Code\UserError;
use App\Exceptions\BusinessException;
use App\Http\Requests\frontend\AuthRequest;
use App\Http\Resources\backend\UserAccountResource;
use App\Service\AuthService;
use App\Service\OperationLogService;
use Illuminate\Http\JsonResponse;
use Throwable;

class AuthController extends AbstractController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly OperationLogService $operationLogService
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
        $data = $request->validated();
        $account = (string) $data['account'];

        try {
            $user = $this->authService->login(
                $account,
                $data['password'],
                $request->ip() ?? '',
                '',
                'frontend'
            );

            $request->session()->regenerate();

            $this->operationLogService->logLogin(
                true,
                $account,
                $user,
                '',
                'FrontendAuthController@login',
                'frontend'
            );

            return $this->success(
                (new UserAccountResource($user))->resolve(),
                '登录成功'
            );
        } catch (BusinessException $e) {
            $this->operationLogService->logLogin(
                false,
                $account,
                null,
                $e->getMessage(),
                'FrontendAuthController@login',
                'frontend'
            );

            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);
            $this->operationLogService->logLogin(
                false,
                $account,
                null,
                '登录失败',
                'FrontendAuthController@login',
                'frontend'
            );

            return $this->error(UserError::AUTH_LOGIN_FAILED, '登录失败');
        }
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout('frontend');

        return $this->success(null, '已退出登录');
    }

    public function me(): JsonResponse
    {
        $user = $this->authService->currentUser('frontend');
        if (! $user) {
            return $this->error(UserError::AUTH_NOT_LOGGED_IN, '未登录');
        }

        return $this->success((new UserAccountResource($user))->resolve());
    }
}
