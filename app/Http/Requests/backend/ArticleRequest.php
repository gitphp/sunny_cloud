<?php

namespace App\Http\Requests\backend;

use App\Enums\ArticleContentType;
use App\Enums\ArticleFlag;
use App\Enums\ArticleStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create', 'update' => [
                'title' => ['required', 'string', 'max:255'],
                'subtitle' => ['nullable', 'string', 'max:128'],
                'art_cover' => ['nullable', 'string', 'max:500'],
                'art_content' => ['nullable', 'string'],
                'content_type' => ['nullable', 'integer', Rule::in(array_column(ArticleContentType::cases(), 'value'))],
                'summary' => ['nullable', 'string', 'max:512'],
                'category_id' => ['required'],
                'tag_ids' => ['nullable', 'array'],
                'tag_ids.*' => ['integer'],
                'author_id' => ['nullable'],
                'author_name' => ['nullable', 'string', 'max:16'],
                'source' => ['nullable', 'string', 'max:64'],
                'source_url' => ['nullable', 'string', 'max:512'],
                'art_status' => ['nullable', 'integer', Rule::in(array_column(ArticleStatus::cases(), 'value'))],
                'is_top' => ['nullable', 'integer', Rule::in(array_column(ArticleFlag::cases(), 'value'))],
                'is_original' => ['nullable', 'integer', Rule::in(array_column(ArticleFlag::cases(), 'value'))],
                'is_commentable' => ['nullable', 'integer', Rule::in(array_column(ArticleFlag::cases(), 'value'))],
                'seo_title' => ['nullable', 'string', 'max:255'],
                'seo_keywords' => ['nullable', 'string', 'max:255'],
                'seo_description' => ['nullable', 'string', 'max:512'],
                'extra_fields' => ['nullable', 'array'],
            ],
            'status' => [
                'art_status' => ['required', 'integer', Rule::in(array_column(ArticleStatus::cases(), 'value'))],
                'reject_reason' => ['nullable', 'string', 'max:512'],
            ],
            'top' => [
                'is_top' => ['required', 'integer', Rule::in(array_column(ArticleFlag::cases(), 'value'))],
            ],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'title.required' => '请输入文章标题',
            'category_id.required' => '请选择文章分类',
            'art_status.in' => '文章状态不正确',
        ];
    }

    protected function scene(): string
    {
        if ($this->isMethod('POST')) {
            return 'create';
        }
        if ($this->isMethod('PATCH') && str_ends_with($this->path(), '/status')) {
            return 'status';
        }
        if ($this->isMethod('PATCH') && str_ends_with($this->path(), '/top')) {
            return 'top';
        }
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return 'update';
        }

        return 'default';
    }
}
