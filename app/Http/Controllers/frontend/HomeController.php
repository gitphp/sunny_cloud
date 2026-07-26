<?php

namespace App\Http\Controllers\frontend;

use App\Service\HomeService;
use Illuminate\Http\JsonResponse;
use Throwable;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly HomeService $homeService
    ) {
    }

    public function index(): JsonResponse
    {
        try {
            return $this->success($this->homeService->index());
        } catch (Throwable $e) {
            report($e);

            return $this->error(900098, '首页数据加载失败');
        }
    }
}
