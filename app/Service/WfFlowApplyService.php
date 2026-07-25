<?php

namespace App\Service;

use App\Constants\Code\CodePrefix;
use App\Constants\Code\WfFlowApplyError;
use App\Enums\WfActionType;
use App\Enums\WfApplyStatus;
use App\Enums\WfCcReadStatus;
use App\Enums\WfPublishStatus;
use App\Exceptions\BusinessException;
use App\Models\UserAccount;
use App\Models\WfFlowApply;
use App\Models\WfFlowCcUser;
use App\Models\WfFlowDefinition;
use App\Models\WfFlowNode;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WfFlowApplyService
{
    public function __construct(
        private readonly WfFlowEngineService $engine
    ) {
    }

    public function paginateMine(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $userId = Auth::guard('backend')->id();
        $query = WfFlowApply::query()
            ->with(['flowType:id,type_name,type_code', 'currentNode:id,node_name'])
            ->where('apply_user_id', $userId)
            ->orderByDesc('id');

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    public function paginateTodo(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $userId = (string) Auth::guard('backend')->id();
        $query = WfFlowApply::query()
            ->with(['flowType:id,type_name,type_code', 'applicant:id,user_name,nick_name', 'currentNode', 'definition.nodes'])
            ->where('apply_status', WfApplyStatus::Pending)
            ->orderByDesc('id');

        $this->applyFilters($query, $filters);

        $all = $query->get()->filter(function (WfFlowApply $apply) use ($userId) {
            $node = $apply->currentNode;
            if (! $node) {
                return (string) $apply->current_approve_uid === $userId;
            }

            return $this->engine->canUserApprove($apply, $node, $userId);
        })->values();

        $page = max(1, (int) ($filters['page'] ?? 1));
        $slice = $all->forPage($page, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $slice,
            $all->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function paginateCc(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $userId = Auth::guard('backend')->id();
        $query = WfFlowCcUser::query()
            ->with(['apply.flowType:id,type_name', 'apply.applicant:id,user_name,nick_name'])
            ->where('cc_uid', $userId)
            ->orderByDesc('id');

        if (isset($filters['is_read']) && $filters['is_read'] !== '' && $filters['is_read'] !== null) {
            $query->where('is_read', (int) $filters['is_read']);
        }
        if (! empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->whereHas('apply', fn ($q) => $q->where('title', 'like', '%'.$kw.'%')
                ->orWhere('apply_no', 'like', '%'.$kw.'%'));
        }

        return $query->paginate($perPage);
    }

    public function detail(WfFlowApply $apply): array
    {
        $apply->load([
            'flowType',
            'definition.forms',
            'definition.nodes',
            'applicant:id,user_name,nick_name',
            'currentNode',
            'currentApprover:id,user_name,nick_name',
            'records.approveUser:id,user_name,nick_name',
            'records.targetUser:id,user_name,nick_name',
            'records.node:id,node_name',
            'ccUsers.user:id,user_name,nick_name',
        ]);

        return $this->toDetailArray($apply);
    }

    public function createDraft(array $data): WfFlowApply
    {
        $definition = $this->assertPublishedDefinition($data['flow_def_id'] ?? 0);
        $userId = Auth::guard('backend')->id();
        $deptId = $this->engine->resolveMainDeptId($userId);

        return WfFlowApply::query()->create([
            'apply_no' => $this->generateApplyNo(),
            'flow_type_id' => $definition->flow_type_id,
            'flow_def_id' => $definition->id,
            'title' => (string) ($data['title'] ?? ''),
            'apply_user_id' => $userId,
            'dept_id' => $deptId,
            'form_data' => $data['form_data'] ?? [],
            'current_node_id' => 0,
            'current_approve_uid' => 0,
            'apply_status' => WfApplyStatus::Draft,
            'remark' => (string) ($data['remark'] ?? ''),
        ]);
    }

    public function updateDraft(WfFlowApply $apply, array $data): WfFlowApply
    {
        $this->assertOwner($apply);
        if ($apply->apply_status !== WfApplyStatus::Draft && $apply->apply_status !== WfApplyStatus::Rejected) {
            throw new BusinessException($this->code(WfFlowApplyError::STATUS_INVALID), '当前状态不可编辑');
        }

        if (! empty($data['flow_def_id']) && (string) $data['flow_def_id'] !== (string) $apply->flow_def_id) {
            $definition = $this->assertPublishedDefinition($data['flow_def_id']);
            $apply->flow_def_id = $definition->id;
            $apply->flow_type_id = $definition->flow_type_id;
        }

        $apply->fill([
            'title' => (string) ($data['title'] ?? $apply->title),
            'form_data' => array_key_exists('form_data', $data) ? ($data['form_data'] ?? []) : $apply->form_data,
            'remark' => (string) ($data['remark'] ?? $apply->remark),
        ]);
        if ($apply->apply_status === WfApplyStatus::Rejected) {
            $apply->apply_status = WfApplyStatus::Draft;
            $apply->current_node_id = 0;
            $apply->current_approve_uid = 0;
        }
        $apply->save();

        return $apply->fresh();
    }

    public function submit(WfFlowApply $apply, array $data = []): WfFlowApply
    {
        return DB::transaction(function () use ($apply, $data) {
            $this->assertOwner($apply);
            if (! in_array($apply->apply_status, [WfApplyStatus::Draft, WfApplyStatus::Rejected], true)) {
                throw new BusinessException($this->code(WfFlowApplyError::STATUS_INVALID), '当前状态不可提交');
            }

            if (! empty($data['title'])) {
                $apply->title = $data['title'];
            }
            if (array_key_exists('form_data', $data)) {
                $apply->form_data = $data['form_data'] ?? [];
            }
            if (array_key_exists('remark', $data)) {
                $apply->remark = (string) ($data['remark'] ?? '');
            }
            if (trim($apply->title) === '') {
                throw new BusinessException($this->code(WfFlowApplyError::TITLE_REQUIRED), '请填写单据标题');
            }

            $definition = $this->assertPublishedDefinition($apply->flow_def_id);
            $definition->load(['nodes', 'conditions', 'forms']);
            if ($definition->nodes->isEmpty()) {
                throw new BusinessException($this->code(WfFlowApplyError::NO_NODE), '流程未配置审批节点');
            }

            $this->assertRequiredForms($definition, $apply->form_data ?? []);

            $first = $this->engine->firstNode($definition, $apply->form_data ?? []);
            if (! $first) {
                throw new BusinessException($this->code(WfFlowApplyError::NO_NODE), '无法确定起始审批节点');
            }

            $approvers = $this->engine->resolveApproverIds($first, $apply);
            if ($approvers === []) {
                throw new BusinessException($this->code(WfFlowApplyError::NO_APPROVER), '未找到审批人，请检查节点配置或任职信息');
            }

            $apply->apply_status = WfApplyStatus::Pending;
            $apply->current_node_id = $first->id;
            $apply->current_approve_uid = $approvers[0];
            $apply->save();

            $this->syncCcUsers($apply, $data['cc_uids'] ?? []);

            return $apply->fresh(['flowType', 'currentNode', 'ccUsers']);
        });
    }

    public function withdraw(WfFlowApply $apply): WfFlowApply
    {
        return DB::transaction(function () use ($apply) {
            $this->assertOwner($apply);
            if ($apply->apply_status !== WfApplyStatus::Pending) {
                throw new BusinessException($this->code(WfFlowApplyError::STATUS_INVALID), '仅审批中单据可撤回');
            }

            $userId = Auth::guard('backend')->id();
            $this->engine->writeRecord($apply, $apply->current_node_id, $userId, WfActionType::Withdraw, '发起人撤回');

            $apply->apply_status = WfApplyStatus::Withdrawn;
            $apply->current_node_id = 0;
            $apply->current_approve_uid = 0;
            $apply->save();

            return $apply->fresh();
        });
    }

    public function void(WfFlowApply $apply): WfFlowApply
    {
        $this->assertOwner($apply);
        if (! in_array($apply->apply_status, [WfApplyStatus::Draft, WfApplyStatus::Withdrawn, WfApplyStatus::Rejected], true)) {
            throw new BusinessException($this->code(WfFlowApplyError::STATUS_INVALID), '当前状态不可作废');
        }

        $apply->apply_status = WfApplyStatus::Voided;
        $apply->current_node_id = 0;
        $apply->current_approve_uid = 0;
        $apply->save();

        return $apply->fresh();
    }

    public function delete(WfFlowApply $apply): void
    {
        $this->assertOwner($apply);
        if ($apply->apply_status !== WfApplyStatus::Draft) {
            throw new BusinessException($this->code(WfFlowApplyError::STATUS_INVALID), '仅草稿可删除');
        }
        $apply->delete();
    }

    public function markCcRead(WfFlowCcUser $cc): WfFlowCcUser
    {
        $userId = Auth::guard('backend')->id();
        if ((string) $cc->cc_uid !== (string) $userId) {
            throw new BusinessException($this->code(WfFlowApplyError::NOT_OWNER), '无权操作该抄送');
        }

        if ($cc->is_read !== WfCcReadStatus::Read) {
            $cc->is_read = WfCcReadStatus::Read;
            $cc->read_time = now();
            $cc->save();

            $this->engine->writeRecord(
                $cc->apply ?: WfFlowApply::query()->findOrFail($cc->apply_id),
                0,
                $userId,
                WfActionType::CcRead,
                '抄送已读'
            );
        }

        return $cc->fresh(['apply']);
    }

    public function toListArray(WfFlowApply $apply): array
    {
        return [
            'id' => (string) $apply->id,
            'apply_no' => $apply->apply_no,
            'flow_type_id' => (string) $apply->flow_type_id,
            'type_name' => $apply->flowType?->type_name,
            'flow_def_id' => (string) $apply->flow_def_id,
            'title' => $apply->title,
            'apply_user_id' => (string) $apply->apply_user_id,
            'apply_user_name' => $apply->applicant?->nick_name ?: $apply->applicant?->user_name,
            'dept_id' => (string) $apply->dept_id,
            'current_node_id' => (string) $apply->current_node_id,
            'current_node_name' => $apply->currentNode?->node_name,
            'current_approve_uid' => (string) $apply->current_approve_uid,
            'apply_status' => $apply->apply_status?->value,
            'apply_status_label' => $apply->apply_status?->label(),
            'remark' => $apply->remark,
            'created_at' => optional($apply->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($apply->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }

    public function toDetailArray(WfFlowApply $apply): array
    {
        $userId = (string) Auth::guard('backend')->id();
        $node = $apply->currentNode;
        $canApprove = $apply->apply_status === WfApplyStatus::Pending && $node
            ? $this->engine->canUserApprove($apply, $node, $userId)
            : false;

        return [
            ...$this->toListArray($apply),
            'form_data' => $apply->form_data ?? [],
            'forms' => $apply->definition?->forms?->map(fn ($f) => [
                'field_name' => $f->field_name,
                'field_key' => $f->field_key,
                'field_type' => $f->field_type?->value,
                'field_options' => $f->field_options ?? [],
                'is_required' => (int) $f->is_required,
            ])->values()->all() ?? [],
            'nodes' => $apply->definition?->nodes?->map(fn (WfFlowNode $n) => [
                'id' => (string) $n->id,
                'node_name' => $n->node_name,
                'node_sort' => (int) $n->node_sort,
                'can_reject' => (int) $n->can_reject,
                'can_add_sign' => (int) $n->can_add_sign,
                'can_transfer' => (int) $n->can_transfer,
            ])->values()->all() ?? [],
            'records' => $apply->records->map(fn ($r) => [
                'id' => (string) $r->id,
                'node_id' => (string) $r->node_id,
                'node_name' => $r->node?->node_name,
                'approve_user_id' => (string) $r->approve_user_id,
                'approve_user_name' => $r->approveUser?->nick_name ?: $r->approveUser?->user_name,
                'action_type' => $r->action_type?->value,
                'action_type_label' => $r->action_type?->label(),
                'target_user_id' => (string) $r->target_user_id,
                'target_user_name' => $r->targetUser?->nick_name ?: $r->targetUser?->user_name,
                'approve_opinion' => $r->approve_opinion,
                'attach_files' => $r->attach_files ?? [],
                'operate_at' => optional($r->operate_at)?->format('Y-m-d H:i:s'),
            ])->values()->all(),
            'cc_users' => $apply->ccUsers->map(fn ($c) => [
                'id' => (string) $c->id,
                'cc_uid' => (string) $c->cc_uid,
                'cc_user_name' => $c->user?->nick_name ?: $c->user?->user_name,
                'is_read' => $c->is_read?->value,
                'is_read_label' => $c->is_read?->label(),
                'read_time' => optional($c->read_time)?->format('Y-m-d H:i:s'),
            ])->values()->all(),
            'permissions' => [
                'can_approve' => $canApprove,
                'can_reject' => $canApprove && (int) ($node?->can_reject ?? 0) === 1,
                'can_transfer' => $canApprove && (int) ($node?->can_transfer ?? 0) === 1,
                'can_add_sign' => $canApprove && (int) ($node?->can_add_sign ?? 0) === 1,
                'can_withdraw' => $apply->apply_status === WfApplyStatus::Pending
                    && (string) $apply->apply_user_id === $userId,
                'can_edit' => in_array($apply->apply_status, [WfApplyStatus::Draft, WfApplyStatus::Rejected], true)
                    && (string) $apply->apply_user_id === $userId,
            ],
            'current_approve_name' => $apply->currentApprover?->nick_name ?: $apply->currentApprover?->user_name,
        ];
    }

    public function ccToListArray(WfFlowCcUser $cc): array
    {
        $apply = $cc->apply;

        return [
            'id' => (string) $cc->id,
            'apply_id' => (string) $cc->apply_id,
            'apply_no' => $apply?->apply_no,
            'title' => $apply?->title,
            'type_name' => $apply?->flowType?->type_name,
            'apply_user_name' => $apply?->applicant?->nick_name ?: $apply?->applicant?->user_name,
            'apply_status' => $apply?->apply_status?->value,
            'apply_status_label' => $apply?->apply_status?->label(),
            'is_read' => $cc->is_read?->value,
            'is_read_label' => $cc->is_read?->label(),
            'read_time' => optional($cc->read_time)?->format('Y-m-d H:i:s'),
            'created_at' => optional($cc->created_at)?->format('Y-m-d H:i:s'),
        ];
    }

    private function applyFilters($query, array $filters): void
    {
        if (! empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('title', 'like', '%'.$kw.'%')
                    ->orWhere('apply_no', 'like', '%'.$kw.'%');
            });
        }
        if (! empty($filters['flow_type_id'])) {
            $query->where('flow_type_id', $filters['flow_type_id']);
        }
        if (isset($filters['apply_status']) && $filters['apply_status'] !== '' && $filters['apply_status'] !== null) {
            $query->where('apply_status', (int) $filters['apply_status']);
        }
    }

    private function assertPublishedDefinition(mixed $id): WfFlowDefinition
    {
        $definition = WfFlowDefinition::query()->find($id);
        if (! $definition) {
            throw new BusinessException($this->code(WfFlowApplyError::DEFINITION_NOT_FOUND), '流程模板不存在');
        }
        if ($definition->is_publish !== WfPublishStatus::Published) {
            throw new BusinessException($this->code(WfFlowApplyError::DEFINITION_NOT_PUBLISHED), '流程模板未发布');
        }

        return $definition;
    }

    private function assertOwner(WfFlowApply $apply): void
    {
        if ((string) $apply->apply_user_id !== (string) Auth::guard('backend')->id()) {
            throw new BusinessException($this->code(WfFlowApplyError::NOT_OWNER), '无权操作该申请单');
        }
    }

    private function assertRequiredForms(WfFlowDefinition $definition, array $formData): void
    {
        foreach ($definition->forms as $form) {
            if ((int) $form->is_required !== 1) {
                continue;
            }
            $key = $form->field_key;
            $val = $formData[$key] ?? null;
            if ($val === null || $val === '' || $val === []) {
                throw new BusinessException(
                    $this->code(WfFlowApplyError::FORM_REQUIRED),
                    '请填写必填项：'.$form->field_name
                );
            }
        }
    }

    private function syncCcUsers(WfFlowApply $apply, array $ccUids): void
    {
        $ccUids = array_values(array_unique(array_filter(array_map('strval', $ccUids))));
        foreach ($ccUids as $uid) {
            if ((string) $uid === (string) $apply->apply_user_id) {
                continue;
            }
            WfFlowCcUser::query()->firstOrCreate(
                ['apply_id' => $apply->id, 'cc_uid' => $uid],
                ['is_read' => WfCcReadStatus::Unread, 'created_at' => now()]
            );
        }
    }

    private function generateApplyNo(): string
    {
        $prefix = 'WF'.date('Ymd');
        $last = WfFlowApply::query()
            ->where('apply_no', 'like', $prefix.'%')
            ->orderByDesc('apply_no')
            ->value('apply_no');

        $seq = 1;
        if ($last && preg_match('/(\d{3})$/', $last, $m)) {
            $seq = (int) $m[1] + 1;
        }

        return $prefix.str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }

    private function code(int $error): int
    {
        return CodePrefix::WF_FLOW_APPLY * 1000 + $error;
    }
}
