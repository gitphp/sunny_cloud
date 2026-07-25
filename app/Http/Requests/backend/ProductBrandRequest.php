<?php

namespace App\Http\Requests\backend;

use App\Enums\ProductShowStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create', 'update' => [
                'brand_name' => ['required', 'string', 'max:32'],
                'alias' => ['nullable', 'string', 'max:64'],
                'is_show' => ['nullable', 'integer', Rule::in(array_column(ProductShowStatus::cases(), 'value'))],
                'sort_order' => ['nullable', 'integer', 'min:0'],
                'brand_remark' => ['nullable', 'string', 'max:512'],
            ],
            'sort' => ['sort_order' => ['required', 'integer', 'min:0']],
            'status' => ['is_show' => ['required', 'integer', Rule::in(array_column(ProductShowStatus::cases(), 'value'))]],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'brand_name.required' => '品牌名称不能为空',
            'brand_name.max' => '品牌名称不能超过32个字符',
        ];
    }

    protected function scene(): string
    {
        if ($this->isMethod('POST')) {
            return 'create';
        }
        if ($this->isMethod('PATCH') && str_ends_with($this->path(), '/sort')) {
            return 'sort';
        }
        if ($this->isMethod('PATCH') && str_ends_with($this->path(), '/status')) {
            return 'status';
        }
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return 'update';
        }

        return 'default';
    }
}
