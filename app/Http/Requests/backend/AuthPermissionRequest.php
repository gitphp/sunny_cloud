<?php

namespace App\Http\Requests\backend;

use App\Enums\PermissionStatus;
use App\Enums\PermissionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthPermissionRequest extends FormRequest
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
            'per_name.required' => '权限名称不能为空',
            'per_code.required' => '权限标识不能为空',
            'per_code.regex' => '权限标识格式不正确，如 user:delete',
            'per_type.required' => '请选择权限类型',
            'per_type.in' => '权限类型不正确',
            'per_method.in' => 'HTTP 方法不正确',
            'per_status.in' => '状态不正确',
            'per_sort.integer' => '排序号必须是整数',
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
            'parent_id' => ['nullable'],
            'per_name' => ['required', 'string', 'max:64'],
            'per_code' => ['required', 'string', 'max:128', 'regex:/^[a-zA-Z][a-zA-Z0-9_:.]*$/'],
            'per_type' => ['required', 'string', Rule::in(array_column(PermissionType::cases(), 'value'))],
            'per_path' => ['nullable', 'string', 'max:255'],
            'per_method' => ['nullable', 'string', 'max:16', Rule::in(['', 'GET', 'POST', 'PUT', 'PATCH', 'DELETE'])],
            'per_icon' => ['nullable', 'string', 'max:64'],
            'per_sort' => ['nullable', 'integer', 'min:0'],
            'per_status' => ['nullable', 'integer', Rule::in(array_column(PermissionStatus::cases(), 'value'))],
        ];
    }

    private function updateRules(): array
    {
        return $this->createRules();
    }

    private function sortRules(): array
    {
        return [
            'per_sort' => ['required', 'integer', 'min:0'],
        ];
    }

    private function statusRules(): array
    {
        return [
            'per_status' => ['required', 'integer', Rule::in(array_column(PermissionStatus::cases(), 'value'))],
        ];
    }
}
