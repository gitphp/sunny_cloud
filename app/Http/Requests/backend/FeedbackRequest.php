<?php

namespace App\Http\Requests\backend;

use App\Enums\FeedbackStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'reply' => [
                'reply_content' => ['required', 'string', 'max:5000'],
            ],
            'status' => [
                'fb_status' => ['required', 'integer', Rule::in(array_column(FeedbackStatus::cases(), 'value'))],
            ],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'reply_content.required' => '请填写回复内容',
            'fb_status.in' => '留言状态不正确',
        ];
    }

    protected function scene(): string
    {
        if ($this->isMethod('POST') && str_ends_with($this->path(), '/reply')) {
            return 'reply';
        }
        if ($this->isMethod('PATCH') && str_ends_with($this->path(), '/status')) {
            return 'status';
        }

        return 'default';
    }
}
