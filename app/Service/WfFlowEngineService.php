<?php

namespace App\Service;

use App\Enums\HrIsMain;
use App\Enums\HrLeaderRoleType;
use App\Enums\WfActionType;
use App\Enums\WfApproveType;
use App\Enums\WfConditionOperator;
use App\Enums\WfNodeMode;
use App\Models\HrDeptLeader;
use App\Models\HrUserDeptPost;
use App\Models\UserAccount;
use App\Models\WfFlowApply;
use App\Models\WfFlowApproveRecord;
use App\Models\WfFlowDefinition;
use App\Models\WfFlowNode;
use App\Models\WfFlowNodeCondition;
use Illuminate\Support\Collection;

class WfFlowEngineService
{
    public function resolveMainDeptId(int|string $userId): int
    {
        $main = HrUserDeptPost::query()
            ->where('user_id', $userId)
            ->where('is_main', HrIsMain::Main)
            ->first();

        return (int) ($main?->dept_id ?? 0);
    }

    /**
     * @return list<string>
     */
    public function resolveApproverIds(WfFlowNode $node, WfFlowApply $apply, array $extraUserIds = []): array
    {
        $ids = match ($node->approve_type) {
            WfApproveType::DirectLeader, WfApproveType::DeptLeader => $this->resolveDeptLeaderIds($apply->apply_user_id, $apply->dept_id),
            WfApproveType::FixedUsers => array_map('strval', $node->approve_target ?? []),
            WfApproveType::Roles => $this->resolveRoleUserIds($node->approve_target ?? []),
            WfApproveType::ApplicantSelect => array_map('strval', $extraUserIds ?: ($apply->form_data['_selected_approvers'] ?? [])),
            default => [],
        };

        $ids = array_values(array_unique(array_filter($ids, fn ($id) => (string) $id !== '0' && $id !== '')));

        return $ids;
    }

    /**
     * @return list<string>
     */
    private function resolveDeptLeaderIds(int|string $userId, int|string $deptId): array
    {
        $deptId = (int) $deptId;
        if ($deptId <= 0) {
            $deptId = $this->resolveMainDeptId($userId);
        }
        if ($deptId <= 0) {
            return [];
        }

        $leaderIds = HrDeptLeader::query()
            ->where('dept_id', $deptId)
            ->where('role_type', HrLeaderRoleType::Primary)
            ->pluck('user_id')
            ->map(fn ($id) => (string) $id)
            ->all();

        if ($leaderIds) {
            return $leaderIds;
        }

        $fallback = \App\Models\HrDepartment::query()->where('id', $deptId)->value('leader_user_id');

        return $fallback ? [(string) $fallback] : [];
    }

