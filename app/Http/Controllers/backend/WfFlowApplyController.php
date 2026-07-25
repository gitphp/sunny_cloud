<?php

namespace App\Http\Controllers\backend;

use App\Enums\WfActionType;
use App\Enums\WfApplyStatus;
use App\Enums\WfCcReadStatus;
use App\Enums\WfPublishStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\WfFlowApplyRequest;
use App\Models\WfFlowApply;
use App\Models\WfFlowCcUser;
use App\Models\WfFlowDefinition;
use App\Service\WfFlowApplyService;
use App\Service\WfFlowApproveService;
use App\Service\WfFlowDefinitionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class WfFlowApplyController extends AbstractController
{
    public function __construct(
        private readonly WfFlowApplyService $applyService,
        private readonly WfFlowApproveService $approveService,
        private readonly WfFlowDefinitionService $definitionService
    ) {
    }

    public function mine(Request $request): JsonResponse
    {
        $paginator = $this->applyService->paginateMine(
            $request->only(['keyword', 'flow_type_id', 'apply_status']),
            (int) $request->query('per_page', 15)
        );

        return $this->success([
            'list' => collect($paginator->items())
                ->map(fn (WfFlowApply $item) => $this->applyService->toListArray($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'apply_status' => WfApplyStatus::labels(),
            ],
        ]);
    }

    public function todo(Request $request): JsonResponse
    {
        $paginator = $this->applyService->paginateTodo(
            array_merge($request->only(['keyword', 'flow_type_id', 'apply_status']), [
                'page' => (int) $request->query('page', 1),
            ]),
            (int) $request->query('per_page', 15)
        );

        return $this->success([
            'list' => collect($paginator->items())
                ->map(fn (WfFlowApply $item) => $this->applyService->toListArray($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'apply_status' => WfApplyStatus::labels(),
                'action_type' => WfActionType::labels(),
            ],
        ]);
    }

    public function ccList(Request $request): JsonResponse
    {
        $paginator = $this->applyService->paginateCc(
            $request->only(['keyword', 'is_read']),
            (int) $request->query('per_page', 15)
        );

        return $this->success([
            'list' => collect($paginator->items())
                ->map(fn (WfFlowCcUser $item) => $this->applyService->ccToListArray($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'is_read' => WfCcReadStatus::labels(),
                'apply_status' => WfApplyStatus::labels(),
            ],
        ]);
    }

    public function publishedDefinitions(Request $request): JsonResponse
    {
        $query = WfFlowDefinition::query()
            ->with('flowType:id,type_name,type_code')
            ->where('is_publish', WfPublishStatus::Published)
            ->orderByDesc('id');

        if (! empty($request->query('flow_type_id'))) {
            $query->where('flow_type_id', $request->query('flow_type_id'));
        }

        $list = $query->get()->map(fn (WfFlowDefinition $d) => [
            'id' => (string) $d->id,
            'flow_name' => $d->flow_name,
            'flow_type_id' => (string) $d->flow_type_id,
            'type_name' => $d->flowType?->type_name,
            'version' => (int) $d->version,
        ])->values()->all();

        return $this->success($list);
    }

    public function definitionDetail(WfFlowDefinition $wfFlowDefinition): JsonResponse
    {
        if ($wfFlowDefinition->is_publish !== WfPublishStatus::Published) {
            return $this->error(880003, '流程模板未发布');
        }

        return $this->success($this->definitionService->detail($wfFlowDefinition));
    }

    public function show(WfFlowApply $wfFlowApply): JsonResponse
    {
        return $this->success($this->applyService->detail($wfFlowApply));
    }

    public function store(WfFlowApplyRequest $request): JsonResponse
    {
        try {
            $apply = $this->applyService->createDraft($request->validated());

            return $this->success($this->applyService->detail($apply), '草稿已保存');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(880099, '保存失败');
        }
    }

    public function update(WfFlowApplyRequest $request, WfFlowApply $wfFlowApply): JsonResponse
    {
        try {
            $apply = $this->applyService->updateDraft($wfFlowApply, $request->validated());

            return $this->success($this->applyService->detail($apply), '保存成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(880099, '保存失败');
        }
    }

    public function submit(WfFlowApplyRequest $request, WfFlowApply $wfFlowApply): JsonResponse
    {
        try {
            $apply = $this->applyService->submit($wfFlowApply, $request->validated());

            return $this->success($this->applyService->detail($apply), '提交成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(880098, '提交失败');
        }
    }

    public function withdraw(WfFlowApply $wfFlowApply): JsonResponse
    {
        try {
            $apply = $this->applyService->withdraw($wfFlowApply);

            return $this->success($this->applyService->detail($apply), '已撤回');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(880098, '撤回失败');
        }
    }

    public function void(WfFlowApply $wfFlowApply): JsonResponse
    {
        try {
            $apply = $this->applyService->void($wfFlowApply);

            return $this->success($this->applyService->detail($apply), '已作废');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(880098, '作废失败');
        }
    }

    public function destroy(WfFlowApply $wfFlowApply): JsonResponse
    {
        try {
            $this->applyService->delete($wfFlowApply);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(880099, '删除失败');
        }
    }

    public function agree(WfFlowApplyRequest $request, WfFlowApply $wfFlowApply): JsonResponse
    {
        try {
            return $this->success($this->approveService->agree($wfFlowApply, $request->validated()), '审批通过');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(890099, '审批失败');
        }
    }

    public function reject(WfFlowApplyRequest $request, WfFlowApply $wfFlowApply): JsonResponse
    {
        try {
            return $this->success($this->approveService->reject($wfFlowApply, $request->validated()), '已驳回');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(890099, '驳回失败');
        }
    }

    public function transfer(WfFlowApplyRequest $request, WfFlowApply $wfFlowApply): JsonResponse
    {
        try {
            return $this->success($this->approveService->transfer($wfFlowApply, $request->validated()), '转审成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(890099, '转审失败');
        }
    }

    public function addSign(WfFlowApplyRequest $request, WfFlowApply $wfFlowApply): JsonResponse
    {
        try {
            return $this->success($this->approveService->addSign($wfFlowApply, $request->validated()), '加签成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(890099, '加签失败');
        }
    }

    public function markCcRead(WfFlowCcUser $wfFlowCcUser): JsonResponse
    {
        try {
            $cc = $this->applyService->markCcRead($wfFlowCcUser);

            return $this->success($this->applyService->ccToListArray($cc), '已标记已读');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(880099, '操作失败');
        }
    }
}
