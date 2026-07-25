<?php

namespace App\Http\Requests\backend;

use App\Enums\HrPostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HrPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create', 'update' => $this->formRules(),
            'sort' => ['post_sort' => ['required', 'integer', 'min:0']],
            'status' => ['post_status' => ['required', 'integer', Rule::in(array_column(HrPostStatus::cases(), 'value'))]],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'post_name.required' => '岗位名称不能为空',
            'post_code.required' => '岗位编码不能为空',
            'post_status.in' => '岗位状态不正确',
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

    private function formRules(): array
    {
        return [
            'post_name' => ['required', 'string', 'max:64'],
            'post_code' => ['required', 'string', 'max:64'],
            'parent_id' => ['nullable'],
            'post_sort' => ['nullable', 'integer', 'min:0'],
            'post_status' => ['nullable', 'integer', Rule::in(array_column(HrPostStatus::cases(), 'value'))],
            'remark' => ['nullable', 'string', 'max:512'],
        ];
    }
}
