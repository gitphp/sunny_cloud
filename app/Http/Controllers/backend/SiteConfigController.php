<?php

namespace App\Http\Controllers\backend;

use App\Exceptions\BusinessException;
use App\Http\Requests\backend\SiteConfigRequest;
use App\Http\Resources\backend\SiteConfigResource;
use App\Models\SiteConfig;
use App\Service\SiteConfigService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class SiteConfigController extends AbstractController
{
    public function __construct(
        private readonly SiteConfigService $siteConfigService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        return $this->success(
            $this->siteConfigService->listGrouped($request->query('conf_group'))
        );
    }

    public function store(SiteConfigRequest $request): JsonResponse
    {
        try {
            $config = $this->siteConfigService->create($request->validated());

            return $this->success(
                (new SiteConfigResource($config))->resolve(),
                '添加成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(980099, '添加失败');
        }
    }

    public function update(SiteConfigRequest $request, SiteConfig $siteConfig): JsonResponse
    {
        try {
            $config = $this->siteConfigService->update($siteConfig, $request->validated());

            return $this->success(
                (new SiteConfigResource($config))->resolve(),
                '修改成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(980099, '修改失败');
        }
    }

    public function batchUpdate(SiteConfigRequest $request): JsonResponse
    {
        try {
            $items = $this->siteConfigService->batchUpdateValues($request->validated('items'));

            return $this->success(['list' => $items], '保存成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(980099, '保存失败');
        }
    }

    public function destroy(SiteConfig $siteConfig): JsonResponse
    {
        try {
            $this->siteConfigService->delete($siteConfig);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(980099, '删除失败');
        }
    }
}
