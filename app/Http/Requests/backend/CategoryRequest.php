<?php

namespace App\Http\Requests\backend;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 场景规则：create / update / sort
     */
    public function rules(): array
    {
        return match ($this->scene()) {
            'create' => $this->createRules(),
            'update' => $this->updateRules(),
            'sort' => $this->sortRules(),
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'name.required' => '分类名称不能为空',
            'name.max' => '分类名称不能超过128个字符',
            'parent_id.integer' => '上级分类格式不正确',
            'sort.integer' => '排序号必须是整数',
            'sort.min' => '排序号不能小于0',
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

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return 'update';
        }

        return 'default';
    }

    private function createRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:128'],
            'parent_id' => ['nullable', 'integer', 'min:0'],
            'sort' => ['nullable', 'integer', 'min:0'],
        ];
    }

    private function updateRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:128'],
            'parent_id' => ['nullable', 'integer', 'min:0'],
            'sort' => ['nullable', 'integer', 'min:0'],
        ];
    }

    private function sortRules(): array
    {
        return [
            'sort' => ['required', 'integer', 'min:0'],
        ];
    }
}
