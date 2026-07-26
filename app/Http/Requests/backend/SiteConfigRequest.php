<?php

namespace App\Http\Requests\backend;

use App\Enums\SiteConfigGroup;
use App\Enums\SiteConfigInputType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SiteConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create', 'update' => [
                'conf_group' => ['required', 'string', Rule::in(array_column(SiteConfigGroup::cases(), 'value'))],
                'conf_key' => ['required', 'string', 'max:128', 'regex:/^[a-zA-Z0-9_\-\.]+$/'],
                'conf_value' => ['nullable', 'string'],
                'conf_desc' => ['nullable', 'string', 'max:255'],
                'input_type' => ['required', 'string', Rule::in(array_column(SiteConfigInputType::cases(), 'value'))],
                'conf_sort' => ['nullable', 'integer', 'min:0'],
            ],
            'batch' => [
                'items' => ['required', 'array', 'min:1'],
                'items.*.id' => ['nullable'],
                'items.*.conf_key' => ['nullable', 'string', 'max:128'],
                'items.*.conf_value' => ['nullable', 'string'],
            ],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'conf_key.required' => '请输入配置键名',
            'conf_key.regex' => '配置键名仅支持字母、数字、下划线、短横线和点',
            'conf_group.in' => '配置分组不正确',
            'input_type.in' => '输入类型不正确',
            'items.required' => '请提交配置项',
        ];
    }

    protected function scene(): string
    {
        if ($this->isMethod('POST') && str_ends_with($this->path(), '/batch')) {
            return 'batch';
        }
        if ($this->isMethod('POST')) {
            return 'create';
        }
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return 'update';
        }

        return 'default';
    }
}
