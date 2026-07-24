<?php

namespace App\Http\Requests\backend;

use App\Enums\DataScope;
use App\Enums\RoleStatus;
use App\Enums\RoleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthRoleRequest extends FormRequest
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
            'role_name.required' => '角色名称不能为空',
            'role_name.max' => '角色名称不能超过64个字符',
            'role_code.required' => '角色标识不能为空',
            'role_code.max' => '角色标识不能超过64个字符',
            'role_code.regex' => '角色标识仅支持字母、数字、下划线',
            'role_type.in' => '角色类型不正确',
            'data_scope.in' => '数据权限范围不正确',
            'role_status.in' => '角色状态不正确',
            'role_sort.integer' => '排序号必须是整数',
            'scope_departments.array' => '指定部门格式不正确',
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
            'role_name' => ['required', 'string', 'max:64'],
            'role_code' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z][a-zA-Z0-9_]*$/'],
            'role_type' => ['nullable', 'integer', Rule::in(array_column(RoleType::cases(), 'value'))],
            'role_sort' => ['nullable', 'integer', 'min:0'],
            'data_scope' => ['required', 'integer', Rule::in(array_column(DataScope::cases(), 'value'))],
            'scope_departments' => ['nullable', 'array'],
            'scope_departments.*' => ['string'],
            'role_status' => ['nullable', 'integer', Rule::in(array_column(RoleStatus::cases(), 'value'))],
            'role_remark' => ['nullable', 'string', 'max:512'],
        ];
    }

    private function updateRules(): array
    {
        return $this->createRules();
    }

    private function sortRules(): array
    {
        return [
            'role_sort' => ['required', 'integer', 'min:0'],
        ];
    }

    private function statusRules(): array
    {
        return [
            'role_status' => ['required', 'integer', Rule::in(array_column(RoleStatus::cases(), 'value'))],
        ];
    }
}
