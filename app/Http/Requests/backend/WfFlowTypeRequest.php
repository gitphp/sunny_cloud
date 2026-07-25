<?php

namespace App\Http\Requests\backend;

use App\Enums\WfStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WfFlowTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create', 'update' => [
                'type_name' => ['required', 'string', 'max:32'],
                'type_code' => ['required', 'string', 'max:32', 'regex:/^[a-z][a-z0-9_]*$/'],
                'icon' => ['nullable', 'string', 'max:255'],
                'sort' => ['nullable', 'integer'],
                'status' => ['nullable', 'integer', Rule::in(array_column(WfStatus::cases(), 'value'))],
            ],
            'sort' => ['sort' => ['required', 'integer']],
            'status' => ['status' => ['required', 'integer', Rule::in(array_column(WfStatus::cases(), 'value'))]],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'type_name.required' => '流程类型名称不能为空',
            'type_code.required' => '流程类型编码不能为空',
            'type_code.regex' => '编码需小写字母开头，仅含小写字母/数字/下划线',
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
