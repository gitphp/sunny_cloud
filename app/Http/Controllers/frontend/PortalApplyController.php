<?php

namespace App\Http\Controllers\frontend;

use App\Exceptions\BusinessException;
use App\Http\Requests\frontend\PortalApplyRequest;
use App\Service\PortalApplyService;
use Illuminate\Http\JsonResponse;
use Throwable;

class PortalApplyController extends AbstractController
{
    public function __construct(
        private readonly PortalApplyService $portalApplyService
    ) {
    }

    public function meta(): JsonResponse
    {
        return $this->success($this->portalApplyService->formMeta());
    }

    public function fetchTkd(PortalApplyRequest $request): JsonResponse
    {
        try {
            $data = $this->portalApplyService->fetchTkd($request->validated()['site_url']);

            return $this->success($data, '获取成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(940003, '获取失败');
        }
    }

    public function store(PortalApplyRequest $request): JsonResponse
    {
        try {
            $bookmark = $this->portalApplyService->create($request->validated());

            return $this->success([
                'id' => (string) $bookmark->id,
            ], '提交成功，请等待审核');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(940099, '提交失败');
        }
    }
}
