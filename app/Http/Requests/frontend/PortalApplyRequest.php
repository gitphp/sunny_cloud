<?php

namespace App\Http\Requests\frontend;

use Illuminate\Foundation\Http\FormRequest;

class PortalApplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route()?->getActionMethod()) {
            'store' => [
                'site_tag' => ['required', 'string', 'max:32'],
                'site_subtitle' => ['nullable', 'string', 'max:128'],
                'site_favicon' => ['nullable', 'string', 'max:512'],
                'site_url' => ['required', 'string', 'max:2048'],
                'category_id' => ['required'],
                'site_intro' => ['nullable', 'string', 'max:1024'],
            ],
            'fetchTkd' => [
                'site_url' => ['required', 'string', 'max:2048'],
            ],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'site_tag.required' => '请填写网站标签',
            'site_url.required' => '请填写网站地址',
            'category_id.required' => '请选择所属分类',
        ];
    }
}
