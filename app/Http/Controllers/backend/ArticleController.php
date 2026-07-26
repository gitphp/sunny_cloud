<?php

namespace App\Http\Controllers\backend;

use App\Enums\ArticleContentType;
use App\Enums\ArticleFlag;
use App\Enums\ArticleStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\ArticleRequest;
use App\Http\Resources\backend\ArticleResource;
use App\Models\Article;
use App\Service\ArticleCategoryService;
use App\Service\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleService $articleService,
        private readonly ArticleCategoryService $articleCategoryService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->articleService->paginate(
            $request->only(['keyword', 'category_id', 'art_status', 'is_top']),
            (int) $request->query('per_page', 15)
        );

        return $this->success([
            'list' => collect($paginator->items())
                ->map(fn (Article $item) => $this->articleService->toListArray($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'art_status' => ArticleStatus::labels(),
                'content_type' => ArticleContentType::labels(),
                'is_top' => ArticleFlag::labels(),
                'is_original' => ArticleFlag::labels(),
                'is_commentable' => ArticleFlag::labels(),
                'categories' => $this->articleCategoryService->getTree(),
            ],
        ]);
    }

    public function show(Article $article): JsonResponse
    {
        try {
            $article->load('category:id,cat_name');

            return $this->success($this->articleService->toArray($article));
        } catch (Throwable $e) {
            report($e);

            return $this->error(930099, '获取文章失败');
        }
    }

    public function store(ArticleRequest $request): JsonResponse
    {
        try {
            $article = $this->articleService->create($request->validated());

            return $this->success(
                (new ArticleResource($article))->resolve(),
                '添加成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(930099, '添加失败');
        }
    }

    public function update(ArticleRequest $request, Article $article): JsonResponse
    {
        try {
            $article = $this->articleService->update($article, $request->validated());

            return $this->success(
                (new ArticleResource($article))->resolve(),
                '修改成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(930099, '修改失败');
        }
    }

    public function updateStatus(ArticleRequest $request, Article $article): JsonResponse
    {
        try {
            $data = $request->validated();
            $article = $this->articleService->updateStatus(
                $article,
                (int) $data['art_status'],
                (string) ($data['reject_reason'] ?? '')
            );

            return $this->success(
                (new ArticleResource($article))->resolve(),
                '状态更新成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(930099, '状态更新失败');
        }
    }

    public function updateTop(ArticleRequest $request, Article $article): JsonResponse
    {
        $article = $this->articleService->updateTop($article, (int) $request->validated('is_top'));

        return $this->success(
            (new ArticleResource($article))->resolve(),
            '置顶更新成功'
        );
    }

    public function destroy(Article $article): JsonResponse
    {
        try {
            $this->articleService->delete($article);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(930099, '删除失败');
        }
    }
}
