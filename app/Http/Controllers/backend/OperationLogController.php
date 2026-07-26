<?php

namespace App\Http\Controllers\backend;

use App\Enums\OperationAction;
use App\Enums\OperationBizType;
use App\Enums\OperatorStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\OperationLogRequest;
use App\Http\Resources\backend\OperationLogResource;
use App\Models\OperationLog;
use App\Service\OperationLogService;
use Illuminate\Http\JsonResponse;
use Throwable;

class OperationLogController extends AbstractController
{
    public function __construct(
        private readonly OperationLogService $operationLogService
    ) {
    }

    public function index(OperationLogRequest $request): JsonResponse
    {
        $paginator = $this->operationLogService->paginate(
            $request->validated(),
            (int) $request->query('per_page', 15)
        );

        return $this->success([
            'list' => collect($paginator->items())
                ->map(fn (OperationLog $item) => $this->operationLogService->toArray($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'action' => OperationAction::labels(),
                'operator_status' => OperatorStatus::labels(),
                'biz_type' => OperationBizType::labels(),
            ],
        ]);
    }

    public function show(OperationLog $operationLog): JsonResponse
    {
        try {
            return $this->success(
                (new OperationLogResource($operationLog))->resolve()
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(910099, '获取日志详情失败');
        }
    }
}
