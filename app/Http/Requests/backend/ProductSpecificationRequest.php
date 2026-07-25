<?php

namespace App\Http\Requests\backend;

use App\Enums\ProductShowStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductSpecificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create', 'update' => [
                'spec_name' => ['required', 'string', 'max:255'],
                'spec_remark' => ['nullable', 'string', 'max:512'],
                'spec_status' => ['nullable', 'integer', Rule::in(array_column(ProductShowStatus::cases(), 'value'))],
                'sort_order' => ['nullable', 'integer', 'min:0'],
            ],
            'sort' => ['sort_order' => ['required', 'integer', 'min:0']],
            'status' => ['spec_status' => ['required', 'integer', Rule::in(array_column(ProductShowStatus::cases(), 'value'))]],
            'value' => [
                'value' => ['required', 'string', 'max:255'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
                'value_status' => ['nullable', 'integer', Rule::in(array_column(ProductShowStatus::cases(), 'value'))],
            ],
            'value_sort' => ['sort_order' => ['required', 'integer', 'min:0']],
            'value_status' => ['value_status' => ['required', 'integer', Rule::in(array_column(ProductShowStatus::cases(), 'value'))]],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'spec_name.required' => '规格名称不能为空',
            'value.required' => '规格值不能为空',
        ];
    }

    protected function scene(): string
    {
        if ($this->isMethod('POST') && str_contains($this->path(), '/values')) {
            return 'value';
        }
        if ($this->isMethod('PUT') && str_contains($this->path(), 'specification-values')) {
            return 'value';
        }
        if ($this->isMethod('PATCH') && str_ends_with($this->path(), '/sort') && str_contains($this->path(), 'specification-values')) {
            return 'value_sort';
        }
        if ($this->isMethod('PATCH') && str_ends_with($this->path(), '/status') && str_contains($this->path(), 'specification-values')) {
            return 'value_status';
        }
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
