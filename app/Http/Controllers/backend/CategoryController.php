<?php

namespace App\Http\Controllers\backend;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\backend\CategoryRequest;
use App\Http\Resources\backend\CategoryResource;
use App\Models\Category;
use App\Service\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Throwable;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $tree = $this->categoryService->getTree($request->query('keyword'));

        return ApiResponseHelper::success($tree);
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        try {
            $category = $this->categoryService->create($request->validated());

            return ApiResponseHelper::success(
                (new CategoryResource($category))->resolve(),
                '添加成功'
            );
        } catch (InvalidArgumentException $e) {
            return ApiResponseHelper::error(2001001, $e->getMessage());
        } catch (Throwable $e) {
            return ApiResponseHelper::error(1001001, '添加失败');
        }
    }

    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        try {
            $category = $this->categoryService->update($category, $request->validated());

            return ApiResponseHelper::success(
                (new CategoryResource($category))->resolve(),
                '修改成功'
            );
        } catch (InvalidArgumentException $e) {
            return ApiResponseHelper::error(2001002, $e->getMessage());
        } catch (Throwable $e) {
            return ApiResponseHelper::error(1001001, '修改失败');
        }
    }

    public function updateSort(CategoryRequest $request, Category $category): JsonResponse
    {
        $category = $this->categoryService->updateSort(
            $category,
            (int) $request->validated('sort')
        );

        return ApiResponseHelper::success(
            (new CategoryResource($category))->resolve(),
            '排序更新成功'
        );
    }

    public function destroy(Category $category): JsonResponse
    {
        try {
            $this->categoryService->delete($category);

            return ApiResponseHelper::success(null, '删除成功');
        } catch (Throwable $e) {
            return ApiResponseHelper::error(1001001, '删除失败');
        }
    }
}
