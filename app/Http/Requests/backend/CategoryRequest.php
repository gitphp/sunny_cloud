<?php

namespace App\Http\Requests\backend;

use App\Enums\CategoryShowType;
use App\Enums\CategoryStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 场景规则：create / update / sort / status
     */
    public function rules(): array
    {
        return match ($this->scene()) {
            'create' => $this->createRules(),
            'update' => $this->updateRules(),
            'sort' => $this->sortRules(),
            'status' => $this->statusRules(),
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'category_name.required' => '分类名称不能为空',
            'category_name.max' => '分类名称不能超过255个字符',
            'parent_id.integer' => '上级分类格式不正确',
            'sort_order.integer' => '排序号必须是整数',
            'sort_order.min' => '排序号不能小于0',
            'show_type.in' => '可见性类型不正确',
            'cat_status.in' => '分类状态不正确',
            'description.max' => '分类描述不能超过512个字符',
            'cat_remark.max' => '备注不能超过512个字符',
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

    private function createRules(): array
    {
        return [
            'category_name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable'],
            'show_type' => ['nullable', 'integer', Rule::in(array_column(CategoryShowType::cases(), 'value'))],
            'cat_status' => ['nullable', 'integer', Rule::in(array_column(CategoryStatus::cases(), 'value'))],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:512'],
            'cat_remark' => ['nullable', 'string', 'max:512'],
        ];
    }

    private function updateRules(): array
    {
        return $this->createRules();
    }

    private function sortRules(): array
    {
        return [
            'sort_order' => ['required', 'integer', 'min:0'],
        ];
    }

    private function statusRules(): array
    {
        return [
            'cat_status' => ['required', 'integer', Rule::in(array_column(CategoryStatus::cases(), 'value'))],
        ];
    }
}
