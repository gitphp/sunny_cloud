<?php

namespace App\Service;

use App\Constants\Code\CodePrefix;
use App\Constants\Code\WfFlowApproveError;
use App\Enums\WfActionType;
use App\Enums\WfApplyStatus;
use App\Exceptions\BusinessException;
use App\Models\UserAccount;
use App\Models\WfFlowApply;
use App\Models\WfFlowNode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WfFlowApproveService
{
    public function __construct(
        private readonly WfFlowEngineService $engine,
        private readonly WfFlowApplyService $applyService
    ) {
    }

    public function agree(WfFlowApply $apply, array $data = []): array
    {
        return DB::transaction(function () use ($apply, $data) {
            [$node, $userId] = $this->assertCanAct($apply);

            if ($this->hasAgreed($apply, $node, $userId)) {
                throw new BusinessException($this->approveCode(WfFlowApproveError::ALREADY_ACTED), '您已审批通过该节点');
            }

            $this->engine->writeRecord(
                $apply,
                $node->id,
                $userId,
                WfActionType::Agree,
                (string) ($data['approve_opinion'] ?? ''),
                0,
                $data['attach_files'] ?? []
            );

            if (! $this->engine->isNodeCompleted($apply->fresh(), $node)) {
                $pending = $this->engine->pendingApproverIds($apply->fresh(), $node);
                $apply->current_approve_uid = $pending[0] ?? 0;
                $apply->save();

                return $this->applyService->detail($apply->fresh());
            }

            $definition = $apply->definition()->with(['nodes', 'conditions'])->first();
            $next = $this->engine->nextNode($definition, $node, $apply->form_data ?? []);

            if (! $next) {
                $apply->apply_status = WfApplyStatus::Approved;
                $apply->current_node_id = 0;
                $apply->current_approve_uid = 0;
                $apply->save();

                return $this->applyService->detail($apply->fresh());
            }

            $approvers = $this->engine->resolveApproverIds($next, $apply);
            if ($approvers === []) {
                throw new BusinessException($this->approveCode(WfFlowApproveError::NO_APPROVER), '下一节点未找到审批人');
            }

            $apply->current_node_id = $next->id;
            $apply->current_approve_uid = $approvers[0];
            $apply->save();

            return $this->applyService->detail($apply->fresh());
        });
    }

    public function reject(WfFlowApply $apply, array $data = []): array
    {
        return DB::transaction(function () use ($apply, $data) {
            [$node, $userId] = $this->assertCanAct($apply);
            if ((int) $node->can_reject !== 1) {
                throw new BusinessException($this->approveCode(WfFlowApproveError::NODE_NOT_ALLOW_REJECT), '当前节点不允许驳回');
            }

            $this->engine->writeRecord(
                $apply,
                $node->id,
                $userId,
                WfActionType::Reject,
                (string) ($data['approve_opinion'] ?? '驳回'),
                0,
                $data['attach_files'] ?? []
            );

            $backNodeId = (int) ($node->back_node_id ?? 0);
            if ($backNodeId > 0) {
                $back = WfFlowNode::query()->where('id', $backNodeId)->where('flow_def_id', $apply->flow_def_id)->first();
                if ($back) {
                    $approvers = $this->engine->resolveApproverIds($back, $apply);
                    $apply->apply_status = WfApplyStatus::Pending;
                    $apply->current_node_id = $back->id;
                    $apply->current_approve_uid = $approvers[0] ?? 0;
                    $apply->save();

                    return $this->applyService->detail($apply->fresh());
                }
            }

            $apply->apply_status = WfApplyStatus::Rejected;
            $apply->current_node_id = 0;
            $apply->current_approve_uid = 0;
            $apply->save();

            return $this->applyService->detail($apply->fresh());
        });
    }

    public function transfer(WfFlowApply $apply, array $data): array
    {
        return DB::transaction(function () use ($apply, $data) {
            [$node, $userId] = $this->assertCanAct($apply);
            if ((int) $node->can_transfer !== 1) {
                throw new BusinessException($this->approveCode(WfFlowApproveError::NODE_NOT_ALLOW_TRANSFER), '当前节点不允许转审');
            }

            $targetId = $data['target_user_id'] ?? 0;
            if (! $targetId || ! UserAccount::query()->where('id', $targetId)->exists()) {
                throw new BusinessException($this->approveCode(WfFlowApproveError::TARGET_REQUIRED), '请选择转审目标人');
            }

            $this->engine->writeRecord(
                $apply,
                $node->id,
                $userId,
                WfActionType::Transfer,
                (string) ($data['approve_opinion'] ?? '转审'),
                $targetId,
                $data['attach_files'] ?? []
            );

            $apply->current_approve_uid = $targetId;
            $apply->save();

            return $this->applyService->detail($apply->fresh());
        });
    }

    public function addSign(WfFlowApply $apply, array $data): array
    {
        return DB::transaction(function () use ($apply, $data) {
            [$node, $userId] = $this->assertCanAct($apply);
            if ((int) $node->can_add_sign !== 1) {
                throw new BusinessException($this->approveCode(WfFlowApproveError::NODE_NOT_ALLOW_ADD_SIGN), '当前节点不允许加签');
            }

            $targetId = $data['target_user_id'] ?? 0;
            if (! $targetId || ! UserAccount::query()->where('id', $targetId)->exists()) {
                throw new BusinessException($this->approveCode(WfFlowApproveError::TARGET_REQUIRED), '请选择加签目标人');
            }

            $this->engine->writeRecord(
                $apply,
                $node->id,
                $userId,
                WfActionType::AddSign,
                (string) ($data['approve_opinion'] ?? '加签'),
                $targetId,
                $data['attach_files'] ?? []
            );

            // 加签后转给目标人先审
            $apply->current_approve_uid = $targetId;
            $apply->save();

            return $this->applyService->detail($apply->fresh());
        });
    }

    /**
     * @return array{0: WfFlowNode, 1: int|string}
     */
    private function assertCanAct(WfFlowApply $apply): array
    {
        if ($apply->apply_status !== WfApplyStatus::Pending) {
            throw new BusinessException($this->approveCode(WfFlowApproveError::STATUS_INVALID), '单据不在审批中');
        }

        $node = $apply->currentNode ?: WfFlowNode::query()->find($apply->current_node_id);
        if (! $node) {
            throw new BusinessException($this->approveCode(WfFlowApproveError::NOT_FOUND), '当前审批节点不存在');
        }

        $userId = Auth::guard('backend')->id();
        $isTransferTarget = (string) $apply->current_approve_uid === (string) $userId;
        if (! $isTransferTarget && ! $this->engine->canUserApprove($apply, $node, $userId)) {
            throw new BusinessException($this->approveCode(WfFlowApproveError::NO_PERMISSION), '您不是当前审批人');
        }

        return [$node, $userId];
    }

    private function hasAgreed(WfFlowApply $apply, WfFlowNode $node, int|string $userId): bool
    {
        return $apply->records()
            ->where('node_id', $node->id)
            ->where('approve_user_id', $userId)
            ->where('action_type', WfActionType::Agree)
            ->exists();
    }

    private function approveCode(int $error): int
    {
        return CodePrefix::WF_FLOW_APPROVE * 1000 + $error;
    }
}
