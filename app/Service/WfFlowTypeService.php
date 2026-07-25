<?php

namespace App\Service;

use App\Constants\Code\CodePrefix;
use App\Constants\Code\WfFlowTypeError;
use App\Enums\WfStatus;
use App\Exceptions\BusinessException;
use App\Models\WfFlowDefinition;
use App\Models\WfFlowType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WfFlowTypeService
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = WfFlowType::query()->orderByDesc('sort')->orderByDesc('id');

        if (! empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('type_name', 'like', '%'.$kw.'%')
                    ->orWhere('type_code', 'like', '%'.$kw.'%');
            });
        }
        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== null) {
            $query->where('status', (int) $filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function allEnabled(): array
    {
        return WfFlowType::query()
            ->where('status', WfStatus::Enabled)
            ->orderByDesc('sort')
            ->orderBy('id')
            ->get()
            ->map(fn (WfFlowType $item) => $this->toArray($item))
            ->all();
    }

    public function create(array $data): WfFlowType
    {
        $this->assertNameUnique($data['type_name']);
        $this->assertCodeUnique($data['type_code']);

        return WfFlowType::query()->create([
            'type_name' => $data['type_name'],
            'type_code' => $data['type_code'],
            'icon' => (string) ($data['icon'] ?? ''),
            'sort' => (int) ($data['sort'] ?? 0),
            'status' => WfStatus::from((int) ($data['status'] ?? WfStatus::Enabled->value)),
        ]);
    }

    public function update(WfFlowType $type, array $data): WfFlowType
    {
        $name = $data['type_name'] ?? $type->type_name;
        $code = $data['type_code'] ?? $type->type_code;
        $this->assertNameUnique($name, (string) $type->id);
        $this->assertCodeUnique($code, (string) $type->id);

        $type->fill([
            'type_name' => $name,
            'type_code' => $code,
            'icon' => (string) ($data['icon'] ?? $type->icon),
            'sort' => (int) ($data['sort'] ?? $type->sort),
            'status' => isset($data['status'])
                ? WfStatus::from((int) $data['status'])
                : $type->status,
        ]);
        $type->save();

        return $type->fresh();
    }

    public function updateSort(WfFlowType $type, int $sort): WfFlowType
    {
        $type->sort = $sort;
        $type->save();

        return $type;
    }

    public function updateStatus(WfFlowType $type, int $status): WfFlowType
    {
        $type->status = WfStatus::from($status);
        $type->save();

        return $type;
    }

    public function delete(WfFlowType $type): void
    {
        if (WfFlowDefinition::query()->where('flow_type_id', $type->id)->exists()) {
            throw new BusinessException(
                $this->code(WfFlowTypeError::DELETE_BLOCKED_HAS_FLOW),
                '存在流程定义，不可删除'
            );
        }
        $type->delete();
    }

    public function toArray(WfFlowType $type): array
    {
        return [
            'id' => (string) $type->id,
            'type_name' => $type->type_name,
            'type_code' => $type->type_code,
            'icon' => $type->icon,
            'sort' => (int) $type->sort,
            'status' => $type->status?->value,
            'status_label' => $type->status?->label(),
        ];
    }

    private function code(int $error): int
    {
        return CodePrefix::WF_FLOW_TYPE * 1000 + $error;
    }

    private function assertNameUnique(string $name, ?string $excludeId = null): void
    {
        $exists = WfFlowType::query()
            ->where('type_name', $name)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();
        if ($exists) {
            throw new BusinessException($this->code(WfFlowTypeError::NAME_DUPLICATED), '流程类型名称已存在');
        }
    }

    private function assertCodeUnique(string $code, ?string $excludeId = null): void
    {
        $exists = WfFlowType::query()
            ->where('type_code', $code)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();
        if ($exists) {
            throw new BusinessException($this->code(WfFlowTypeError::CODE_DUPLICATED), '流程类型编码已存在');
        }
    }
}
