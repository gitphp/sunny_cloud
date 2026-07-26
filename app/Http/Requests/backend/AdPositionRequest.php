<?php

namespace App\Http\Requests\backend;

use App\Enums\AdAuditStatus;
use App\Enums\AdCostType;
use App\Enums\AdDeviceType;
use App\Enums\AdDisplayFrequency;
use App\Enums\AdLinkType;
use App\Enums\AdPlatform;
use App\Enums\AdShowTimeType;
use App\Enums\AdStatus;
use App\Enums\AdTargetUserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdPositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create', 'update' => [
                'ad_title' => ['required', 'string', 'max:128'],
                'subtitle' => ['nullable', 'string', 'max:255'],
                'cover_url' => ['nullable', 'string', 'max:512'],
                'cover_mobile' => ['nullable', 'string', 'max:512'],
                'cover_thumb' => ['nullable', 'string', 'max:512'],
                'video_url' => ['nullable', 'string', 'max:512'],
                'link_type' => ['required', 'integer', Rule::in(array_column(AdLinkType::cases(), 'value'))],
                'link_url' => ['nullable', 'string', 'max:512'],
                'link_params' => ['nullable', 'array'],
                'app_id' => ['nullable', 'string', 'max:128'],
                'app_path' => ['nullable', 'string', 'max:255'],
                'position_code' => ['required', 'string', 'max:64'],
                'platform' => ['required', 'integer', Rule::in(array_column(AdPlatform::cases(), 'value'))],
                'device_type' => ['nullable', 'integer', Rule::in(array_column(AdDeviceType::cases(), 'value'))],
                'target_user_type' => ['nullable', 'integer', Rule::in(array_column(AdTargetUserType::cases(), 'value'))],
                'target_user_group_ids' => ['nullable', 'array'],
                'target_user_group_ids.*' => ['integer'],
                'target_region' => ['nullable', 'array'],
                'start_time' => ['required', 'date'],
                'end_time' => ['required', 'date', 'after:start_time'],
                'show_time_type' => ['nullable', 'integer', Rule::in(array_column(AdShowTimeType::cases(), 'value'))],
                'time_slots' => ['nullable', 'array'],
                'weekdays' => ['nullable', 'array'],
                'weekdays.*' => ['integer', 'min:1', 'max:7'],
                'sort' => ['nullable', 'integer', 'min:0'],
                'display_frequency' => ['nullable', 'integer', Rule::in(array_column(AdDisplayFrequency::cases(), 'value'))],
                'daily_impression_limit' => ['nullable', 'integer', 'min:0'],
                'daily_click_limit' => ['nullable', 'integer', 'min:0'],
                'budget' => ['nullable', 'numeric', 'min:0'],
                'cost_type' => ['nullable', 'integer', Rule::in(array_column(AdCostType::cases(), 'value'))],
                'bid_price' => ['nullable', 'numeric', 'min:0'],
                'status' => ['nullable', 'integer', Rule::in(array_column(AdStatus::cases(), 'value'))],
                'audit_status' => ['nullable', 'integer', Rule::in(array_column(AdAuditStatus::cases(), 'value'))],
            ],
            'sort' => ['sort' => ['required', 'integer', 'min:0']],
            'status' => ['status' => ['required', 'integer', Rule::in(array_column(AdStatus::cases(), 'value'))]],
            'audit' => [
                'audit_status' => ['required', 'integer', Rule::in([
                    AdAuditStatus::Pending->value,
                    AdAuditStatus::Approved->value,
                    AdAuditStatus::Rejected->value,
                ])],
                'reject_reason' => ['nullable', 'string', 'max:512'],
            ],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'ad_title.required' => '请输入广告标题',
            'position_code.required' => '请选择广告位',
            'start_time.required' => '请选择开始时间',
            'end_time.required' => '请选择结束时间',
            'end_time.after' => '结束时间必须晚于开始时间',
            'link_type.in' => '跳转类型不正确',
            'platform.in' => '投放平台不正确',
            'status.in' => '状态不正确',
        ];
    }

    protected function scene(): string
    {
        if ($this->isMethod('POST') && str_ends_with($this->path(), '/audit')) {
            return 'audit';
        }
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