    /**
     * @param  list<int|string>  $roleIds
     * @return list<string>
     */
    private function resolveRoleUserIds(array $roleIds): array
    {
        if ($roleIds === []) {
            return [];
        }

        return UserAccount::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('auth_role.id', $roleIds))
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();
    }

    public function firstNode(WfFlowDefinition $definition, array $formData): ?WfFlowNode
    {
        $definition->loadMissing(['nodes', 'conditions']);
        $jumpId = $this->evaluateJump($definition, 0, $formData);
        if ($jumpId) {
            return $definition->nodes->firstWhere('id', $jumpId);
        }

        return $definition->nodes->sortBy('node_sort')->first();
    }

    public function nextNode(WfFlowDefinition $definition, WfFlowNode $current, array $formData): ?WfFlowNode
    {
        $definition->loadMissing(['nodes', 'conditions']);
        $jumpId = $this->evaluateJump($definition, (int) $current->id, $formData);
        if ($jumpId) {
            return $definition->nodes->firstWhere('id', $jumpId);
        }

        return $definition->nodes
            ->where('node_sort', '>', $current->node_sort)
            ->sortBy('node_sort')
            ->first();
    }

    private function evaluateJump(WfFlowDefinition $definition, int $preNodeId, array $formData): int
    {
        /** @var Collection<int, WfFlowNodeCondition> $conditions */
        $conditions = $definition->conditions->where('pre_node_id', $preNodeId);
        foreach ($conditions as $condition) {
            if ($this->matchCondition($condition, $formData)) {
                return (int) $condition->jump_node_id;
            }
        }

        return 0;
    }

    private function matchCondition(WfFlowNodeCondition $condition, array $formData): bool
    {
        $field = $condition->condition_field;
        $left = $formData[$field] ?? null;
        $right = $condition->condition_value;
        $op = $condition->condition_operator;

        if ($left === null || $op === null) {
            return false;
        }

        $leftNum = is_numeric($left) ? (float) $left : null;
        $rightNum = is_numeric($right) ? (float) $right : null;
        $numeric = $leftNum !== null && $rightNum !== null;

        return match ($op) {
            WfConditionOperator::Gt => $numeric && $leftNum > $rightNum,
            WfConditionOperator::Gte => $numeric && $leftNum >= $rightNum,
            WfConditionOperator::Lt => $numeric && $leftNum < $rightNum,
            WfConditionOperator::Lte => $numeric && $leftNum <= $rightNum,
            WfConditionOperator::Eq => $numeric ? $leftNum == $rightNum : (string) $left === (string) $right,
            WfConditionOperator::Neq => $numeric ? $leftNum != $rightNum : (string) $left !== (string) $right,
            default => false,
        };
    }

    /**
     * @return list<string>
     */
    public function pendingApproverIds(WfFlowApply $apply, WfFlowNode $node): array
    {
        $all = $this->resolveApproverIds($node, $apply);
        if ($node->node_mode === WfNodeMode::OrSign) {
            return $all;
        }

        $acted = WfFlowApproveRecord::query()
            ->where('apply_id', $apply->id)
            ->where('node_id', $node->id)
            ->where('action_type', WfActionType::Agree)
            ->pluck('approve_user_id')
            ->map(fn ($id) => (string) $id)
            ->all();

        return array_values(array_diff($all, $acted));
    }

    public function canUserApprove(WfFlowApply $apply, WfFlowNode $node, int|string $userId): bool
    {
        $uid = (string) $userId;
        if ((string) $apply->current_approve_uid === $uid) {
            return true;
        }

        $last = WfFlowApproveRecord::query()
            ->where('apply_id', $apply->id)
            ->where('node_id', $node->id)
            ->orderByDesc('id')
            ->first();

        // 转审/加签后仅当前待办人可处理
        if ($last && in_array($last->action_type, [WfActionType::Transfer, WfActionType::AddSign], true)) {
            return false;
        }

        return in_array($uid, $this->pendingApproverIds($apply, $node), true);
    }

    public function isNodeCompleted(WfFlowApply $apply, WfFlowNode $node): bool
    {
        $pending = $this->pendingApproverIds($apply, $node);
        if ($node->node_mode === WfNodeMode::OrSign) {
            return WfFlowApproveRecord::query()
                ->where('apply_id', $apply->id)
                ->where('node_id', $node->id)
                ->where('action_type', WfActionType::Agree)
                ->exists();
        }

        return $pending === [];
    }

    public function writeRecord(
        WfFlowApply $apply,
        int|string $nodeId,
        int|string $userId,
        WfActionType $action,
        string $opinion = '',
        int|string $targetUserId = 0,
        array $attachFiles = []
    ): WfFlowApproveRecord {
        return WfFlowApproveRecord::query()->create([
            'apply_id' => $apply->id,
            'node_id' => $nodeId,
            'approve_user_id' => $userId,
            'action_type' => $action,
            'target_user_id' => $targetUserId,
            'approve_opinion' => $opinion,
            'attach_files' => $attachFiles,
            'operate_at' => now(),
        ]);
    }
}
