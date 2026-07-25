<?php

namespace App\Http\Controllers\backend;

use App\Enums\CategoryLevel;
use App\Enums\ProductShowStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\ProductCategoryRequest;
use App\Http\Resources\backend\ProductCategoryResource;
use App\Models\ProductCategory;
use App\Service\ProductCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ProductCategoryController extends AbstractController
{
    public function __construct(
        private readonly ProductCategoryService $productCategoryService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $tree = $this->productCategoryService->getTree($request->query('keyword'));

        return $this->success([
            'list' => $tree,
            'options' => [
                'cat_status' => ProductShowStatus::labels(),
                'level' => CategoryLevel::labels(),
            ],
        ]);
    }

    public function store(ProductCategoryRequest $request): JsonResponse
    {
        try {
            $category = $this->productCategoryService->create($request->validated());

            return $this->success((new ProductCategoryResource($category))->resolve(), '添加成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(200099, '添加失败');
        }
    }

    public function update(ProductCategoryRequest $request, ProductCategory $productCategory): JsonResponse
    {
        try {
            $category = $this->productCategoryService->update($productCategory, $request->validated());

            return $this->success((new ProductCategoryResource($category))->resolve(), '修改成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(200099, '修改失败');
        }
    }

    public function updateSort(ProductCategoryRequest $request, ProductCategory $productCategory): JsonResponse
    {
        $category = $this->productCategoryService->updateSort(
            $productCategory,
            (int) $request->validated('sort_order')
        );

        return $this->success((new ProductCategoryResource($category))->resolve(), '排序更新成功');
    }

    public function updateStatus(ProductCategoryRequest $request, ProductCategory $productCategory): JsonResponse
    {
        $category = $this->productCategoryService->updateStatus(
            $productCategory,
            (int) $request->validated('cat_status')
        );

        return $this->success((new ProductCategoryResource($category))->resolve(), '状态更新成功');
    }

    public function destroy(ProductCategory $productCategory): JsonResponse
    {
        try {
            $this->productCategoryService->delete($productCategory);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(200099, '删除失败');
        }
    }
}
