<?php

namespace App\Service;

use App\Constants\Code\CodePrefix;
use App\Constants\Code\ProductSpecificationError;
use App\Enums\ProductShowStatus;
use App\Exceptions\BusinessException;
use App\Models\ProductSpecification;
use App\Models\ProductSpecificationValue;
use App\Support\SeqCode;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductSpecificationService
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ProductSpecification::query()
            ->with(['values' => fn ($q) => $q->orderByDesc('sort_order')->orderBy('id')])
            ->orderByDesc('sort_order')
            ->orderByDesc('id');

        if (! empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('spec_name', 'like', '%'.$kw.'%')
                    ->orWhere('spec_code', 'like', '%'.$kw.'%');
            });
        }
        if (isset($filters['spec_status']) && $filters['spec_status'] !== '' && $filters['spec_status'] !== null) {
            $query->where('spec_status', (int) $filters['spec_status']);
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): ProductSpecification
    {
        $this->assertNameUnique($data['spec_name']);

        return ProductSpecification::query()->create([
            'spec_code' => SeqCode::next(ProductSpecification::class, 'spec_code', 'GL'),
            'spec_name' => $data['spec_name'],
            'spec_remark' => (string) ($data['spec_remark'] ?? ''),
            'spec_status' => ProductShowStatus::from((int) ($data['spec_status'] ?? ProductShowStatus::Visible->value)),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'created_by' => Auth::guard('backend')->id(),
            'updated_by' => Auth::guard('backend')->id(),
        ]);
    }

    public function update(ProductSpecification $spec, array $data): ProductSpecification
    {
        $name = $data['spec_name'] ?? $spec->spec_name;
        $this->assertNameUnique($name, (string) $spec->id);

        $spec->fill([
            'spec_name' => $name,
            'spec_remark' => (string) ($data['spec_remark'] ?? $spec->spec_remark),
            'spec_status' => isset($data['spec_status'])
                ? ProductShowStatus::from((int) $data['spec_status'])
                : $spec->spec_status,
            'sort_order' => (int) ($data['sort_order'] ?? $spec->sort_order),
            'updated_by' => Auth::guard('backend')->id(),
        ]);
        $spec->save();

        return $spec->fresh('values');
    }

    public function updateSort(ProductSpecification $spec, int $sort): ProductSpecification
    {
        $spec->sort_order = $sort;
        $spec->updated_by = Auth::guard('backend')->id();
        $spec->save();

        return $spec;
    }

    public function updateStatus(ProductSpecification $spec, int $status): ProductSpecification
    {
        $spec->spec_status = ProductShowStatus::from($status);
        $spec->updated_by = Auth::guard('backend')->id();
        $spec->save();

        return $spec;
    }

    public function delete(ProductSpecification $spec): void
    {
        DB::transaction(function () use ($spec) {
            $operator = Auth::guard('backend')->id();
            ProductSpecificationValue::query()
                ->where('spec_id', $spec->id)
                ->update(['deleted_by' => $operator]);
            ProductSpecificationValue::query()->where('spec_id', $spec->id)->delete();

            $spec->deleted_by = $operator;
            $spec->save();
            $spec->delete();
        });
    }

    public function listValues(ProductSpecification $spec): array
    {
        return $spec->values()
            ->get()
            ->map(fn (ProductSpecificationValue $v) => $this->valueToArray($v))
            ->all();
    }

    public function createValue(ProductSpecification $spec, array $data): ProductSpecificationValue
    {
        $this->assertValueUnique($spec->id, $data['value']);

        return ProductSpecificationValue::query()->create([
            'spec_id' => $spec->id,
            'value_code' => SeqCode::next(ProductSpecificationValue::class, 'value_code', 'GV'),
            'value' => $data['value'],
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'value_status' => ProductShowStatus::from((int) ($data['value_status'] ?? ProductShowStatus::Visible->value)),
            'created_by' => Auth::guard('backend')->id(),
            'updated_by' => Auth::guard('backend')->id(),
        ]);
    }

    public function updateValue(ProductSpecificationValue $value, array $data): ProductSpecificationValue
    {
        $name = $data['value'] ?? $value->value;
        $this->assertValueUnique($value->spec_id, $name, (string) $value->id);

        $value->fill([
            'value' => $name,
            'sort_order' => (int) ($data['sort_order'] ?? $value->sort_order),
            'value_status' => isset($data['value_status'])
                ? ProductShowStatus::from((int) $data['value_status'])
                : $value->value_status,
            'updated_by' => Auth::guard('backend')->id(),
        ]);
        $value->save();

        return $value->fresh();
    }

    public function updateValueSort(ProductSpecificationValue $value, int $sort): ProductSpecificationValue
    {
        $value->sort_order = $sort;
        $value->updated_by = Auth::guard('backend')->id();
        $value->save();

        return $value;
    }

    public function updateValueStatus(ProductSpecificationValue $value, int $status): ProductSpecificationValue
    {
        $value->value_status = ProductShowStatus::from($status);
        $value->updated_by = Auth::guard('backend')->id();
        $value->save();

        return $value;
    }

    public function deleteValue(ProductSpecificationValue $value): void
    {
        $value->deleted_by = Auth::guard('backend')->id();
        $value->save();
        $value->delete();
    }

    public function toArray(ProductSpecification $spec): array
    {
        return [
            'id' => (string) $spec->id,
            'spec_code' => $spec->spec_code,
            'spec_name' => $spec->spec_name,
            'spec_remark' => $spec->spec_remark,
            'spec_status' => $spec->spec_status?->value,
            'spec_status_label' => $spec->spec_status?->label(),
            'sort_order' => (int) $spec->sort_order,
            'values' => $spec->relationLoaded('values')
                ? $spec->values->map(fn (ProductSpecificationValue $v) => $this->valueToArray($v))->values()->all()
                : [],
        ];
    }

    public function valueToArray(ProductSpecificationValue $value): array
    {
        return [
            'id' => (string) $value->id,
            'spec_id' => (string) $value->spec_id,
            'value_code' => $value->value_code,
            'value' => $value->value,
            'sort_order' => (int) $value->sort_order,
            'value_status' => $value->value_status?->value,
            'value_status_label' => $value->value_status?->label(),
        ];
    }

    private function code(int $error): int
    {
        return CodePrefix::PRODUCT_SPEC * 1000 + $error;
    }

    private function assertNameUnique(string $name, ?string $excludeId = null): void
    {
        $exists = ProductSpecification::query()
            ->where('spec_name', $name)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException(
                $this->code(ProductSpecificationError::NAME_DUPLICATED),
                '规格名称已存在'
            );
        }
    }

    private function assertValueUnique(mixed $specId, string $value, ?string $excludeId = null): void
    {
        $exists = ProductSpecificationValue::query()
            ->where('spec_id', $specId)
            ->where('value', $value)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException(
                CodePrefix::PRODUCT_SPEC_VALUE * 1000 + ProductSpecificationError::VALUE_DUPLICATED,
                '规格值已存在'
            );
        }
    }
}
