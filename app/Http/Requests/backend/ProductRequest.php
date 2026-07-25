<?php

namespace App\Http\Requests\backend;

use App\Enums\ProductMediaType;
use App\Enums\ProductSkuSaleStatus;
use App\Enums\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create', 'update' => $this->formRules(),
            'status' => [
                'product_status' => ['required', 'integer', Rule::in(array_column(ProductStatus::cases(), 'value'))],
            ],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'product_name.required' => '商品名称不能为空',
            'product_model.required' => '型号不能为空',
            'category_id.required' => '请选择商品分类',
            'product_status.required' => '请设置商品状态',
        ];
    }

    protected function scene(): string
    {
        if ($this->isMethod('POST')) {
            return 'create';
        }
        if ($this->isMethod('PATCH') && str_ends_with($this->path(), '/status')) {
            return 'status';
        }
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return 'update';
        }

        return 'default';
    }

    private function formRules(): array
    {
        return [
            'product_name' => ['required', 'string', 'max:64'],
            'product_model' => ['required', 'string', 'max:128'],
            'category_id' => ['required'],
            'brand_id' => ['nullable'],
            'material_quality' => ['nullable', 'string', 'max:128'],
            'filling' => ['nullable', 'string', 'max:128'],
            'short_desc' => ['nullable', 'string'],
            'main_image_url' => ['nullable', 'string', 'max:512'],
            'product_status' => ['required', 'integer', Rule::in(array_column(ProductStatus::cases(), 'value'))],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'skus' => ['nullable', 'array'],
            'skus.*.price' => ['nullable', 'numeric', 'min:0'],
            'skus.*.market_price' => ['nullable', 'numeric', 'min:0'],
            'skus.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'skus.*.stock_num' => ['nullable', 'integer', 'min:0'],
            'skus.*.weight' => ['nullable', 'numeric', 'min:0'],
            'skus.*.volume' => ['nullable', 'numeric', 'min:0'],
            'skus.*.sale_status' => ['nullable', 'integer', Rule::in(array_column(ProductSkuSaleStatus::cases(), 'value'))],
            'skus.*.spec_values' => ['nullable', 'array'],
            'skus.*.spec_values.*.spec_id' => ['required'],
            'skus.*.spec_values.*.spec_value_id' => ['required'],
            'media' => ['nullable', 'array'],
            'media.*.media_type' => ['required', 'integer', Rule::in(array_column(ProductMediaType::cases(), 'value'))],
            'media.*.file_url' => ['required', 'string'],
            'media.*.file_name' => ['nullable', 'string', 'max:255'],
            'media.*.file_key' => ['nullable', 'string', 'max:512'],
            'media.*.storage_provider' => ['nullable', 'string', 'max:32'],
            'media.*.extension' => ['nullable', 'string', 'max:16'],
            'media.*.file_size' => ['nullable', 'integer', 'min:0'],
            'media.*.file_type' => ['nullable', 'string', 'max:32'],
            'media.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
