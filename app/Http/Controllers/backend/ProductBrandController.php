<?php

namespace App\Http\Controllers\backend;

use App\Enums\ProductIsSystem;
use App\Enums\ProductShowStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\ProductBrandRequest;
use App\Http\Resources\backend\ProductBrandResource;
use App\Models\ProductBrand;
use App\Service\ProductBrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ProductBrandController extends AbstractController
{
    public function __construct(
        private readonly ProductBrandService $productBrandService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->productBrandService->paginate(
            $request->only(['keyword', 'is_show']),
            (int) $request->query('per_page', 15)
        );

        return $this->success([
            'list' => collect($paginator->items())
                ->map(fn (ProductBrand $item) => $this->productBrandService->toArray($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'is_show' => ProductShowStatus::labels(),
                'is_system' => ProductIsSystem::labels(),
            ],
        ]);
    }

    public function store(ProductBrandRequest $request): JsonResponse
    {
        try {
            $brand = $this->productBrandService->create($request->validated());

            return $this->success((new ProductBrandResource($brand))->resolve(), '添加成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(100099, '添加失败');
        }
    }

    public function update(ProductBrandRequest $request, ProductBrand $productBrand): JsonResponse
    {
        try {
            $brand = $this->productBrandService->update($productBrand, $request->validated());

            return $this->success((new ProductBrandResource($brand))->resolve(), '修改成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(100099, '修改失败');
        }
    }

    public function updateSort(ProductBrandRequest $request, ProductBrand $productBrand): JsonResponse
    {
        $brand = $this->productBrandService->updateSort($productBrand, (int) $request->validated('sort_order'));

        return $this->success((new ProductBrandResource($brand))->resolve(), '排序更新成功');
    }

    public function updateStatus(ProductBrandRequest $request, ProductBrand $productBrand): JsonResponse
    {
        $brand = $this->productBrandService->updateStatus($productBrand, (int) $request->validated('is_show'));

        return $this->success((new ProductBrandResource($brand))->resolve(), '状态更新成功');
    }

    public function destroy(ProductBrand $productBrand): JsonResponse
    {
        try {
            $this->productBrandService->delete($productBrand);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(100099, '删除失败');
        }
    }
}
