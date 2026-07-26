<?php

namespace App\Service;

use App\Constants\Code\AdSlotError;
use App\Constants\Code\CodePrefix;
use App\Enums\AdSlotStatus;
use App\Enums\OperationAction;
use App\Enums\OperationBizType;
use App\Exceptions\BusinessException;
use App\Models\AdPosition;
use App\Models\AdSlot;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AdSlotService
{
    public function __construct(
        private readonly OperationLogService $operationLogService
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = AdSlot::query()->orderByDesc('id');

        if (! empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('slot_code', 'like', '%'.$kw.'%')
                    ->orWhere('slot_name', 'like', '%'.$kw.'%')
                    ->orWhere('description', 'like', '%'.$kw.'%');
            });
        }

        if (isset($filters['slot_status']) && $filters['slot_status'] !== '' && $filters['slot_status'] !== null) {
            $query->where('slot_status', (int) $filters['slot_status']);
        }

        if (isset($filters['is_system']) && $filters['is_system'] !== '' && $filters['is_system'] !== null) {
            $query->where('is_system', (int) $filters['is_system']);
        }

        return $query->paginate($perPage);
    }

    public function options(): Collection
    {
        return AdSlot::query()
            ->where('slot_status', AdSlotStatus::Enabled)
            ->orderBy('slot_code')
            ->get(['id', 'slot_code', 'slot_name', 'width', 'height', 'max_items']);
    }

    public function create(array $data): AdSlot
    {
        $code = trim((string) $data['slot_code']);
        $this->assertCodeUnique($code);

        $slot = AdSlot::query()->create([
            'slot_code' => $code,
            'slot_name' => (string) $data['slot_name'],
            'description' => (string) ($data['description'] ?? ''),
            'width' => (int) ($data['width'] ?? 0),
            'height' => (int) ($data['height'] ?? 0),
            'max_items' => max(1, (int) ($data['max_items'] ?? 1)),
            'is_system' => (int) ($data['is_system'] ?? 0) === 1 ? 1 : 0,
            'slot_status' => AdSlotStatus::from((int) ($data['slot_status'] ?? AdSlotStatus::Enabled->value)),
        ]);

        $this->operationLogService->logCrud(
            OperationAction::Insert,
            OperationBizType::AdSlot,
            'ad_slot_created',
            $slot->id,
            $slot->slot_name,
            null,
            $this->toArray($slot),
            'AdSlotService@create'
        );

        return $slot;
    }

    public function update(AdSlot $slot, array $data): AdSlot
    {
        $code = trim((string) ($data['slot_code'] ?? $slot->slot_code));
        $this->assertCodeUnique($code, $slot->id);

        $old = $this->toArray($slot);
        $oldCode = $slot->slot_code;

        $slot->fill([
            'slot_code' => $code,
            'slot_name' => (string) ($data['slot_name'] ?? $slot->slot_name),
            'description' => (string) ($data['description'] ?? $slot->description),
            'width' => (int) ($data['width'] ?? $slot->width),
            'height' => (int) ($data['height'] ?? $slot->height),
            'max_items' => max(1, (int) ($data['max_items'] ?? $slot->max_items)),
            'is_system' => isset($data['is_system'])
                ? ((int) $data['is_system'] === 1 ? 1 : 0)
                : $slot->is_system,
            'slot_status' => isset($data['slot_status'])
                ? AdSlotStatus::from((int) $data['slot_status'])
                : $slot->slot_status,
        ]);
        $slot->save();

        if ($oldCode !== $code) {
            AdPosition::query()->where('position_code', $oldCode)->update(['position_code' => $code]);
        }

        $slot = $slot->fresh();

        $this->operationLogService->logCrud(
            OperationAction::Update,
            OperationBizType::AdSlot,
            'ad_slot_updated',
            $slot->id,
            $slot->slot_name,
            $old,
            $this->toArray($slot),
            'AdSlotService@update'
        );

        return $slot;
    }

    public function updateStatus(AdSlot $slot, int $status): AdSlot
    {
        $slot->slot_status = AdSlotStatus::from($status);
        $slot->save();

        return $slot;
    }

    public function delete(AdSlot $slot): void
    {
        if ((int) $slot->is_system === 1) {
            throw new BusinessException($this->code(AdSlotError::DELETE_BLOCKED_SYSTEM), '系统预设广告位不可删除');
        }

        $hasAds = AdPosition::query()->where('position_code', $slot->slot_code)->exists();
        if ($hasAds) {
            throw new BusinessException($this->code(AdSlotError::DELETE_BLOCKED_HAS_ADS), '该广告位下仍有广告，无法删除');
        }

        $old = $this->toArray($slot);
        $slot->delete();

        $this->operationLogService->logCrud(
            OperationAction::Delete,
            OperationBizType::AdSlot,
            'ad_slot_deleted',
            $old['id'],
            $old['slot_name'],
            $old,
            null,
            'AdSlotService@delete'
        );
    }

    public function toArray(AdSlot $slot): array
    {
        return [
            'id' => (string) $slot->id,
            'slot_code' => $slot->slot_code,
            'slot_name' => $slot->slot_name,
            'description' => $slot->description,
            'width' => (int) $slot->width,
            'height' => (int) $slot->height,
            'max_items' => (int) $slot->max_items,
            'is_system' => (int) $slot->is_system,
            'slot_status' => $slot->slot_status?->value,
            'slot_status_label' => $slot->slot_status?->label(),
            'created_at' => optional($slot->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($slot->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }

    public function toOptionArray(AdSlot $slot): array
    {
        return [
            'id' => (string) $slot->id,
            'slot_code' => $slot->slot_code,
            'slot_name' => $slot->slot_name,
            'width' => (int) $slot->width,
            'height' => (int) $slot->height,
            'max_items' => (int) $slot->max_items,
        ];
    }

    private function assertCodeUnique(string $code, int|string|null $ignoreId = null): void
    {
        $query = AdSlot::query()->where('slot_code', $code);
        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }
        if ($query->exists()) {
            throw new BusinessException($this->code(AdSlotError::CODE_DUPLICATED), '广告位编码已存在');
        }
    }

    private function code(int $error): int
    {
        return CodePrefix::AD_SLOT * 1000 + $error;
    }
}
