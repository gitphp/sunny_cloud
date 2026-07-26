<?php

namespace App\Http\Controllers\backend;

use App\Service\DashboardService;
use Illuminate\Http\JsonResponse;
use Throwable;

class DashboardController extends AbstractController
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {
    }

    public function overview(): JsonResponse
    {
        try {
            return $this->success($this->dashboardService->overview());
        } catch (Throwable $e) {
            report($e);

            return $this->error(900099, '加载仪表盘失败');
        }
    }
}
