<?php

namespace App\Http\Requests\backend;

use App\Enums\AdSlotStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create', 'update' => [
                'slot_code' => ['required', 'string', 'max:32', 'regex:/^[a-zA-Z0-9_\-]+$/'],
                'slot_name' => ['required', 'string', 'max:128'],
                'description' => ['nullable', 'string', 'max:255'],
                'width' => ['nullable', 'integer', 'min:0'],
                'height' => ['nullable', 'integer', 'min:0'],
                'max_items' => ['nullable', 'integer', 'min:1'],
                'is_system' => ['nullable', 'integer', Rule::in([0, 1])],
                'slot_status' => ['nullable', 'integer', Rule::in(array_column(AdSlotStatus::cases(), 'value'))],
            ],
            'status' => [
                'slot_status' => ['required', 'integer', Rule::in(array_column(AdSlotStatus::cases(), 'value'))],
            ],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'slot_code.required' => '请输入广告位编码',
            'slot_code.regex' => '广告位编码仅支持字母、数字、下划线和中划线',
            'slot_name.required' => '请输入广告位名称',
            'slot_status.in' => '状态不正确',
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
}
