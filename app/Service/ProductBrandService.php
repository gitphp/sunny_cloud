<?php

namespace App\Service;

use App\Constants\Code\CodePrefix;
use App\Constants\Code\ProductBrandError;
use App\Enums\ProductIsSystem;
use App\Enums\ProductShowStatus;
use App\Exceptions\BusinessException;
use App\Models\ProductBrand;
use App\Support\SeqCode;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ProductBrandService
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ProductBrand::query()->orderByDesc('sort_order')->orderByDesc('id');

        if (! empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('brand_name', 'like', '%'.$kw.'%')
                    ->orWhere('brand_code', 'like', '%'.$kw.'%')
                    ->orWhere('alias', 'like', '%'.$kw.'%');
            });
        }
        if (isset($filters['is_show']) && $filters['is_show'] !== '' && $filters['is_show'] !== null) {
            $query->where('is_show', (int) $filters['is_show']);
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): ProductBrand
    {
        $this->assertNameUnique($data['brand_name']);

        return ProductBrand::query()->create([
            'brand_code' => SeqCode::next(ProductBrand::class, 'brand_code', 'BR'),
            'brand_name' => $data['brand_name'],
            'alias' => (string) ($data['alias'] ?? ''),
            'is_system' => ProductIsSystem::Custom,
            'is_show' => ProductShowStatus::from((int) ($data['is_show'] ?? ProductShowStatus::Visible->value)),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'brand_remark' => (string) ($data['brand_remark'] ?? ''),
            'created_by' => Auth::guard('backend')->id(),
            'updated_by' => Auth::guard('backend')->id(),
        ]);
    }

    public function update(ProductBrand $brand, array $data): ProductBrand
    {
        $name = $data['brand_name'] ?? $brand->brand_name;
        $this->assertNameUnique($name, (string) $brand->id);

        $brand->fill([
            'brand_name' => $name,
            'alias' => (string) ($data['alias'] ?? $brand->alias),
            'is_show' => isset($data['is_show'])
                ? ProductShowStatus::from((int) $data['is_show'])
                : $brand->is_show,
            'sort_order' => (int) ($data['sort_order'] ?? $brand->sort_order),
            'brand_remark' => (string) ($data['brand_remark'] ?? $brand->brand_remark),
            'updated_by' => Auth::guard('backend')->id(),
        ]);
        $brand->save();

        return $brand->fresh();
    }

    public function updateSort(ProductBrand $brand, int $sort): ProductBrand
    {
        $brand->sort_order = $sort;
        $brand->updated_by = Auth::guard('backend')->id();
        $brand->save();

        return $brand;
    }

    public function updateStatus(ProductBrand $brand, int $status): ProductBrand
    {
        $brand->is_show = ProductShowStatus::from($status);
        $brand->updated_by = Auth::guard('backend')->id();
        $brand->save();

        return $brand;
    }

    public function delete(ProductBrand $brand): void
    {
        if ($brand->isSystemBrand()) {
            throw new BusinessException(
                $this->code(ProductBrandError::DELETE_BLOCKED_SYSTEM),
                '系统预设品牌不可删除'
            );
        }

        $brand->deleted_by = Auth::guard('backend')->id();
        $brand->save();
        $brand->delete();
    }

    public function toArray(ProductBrand $brand): array
    {
        return [
            'id' => (string) $brand->id,
            'brand_code' => $brand->brand_code,
            'brand_name' => $brand->brand_name,
            'alias' => $brand->alias,
            'is_system' => $brand->is_system?->value,
            'is_system_label' => $brand->is_system?->label(),
            'is_show' => $brand->is_show?->value,
            'is_show_label' => $brand->is_show?->label(),
            'sort_order' => (int) $brand->sort_order,
            'brand_remark' => $brand->brand_remark,
        ];
    }

    private function code(int $error): int
    {
        return CodePrefix::PRODUCT_BRAND * 1000 + $error;
    }

    private function assertNameUnique(string $name, ?string $excludeId = null): void
    {
        $exists = ProductBrand::query()
            ->where('brand_name', $name)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException(
                $this->code(ProductBrandError::NAME_DUPLICATED),
                '品牌名称已存在'
            );
        }
    }
}
