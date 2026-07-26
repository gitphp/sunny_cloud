<?php

namespace App\Http\Controllers\backend;

use App\Enums\AdSlotStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\AdSlotRequest;
use App\Http\Resources\backend\AdSlotResource;
use App\Models\AdSlot;
use App\Service\AdSlotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AdSlotController extends AbstractController
{
    public function __construct(
        private readonly AdSlotService $adSlotService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->adSlotService->paginate(
            $request->only(['keyword', 'slot_status', 'is_system']),
            (int) $request->query('per_page', 15)
        );

        return $this->success([
            'list' => collect($paginator->items())
                ->map(fn (AdSlot $item) => $this->adSlotService->toArray($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'slot_status' => AdSlotStatus::labels(),
                'is_system' => [0 => '否', 1 => '是'],
            ],
        ]);
    }

    public function options(): JsonResponse
    {
        $list = $this->adSlotService->options()
            ->map(fn (AdSlot $item) => $this->adSlotService->toOptionArray($item))
            ->values()
            ->all();

        return $this->success(['list' => $list]);
    }

    public function store(AdSlotRequest $request): JsonResponse
    {
        try {
            $slot = $this->adSlotService->create($request->validated());

            return $this->success(
                (new AdSlotResource($slot))->resolve(),
                '添加成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(990099, '添加失败');
        }
    }

    public function update(AdSlotRequest $request, AdSlot $adSlot): JsonResponse
    {
        try {
            $slot = $this->adSlotService->update($adSlot, $request->validated());

            return $this->success(
                (new AdSlotResource($slot))->resolve(),
                '修改成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(990099, '修改失败');
        }
    }

    public function updateStatus(AdSlotRequest $request, AdSlot $adSlot): JsonResponse
    {
        $slot = $this->adSlotService->updateStatus($adSlot, (int) $request->validated('slot_status'));

        return $this->success(
            (new AdSlotResource($slot))->resolve(),
            '状态更新成功'
        );
    }

    public function destroy(AdSlot $adSlot): JsonResponse
    {
        try {
            $this->adSlotService->delete($adSlot);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(990099, '删除失败');
        }
    }
}
