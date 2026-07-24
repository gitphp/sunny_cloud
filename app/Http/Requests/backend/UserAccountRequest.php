<?php

namespace App\Http\Requests\backend;

use App\Enums\RealAuthStatus;
use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create' => $this->createRules(),
            'update' => $this->updateRules(),
            'status' => $this->statusRules(),
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'user_name.required' => '请输入用户名',
            'nick_name.required' => '请输入昵称',
            'user_mobile.required' => '请输入手机号',
            'user_mobile.regex' => '手机号格式不正确',
            'password.required' => '请输入密码',
            'password.min' => '密码至少6位',
            'user_status.in' => '账号状态不正确',
            'real_auth_status.in' => '实名状态不正确',
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

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return 'update';
        }

        return 'default';
    }

    private function createRules(): array
    {
        return [
            'user_name' => ['required', 'string', 'min:3', 'max:32', 'regex:/^[a-zA-Z0-9_]+$/'],
            'nick_name' => ['required', 'string', 'max:32'],
            'user_mobile' => ['required', 'string', 'regex:/^1[3-9]\d{9}$/'],
            'user_email' => ['required', 'email', 'max:128'],
            'password' => ['required', 'string', 'min:6', 'max:64'],
            'user_status' => ['nullable', 'integer', Rule::in(array_column(UserStatus::cases(), 'value'))],
            'real_auth_status' => ['nullable', 'integer', Rule::in(array_column(RealAuthStatus::cases(), 'value'))],
            'lock_reason' => ['nullable', 'string', 'max:255'],
            'lock_expire_time' => ['nullable', 'date'],
            'register_channel' => ['nullable', 'string', Rule::in(['web', 'app', 'mini', 'ios', 'android'])],
        ];
    }

    private function updateRules(): array
    {
        return [
            'user_name' => ['required', 'string', 'min:3', 'max:32', 'regex:/^[a-zA-Z0-9_]+$/'],
            'nick_name' => ['required', 'string', 'max:32'],
            'user_mobile' => ['required', 'string', 'regex:/^1[3-9]\d{9}$/'],
            'user_email' => ['required', 'email', 'max:128'],
            'password' => ['nullable', 'string', 'min:6', 'max:64'],
            'user_status' => ['nullable', 'integer', Rule::in(array_column(UserStatus::cases(), 'value'))],
            'real_auth_status' => ['nullable', 'integer', Rule::in(array_column(RealAuthStatus::cases(), 'value'))],
            'lock_reason' => ['nullable', 'string', 'max:255'],
            'lock_expire_time' => ['nullable', 'date'],
        ];
    }

    private function statusRules(): array
    {
        return [
            'user_status' => ['required', 'integer', Rule::in(array_column(UserStatus::cases(), 'value'))],
            'lock_reason' => ['nullable', 'string', 'max:255'],
            'lock_expire_time' => ['nullable', 'date'],
        ];
    }
}
