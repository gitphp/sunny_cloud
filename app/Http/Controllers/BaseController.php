<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use Illuminate\Http\JsonResponse;

abstract class BaseController extends Controller
{
    protected function success(mixed $data = null, string $message = 'ok', int $code = 0): JsonResponse
    {
        return ApiResponseHelper::success($data, $message, $code);
    }

    protected function error(int $code = 1001001, string $message = 'error', mixed $data = null): JsonResponse
    {
        return ApiResponseHelper::error($code, $message, $data);
    }
}
