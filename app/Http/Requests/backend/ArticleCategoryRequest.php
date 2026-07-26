<?php

namespace App\Http\Requests\backend;

use App\Enums\ArticleCategoryStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create', 'update' => [
                'cat_name' => ['required', 'string', 'max:32'],
                'cat_url' => ['nullable', 'string', 'max:32', 'regex:/^[a-z0-9\-]*$/'],
                'parent_id' => ['nullable'],
                'description' => ['nullable', 'string', 'max:255'],
                'cat_sort' => ['nullable', 'integer', 'min:0'],
                'status' => ['nullable', 'integer', Rule::in(array_column(ArticleCategoryStatus::cases(), 'value'))],
            ],
            'sort' => ['cat_sort' => ['required', 'integer', 'min:0']],
            'status' => ['status' => ['required', 'integer', Rule::in(array_column(ArticleCategoryStatus::cases(), 'value'))]],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'cat_name.required' => '请输入分类名称',
            'cat_url.regex' => 'URL别名仅支持小写字母、数字和短横线',
            'status.in' => '分类状态不正确',
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
