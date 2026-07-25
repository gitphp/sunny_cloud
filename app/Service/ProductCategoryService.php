<?php

namespace App\Service;

use App\Constants\Code\CodePrefix;
use App\Constants\Code\ProductCategoryError;
use App\Enums\CategoryLevel;
use App\Enums\ProductShowStatus;
use App\Exceptions\BusinessException;
use App\Models\ProductCategory;
use App\Support\SeqCode;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class ProductCategoryService
{
    public function getTree(?string $keyword = null): array
    {
        $baseQuery = ProductCategory::query()->orderByDesc('sort_order')->orderBy('id');

        if ($keyword !== null && $keyword !== '') {
            $matchedIds = (clone $baseQuery)
                ->where(function ($q) use ($keyword) {
                    $q->where('category_name', 'like', '%'.$keyword.'%')
                        ->orWhere('category_code', 'like', '%'.$keyword.'%');
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

    public function create(array $data): ProductCategory
    {
        $parentId = $this->normalizeParentId($data['parent_id'] ?? 0);
        $parent = $this->assertParentExists($parentId);
        $level = $this->resolveLevel($parent);
        $this->assertNameUnique($data['category_name'], $parentId);

        return ProductCategory::query()->create([
            'category_code' => SeqCode::next(ProductCategory::class, 'category_code', 'FL'),
            'category_name' => $data['category_name'],
            'parent_id' => $parentId,
            'level' => CategoryLevel::from($level),
            'product_count' => 0,
            'unit' => (string) ($data['unit'] ?? ''),
            'cat_status' => ProductShowStatus::from((int) ($data['cat_status'] ?? ProductShowStatus::Visible->value)),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'cat_remark' => (string) ($data['cat_remark'] ?? ''),
            'created_by' => Auth::guard('backend')->id(),
            'updated_by' => Auth::guard('backend')->id(),
        ]);
    }

    public function update(ProductCategory $category, array $data): ProductCategory
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

        $name = $data['category_name'] ?? $category->category_name;
        $this->assertNameUnique($name, $parentId, (string) $category->id);
        $oldLevel = $category->level?->value ?? 1;

        $category->fill([
            'category_name' => $name,
            'parent_id' => $parentId,
            'level' => CategoryLevel::from($level),
            'unit' => (string) ($data['unit'] ?? $category->unit),
            'cat_status' => isset($data['cat_status'])
                ? ProductShowStatus::from((int) $data['cat_status'])
                : $category->cat_status,
            'sort_order' => (int) ($data['sort_order'] ?? $category->sort_order),
            'cat_remark' => (string) ($data['cat_remark'] ?? $category->cat_remark),
            'updated_by' => Auth::guard('backend')->id(),
        ]);
        $category->save();

        if ($oldLevel !== $level) {
            $this->refreshDescendantLevels($category);
        }

        return $category->fresh();
    }

    public function updateSort(ProductCategory $category, int $sort): ProductCategory
    {
        $category->sort_order = $sort;
        $category->updated_by = Auth::guard('backend')->id();
        $category->save();

        return $category;
    }

    public function updateStatus(ProductCategory $category, int $status): ProductCategory
    {
        $category->cat_status = ProductShowStatus::from($status);
        $category->updated_by = Auth::guard('backend')->id();
        $category->save();

        return $category;
    }

    public function delete(ProductCategory $category): void
    {
        if ($category->children()->exists()) {
            throw new BusinessException(
                $this->code(ProductCategoryError::DELETE_BLOCKED_HAS_CHILDREN),
                '存在子分类，不可删除'
            );
        }

        if ((int) $category->product_count > 0) {
            throw new BusinessException(
                $this->code(ProductCategoryError::DELETE_BLOCKED_HAS_PRODUCTS),
                '分类下存在商品，不可删除'
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

    private function assertParentExists(int|string $parentId): ?ProductCategory
    {
        if ((string) $parentId === '0') {
            return null;
        }

        $parent = ProductCategory::query()->where('id', $parentId)->first();
        if (! $parent) {
            throw new BusinessException(
                $this->code(ProductCategoryError::PARENT_NOT_FOUND),
                '上级分类不存在'
            );
        }

        return $parent;
    }

    private function resolveLevel(?ProductCategory $parent): int
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

    private function assertSubtreeLevelAllowed(ProductCategory $category, int $newLevel): void
    {
        $depth = $this->subtreeDepth($category);
        if ($newLevel + $depth - 1 > CategoryLevel::max()) {
            throw new BusinessException(
                $this->code(ProductCategoryError::LEVEL_EXCEEDED),
                '移动后将超过三级分类限制'
            );
        }
    }

    private function subtreeDepth(ProductCategory $category): int
    {
        $max = 1;
        foreach (ProductCategory::query()->where('parent_id', $category->id)->get() as $child) {
            $max = max($max, 1 + $this->subtreeDepth($child));
        }

        return $max;
    }

    private function refreshDescendantLevels(ProductCategory $category): void
    {
        $parentLevel = $category->level?->value ?? 1;
        foreach (ProductCategory::query()->where('parent_id', $category->id)->get() as $child) {
            $child->level = CategoryLevel::from($parentLevel + 1);
            $child->updated_by = Auth::guard('backend')->id();
            $child->save();
            $this->refreshDescendantLevels($child);
        }
    }

    private function assertNameUnique(string $name, int|string $parentId, ?string $excludeId = null): void
    {
        $exists = ProductCategory::query()
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

    private function assertNotDescendant(ProductCategory $category, int|string $parentId): void
    {
        if ((string) $parentId === '0') {
            return;
        }

        $ids = $this->collectDescendantIds($category);
        if (in_array((string) $parentId, $ids, true)) {
            throw new BusinessException(
                $this->code(ProductCategoryError::PARENT_INVALID),
                '上级分类不能是当前分类的子级'
            );
        }
    }

    private function collectDescendantIds(ProductCategory $category): array
    {
        $ids = [];
        foreach (ProductCategory::query()->where('parent_id', $category->id)->get() as $child) {
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

    private function toArray(ProductCategory $category): array
    {
        return [
            'id' => (string) $category->id,
            'category_code' => $category->category_code,
            'category_name' => $category->category_name,
            'parent_id' => (string) $category->parent_id,
            'level' => $category->level?->value,
            'level_label' => $category->level?->label(),
            'product_count' => (int) $category->product_count,
            'unit' => $category->unit,
            'cat_status' => $category->cat_status?->value,
            'cat_status_label' => $category->cat_status?->label(),
            'sort_order' => (int) $category->sort_order,
            'cat_remark' => $category->cat_remark,
        ];
    }
}
