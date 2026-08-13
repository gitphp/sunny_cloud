<?php

namespace App\Service;

use App\Constants\Code\CodePrefix;
use App\Constants\Code\ProductCategoryError;
use App\Enums\CategoryLevel;
use App\Enums\CategoryShowType;
use App\Enums\CategoryStatus;
use App\Enums\CategoryType;
use App\Exceptions\BusinessException;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    public function getTree(?string $keyword = null, ?int $categoryType = null): array
    {
        $baseQuery = Category::query()->orderByDesc('sort_order')->orderBy('id');

        if ($categoryType !== null) {
            $baseQuery->where('category_type', $categoryType);
        }

        if ($keyword !== null && $keyword !== '') {
            $matchedIds = (clone $baseQuery)
                ->where('category_name', 'like', '%'.$keyword.'%')
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

    public function create(array $data): Category
    {
        $parentId = $this->normalizeParentId($data['parent_id'] ?? 0);
        $parent = $this->assertParentExists($parentId);
        $level = $this->resolveLevel($parent);

        $this->assertNameUnique($data['category_name'], $parentId);

        $categoryType = isset($data['category_type'])
            ? CategoryType::from((int) $data['category_type'])
            : ($parent?->category_type ?? CategoryType::Content);

        return Category::query()->create([
            'category_name' => $data['category_name'],
            'parent_id' => $parentId,
            'category_type' => $categoryType,
            'show_type' => CategoryShowType::from((int) ($data['show_type'] ?? CategoryShowType::All->value)),
            'cat_status' => CategoryStatus::from((int) ($data['cat_status'] ?? CategoryStatus::Visible->value)),
            'level' => CategoryLevel::from($level),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'description' => (string) ($data['description'] ?? ''),
            'cat_remark' => (string) ($data['cat_remark'] ?? ''),
            'created_by' => Auth::guard('backend')->id(),
            'updated_by' => Auth::guard('backend')->id(),
        ]);
    }

    public function update(Category $category, array $data): Category
    {
        $parentId = $this->normalizeParentId($data['parent_id'] ?? $category->parent_id);

        if ((string) $parentId === (string) $category->id) {
            throw new BusinessException(
                $this->code(ProductCategoryError::PARENT_INVALID),
                '上级分类不能是自身'
            );
        }

        $parent = $this->assertParentExists($parentId);
        $this->assertNotDescendant($category, $parentId);

        $level = $this->resolveLevel($parent);
        $this->assertSubtreeLevelAllowed($category, $level);

        $categoryName = $data['category_name'] ?? $category->category_name;
        $this->assertNameUnique($categoryName, $parentId, (string) $category->id);

        $oldLevel = $category->level?->value ?? 1;

        $category->fill([
            'category_name' => $categoryName,
            'parent_id' => $parentId,
            'category_type' => isset($data['category_type'])
                ? CategoryType::from((int) $data['category_type'])
                : ($category->category_type ?? CategoryType::Content),
            'show_type' => isset($data['show_type'])
                ? CategoryShowType::from((int) $data['show_type'])
                : $category->show_type,
            'cat_status' => isset($data['cat_status'])
                ? CategoryStatus::from((int) $data['cat_status'])
                : $category->cat_status,
            'level' => CategoryLevel::from($level),
            'sort_order' => (int) ($data['sort_order'] ?? $category->sort_order),
            'description' => (string) ($data['description'] ?? $category->description),
            'cat_remark' => (string) ($data['cat_remark'] ?? $category->cat_remark),
            'updated_by' => Auth::guard('backend')->id(),
        ]);
        $category->save();

        if ($oldLevel !== $level) {
            $this->refreshDescendantLevels($category);
        }

        return $category->fresh();
    }

    public function updateSort(Category $category, int $sortOrder): Category
    {
        $category->sort_order = $sortOrder;
        $category->updated_by = Auth::guard('backend')->id();
        $category->save();

        return $category;
    }

    public function updateStatus(Category $category, int $status): Category
    {
        $category->cat_status = CategoryStatus::from($status);
        $category->updated_by = Auth::guard('backend')->id();
        $category->save();

        return $category;
    }

    public function delete(Category $category): void
    {
        if ($category->children()->exists()) {
            throw new BusinessException(
                $this->code(ProductCategoryError::DELETE_BLOCKED_HAS_CHILDREN),
                '存在子分类，不可删除'
            );
        }

        $category->deleted_by = Auth::guard('backend')->id();
        $category->save();
        $category->delete();
    }

    private function code(int $error): int
    {
        return CodePrefix::PRODUCT_CATEGORY * 1000 + $error;
    }

    private function normalizeParentId(mixed $parentId): int|string
    {
        if ($parentId === null || $parentId === '' || (int) $parentId === 0) {
            return 0;
        }

        return $parentId;
    }

    private function assertParentExists(int|string $parentId): ?Category
    {
        if ((string) $parentId === '0') {
            return null;
        }

        $parent = Category::query()->where('id', $parentId)->first();
        if (! $parent) {
            throw new BusinessException(
                $this->code(ProductCategoryError::PARENT_NOT_FOUND),
                '上级分类不存在'
            );
        }

        return $parent;
    }

    private function resolveLevel(?Category $parent): int
    {
        $level = $parent ? (($parent->level?->value ?? 1) + 1) : CategoryLevel::Level1->value;

        if ($level > CategoryLevel::max()) {
            throw new BusinessException(
                $this->code(ProductCategoryError::LEVEL_EXCEEDED),
                '最多支持三级分类'
            );
        }

        return $level;
    }

    private function assertSubtreeLevelAllowed(Category $category, int $newLevel): void
    {
        $depth = $this->subtreeDepth($category);
        if ($newLevel + $depth - 1 > CategoryLevel::max()) {
            throw new BusinessException(
                $this->code(ProductCategoryError::LEVEL_EXCEEDED),
                '移动后将超过三级分类限制'
            );
        }
    }

    private function subtreeDepth(Category $category): int
    {
        $max = 1;
        foreach (Category::query()->where('parent_id', $category->id)->get() as $child) {
            $max = max($max, 1 + $this->subtreeDepth($child));
        }

        return $max;
    }

    private function refreshDescendantLevels(Category $category): void
    {
        $parentLevel = $category->level?->value ?? 1;
        $children = Category::query()->where('parent_id', $category->id)->get();

        foreach ($children as $child) {
            $child->level = CategoryLevel::from($parentLevel + 1);
            $child->updated_by = Auth::guard('backend')->id();
            $child->save();
            $this->refreshDescendantLevels($child);
        }
    }

    private function assertNameUnique(string $name, int|string $parentId, ?string $excludeId = null): void
    {
        $exists = Category::query()
            ->where('parent_id', $parentId)
            ->where('category_name', $name)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException(
                $this->code(ProductCategoryError::NAME_DUPLICATED),
                '同级分类名称已存在'
            );
        }
    }

    private function assertNotDescendant(Category $category, int|string $parentId): void
    {
        if ((string) $parentId === '0') {
            return;
        }

        $descendantIds = $this->collectDescendantIds($category);
        if (in_array((string) $parentId, $descendantIds, true)) {
            throw new BusinessException(
                $this->code(ProductCategoryError::PARENT_INVALID),
                '上级分类不能是当前分类的子级'
            );
        }
    }

    private function collectDescendantIds(Category $category): array
    {
        $ids = [];
        $children = Category::query()->where('parent_id', $category->id)->get();
        foreach ($children as $child) {
            $ids[] = (string) $child->id;
            $ids = array_merge($ids, $this->collectDescendantIds($child));
        }

        return $ids;
    }

    private function collectAncestorIds(Collection $all, array $matchedIds): array
    {
        $map = $all->keyBy(fn ($item) => (string) $item->id);
        $keep = [];

        foreach ($matchedIds as $id) {
            $current = $map->get((string) $id);
            while ($current) {
                $keep[(string) $current->id] = true;
                if ((string) $current->parent_id === '0') {
                    break;
                }
                $current = $map->get((string) $current->parent_id);
            }
        }

        return array_keys($keep);
    }

    private function buildTree(Collection $items, int|string $parentId = 0): array
    {
        $tree = [];
        $parentKey = (string) $parentId;
        foreach ($items as $item) {
            if ((string) $item->parent_id !== $parentKey) {
                continue;
            }
            $node = $this->toArray($item);
            $node['children'] = $this->buildTree($items, $item->id);
            $tree[] = $node;
        }

        return $tree;
    }

    private function toArray(Category $category): array
    {
        return [
            'id' => (string) $category->id,
            'category_name' => $category->category_name,
            'parent_id' => (string) $category->parent_id,
            'category_type' => $category->category_type?->value ?? CategoryType::Content->value,
            'category_type_label' => $category->category_type?->label() ?? CategoryType::Content->label(),
            'show_type' => $category->show_type?->value,
            'show_type_label' => $category->show_type?->label(),
            'cat_status' => $category->cat_status?->value,
            'cat_status_label' => $category->cat_status?->label(),
            'level' => $category->level?->value,
            'level_label' => $category->level?->label(),
            'sort_order' => (int) $category->sort_order,
            'description' => $category->description,
            'cat_remark' => $category->cat_remark,
        ];
    }
}
