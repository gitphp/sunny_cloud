<?php

namespace App\Service;

use App\Constants\Code\CodePrefix;
use App\Constants\Code\WfFlowDefinitionError;
use App\Enums\WfApproveType;
use App\Enums\WfConditionOperator;
use App\Enums\WfFieldType;
use App\Enums\WfNodeMode;
use App\Enums\WfPublishStatus;
use App\Exceptions\BusinessException;
use App\Models\WfFlowDefinition;
use App\Models\WfFlowForm;
use App\Models\WfFlowNode;
use App\Models\WfFlowNodeCondition;
use App\Models\WfFlowType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WfFlowDefinitionService
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = WfFlowDefinition::query()
            ->with('flowType:id,type_name,type_code')
            ->orderByDesc('id');

        if (! empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->where('flow_name', 'like', '%'.$kw.'%');
        }
        if (! empty($filters['flow_type_id'])) {
            $query->where('flow_type_id', $filters['flow_type_id']);
        }
        if (isset($filters['is_publish']) && $filters['is_publish'] !== '' && $filters['is_publish'] !== null) {
            $query->where('is_publish', (int) $filters['is_publish']);
        }

        return $query->paginate($perPage);
    }

    public function detail(WfFlowDefinition $definition): array
    {
        $definition->load(['flowType', 'forms', 'nodes', 'conditions']);

        return $this->toDetailArray($definition);
    }

    public function create(array $data): WfFlowDefinition
    {
        $this->assertType($data['flow_type_id'] ?? 0);
        $this->assertNameUnique($data['flow_name'], $data['flow_type_id']);

        return DB::transaction(function () use ($data) {
            $definition = WfFlowDefinition::query()->create([
                'flow_type_id' => $data['flow_type_id'],
                'flow_name' => $data['flow_name'],
                'version' => (int) ($data['version'] ?? 1),
                'remark' => (string) ($data['remark'] ?? ''),
                'apply_scope' => $data['apply_scope'] ?? [],
                'is_publish' => WfPublishStatus::Draft,
                'created_by' => Auth::guard('backend')->id() ?? 0,
            ]);

            $this->syncForms($definition, $data['forms'] ?? []);
            $this->syncNodes($definition, $data['nodes'] ?? []);
            $this->syncConditions($definition, $data['conditions'] ?? []);

            return $definition->fresh(['flowType', 'forms', 'nodes', 'conditions']);
        });
    }

    public function update(WfFlowDefinition $definition, array $data): WfFlowDefinition
    {
        $typeId = $data['flow_type_id'] ?? $definition->flow_type_id;
        $this->assertType($typeId);
        $name = $data['flow_name'] ?? $definition->flow_name;
        $this->assertNameUnique($name, $typeId, (string) $definition->id);

        return DB::transaction(function () use ($definition, $data, $typeId, $name) {
            $definition->fill([
                'flow_type_id' => $typeId,
                'flow_name' => $name,
                'version' => (int) ($data['version'] ?? $definition->version),
                'remark' => (string) ($data['remark'] ?? $definition->remark),
                'apply_scope' => array_key_exists('apply_scope', $data) ? ($data['apply_scope'] ?? []) : $definition->apply_scope,
            ]);
            $definition->save();

            if (array_key_exists('forms', $data)) {
                $this->replaceForms($definition, $data['forms'] ?? []);
            }
            if (array_key_exists('nodes', $data)) {
                $this->replaceNodes($definition, $data['nodes'] ?? []);
            }
            if (array_key_exists('conditions', $data) || array_key_exists('nodes', $data)) {
                $this->replaceConditions($definition, $data['conditions'] ?? []);
            }

            return $definition->fresh(['flowType', 'forms', 'nodes', 'conditions']);
        });
    }

    public function publish(WfFlowDefinition $definition): WfFlowDefinition
    {
        if ($definition->is_publish === WfPublishStatus::Published) {
            throw new BusinessException(
                $this->code(WfFlowDefinitionError::ALREADY_PUBLISHED),
                '流程已发布'
            );
        }
        if (! $definition->nodes()->exists()) {
            throw new BusinessException(
                $this->code(WfFlowDefinitionError::PUBLISH_NEED_NODE),
                '发布前请配置审批节点'
            );
        }

        $definition->is_publish = WfPublishStatus::Published;
        $definition->save();

        return $definition->fresh(['flowType', 'forms', 'nodes', 'conditions']);
    }

    public function unpublish(WfFlowDefinition $definition): WfFlowDefinition
    {
        $definition->is_publish = WfPublishStatus::Draft;
        $definition->save();

        return $definition->fresh(['flowType', 'forms', 'nodes', 'conditions']);
    }

    public function delete(WfFlowDefinition $definition): void
    {
        DB::transaction(function () use ($definition) {
            WfFlowForm::query()->where('flow_def_id', $definition->id)->delete();
            WfFlowNode::query()->where('flow_def_id', $definition->id)->delete();
            WfFlowNodeCondition::query()->where('flow_def_id', $definition->id)->delete();
            $definition->delete();
        });
    }

    public function toListArray(WfFlowDefinition $definition): array
    {
        return [
            'id' => (string) $definition->id,
            'flow_type_id' => (string) $definition->flow_type_id,
            'type_name' => $definition->flowType?->type_name,
            'type_code' => $definition->flowType?->type_code,
            'flow_name' => $definition->flow_name,
            'version' => (int) $definition->version,
            'remark' => $definition->remark,
            'is_publish' => $definition->is_publish?->value,
            'is_publish_label' => $definition->is_publish?->label(),
            'created_at' => optional($definition->created_at)?->format('Y-m-d H:i:s'),
        ];
    }

    public function toDetailArray(WfFlowDefinition $definition): array
    {
        return [
            ...$this->toListArray($definition),
            'apply_scope' => $definition->apply_scope ?? [],
            'forms' => $definition->forms->map(fn (WfFlowForm $f) => $this->formToArray($f))->values()->all(),
            'nodes' => $definition->nodes->map(fn (WfFlowNode $n) => $this->nodeToArray($n))->values()->all(),
            'conditions' => $definition->conditions->map(fn (WfFlowNodeCondition $c) => $this->conditionToArray($c, $definition))->values()->all(),
        ];
    }

    private function code(int $error): int
    {
        return CodePrefix::WF_FLOW_DEFINITION * 1000 + $error;
    }

    private function assertType(mixed $typeId): void
    {
        if (! WfFlowType::query()->where('id', $typeId)->exists()) {
            throw new BusinessException($this->code(WfFlowDefinitionError::TYPE_NOT_FOUND), '流程类型不存在');
        }
    }

    private function assertNameUnique(string $name, mixed $typeId, ?string $excludeId = null): void
    {
        $exists = WfFlowDefinition::query()
            ->where('flow_type_id', $typeId)
            ->where('flow_name', $name)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();
        if ($exists) {
            throw new BusinessException($this->code(WfFlowDefinitionError::NAME_DUPLICATED), '同类型下流程名称已存在');
        }
    }

    private function syncForms(WfFlowDefinition $definition, array $forms): void
    {
        $keys = [];
        foreach ($forms as $i => $row) {
            $key = (string) ($row['field_key'] ?? '');
            if ($key === '') {
                continue;
            }
            if (isset($keys[$key])) {
                throw new BusinessException(
                    $this->code(WfFlowDefinitionError::FIELD_KEY_DUPLICATED),
                    '表单字段标识重复：'.$key
                );
            }
            $keys[$key] = true;

            WfFlowForm::query()->create([
                'flow_def_id' => $definition->id,
                'field_name' => (string) ($row['field_name'] ?? ''),
                'field_key' => $key,
                'field_type' => WfFieldType::from((string) ($row['field_type'] ?? WfFieldType::Input->value)),
                'field_options' => $row['field_options'] ?? [],
                'is_required' => (int) ($row['is_required'] ?? 1),
                'sort' => (int) ($row['sort'] ?? $i),
                'created_at' => now(),
            ]);
        }
    }

    private function replaceForms(WfFlowDefinition $definition, array $forms): void
    {
        WfFlowForm::query()->where('flow_def_id', $definition->id)->delete();
        $this->syncForms($definition, $forms);
    }

    private function syncNodes(WfFlowDefinition $definition, array $nodes): void
    {
        foreach ($nodes as $i => $row) {
            $approveType = WfApproveType::from((int) ($row['approve_type'] ?? WfApproveType::FixedUsers->value));
            $target = $row['approve_target'] ?? [];
            if (in_array($approveType, [WfApproveType::FixedUsers, WfApproveType::Roles], true)
                && (! is_array($target) || $target === [])) {
                throw new BusinessException(
                    $this->code(WfFlowDefinitionError::NODE_TARGET_REQUIRED),
                    '请配置审批目标：'.($row['node_name'] ?? '节点')
                );
            }

            WfFlowNode::query()->create([
                'flow_def_id' => $definition->id,
                'node_name' => (string) ($row['node_name'] ?? ''),
                'node_sort' => (int) ($row['node_sort'] ?? ($i + 1)),
                'approve_type' => $approveType,
                'approve_target' => is_array($target) ? $target : [],
                'node_mode' => WfNodeMode::from((int) ($row['node_mode'] ?? WfNodeMode::OrSign->value)),
                'can_reject' => (int) ($row['can_reject'] ?? 1),
                'can_add_sign' => (int) ($row['can_add_sign'] ?? 1),
                'can_transfer' => (int) ($row['can_transfer'] ?? 1),
                'back_node_id' => $row['back_node_id'] ?? 0,
                'created_at' => now(),
            ]);
        }
    }

    private function replaceNodes(WfFlowDefinition $definition, array $nodes): void
    {
        WfFlowNodeCondition::query()->where('flow_def_id', $definition->id)->delete();
        WfFlowNode::query()->where('flow_def_id', $definition->id)->delete();
        $this->syncNodes($definition, $nodes);
    }

    private function syncConditions(WfFlowDefinition $definition, array $conditions): void
    {
        $definition->load('nodes');
        $sortMap = $definition->nodes->mapWithKeys(fn (WfFlowNode $n) => [(int) $n->node_sort => (string) $n->id])->all();
        $idSet = $definition->nodes->pluck('id')->map(fn ($id) => (string) $id)->all();

        foreach ($conditions as $row) {
            $field = (string) ($row['condition_field'] ?? '');
            if ($field === '') {
                continue;
            }

            $preId = $this->resolveConditionNodeId($row, 'pre_node_id', 'pre_node_sort', $sortMap, $idSet, true);
            $jumpId = $this->resolveConditionNodeId($row, 'jump_node_id', 'jump_node_sort', $sortMap, $idSet, false);
            if ($jumpId <= 0) {
                throw new BusinessException(
                    $this->code(WfFlowDefinitionError::CONDITION_NODE_INVALID),
                    '条件跳转节点无效'
                );
            }

            $operator = (string) ($row['condition_operator'] ?? WfConditionOperator::Gt->value);
            WfFlowNodeCondition::query()->create([
                'flow_def_id' => $definition->id,
                'pre_node_id' => $preId,
                'condition_field' => $field,
                'condition_operator' => WfConditionOperator::from($operator),
                'condition_value' => (string) ($row['condition_value'] ?? ''),
                'jump_node_id' => $jumpId,
                'created_at' => now(),
            ]);
        }
    }

    private function replaceConditions(WfFlowDefinition $definition, array $conditions): void
    {
        WfFlowNodeCondition::query()->where('flow_def_id', $definition->id)->delete();
        $this->syncConditions($definition, $conditions);
    }

    /**
     * @param  array<int, string>  $sortMap
     * @param  list<string>  $idSet
     */
    private function resolveConditionNodeId(
        array $row,
        string $idKey,
        string $sortKey,
        array $sortMap,
        array $idSet,
        bool $allowZero
    ): int {
        if (array_key_exists($sortKey, $row) && $row[$sortKey] !== null && $row[$sortKey] !== '') {
            $sort = (int) $row[$sortKey];
            if ($sort === 0 && $allowZero) {
                return 0;
            }
            if (! isset($sortMap[$sort])) {
                throw new BusinessException(
                    $this->code(WfFlowDefinitionError::CONDITION_NODE_INVALID),
                    '条件关联节点顺序无效'
                );
            }

            return (int) $sortMap[$sort];
        }

        $id = (string) ($row[$idKey] ?? '0');
        if ($id === '0' || $id === '') {
            if ($allowZero) {
                return 0;
            }

            return 0;
        }
        if (! in_array($id, $idSet, true)) {
            throw new BusinessException(
                $this->code(WfFlowDefinitionError::CONDITION_NODE_INVALID),
                '条件关联节点无效'
            );
        }

        return (int) $id;
    }

    private function formToArray(WfFlowForm $form): array
    {
        return [
            'id' => (string) $form->id,
            'flow_def_id' => (string) $form->flow_def_id,
            'field_name' => $form->field_name,
            'field_key' => $form->field_key,
            'field_type' => $form->field_type?->value,
            'field_type_label' => $form->field_type?->label(),
            'field_options' => $form->field_options ?? [],
            'is_required' => (int) $form->is_required,
            'sort' => (int) $form->sort,
        ];
    }

    private function nodeToArray(WfFlowNode $node): array
    {
        return [
            'id' => (string) $node->id,
            'flow_def_id' => (string) $node->flow_def_id,
            'node_name' => $node->node_name,
            'node_sort' => (int) $node->node_sort,
            'approve_type' => $node->approve_type?->value,
            'approve_type_label' => $node->approve_type?->label(),
            'approve_target' => $node->approve_target ?? [],
            'node_mode' => $node->node_mode?->value,
            'node_mode_label' => $node->node_mode?->label(),
            'can_reject' => (int) $node->can_reject,
            'can_add_sign' => (int) $node->can_add_sign,
            'can_transfer' => (int) $node->can_transfer,
            'back_node_id' => (string) $node->back_node_id,
        ];
    }

    private function conditionToArray(WfFlowNodeCondition $condition, WfFlowDefinition $definition): array
    {
        $pre = $definition->nodes->firstWhere('id', $condition->pre_node_id);
        $jump = $definition->nodes->firstWhere('id', $condition->jump_node_id);

        return [
            'id' => (string) $condition->id,
            'flow_def_id' => (string) $condition->flow_def_id,
            'pre_node_id' => (string) $condition->pre_node_id,
            'pre_node_sort' => $condition->pre_node_id ? (int) ($pre?->node_sort ?? 0) : 0,
            'pre_node_name' => $condition->pre_node_id ? ($pre?->node_name ?? '') : '发起',
            'condition_field' => $condition->condition_field,
            'condition_operator' => $condition->condition_operator?->value,
            'condition_operator_label' => $condition->condition_operator?->label(),
            'condition_value' => $condition->condition_value,
            'jump_node_id' => (string) $condition->jump_node_id,
            'jump_node_sort' => (int) ($jump?->node_sort ?? 0),
            'jump_node_name' => $jump?->node_name ?? '',
        ];
    }
}
