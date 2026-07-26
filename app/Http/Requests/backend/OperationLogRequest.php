<?php

namespace App\Http\Requests\backend;

use App\Enums\OperationAction;
use App\Enums\OperatorStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OperationLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:128'],
            'biz_type' => ['nullable', 'string', 'max:16'],
            'action' => ['nullable', 'string', Rule::in(array_column(OperationAction::cases(), 'value'))],
            'operator_status' => ['nullable', 'integer', Rule::in(array_column(OperatorStatus::cases(), 'value'))],
            'operator_id' => ['nullable', 'integer', 'min:0'],
            'biz_id' => ['nullable', 'integer', 'min:0'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'action.in' => '操作类型不正确',
            'operator_status.in' => '操作状态不正确',
            'date_to.after_or_equal' => '结束日期不能早于开始日期',
        ];
    }
}
