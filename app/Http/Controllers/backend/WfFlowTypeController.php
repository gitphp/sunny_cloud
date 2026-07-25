<?php

namespace App\Http\Controllers\backend;

use App\Enums\WfStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\WfFlowTypeRequest;
use App\Models\WfFlowType;
use App\Service\WfFlowTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class WfFlowTypeController extends AbstractController
{
    public function __construct(
        private readonly WfFlowTypeService $wfFlowTypeService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->wfFlowTypeService->paginate(
            $request->only(['keyword', 'status']),
            (int) $request->query('per_page', 15)
        );

        return $this->success([
            'list' => collect($paginator->items())
                ->map(fn (WfFlowType $item) => $this->wfFlowTypeService->toArray($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'status' => WfStatus::labels(),
            ],
        ]);
    }

    public function options(): JsonResponse
    {
        return $this->success($this->wfFlowTypeService->allEnabled());
    }

    public function store(WfFlowTypeRequest $request): JsonResponse
    {
        try {
            $type = $this->wfFlowTypeService->create($request->validated());

            return $this->success($this->wfFlowTypeService->toArray($type), '添加成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(860099, '添加失败');
        }
    }

    public function update(WfFlowTypeRequest $request, WfFlowType $wfFlowType): JsonResponse
    {
        try {
            $type = $this->wfFlowTypeService->update($wfFlowType, $request->validated());

            return $this->success($this->wfFlowTypeService->toArray($type), '修改成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(860099, '修改失败');
        }
    }

    public function updateSort(WfFlowTypeRequest $request, WfFlowType $wfFlowType): JsonResponse
    {
        $type = $this->wfFlowTypeService->updateSort($wfFlowType, (int) $request->validated('sort'));

        return $this->success($this->wfFlowTypeService->toArray($type), '排序更新成功');
    }

    public function updateStatus(WfFlowTypeRequest $request, WfFlowType $wfFlowType): JsonResponse
    {
        $type = $this->wfFlowTypeService->updateStatus($wfFlowType, (int) $request->validated('status'));

        return $this->success($this->wfFlowTypeService->toArray($type), '状态更新成功');
    }

    public function destroy(WfFlowType $wfFlowType): JsonResponse
    {
        try {
            $this->wfFlowTypeService->delete($wfFlowType);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(860099, '删除失败');
        }
    }
}
