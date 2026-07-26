<?php

namespace App\Service;

use App\Constants\Code\AdPositionError;
use App\Constants\Code\CodePrefix;
use App\Enums\AdAuditStatus;
use App\Enums\AdCostType;
use App\Enums\AdDeviceType;
use App\Enums\AdDisplayFrequency;
use App\Enums\AdLinkType;
use App\Enums\AdPlatform;
use App\Enums\AdShowTimeType;
use App\Enums\AdStatus;
use App\Enums\AdTargetUserType;
use App\Enums\OperationAction;
use App\Enums\OperationBizType;
use App\Exceptions\BusinessException;
use App\Models\AdPosition;
use App\Models\AdSlot;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AdPositionService
{
    public function __construct(
        private readonly OperationLogService $operationLogService
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = AdPosition::query()
            ->with('slot:id,slot_code,slot_name')
            ->orderByDesc('sort')
            ->orderByDesc('id');

        if (! empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('ad_title', 'like', '%'.$kw.'%')
                    ->orWhere('subtitle', 'like', '%'.$kw.'%')
                    ->orWhere('position_code', 'like', '%'.$kw.'%');
            });
        }

        if (! empty($filters['position_code'])) {
            $query->where('position_code', (string) $filters['position_code']);
        }

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== null) {
            $query->where('status', (int) $filters['status']);
        }

        if (isset($filters['platform']) && $filters['platform'] !== '' && $filters['platform'] !== null) {
            $query->where('platform', (int) $filters['platform']);
        }

        if (isset($filters['audit_status']) && $filters['audit_status'] !== '' && $filters['audit_status'] !== null) {
            $query->where('audit_status', (int) $filters['audit_status']);
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): AdPosition
    {
        $payload = $this->normalizePayload($data);
        $this->assertSlot($payload['position_code']);
        $this->assertTimeRange($payload['start_time'], $payload['end_time']);

        $ad = AdPosition::query()->create(array_merge($payload, [
            'impression_count' => 0,
            'click_count' => 0,
            'click_rate' => 0,
            'daily_stats' => null,
            'created_by' => Auth::guard('backend')->id() ?? 0,
            'reviewer_id' => 0,
            'reviewed_at' => null,
            'reject_reason' => '',
        ]));

        $this->operationLogService->logCrud(
            OperationAction::Insert,
            OperationBizType::AdPosition,
            'ad_position_created',
            $ad->id,
            $ad->ad_title,
            null,
            $this->toArray($ad),
            'AdPositionService@create'
        );

        return $ad->load('slot:id,slot_code,slot_name');
    }

    public function update(AdPosition $ad, array $data): AdPosition
    {
        $payload = $this->normalizePayload($data, $ad);
        $this->assertSlot($payload['position_code']);
        $this->assertTimeRange($payload['start_time'], $payload['end_time']);

        $old = $this->toArray($ad);
        $ad->fill($payload);
        $ad->save();
        $ad = $ad->fresh()->load('slot:id,slot_code,slot_name');

        $this->operationLogService->logCrud(
            OperationAction::Update,
            OperationBizType::AdPosition,
            'ad_position_updated',
            $ad->id,
            $ad->ad_title,
            $old,
            $this->toArray($ad),
            'AdPositionService@update'
        );

        return $ad;
    }

    public function updateSort(AdPosition $ad, int $sort): AdPosition
    {
        $ad->sort = $sort;
        $ad->save();

        return $ad;
    }

    public function updateStatus(AdPosition $ad, int $status): AdPosition
    {
        $ad->status = AdStatus::from($status);
        $ad->save();

        return $ad;
    }

    public function audit(AdPosition $ad, int $auditStatus, string $rejectReason = ''): AdPosition
    {
        $audit = AdAuditStatus::from($auditStatus);
        if ($audit === AdAuditStatus::Rejected && trim($rejectReason) === '') {
            throw new BusinessException($this->code(AdPositionError::REJECT_REASON_REQUIRED), '请填写驳回原因');
        }

        $ad->audit_status = $audit;
        $ad->reviewer_id = Auth::guard('backend')->id() ?? 0;
        $ad->reviewed_at = now();
        $ad->reject_reason = $audit === AdAuditStatus::Rejected ? trim($rejectReason) : '';

        if ($audit === AdAuditStatus::Approved) {
            $ad->status = AdStatus::Approved;
        } elseif ($audit === AdAuditStatus::Rejected) {
            $ad->status = AdStatus::Rejected;
        } elseif ($audit === AdAuditStatus::Pending) {
            $ad->status = AdStatus::Pending;
        }

        $ad->save();

        return $ad->fresh()->load('slot:id,slot_code,slot_name');
    }

    public function delete(AdPosition $ad): void
    {
        $old = $this->toArray($ad);
        $ad->delete();

        $this->operationLogService->logCrud(
            OperationAction::Delete,
            OperationBizType::AdPosition,
            'ad_position_deleted',
            $old['id'],
            $old['ad_title'],
            $old,
            null,
            'AdPositionService@delete'
        );
    }

    public function toListArray(AdPosition $ad): array
    {
        return [
            'id' => (string) $ad->id,
            'ad_title' => $ad->ad_title,
            'subtitle' => $ad->subtitle,
            'cover_url' => $ad->cover_url,
            'cover_thumb' => $ad->cover_thumb ?: $ad->cover_url,
            'position_code' => $ad->position_code,
            'slot_name' => $ad->slot?->slot_name ?? '',
            'platform' => $ad->platform?->value,
            'platform_label' => $ad->platform?->label(),
            'link_type' => $ad->link_type?->value,
            'link_type_label' => $ad->link_type?->label(),
            'start_time' => optional($ad->start_time)?->format('Y-m-d H:i:s'),
            'end_time' => optional($ad->end_time)?->format('Y-m-d H:i:s'),
            'sort' => (int) $ad->sort,
            'status' => $ad->status?->value,
            'status_label' => $ad->status?->label(),
            'audit_status' => $ad->audit_status?->value,
            'audit_status_label' => $ad->audit_status?->label(),
            'impression_count' => (int) $ad->impression_count,
            'click_count' => (int) $ad->click_count,
            'click_rate' => (string) $ad->click_rate,
            'updated_at' => optional($ad->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }

    public function toArray(AdPosition $ad): array
    {
        return [
            'id' => (string) $ad->id,
            'ad_title' => $ad->ad_title,
            'subtitle' => $ad->subtitle,
            'cover_url' => $ad->cover_url,
            'cover_mobile' => $ad->cover_mobile,
            'cover_thumb' => $ad->cover_thumb,
            'video_url' => $ad->video_url,
            'link_type' => $ad->link_type?->value,
            'link_type_label' => $ad->link_type?->label(),
            'link_url' => $ad->link_url,
            'link_params' => $ad->link_params,
            'app_id' => $ad->app_id,
            'app_path' => $ad->app_path,
            'position_code' => $ad->position_code,
            'slot_name' => $ad->slot?->slot_name ?? '',
            'platform' => $ad->platform?->value,
            'platform_label' => $ad->platform?->label(),
            'device_type' => $ad->device_type?->value,
            'device_type_label' => $ad->device_type?->label(),
            'target_user_type' => $ad->target_user_type?->value,
            'target_user_type_label' => $ad->target_user_type?->label(),
            'target_user_group_ids' => $ad->target_user_group_ids ?? [],
            'target_region' => $ad->target_region,
            'start_time' => optional($ad->start_time)?->format('Y-m-d H:i:s'),
            'end_time' => optional($ad->end_time)?->format('Y-m-d H:i:s'),
            'show_time_type' => $ad->show_time_type?->value,
            'show_time_type_label' => $ad->show_time_type?->label(),
            'time_slots' => $ad->time_slots ?? [],
            'weekdays' => $ad->weekdays ?? [],
            'sort' => (int) $ad->sort,
            'display_frequency' => $ad->display_frequency?->value,
            'display_frequency_label' => $ad->display_frequency?->label(),
            'daily_impression_limit' => (int) $ad->daily_impression_limit,
            'daily_click_limit' => (int) $ad->daily_click_limit,
            'budget' => $ad->budget !== null ? (string) $ad->budget : null,
            'cost_type' => $ad->cost_type?->value,
            'cost_type_label' => $ad->cost_type?->label(),
            'bid_price' => $ad->bid_price !== null ? (string) $ad->bid_price : null,
            'status' => $ad->status?->value,
            'status_label' => $ad->status?->label(),
            'audit_status' => $ad->audit_status?->value,
            'audit_status_label' => $ad->audit_status?->label(),
            'reviewer_id' => (string) $ad->reviewer_id,
            'reviewed_at' => optional($ad->reviewed_at)?->format('Y-m-d H:i:s'),
            'reject_reason' => $ad->reject_reason,
            'impression_count' => (int) $ad->impression_count,
            'click_count' => (int) $ad->click_count,
            'click_rate' => (string) $ad->click_rate,
            'daily_stats' => $ad->daily_stats,
            'created_by' => (string) $ad->created_by,
            'created_at' => optional($ad->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($ad->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }

    private function normalizePayload(array $data, ?AdPosition $ad = null): array
    {
        $start = $data['start_time'] ?? optional($ad?->start_time)?->format('Y-m-d H:i:s');
        $end = $data['end_time'] ?? optional($ad?->end_time)?->format('Y-m-d H:i:s');

        return [
            'ad_title' => (string) ($data['ad_title'] ?? $ad?->ad_title ?? ''),
            'subtitle' => (string) ($data['subtitle'] ?? $ad?->subtitle ?? ''),
            'cover_url' => (string) ($data['cover_url'] ?? $ad?->cover_url ?? ''),
            'cover_mobile' => (string) ($data['cover_mobile'] ?? $ad?->cover_mobile ?? ''),
            'cover_thumb' => (string) ($data['cover_thumb'] ?? $ad?->cover_thumb ?? ''),
            'video_url' => (string) ($data['video_url'] ?? $ad?->video_url ?? ''),
            'link_type' => AdLinkType::from((int) ($data['link_type'] ?? $ad?->link_type?->value ?? AdLinkType::Internal->value)),
            'link_url' => (string) ($data['link_url'] ?? $ad?->link_url ?? ''),
            'link_params' => $this->nullableArray($data, 'link_params', $ad?->link_params),
            'app_id' => (string) ($data['app_id'] ?? $ad?->app_id ?? ''),
            'app_path' => (string) ($data['app_path'] ?? $ad?->app_path ?? ''),
            'position_code' => (string) ($data['position_code'] ?? $ad?->position_code ?? ''),
            'platform' => AdPlatform::from((int) ($data['platform'] ?? $ad?->platform?->value ?? AdPlatform::All->value)),
            'device_type' => AdDeviceType::from((int) ($data['device_type'] ?? $ad?->device_type?->value ?? AdDeviceType::All->value)),
            'target_user_type' => AdTargetUserType::from((int) ($data['target_user_type'] ?? $ad?->target_user_type?->value ?? AdTargetUserType::All->value)),
            'target_user_group_ids' => $this->nullableArray($data, 'target_user_group_ids', $ad?->target_user_group_ids) ?? [],
            'target_region' => $this->nullableArray($data, 'target_region', $ad?->target_region),
            'start_time' => $start,
            'end_time' => $end,
            'show_time_type' => AdShowTimeType::from((int) ($data['show_time_type'] ?? $ad?->show_time_type?->value ?? AdShowTimeType::AllDay->value)),
            'time_slots' => $this->nullableArray($data, 'time_slots', $ad?->time_slots) ?? [],
            'weekdays' => $this->nullableArray($data, 'weekdays', $ad?->weekdays) ?? [],
            'sort' => (int) ($data['sort'] ?? $ad?->sort ?? 0),
            'display_frequency' => AdDisplayFrequency::from((int) ($data['display_frequency'] ?? $ad?->display_frequency?->value ?? AdDisplayFrequency::DailyOnce->value)),
            'daily_impression_limit' => (int) ($data['daily_impression_limit'] ?? $ad?->daily_impression_limit ?? 0),
            'daily_click_limit' => (int) ($data['daily_click_limit'] ?? $ad?->daily_click_limit ?? 0),
            'budget' => array_key_exists('budget', $data) ? $data['budget'] : $ad?->budget,
            'cost_type' => AdCostType::from((int) ($data['cost_type'] ?? $ad?->cost_type?->value ?? AdCostType::Cpm->value)),
            'bid_price' => array_key_exists('bid_price', $data) ? $data['bid_price'] : $ad?->bid_price,
            'status' => AdStatus::from((int) ($data['status'] ?? $ad?->status?->value ?? AdStatus::Draft->value)),
            'audit_status' => AdAuditStatus::from((int) ($data['audit_status'] ?? $ad?->audit_status?->value ?? AdAuditStatus::None->value)),
        ];
    }

    private function nullableArray(array $data, string $key, mixed $fallback): mixed
    {
        if (! array_key_exists($key, $data)) {
            return $fallback;
        }

        return $data[$key];
    }

    private function assertSlot(string $code): void
    {
        if ($code === '' || ! AdSlot::query()->where('slot_code', $code)->exists()) {
            throw new BusinessException($this->code(AdPositionError::SLOT_INVALID), '广告位编码无效');
        }
    }

    private function assertTimeRange(mixed $start, mixed $end): void
    {
        try {
            $startAt = Carbon::parse($start);
            $endAt = Carbon::parse($end);
        } catch (\Throwable) {
            throw new BusinessException($this->code(AdPositionError::TIME_INVALID), '投放时间格式不正确');
        }

        if ($endAt->lte($startAt)) {
            throw new BusinessException($this->code(AdPositionError::TIME_INVALID), '结束时间必须晚于开始时间');
        }
    }

    private function code(int $error): int
    {
        return CodePrefix::AD_POSITION * 1000 + $error;
    }
}
