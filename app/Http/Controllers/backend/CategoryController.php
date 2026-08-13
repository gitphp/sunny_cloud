<?php

namespace App\Http\Controllers\backend;

use App\Enums\CategoryLevel;
use App\Enums\CategoryShowType;
use App\Enums\CategoryStatus;
use App\Enums\CategoryType;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\CategoryRequest;
use App\Http\Resources\backend\CategoryResource;
use App\Models\Category;
use App\Service\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $type = $request->query('category_type');
        $tree = $this->categoryService->getTree(
            $request->query('keyword'),
            $type !== null && $type !== '' ? (int) $type : null
        );

        return $this->success([
            'list' => $tree,
            'options' => [
                'category_type' => CategoryType::labels(),
                'show_type' => CategoryShowType::labels(),
                'cat_status' => CategoryStatus::labels(),
                'level' => CategoryLevel::labels(),
            ],
        ]);
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        try {
            $category = $this->categoryService->create($request->validated());

            return $this->success(
                (new CategoryResource($category))->resolve(),
                '添加成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(200099, '添加失败');
        }
    }

    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        try {
            $category = $this->categoryService->update($category, $request->validated());

            return $this->success(
                (new CategoryResource($category))->resolve(),
                '修改成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(200099, '修改失败');
        }
    }

    public function updateSort(CategoryRequest $request, Category $category): JsonResponse
    {
        $category = $this->categoryService->updateSort(
            $category,
            (int) $request->validated('sort_order')
        );

        return $this->success(
            (new CategoryResource($category))->resolve(),
            '排序更新成功'
        );
    }

    public function updateStatus(CategoryRequest $request, Category $category): JsonResponse
    {
        $category = $this->categoryService->updateStatus(
            $category,
            (int) $request->validated('cat_status')
        );

        return $this->success(
            (new CategoryResource($category))->resolve(),
            '状态更新成功'
        );
    }

    public function destroy(Category $category): JsonResponse
    {
        try {
            $this->categoryService->delete($category);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(200099, '删除失败');
        }
    }
}
