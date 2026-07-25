<?php

namespace App\Http\Requests\backend;

use App\Enums\HrDeptStatus;
use App\Enums\HrLeaderRoleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HrDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create', 'update' => $this->formRules(),
            'sort' => ['dept_sort' => ['required', 'integer', 'min:0']],
            'status' => ['dept_status' => ['required', 'integer', Rule::in(array_column(HrDeptStatus::cases(), 'value'))]],
            'leaders' => [
                'leaders' => ['required', 'array'],
                'leaders.*.user_id' => ['required'],
                'leaders.*.role_type' => ['nullable', 'integer', Rule::in(array_column(HrLeaderRoleType::cases(), 'value'))],
            ],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'dept_name.required' => '部门名称不能为空',
            'dept_code.required' => '部门编码不能为空',
            'dept_status.in' => '部门状态不正确',
            'leaders.required' => '负责人列表不能为空',
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
        if ($this->isMethod('PUT') && str_ends_with($this->path(), '/leaders')) {
            return 'leaders';
        }
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return 'update';
        }

        return 'default';
    }

    private function formRules(): array
    {
        return [
            'dept_name' => ['required', 'string', 'max:64'],
            'dept_code' => ['required', 'string', 'max:64'],
            'parent_id' => ['nullable'],
            'leader_user_id' => ['nullable'],
            'dept_phone' => ['nullable', 'string', 'max:16'],
            'dept_sort' => ['nullable', 'integer', 'min:0'],
            'dept_status' => ['nullable', 'integer', Rule::in(array_column(HrDeptStatus::cases(), 'value'))],
        ];
    }
}
