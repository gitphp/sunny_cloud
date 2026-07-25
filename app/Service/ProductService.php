<?php

namespace App\Service;

use App\Constants\Code\CodePrefix;
use App\Constants\Code\ProductError;
use App\Enums\ProductMediaType;
use App\Enums\ProductSkuSaleStatus;
use App\Enums\ProductStatus;
use App\Exceptions\BusinessException;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductMedia;
use App\Models\ProductSku;
use App\Models\ProductSkuSpecValue;
use App\Models\ProductSpecification;
use App\Models\ProductSpecificationValue;
use App\Support\SeqCode;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::query()
            ->with(['category:id,category_name', 'brand:id,brand_name'])
            ->orderByDesc('sort_order')
            ->orderByDesc('id');

        if (! empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('product_name', 'like', '%'.$kw.'%')
                    ->orWhere('product_model', 'like', '%'.$kw.'%')
                    ->orWhere('auto_code', 'like', '%'.$kw.'%');
            });
        }
        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (! empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }
        if (isset($filters['product_status']) && $filters['product_status'] !== '' && $filters['product_status'] !== null) {
            $query->where('product_status', (int) $filters['product_status']);
        }

        return $query->paginate($perPage);
    }

    public function detail(Product $product): array
    {
        $product->load([
            'category:id,category_name',
            'brand:id,brand_name',
            'skus.specValues.value',
            'skus.specValues.specification',
            'media',
        ]);

        return $this->toDetailArray($product);
    }

    public function create(array $data): Product
    {
        $this->assertCategory($data['category_id'] ?? 0);
        $this->assertBrand($data['brand_id'] ?? 0);

        return DB::transaction(function () use ($data) {
            $operator = Auth::guard('backend')->id();
            $mainImage = $this->extractMainImageUrl($data);

            $product = Product::query()->create([
                'auto_code' => SeqCode::next(Product::class, 'auto_code', 'SP'),
                'product_name' => $data['product_name'],
                'product_model' => (string) ($data['product_model'] ?? ''),
                'category_id' => $data['category_id'],
                'brand_id' => $data['brand_id'] ?? 0,
                'material_quality' => (string) ($data['material_quality'] ?? ''),
                'filling' => (string) ($data['filling'] ?? ''),
                'short_desc' => $data['short_desc'] ?? null,
                'main_image_url' => $mainImage,
                'product_status' => ProductStatus::from((int) ($data['product_status'] ?? ProductStatus::OnShelf->value)),
                'sort_order' => (int) ($data['sort_order'] ?? 0),
                'created_by' => $operator,
                'updated_by' => $operator,
            ]);

            $this->syncSkus($product, $data['skus'] ?? []);
            $this->syncMedia($product, $data['media'] ?? [], $mainImage);
            $this->bumpCategoryCount((int) $product->category_id, 1);

            return $product->fresh(['category', 'brand', 'skus.specValues', 'media']);
        });
    }

    public function update(Product $product, array $data): Product
    {
        $this->assertCategory($data['category_id'] ?? $product->category_id);
        $this->assertBrand($data['brand_id'] ?? $product->brand_id);

        return DB::transaction(function () use ($product, $data) {
            $operator = Auth::guard('backend')->id();
            $oldCategoryId = (int) $product->category_id;
            $mainImage = $this->extractMainImageUrl($data, $product->main_image_url);

            $product->fill([
                'product_name' => $data['product_name'] ?? $product->product_name,
                'product_model' => (string) ($data['product_model'] ?? $product->product_model),
                'category_id' => $data['category_id'] ?? $product->category_id,
                'brand_id' => $data['brand_id'] ?? $product->brand_id,
                'material_quality' => (string) ($data['material_quality'] ?? $product->material_quality),
                'filling' => (string) ($data['filling'] ?? $product->filling),
                'short_desc' => array_key_exists('short_desc', $data) ? $data['short_desc'] : $product->short_desc,
                'main_image_url' => $mainImage,
                'product_status' => isset($data['product_status'])
                    ? ProductStatus::from((int) $data['product_status'])
                    : $product->product_status,
                'sort_order' => (int) ($data['sort_order'] ?? $product->sort_order),
                'updated_by' => $operator,
            ]);
            $product->save();

            if (array_key_exists('skus', $data)) {
                $this->replaceSkus($product, $data['skus'] ?? []);
            }
            if (array_key_exists('media', $data)) {
                $this->replaceMedia($product, $data['media'] ?? [], $mainImage);
            }

            $newCategoryId = (int) $product->category_id;
            if ($oldCategoryId !== $newCategoryId) {
                $this->bumpCategoryCount($oldCategoryId, -1);
                $this->bumpCategoryCount($newCategoryId, 1);
            }

            return $product->fresh(['category', 'brand', 'skus.specValues', 'media']);
        });
    }

    public function updateStatus(Product $product, int $status): Product
    {
        $product->product_status = ProductStatus::from($status);
        $product->updated_by = Auth::guard('backend')->id();
        $product->save();

        return $product;
    }

    public function delete(Product $product): void
    {
        DB::transaction(function () use ($product) {
            $operator = Auth::guard('backend')->id();
            $categoryId = (int) $product->category_id;

            foreach ($product->skus as $sku) {
                ProductSkuSpecValue::query()->where('sku_id', $sku->id)->update(['deleted_by' => $operator]);
                ProductSkuSpecValue::query()->where('sku_id', $sku->id)->delete();
                $sku->deleted_by = $operator;
                $sku->save();
                $sku->delete();
            }

            ProductMedia::query()->where('product_id', $product->id)->update(['deleted_by' => $operator]);
            ProductMedia::query()->where('product_id', $product->id)->delete();

            $product->deleted_by = $operator;
            $product->save();
            $product->delete();

            $this->bumpCategoryCount($categoryId, -1);
        });
    }

    public function toListArray(Product $product): array
    {
        return [
            'id' => (string) $product->id,
            'auto_code' => $product->auto_code,
            'product_name' => $product->product_name,
            'product_model' => $product->product_model,
            'category_id' => (string) $product->category_id,
            'category_name' => $product->category?->category_name,
            'brand_id' => (string) $product->brand_id,
            'brand_name' => $product->brand?->brand_name,
            'main_image_url' => $product->main_image_url,
            'product_status' => $product->product_status?->value,
            'product_status_label' => $product->product_status?->label(),
            'sort_order' => (int) $product->sort_order,
            'created_at' => optional($product->created_at)?->format('Y-m-d H:i:s'),
        ];
    }

    public function toDetailArray(Product $product): array
    {
        $skus = $product->skus->map(function (ProductSku $sku) {
            $specValues = $sku->specValues->map(fn (ProductSkuSpecValue $row) => [
                'spec_id' => (string) $row->spec_id,
                'spec_name' => $row->specification?->spec_name,
                'spec_value_id' => (string) $row->spec_value_id,
                'value' => $row->value?->value,
            ])->values()->all();

            return [
                'id' => (string) $sku->id,
                'sku_code' => $sku->sku_code,
                'price' => (float) $sku->price,
                'market_price' => (float) $sku->market_price,
                'cost_price' => (float) $sku->cost_price,
                'stock_num' => (int) $sku->stock_num,
                'weight' => (float) $sku->weight,
                'volume' => (float) $sku->volume,
                'sale_status' => $sku->sale_status?->value,
                'sort_order' => (int) $sku->sort_order,
                'spec_values' => $specValues,
            ];
        })->values()->all();

        $media = $product->media->map(fn (ProductMedia $item) => [
            'id' => (string) $item->id,
            'media_type' => $item->media_type?->value,
            'media_type_label' => $item->media_type?->label(),
            'file_url' => $item->file_url,
            'file_name' => $item->file_name,
            'file_key' => $item->file_key,
            'storage_provider' => $item->storage_provider,
            'extension' => $item->extension,
            'file_size' => (int) $item->file_size,
            'file_type' => $item->file_type,
            'sort_order' => (int) $item->sort_order,
        ])->values()->all();

        return [
            'id' => (string) $product->id,
            'auto_code' => $product->auto_code,
            'product_name' => $product->product_name,
            'product_model' => $product->product_model,
            'category_id' => (string) $product->category_id,
            'category_name' => $product->category?->category_name,
            'brand_id' => (string) $product->brand_id,
            'brand_name' => $product->brand?->brand_name,
            'material_quality' => $product->material_quality,
            'filling' => $product->filling,
            'short_desc' => $product->short_desc,
            'main_image_url' => $product->main_image_url,
            'product_status' => $product->product_status?->value,
            'product_status_label' => $product->product_status?->label(),
            'sort_order' => (int) $product->sort_order,
            'skus' => $skus,
            'media' => $media,
            'spec_rows' => $this->rebuildSpecRows($skus),
        ];
    }

    private function code(int $error): int
    {
        return CodePrefix::PRODUCT * 1000 + $error;
    }

    private function assertCategory(mixed $categoryId): void
    {
        if ((string) $categoryId === '0' || $categoryId === null || $categoryId === '') {
            throw new BusinessException($this->code(ProductError::CATEGORY_NOT_FOUND), '请选择商品分类');
        }
        if (! ProductCategory::query()->where('id', $categoryId)->exists()) {
            throw new BusinessException($this->code(ProductError::CATEGORY_NOT_FOUND), '商品分类不存在');
        }
    }

    private function assertBrand(mixed $brandId): void
    {
        if ((string) $brandId === '0' || $brandId === null || $brandId === '') {
            return;
        }
        if (! ProductBrand::query()->where('id', $brandId)->exists()) {
            throw new BusinessException($this->code(ProductError::BRAND_NOT_FOUND), '商品品牌不存在');
        }
    }

    private function extractMainImageUrl(array $data, string $fallback = ''): string
    {
        if (! empty($data['main_image_url'])) {
            return (string) $data['main_image_url'];
        }
        foreach ($data['media'] ?? [] as $item) {
            if ((int) ($item['media_type'] ?? 0) === ProductMediaType::MainImage->value && ! empty($item['file_url'])) {
                return (string) $item['file_url'];
            }
        }

        return $fallback;
    }

    private function syncSkus(Product $product, array $skus): void
    {
        $sort = 0;
        foreach ($skus as $row) {
            $this->createSku($product, $row, $sort++);
        }
    }

    private function replaceSkus(Product $product, array $skus): void
    {
        $operator = Auth::guard('backend')->id();
        foreach ($product->skus as $sku) {
            ProductSkuSpecValue::query()->where('sku_id', $sku->id)->update(['deleted_by' => $operator]);
            ProductSkuSpecValue::query()->where('sku_id', $sku->id)->delete();
            $sku->deleted_by = $operator;
            $sku->save();
            $sku->delete();
        }
        $this->syncSkus($product->fresh(), $skus);
    }

    private function createSku(Product $product, array $row, int $sort): ProductSku
    {
        $operator = Auth::guard('backend')->id();
        $specValues = $row['spec_values'] ?? [];
        $this->assertSkuSpecValues($specValues);

        $sku = ProductSku::query()->create([
            'product_id' => $product->id,
            'sku_code' => SeqCode::next(ProductSku::class, 'sku_code', 'SKU'),
            'price' => (float) ($row['price'] ?? 0),
            'market_price' => (float) ($row['market_price'] ?? 0),
            'cost_price' => (float) ($row['cost_price'] ?? 0),
            'stock_num' => (int) ($row['stock_num'] ?? 0),
            'weight' => (float) ($row['weight'] ?? 0),
            'volume' => (float) ($row['volume'] ?? 0),
            'sale_status' => ProductSkuSaleStatus::from((int) ($row['sale_status'] ?? ProductSkuSaleStatus::OnShelf->value)),
            'sort_order' => (int) ($row['sort_order'] ?? $sort),
            'created_by' => $operator,
            'updated_by' => $operator,
        ]);

        $seenSpecs = [];
        foreach ($specValues as $sv) {
            $specId = (string) ($sv['spec_id'] ?? '0');
            if (isset($seenSpecs[$specId])) {
                throw new BusinessException(
                    $this->code(ProductError::SKU_SPEC_DIMENSION_DUPLICATED),
                    '同一SKU不能包含同一规格的多个值'
                );
            }
            $seenSpecs[$specId] = true;

            ProductSkuSpecValue::query()->create([
                'sku_id' => $sku->id,
                'spec_id' => $sv['spec_id'],
                'spec_value_id' => $sv['spec_value_id'],
                'created_by' => $operator,
                'updated_by' => $operator,
            ]);
        }

        return $sku;
    }

    private function assertSkuSpecValues(array $specValues): void
    {
        foreach ($specValues as $sv) {
            $specId = $sv['spec_id'] ?? null;
            $valueId = $sv['spec_value_id'] ?? null;
            if (! $specId || ! $valueId) {
                throw new BusinessException(
                    $this->code(ProductError::SKU_SPEC_VALUE_NOT_FOUND),
                    '规格值不存在或不可用'
                );
            }
            $exists = ProductSpecificationValue::query()
                ->where('id', $valueId)
                ->where('spec_id', $specId)
                ->exists();
            if (! $exists || ! ProductSpecification::query()->where('id', $specId)->exists()) {
                throw new BusinessException(
                    $this->code(ProductError::SKU_SPEC_VALUE_NOT_FOUND),
                    '规格值不存在或不可用'
                );
            }
        }
    }

    private function syncMedia(Product $product, array $media, string $mainImage): void
    {
        $operator = Auth::guard('backend')->id();
        $sort = 0;
        $hasMain = false;

        foreach ($media as $item) {
            $type = (int) ($item['media_type'] ?? 0);
            if ($type === ProductMediaType::MainImage->value) {
                $hasMain = true;
            }
            if (empty($item['file_url'])) {
                continue;
            }
            ProductMedia::query()->create([
                'product_id' => $product->id,
                'media_type' => ProductMediaType::from($type),
                'file_url' => (string) $item['file_url'],
                'file_name' => (string) ($item['file_name'] ?? ''),
                'file_key' => (string) ($item['file_key'] ?? ''),
                'storage_provider' => (string) ($item['storage_provider'] ?? 'local'),
                'extension' => (string) ($item['extension'] ?? ''),
                'file_size' => (int) ($item['file_size'] ?? 0),
                'file_type' => (string) ($item['file_type'] ?? ''),
                'sort_order' => (int) ($item['sort_order'] ?? $sort++),
                'created_by' => $operator,
                'updated_by' => $operator,
            ]);
        }

        if (! $hasMain && $mainImage !== '') {
            ProductMedia::query()->create([
                'product_id' => $product->id,
                'media_type' => ProductMediaType::MainImage,
                'file_url' => $mainImage,
                'file_name' => '',
                'file_key' => ltrim($mainImage, '/'),
                'storage_provider' => 'local',
                'extension' => pathinfo($mainImage, PATHINFO_EXTENSION) ?: '',
                'file_size' => 0,
                'file_type' => '',
                'sort_order' => 0,
                'created_by' => $operator,
                'updated_by' => $operator,
            ]);
        }
    }

    private function replaceMedia(Product $product, array $media, string $mainImage): void
    {
        $operator = Auth::guard('backend')->id();
        ProductMedia::query()->where('product_id', $product->id)->update(['deleted_by' => $operator]);
        ProductMedia::query()->where('product_id', $product->id)->delete();
        $this->syncMedia($product, $media, $mainImage);
    }

    private function bumpCategoryCount(int $categoryId, int $delta): void
    {
        if ($categoryId <= 0 || $delta === 0) {
            return;
        }
        $category = ProductCategory::query()->where('id', $categoryId)->first();
        if (! $category) {
            return;
        }
        $category->product_count = max(0, (int) $category->product_count + $delta);
        $category->save();
    }

    private function rebuildSpecRows(array $skus): array
    {
        $map = [];
        foreach ($skus as $sku) {
            foreach ($sku['spec_values'] ?? [] as $sv) {
                $specId = (string) $sv['spec_id'];
                if (! isset($map[$specId])) {
                    $map[$specId] = [
                        'spec_id' => $specId,
                        'spec_name' => $sv['spec_name'] ?? '',
                        'spec_value_ids' => [],
                    ];
                }
                $valueId = (string) $sv['spec_value_id'];
                if (! in_array($valueId, $map[$specId]['spec_value_ids'], true)) {
                    $map[$specId]['spec_value_ids'][] = $valueId;
                }
            }
        }

        return array_values($map);
    }
}
