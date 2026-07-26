<?php

namespace App\Http\Requests\backend;

use App\Enums\FriendLinkStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FriendLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create', 'update' => [
                'link_name' => ['required', 'string', 'max:32'],
                'link_url' => ['required', 'string', 'max:512'],
                'link_logo' => ['nullable', 'string', 'max:512'],
                'link_desc' => ['nullable', 'string', 'max:255'],
                'link_sort' => ['nullable', 'integer', 'min:0'],
                'link_status' => ['nullable', 'integer', Rule::in(array_column(FriendLinkStatus::cases(), 'value'))],
            ],
            'sort' => ['link_sort' => ['required', 'integer', 'min:0']],
            'status' => ['link_status' => ['required', 'integer', Rule::in(array_column(FriendLinkStatus::cases(), 'value'))]],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'link_name.required' => '请输入网站名称',
            'link_url.required' => '请输入网站链接',
            'link_status.in' => '链接状态不正确',
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
