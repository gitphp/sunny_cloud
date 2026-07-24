<?php

namespace App\Http\Requests\backend;

use Illuminate\Foundation\Http\FormRequest;

class UserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role_ids' => ['present', 'array'],
            'role_ids.*' => ['string'],
        ];
    }

    public function messages(): array
    {
        return [
            'role_ids.present' => '角色列表参数必填',
            'role_ids.array' => '角色列表格式不正确',
        ];
    }
}
