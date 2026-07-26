<?php

namespace App\Http\Requests\backend;

use App\Enums\BookMarkBold;
use App\Enums\BookMarkStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookMarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create', 'update' => [
                'category_id' => ['nullable'],
                'short_title' => ['nullable', 'string', 'max:16'],
                'book_title' => ['required', 'string', 'max:128'],
                'book_url' => ['required', 'string', 'max:2048'],
                'book_favicon' => ['nullable', 'string', 'max:512'],
                'book_desc' => ['nullable', 'string', 'max:1024'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
                'status' => ['nullable', 'integer', Rule::in(array_column(BookMarkStatus::cases(), 'value'))],
                'is_bold' => ['nullable', 'integer', Rule::in(array_column(BookMarkBold::cases(), 'value'))],
            ],
            'sort' => ['sort_order' => ['required', 'integer', 'min:0']],
            'status' => ['status' => ['required', 'integer', Rule::in(array_column(BookMarkStatus::cases(), 'value'))]],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'book_title.required' => '请输入书签标题',
            'book_url.required' => '请输入链接地址',
            'status.in' => '书签状态不正确',
            'is_bold.in' => '加粗选项不正确',
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
