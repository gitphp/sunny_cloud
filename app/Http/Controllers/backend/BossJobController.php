<?php

namespace App\Http\Controllers\backend;

use App\Enums\BossJobHot;
use App\Enums\BossJobStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\BossJobRequest;
use App\Http\Resources\backend\BossJobResource;
use App\Models\BossJob;
use App\Service\BossJobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class BossJobController extends AbstractController
{
    public function __construct(
        private readonly BossJobService $bossJobService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->bossJobService->paginate(
            $request->only(['keyword', 'job_status', 'is_hot']),
            (int) $request->query('per_page', 15)
        );

        return $this->success([
            'list' => collect($paginator->items())
                ->map(fn (BossJob $item) => $this->bossJobService->toListArray($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'job_status' => BossJobStatus::labels(),
                'is_hot' => BossJobHot::labels(),
            ],
        ]);
    }

    public function show(BossJob $bossJob): JsonResponse
    {
        return $this->success($this->bossJobService->toArray($bossJob));
    }

    public function store(BossJobRequest $request): JsonResponse
    {
        try {
            $job = $this->bossJobService->create($request->validated());

            return $this->success(
                (new BossJobResource($job))->resolve(),
                '添加成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(960099, '添加失败');
        }
    }

    public function update(BossJobRequest $request, BossJob $bossJob): JsonResponse
    {
        try {
            $job = $this->bossJobService->update($bossJob, $request->validated());

            return $this->success(
                (new BossJobResource($job))->resolve(),
                '修改成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(960099, '修改失败');
        }
    }

    public function updateSort(BossJobRequest $request, BossJob $bossJob): JsonResponse
    {
        $job = $this->bossJobService->updateSort($bossJob, (int) $request->validated('job_sort'));

        return $this->success(
            (new BossJobResource($job))->resolve(),
            '排序更新成功'
        );
    }

    public function updateStatus(BossJobRequest $request, BossJob $bossJob): JsonResponse
    {
        $job = $this->bossJobService->updateStatus($bossJob, (int) $request->validated('job_status'));

        return $this->success(
            (new BossJobResource($job))->resolve(),
            '状态更新成功'
        );
    }

    public function updateHot(BossJobRequest $request, BossJob $bossJob): JsonResponse
    {
        $job = $this->bossJobService->updateHot($bossJob, (int) $request->validated('is_hot'));

        return $this->success(
            (new BossJobResource($job))->resolve(),
            '急聘状态更新成功'
        );
    }

    public function destroy(BossJob $bossJob): JsonResponse
    {
        try {
            $this->bossJobService->delete($bossJob);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(960099, '删除失败');
        }
    }
}
