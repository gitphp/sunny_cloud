<?php

namespace App\Http\Requests\backend;

use App\Enums\ProductShowStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create', 'update' => [
                'category_name' => ['required', 'string', 'max:255'],
                'parent_id' => ['nullable'],
                'unit' => ['nullable', 'string', 'max:32'],
                'cat_status' => ['nullable', 'integer', Rule::in(array_column(ProductShowStatus::cases(), 'value'))],
                'sort_order' => ['nullable', 'integer', 'min:0'],
                'cat_remark' => ['nullable', 'string', 'max:512'],
            ],
            'sort' => ['sort_order' => ['required', 'integer', 'min:0']],
            'status' => ['cat_status' => ['required', 'integer', Rule::in(array_column(ProductShowStatus::cases(), 'value'))]],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'category_name.required' => '分类名称不能为空',
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
