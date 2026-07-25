<?php

namespace App\Http\Requests\backend;

use App\Enums\WfApproveType;
use App\Enums\WfConditionOperator;
use App\Enums\WfFieldType;
use App\Enums\WfNodeMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WfFlowDefinitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create', 'update' => [
                'flow_type_id' => ['required'],
                'flow_name' => ['required', 'string', 'max:128'],
                'version' => ['nullable', 'integer', 'min:1'],
                'remark' => ['nullable', 'string', 'max:512'],
                'apply_scope' => ['nullable', 'array'],
                'forms' => ['nullable', 'array'],
                'forms.*.field_name' => ['required', 'string', 'max:64'],
                'forms.*.field_key' => ['required', 'string', 'max:64', 'regex:/^[a-z][a-z0-9_]*$/'],
                'forms.*.field_type' => ['required', 'string', Rule::in(array_column(WfFieldType::cases(), 'value'))],
                'forms.*.field_options' => ['nullable', 'array'],
                'forms.*.is_required' => ['nullable', 'integer', 'in:0,1'],
                'forms.*.sort' => ['nullable', 'integer'],
                'nodes' => ['nullable', 'array'],
                'nodes.*.node_name' => ['required', 'string', 'max:64'],
                'nodes.*.node_sort' => ['nullable', 'integer', 'min:1'],
                'nodes.*.approve_type' => ['required', 'integer', Rule::in(array_column(WfApproveType::cases(), 'value'))],
                'nodes.*.approve_target' => ['nullable', 'array'],
                'nodes.*.node_mode' => ['nullable', 'integer', Rule::in(array_column(WfNodeMode::cases(), 'value'))],
                'nodes.*.can_reject' => ['nullable', 'integer', 'in:0,1'],
                'nodes.*.can_add_sign' => ['nullable', 'integer', 'in:0,1'],
                'nodes.*.can_transfer' => ['nullable', 'integer', 'in:0,1'],
                'nodes.*.back_node_id' => ['nullable'],
                'conditions' => ['nullable', 'array'],
                'conditions.*.pre_node_id' => ['nullable'],
                'conditions.*.pre_node_sort' => ['nullable', 'integer', 'min:0'],
                'conditions.*.condition_field' => ['required', 'string', 'max:64'],
                'conditions.*.condition_operator' => ['required', 'string', Rule::in(array_column(WfConditionOperator::cases(), 'value'))],
                'conditions.*.condition_value' => ['required', 'string', 'max:128'],
                'conditions.*.jump_node_id' => ['nullable'],
                'conditions.*.jump_node_sort' => ['nullable', 'integer', 'min:1'],
            ],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'flow_type_id.required' => '请选择流程类型',
            'flow_name.required' => '流程名称不能为空',
            'forms.*.field_name.required' => '表单字段名称不能为空',
            'forms.*.field_key.required' => '表单字段标识不能为空',
            'nodes.*.node_name.required' => '节点名称不能为空',
            'nodes.*.approve_type.required' => '请选择审批人类型',
        ];
    }

    protected function scene(): string
    {
        if ($this->isMethod('POST')) {
            return 'create';
        }
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return 'update';
        }

        return 'default';
    }
}
