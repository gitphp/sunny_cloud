<?php

namespace App\Http\Requests\frontend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'login' => [
                'account' => ['required', 'string', 'max:128'],
                'password' => ['required', 'string', 'min:6', 'max:64'],
            ],
            'register' => [
                'user_name' => ['required', 'string', 'min:3', 'max:32', 'regex:/^[a-zA-Z0-9_]+$/'],
                'nick_name' => ['required', 'string', 'max:32'],
                'user_mobile' => ['required', 'string', 'regex:/^1[3-9]\d{9}$/'],
                'user_email' => ['required', 'email', 'max:128'],
                'password' => ['required', 'string', 'min:6', 'max:64', 'confirmed'],
                'register_channel' => ['nullable', 'string', Rule::in(['web', 'app', 'mini', 'ios', 'android'])],
                'register_device' => ['nullable', 'string', 'max:128'],
            ],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'account.required' => '请输入账号',
            'password.required' => '请输入密码',
            'password.min' => '密码至少6位',
            'password.confirmed' => '两次密码不一致',
            'user_name.required' => '请输入用户名',
            'nick_name.required' => '请输入昵称',
            'user_mobile.required' => '请输入手机号',
            'user_mobile.regex' => '手机号格式不正确',
            'user_email.required' => '请输入邮箱',
            'user_email.email' => '邮箱格式不正确',
        ];
    }

    protected function scene(): string
    {
        if ($this->is('*/login')) {
            return 'login';
        }

        if ($this->is('*/register')) {
            return 'register';
        }

        return 'default';
    }
}
