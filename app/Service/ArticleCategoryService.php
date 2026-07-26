<?php

namespace App\Service;

use App\Constants\Code\ArticleCategoryError;
use App\Constants\Code\CodePrefix;
use App\Enums\ArticleCategoryStatus;
use App\Enums\OperationAction;
use App\Enums\OperationBizType;
use App\Exceptions\BusinessException;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Database\Eloquent\Collection;

class ArticleCategoryService
{
    public function __construct(
        private readonly OperationLogService $operationLogService
    ) {
    }

    public function getTree(?string $keyword = null): array
    {
        $baseQuery = ArticleCategory::query()->orderByDesc('cat_sort')->orderBy('id');

        if ($keyword !== null && $keyword !== '') {
            $matchedIds = (clone $baseQuery)
                ->where(function ($q) use ($keyword) {
                    $q->where('cat_name', 'like', '%'.$keyword.'%')
                        ->orWhere('cat_url', 'like', '%'.$keyword.'%');
                })
                ->pluck('id')
                ->all();

            if ($matchedIds === []) {
                return [];
            }

            $all = $baseQuery->get();
            $keepIds = $this->collectAncestorIds($all, $matchedIds);

            return $this->buildTree($all->whereIn('id', $keepIds)->values());
        }

        return $this->buildTree($baseQuery->get());
    }

    public function create(array $data): ArticleCategory
    {
        $parentId = $this->normalizeParentId($data['parent_id'] ?? 0);
        $this->assertParentExists($parentId);

        $catName = (string) $data['cat_name'];
        $catUrl = (string) ($data['cat_url'] ?? '');
        $this->assertNameUnique($catName, $parentId);
        $this->assertUrlUnique($catUrl);

        $category = ArticleCategory::query()->create([
            'parent_id' => $parentId,
            'cat_name' => $catName,
            'cat_url' => $catUrl,
            'description' => (string) ($data['description'] ?? ''),
            'cat_sort' => (int) ($data['cat_sort'] ?? 0),
            'status' => ArticleCategoryStatus::from((int) ($data['status'] ?? ArticleCategoryStatus::Enabled->value)),
        ]);

        $this->operationLogService->logCrud(
            OperationAction::Insert,
            OperationBizType::ArticleCategory,
            'article_category_created',
            $category->id,
            $category->cat_name,
            null,
            $this->toArray($category),
            'ArticleCategoryService@create'
        );

        return $category;
    }

    public function update(ArticleCategory $category, array $data): ArticleCategory
    {
        $parentId = $this->normalizeParentId($data['parent_id'] ?? $category->parent_id);

        if ((string) $parentId === (string) $category->id) {
            throw new BusinessException($this->code(ArticleCategoryError::PARENT_INVALID), '上级分类不能是自身');
        }

        $this->assertParentExists($parentId);
        $this->assertNotDescendant($category, $parentId);

        $catName = (string) ($data['cat_name'] ?? $category->cat_name);
        $catUrl = (string) ($data['cat_url'] ?? $category->cat_url);
        $this->assertNameUnique($catName, $parentId, (string) $category->id);
        $this->assertUrlUnique($catUrl, (string) $category->id);

        $old = $this->toArray($category);

        $category->fill([
            'parent_id' => $parentId,
            'cat_name' => $catName,
            'cat_url' => $catUrl,
            'description' => (string) ($data['description'] ?? $category->description),
            'cat_sort' => (int) ($data['cat_sort'] ?? $category->cat_sort),
            'status' => isset($data['status'])
                ? ArticleCategoryStatus::from((int) $data['status'])
                : $category->status,
        ]);
        $category->save();
        $category = $category->fresh();

        $this->operationLogService->logCrud(
            OperationAction::Update,
            OperationBizType::ArticleCategory,
            'article_category_updated',
            $category->id,
            $category->cat_name,
            $old,
            $this->toArray($category),
            'ArticleCategoryService@update'
        );

        return $category;
    }

    public function updateSort(ArticleCategory $category, int $sort): ArticleCategory
    {
        $category->cat_sort = $sort;
        $category->save();

        return $category;
    }

    public function updateStatus(ArticleCategory $category, int $status): ArticleCategory
    {
        $category->status = ArticleCategoryStatus::from($status);
        $category->save();

        return $category;
    }

