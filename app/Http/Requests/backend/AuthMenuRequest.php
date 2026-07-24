<?php

namespace App\Http\Requests\backend;

use App\Enums\MenuStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
            'menu_name.required' => '菜单名称不能为空',
            'menu_name.max' => '菜单名称不能超过64个字符',
            'parent_id.integer' => '上级菜单格式不正确',
            'menu_sort.integer' => '排序号必须是整数',
            'menu_sort.min' => '排序号不能小于0',
            'menu_status.in' => '菜单状态不正确',
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
            'menu_name' => ['required', 'string', 'max:64'],
            'parent_id' => ['nullable', 'integer', 'min:0'],
            'menu_icon' => ['nullable', 'string', 'max:64'],
            'menu_path' => ['nullable', 'string', 'max:255'],
            'component' => ['nullable', 'string', 'max:255'],
            'permission_code' => ['nullable', 'string', 'max:128'],
            'menu_sort' => ['nullable', 'integer', 'min:0'],
            'menu_status' => ['nullable', 'integer', Rule::in(array_column(MenuStatus::cases(), 'value'))],
        ];
    }

    private function updateRules(): array
    {
        return $this->createRules();
    }

    private function sortRules(): array
    {
        return [
            'menu_sort' => ['required', 'integer', 'min:0'],
        ];
    }

    private function statusRules(): array
    {
        return [
            'menu_status' => ['required', 'integer', Rule::in(array_column(MenuStatus::cases(), 'value'))],
        ];
    }
}
