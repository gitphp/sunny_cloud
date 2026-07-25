<?php

namespace App\Http\Requests\backend;

use App\Enums\HrIsMain;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HrUserDeptPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required'],
            'dept_id' => ['required'],
            'post_id' => ['required'],
            'is_main' => ['nullable', 'integer', Rule::in(array_column(HrIsMain::cases(), 'value'))],
            'remark' => ['nullable', 'string', 'max:512'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => '请选择员工',
            'dept_id.required' => '请选择部门',
            'post_id.required' => '请选择岗位',
            'is_main.in' => '主岗标识不正确',
        ];
    }
}
