<?php

namespace App\Http\Requests\backend;

use Illuminate\Foundation\Http\FormRequest;

class WfFlowApplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create' => [
                'flow_def_id' => ['required'],
                'title' => ['nullable', 'string', 'max:256'],
                'form_data' => ['nullable', 'array'],
                'remark' => ['nullable', 'string', 'max:1024'],
                'cc_uids' => ['nullable', 'array'],
            ],
            'update' => [
                'flow_def_id' => ['nullable'],
                'title' => ['nullable', 'string', 'max:256'],
                'form_data' => ['nullable', 'array'],
                'remark' => ['nullable', 'string', 'max:1024'],
                'cc_uids' => ['nullable', 'array'],
            ],
            'submit' => [
                'title' => ['nullable', 'string', 'max:256'],
                'form_data' => ['nullable', 'array'],
                'remark' => ['nullable', 'string', 'max:1024'],
                'cc_uids' => ['nullable', 'array'],
            ],
            'approve' => [
                'approve_opinion' => ['nullable', 'string', 'max:2048'],
                'attach_files' => ['nullable', 'array'],
                'target_user_id' => ['nullable'],
            ],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'flow_def_id.required' => '请选择流程模板',
        ];
    }

    protected function scene(): string
    {
        $route = $this->route()?->getActionMethod();

        return match ($route) {
            'store' => 'create',
            'update' => 'update',
            'submit' => 'submit',
            'agree', 'reject', 'transfer', 'addSign' => 'approve',
            default => 'default',
        };
    }
}
