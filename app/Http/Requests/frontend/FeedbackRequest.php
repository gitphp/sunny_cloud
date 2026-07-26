<?php

namespace App\Http\Requests\frontend;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fb_name' => ['required', 'string', 'max:32'],
            'fb_phone' => ['nullable', 'string', 'max:16'],
            'fb_email' => ['nullable', 'email', 'max:32'],
            'fb_company' => ['nullable', 'string', 'max:32'],
            'fb_title' => ['required', 'string', 'max:128'],
            'fb_content' => ['required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'fb_name.required' => '请填写联系人姓名',
            'fb_title.required' => '请填写留言标题',
            'fb_content.required' => '请填写留言内容',
            'fb_email.email' => '邮箱格式不正确',
        ];
    }
}
