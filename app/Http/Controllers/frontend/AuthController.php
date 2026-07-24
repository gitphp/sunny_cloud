<?php

namespace App\Http\Controllers\frontend;

use App\Exceptions\BusinessException;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\frontend\AuthRequest;
use App\Http\Resources\backend\UserAccountResource;
use App\Service\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AuthController extends Controller
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

            return ApiResponseHelper::success(
                (new UserAccountResource($user))->resolve(),
                '注册成功'
            );
        } catch (BusinessException $e) {
            return ApiResponseHelper::error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            return ApiResponseHelper::error(2001099, '注册失败');
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
                '',
                'frontend'
            );

            $request->session()->regenerate();

            return ApiResponseHelper::success(
                (new UserAccountResource($user))->resolve(),
                '登录成功'
            );
        } catch (BusinessException $e) {
            return ApiResponseHelper::error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            return ApiResponseHelper::error(2001098, '登录失败');
        }
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout('frontend');

        return ApiResponseHelper::success(null, '已退出登录');
    }

    public function me(): JsonResponse
    {
        $user = $this->authService->currentUser('frontend');
        if (! $user) {
            return ApiResponseHelper::error(2001003, '未登录');
        }

        return ApiResponseHelper::success((new UserAccountResource($user))->resolve());
    }
}
