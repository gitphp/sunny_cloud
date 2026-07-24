<?php

namespace App\Http\Requests\backend;

use Illuminate\Foundation\Http\FormRequest;

class AuthRoleGrantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'menus' => [
                'menu_ids' => ['present', 'array'],
                'menu_ids.*' => ['string'],
            ],
            'permissions' => [
                'permission_ids' => ['present', 'array'],
                'permission_ids.*' => ['string'],
            ],
            'grant' => [
                'menu_ids' => ['present', 'array'],
                'menu_ids.*' => ['string'],
                'permission_ids' => ['present', 'array'],
                'permission_ids.*' => ['string'],
            ],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'menu_ids.present' => '菜单列表参数必填',
            'menu_ids.array' => '菜单列表格式不正确',
            'permission_ids.present' => '权限列表参数必填',
            'permission_ids.array' => '权限列表格式不正确',
        ];
    }

    protected function scene(): string
    {
        if (str_ends_with($this->path(), '/menus')) {
            return 'menus';
        }
        if (str_ends_with($this->path(), '/permissions')) {
            return 'permissions';
        }
        if (str_ends_with($this->path(), '/grant')) {
            return 'grant';
        }

        return 'default';
    }
}