    public function delete(ArticleCategory $category): void
    {
        if ($category->children()->exists()) {
            throw new BusinessException(
                $this->code(ArticleCategoryError::DELETE_BLOCKED_HAS_CHILDREN),
                '存在子分类，不可删除'
            );
        }

        if (Article::query()->where('category_id', $category->id)->exists()) {
            throw new BusinessException(
                $this->code(ArticleCategoryError::DELETE_BLOCKED_HAS_ARTICLES),
                '分类下存在文章，不可删除'
            );
        }

        $old = $this->toArray($category);
        $category->delete();

        $this->operationLogService->logCrud(
            OperationAction::Delete,
            OperationBizType::ArticleCategory,
            'article_category_deleted',
            $old['id'],
            $old['cat_name'],
            $old,
            null,
            'ArticleCategoryService@delete'
        );
    }

    public function toArray(ArticleCategory $category, array $children = []): array
    {
        return [
            'id' => (string) $category->id,
            'parent_id' => (string) $category->parent_id,
            'cat_name' => $category->cat_name,
            'cat_url' => $category->cat_url,
            'description' => $category->description,
            'cat_sort' => (int) $category->cat_sort,
            'status' => $category->status?->value,
            'status_label' => $category->status?->label(),
            'created_at' => optional($category->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($category->updated_at)?->format('Y-m-d H:i:s'),
            'children' => $children,
        ];
    }

    private function buildTree(Collection $items): array
    {
        $map = [];
        foreach ($items as $item) {
            $map[(string) $item->id] = $this->toArray($item, []);
        }

        $tree = [];
        foreach ($items as $item) {
            $id = (string) $item->id;
            $parentId = (string) $item->parent_id;
            if ($parentId !== '0' && isset($map[$parentId])) {
                $map[$parentId]['children'][] = &$map[$id];
            } else {
                $tree[] = &$map[$id];
            }
        }

        return $tree;
    }

    private function collectAncestorIds(Collection $all, array $matchedIds): array
    {
        $byId = $all->keyBy(fn ($item) => (string) $item->id);
        $keep = [];

        foreach ($matchedIds as $id) {
            $currentId = (string) $id;
            while ($currentId !== '0' && $currentId !== '' && isset($byId[$currentId])) {
                if (isset($keep[$currentId])) {
                    break;
                }
                $keep[$currentId] = true;
                $currentId = (string) $byId[$currentId]->parent_id;
            }
        }

        return array_keys($keep);
    }

    private function normalizeParentId(mixed $parentId): int
    {
        if ($parentId === null || $parentId === '' || $parentId === '0') {
            return 0;
        }

        return (int) $parentId;
    }

    private function assertParentExists(int $parentId): ?ArticleCategory
    {
        if ($parentId === 0) {
            return null;
        }

        $parent = ArticleCategory::query()->find($parentId);
        if (! $parent) {
            throw new BusinessException($this->code(ArticleCategoryError::PARENT_INVALID), '上级分类不存在');
        }

        return $parent;
    }

    private function assertNotDescendant(ArticleCategory $category, int $parentId): void
    {
        if ($parentId === 0) {
            return;
        }

        $all = ArticleCategory::query()->get(['id', 'parent_id']);
        $childrenMap = [];
        foreach ($all as $item) {
            $childrenMap[(string) $item->parent_id][] = (string) $item->id;
        }

        $stack = [(string) $category->id];
        while ($stack !== []) {
            $current = array_pop($stack);
            foreach ($childrenMap[$current] ?? [] as $childId) {
                if ($childId === (string) $parentId) {
                    throw new BusinessException(
                        $this->code(ArticleCategoryError::PARENT_INVALID),
                        '上级分类不能是自己的子分类'
                    );
                }
                $stack[] = $childId;
            }
        }
    }

    private function assertNameUnique(string $name, int $parentId, ?string $excludeId = null): void
    {
        $exists = ArticleCategory::query()
            ->where('cat_name', $name)
            ->where('parent_id', $parentId)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException($this->code(ArticleCategoryError::NAME_DUPLICATED), '同级分类名称已存在');
        }
    }

    private function assertUrlUnique(string $url, ?string $excludeId = null): void
    {
        if ($url === '') {
            return;
        }

        $exists = ArticleCategory::query()
            ->where('cat_url', $url)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException($this->code(ArticleCategoryError::URL_DUPLICATED), 'URL别名已存在');
        }
    }

    private function code(int $error): int
    {
        return CodePrefix::ARTICLE_CATEGORY * 1000 + $error;
    }
}
