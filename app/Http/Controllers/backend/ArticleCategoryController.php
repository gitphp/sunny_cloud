<?php

namespace App\Http\Controllers\backend;

use App\Enums\ArticleCategoryStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\ArticleCategoryRequest;
use App\Http\Resources\backend\ArticleCategoryResource;
use App\Models\ArticleCategory;
use App\Service\ArticleCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ArticleCategoryController extends AbstractController
{
    public function __construct(
        private readonly ArticleCategoryService $articleCategoryService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $tree = $this->articleCategoryService->getTree($request->query('keyword'));

        return $this->success([
            'list' => $tree,
            'options' => [
                'status' => ArticleCategoryStatus::labels(),
            ],
        ]);
    }

    public function store(ArticleCategoryRequest $request): JsonResponse
    {
        try {
            $category = $this->articleCategoryService->create($request->validated());

            return $this->success(
                (new ArticleCategoryResource($category))->resolve(),
                '添加成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(920099, '添加失败');
        }
    }

    public function update(ArticleCategoryRequest $request, ArticleCategory $articleCategory): JsonResponse
    {
        try {
            $category = $this->articleCategoryService->update($articleCategory, $request->validated());

            return $this->success(
                (new ArticleCategoryResource($category))->resolve(),
                '修改成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(920099, '修改失败');
        }
    }

    public function updateSort(ArticleCategoryRequest $request, ArticleCategory $articleCategory): JsonResponse
    {
        $category = $this->articleCategoryService->updateSort(
            $articleCategory,
            (int) $request->validated('cat_sort')
        );

        return $this->success(
            (new ArticleCategoryResource($category))->resolve(),
            '排序更新成功'
        );
    }

    public function updateStatus(ArticleCategoryRequest $request, ArticleCategory $articleCategory): JsonResponse
    {
        $category = $this->articleCategoryService->updateStatus(
            $articleCategory,
            (int) $request->validated('status')
        );

        return $this->success(
            (new ArticleCategoryResource($category))->resolve(),
            '状态更新成功'
        );
    }

    public function destroy(ArticleCategory $articleCategory): JsonResponse
    {
        try {
            $this->articleCategoryService->delete($articleCategory);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(920099, '删除失败');
        }
    }
}
