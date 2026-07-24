<?php

namespace App\Service;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CategoryService
{
    public function getTree(?string $keyword = null): array
    {
        $query = Category::query()->orderByDesc('sort')->orderBy('id');

        if ($keyword !== null && $keyword !== '') {
            $matchedIds = Category::query()
                ->where('name', 'like', '%'.$keyword.'%')
                ->pluck('id')
                ->all();

            if ($matchedIds === []) {
                return [];
            }

            $all = Category::query()->orderByDesc('sort')->orderBy('id')->get();
            $keepIds = $this->collectAncestorIds($all, $matchedIds);

            return $this->buildTree(
                $all->whereIn('id', $keepIds)->values()
            );
        }

        $roots = Category::query()
            ->where('parent_id', 0)
            ->with('childrenRecursive')
            ->orderByDesc('sort')
            ->orderBy('id')
            ->get();

        return $this->formatTree($roots);
    }

    public function create(array $data): Category
    {
        $parentId = (int) ($data['parent_id'] ?? 0);
        $this->assertParentExists($parentId);

        return Category::query()->create([
            'name' => $data['name'],
            'parent_id' => $parentId,
            'sort' => (int) ($data['sort'] ?? 0),
        ]);
    }

    public function update(Category $category, array $data): Category
    {
        $parentId = (int) ($data['parent_id'] ?? $category->parent_id);

        if ($parentId === (int) $category->id) {
            throw new InvalidArgumentException('上级分类不能是自身');
        }

        $this->assertParentExists($parentId);
        $this->assertNotDescendant($category, $parentId);

        $category->fill([
            'name' => $data['name'] ?? $category->name,
            'parent_id' => $parentId,
            'sort' => (int) ($data['sort'] ?? $category->sort),
        ]);
        $category->save();

        return $category->fresh();
    }

    public function updateSort(Category $category, int $sort): Category
    {
        $category->sort = $sort;
        $category->save();

        return $category;
    }

    public function delete(Category $category): void
    {
        DB::transaction(function () use ($category) {
            $this->deleteRecursive($category);
        });
    }

    private function deleteRecursive(Category $category): void
    {
        foreach ($category->children as $child) {
            $this->deleteRecursive($child);
        }
        $category->delete();
    }

    private function assertParentExists(int $parentId): void
    {
        if ($parentId === 0) {
            return;
        }

        if (! Category::query()->where('id', $parentId)->exists()) {
            throw new InvalidArgumentException('上级分类不存在');
        }
    }

    private function assertNotDescendant(Category $category, int $parentId): void
    {
        if ($parentId === 0) {
            return;
        }

        $descendantIds = $this->collectDescendantIds($category);
        if (in_array($parentId, $descendantIds, true)) {
            throw new InvalidArgumentException('上级分类不能是当前分类的子级');
        }
    }

    private function collectDescendantIds(Category $category): array
    {
        $ids = [];
        $children = Category::query()->where('parent_id', $category->id)->get();
        foreach ($children as $child) {
            $ids[] = (int) $child->id;
            $ids = array_merge($ids, $this->collectDescendantIds($child));
        }

        return $ids;
    }

    private function collectAncestorIds(Collection $all, array $matchedIds): array
    {
        $map = $all->keyBy('id');
        $keep = [];

        foreach ($matchedIds as $id) {
            $current = $map->get($id);
            while ($current) {
                $keep[$current->id] = true;
                if ((int) $current->parent_id === 0) {
                    break;
                }
                $current = $map->get($current->parent_id);
            }
        }

        return array_map('intval', array_keys($keep));
    }

    private function buildTree(Collection $items, int $parentId = 0): array
    {
        $tree = [];
        foreach ($items->where('parent_id', $parentId)->values() as $item) {
            $node = $this->toArray($item);
            $node['children'] = $this->buildTree($items, (int) $item->id);
            $tree[] = $node;
        }

        return $tree;
    }

    private function formatTree(Collection $nodes): array
    {
        return $nodes->map(function (Category $node) {
            $item = $this->toArray($node);
            $children = $node->relationLoaded('childrenRecursive')
                ? $node->childrenRecursive
                : $node->children;
            $item['children'] = $this->formatTree($children);

            return $item;
        })->values()->all();
    }

    private function toArray(Category $category): array
    {
        return [
            'id' => (int) $category->id,
            'name' => $category->name,
            'parent_id' => (int) $category->parent_id,
            'sort' => (int) $category->sort,
        ];
    }
}
