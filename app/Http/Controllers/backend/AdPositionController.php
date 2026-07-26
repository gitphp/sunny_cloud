<?php

namespace App\Http\Controllers\backend;

use App\Enums\AdAuditStatus;
use App\Enums\AdCostType;
use App\Enums\AdDeviceType;
use App\Enums\AdDisplayFrequency;
use App\Enums\AdLinkType;
use App\Enums\AdPlatform;
use App\Enums\AdShowTimeType;
use App\Enums\AdStatus;
use App\Enums\AdTargetUserType;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\AdPositionRequest;
use App\Http\Resources\backend\AdPositionResource;
use App\Models\AdPosition;
use App\Service\AdPositionService;
use App\Service\AdSlotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AdPositionController extends AbstractController
{
    public function __construct(
        private readonly AdPositionService $adPositionService,
        private readonly AdSlotService $adSlotService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->adPositionService->paginate(
            $request->only(['keyword', 'position_code', 'status', 'platform', 'audit_status']),
            (int) $request->query('per_page', 15)
        );

        $slots = $this->adSlotService->options()
            ->map(fn ($item) => $this->adSlotService->toOptionArray($item))
            ->values()
            ->all();

        return $this->success([
            'list' => collect($paginator->items())
                ->map(fn (AdPosition $item) => $this->adPositionService->toListArray($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'slots' => $slots,
                'link_type' => AdLinkType::labels(),
                'platform' => AdPlatform::labels(),
                'device_type' => AdDeviceType::labels(),
                'target_user_type' => AdTargetUserType::labels(),
                'show_time_type' => AdShowTimeType::labels(),
                'display_frequency' => AdDisplayFrequency::labels(),
                'cost_type' => AdCostType::labels(),
                'status' => AdStatus::labels(),
                'audit_status' => AdAuditStatus::labels(),
                'weekdays' => [
                    1 => '周一',
                    2 => '周二',
                    3 => '周三',
                    4 => '周四',
                    5 => '周五',
                    6 => '周六',
                    7 => '周日',
                ],
            ],
        ]);
    }

    public function show(AdPosition $adPosition): JsonResponse
    {
        $adPosition->load('slot:id,slot_code,slot_name');

        return $this->success($this->adPositionService->toArray($adPosition));
    }

    public function store(AdPositionRequest $request): JsonResponse
    {
        try {
            $ad = $this->adPositionService->create($request->validated());

            return $this->success(
                (new AdPositionResource($ad))->resolve(),
                '添加成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(991099, '添加失败');
        }
    }

    public function update(AdPositionRequest $request, AdPosition $adPosition): JsonResponse
    {
        try {
            $ad = $this->adPositionService->update($adPosition, $request->validated());

            return $this->success(
                (new AdPositionResource($ad))->resolve(),
                '修改成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(991099, '修改失败');
        }
    }

    public function updateSort(AdPositionRequest $request, AdPosition $adPosition): JsonResponse
    {
        $ad = $this->adPositionService->updateSort($adPosition, (int) $request->validated('sort'));

        return $this->success(
            (new AdPositionResource($ad->load('slot:id,slot_code,slot_name')))->resolve(),
            '排序更新成功'
        );
    }

    public function updateStatus(AdPositionRequest $request, AdPosition $adPosition): JsonResponse
    {
        $ad = $this->adPositionService->updateStatus($adPosition, (int) $request->validated('status'));

        return $this->success(
            (new AdPositionResource($ad->load('slot:id,slot_code,slot_name')))->resolve(),
            '状态更新成功'
        );
    }

    public function audit(AdPositionRequest $request, AdPosition $adPosition): JsonResponse
    {
        try {
            $data = $request->validated();
            $ad = $this->adPositionService->audit(
                $adPosition,
                (int) $data['audit_status'],
                (string) ($data['reject_reason'] ?? '')
            );

            return $this->success(
                (new AdPositionResource($ad))->resolve(),
                '审核成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(991099, '审核失败');
        }
    }

    public function destroy(AdPosition $adPosition): JsonResponse
    {
        try {
            $this->adPositionService->delete($adPosition);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(991099, '删除失败');
        }
    }
}
