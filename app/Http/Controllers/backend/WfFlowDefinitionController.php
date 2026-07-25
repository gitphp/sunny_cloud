<?php

namespace App\Http\Controllers\backend;

use App\Enums\WfApproveType;
use App\Enums\WfConditionOperator;
use App\Enums\WfFieldType;
use App\Enums\WfNodeMode;
use App\Enums\WfPublishStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\WfFlowDefinitionRequest;
use App\Models\WfFlowDefinition;
use App\Service\WfFlowDefinitionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class WfFlowDefinitionController extends AbstractController
{
    public function __construct(
        private readonly WfFlowDefinitionService $wfFlowDefinitionService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->wfFlowDefinitionService->paginate(
            $request->only(['keyword', 'flow_type_id', 'is_publish']),
            (int) $request->query('per_page', 15)
        );

        return $this->success([
            'list' => collect($paginator->items())
                ->map(fn (WfFlowDefinition $item) => $this->wfFlowDefinitionService->toListArray($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'is_publish' => WfPublishStatus::labels(),
                'field_type' => WfFieldType::labels(),
                'approve_type' => WfApproveType::labels(),
                'node_mode' => WfNodeMode::labels(),
                'condition_operator' => WfConditionOperator::labels(),
            ],
        ]);
    }

    public function show(WfFlowDefinition $wfFlowDefinition): JsonResponse
    {
        return $this->success($this->wfFlowDefinitionService->detail($wfFlowDefinition));
    }

    public function store(WfFlowDefinitionRequest $request): JsonResponse
    {
        try {
            $definition = $this->wfFlowDefinitionService->create($request->validated());

            return $this->success($this->wfFlowDefinitionService->detail($definition), '添加成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(870099, '添加失败');
        }
    }

    public function update(WfFlowDefinitionRequest $request, WfFlowDefinition $wfFlowDefinition): JsonResponse
    {
        try {
            $definition = $this->wfFlowDefinitionService->update($wfFlowDefinition, $request->validated());

            return $this->success($this->wfFlowDefinitionService->detail($definition), '修改成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(870099, '修改失败');
        }
    }

    public function publish(WfFlowDefinition $wfFlowDefinition): JsonResponse
    {
        try {
            $definition = $this->wfFlowDefinitionService->publish($wfFlowDefinition);

            return $this->success($this->wfFlowDefinitionService->detail($definition), '发布成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(870098, '发布失败');
        }
    }

    public function unpublish(WfFlowDefinition $wfFlowDefinition): JsonResponse
    {
        $definition = $this->wfFlowDefinitionService->unpublish($wfFlowDefinition);

        return $this->success($this->wfFlowDefinitionService->detail($definition), '已设为草稿');
    }

    public function destroy(WfFlowDefinition $wfFlowDefinition): JsonResponse
    {
        try {
            $this->wfFlowDefinitionService->delete($wfFlowDefinition);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(870099, '删除失败');
        }
    }
}
