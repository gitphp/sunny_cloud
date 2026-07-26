<?php

namespace App\Http\Requests\backend;

use App\Enums\BossJobHot;
use App\Enums\BossJobStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BossJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->scene()) {
            'create', 'update' => [
                'job_title' => ['required', 'string', 'max:64'],
                'department' => ['nullable', 'string', 'max:64'],
                'workplace' => ['nullable', 'string', 'max:128'],
                'experience' => ['nullable', 'string', 'max:64'],
                'education' => ['nullable', 'string', 'max:64'],
                'salary_range' => ['nullable', 'string', 'max:64'],
                'description' => ['nullable', 'string'],
                'requirements' => ['nullable', 'string'],
                'benefits' => ['nullable', 'string'],
                'is_hot' => ['nullable', 'integer', Rule::in(array_column(BossJobHot::cases(), 'value'))],
                'job_status' => ['nullable', 'integer', Rule::in(array_column(BossJobStatus::cases(), 'value'))],
                'expire_at' => ['nullable', 'date'],
                'job_sort' => ['nullable', 'integer', 'min:0'],
            ],
            'sort' => ['job_sort' => ['required', 'integer', 'min:0']],
            'status' => ['job_status' => ['required', 'integer', Rule::in(array_column(BossJobStatus::cases(), 'value'))]],
            'hot' => ['is_hot' => ['required', 'integer', Rule::in(array_column(BossJobHot::cases(), 'value'))]],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'job_title.required' => '请输入职位名称',
            'job_status.in' => '职位状态不正确',
            'is_hot.in' => '急聘选项不正确',
        ];
    }

    protected function scene(): string
    {
        if ($this->isMethod('POST')) {
            return 'create';
        }
        if ($this->isMethod('PATCH') && str_ends_with($this->path(), '/sort')) {
            return 'sort';
        }
        if ($this->isMethod('PATCH') && str_ends_with($this->path(), '/status')) {
            return 'status';
        }
        if ($this->isMethod('PATCH') && str_ends_with($this->path(), '/hot')) {
            return 'hot';
        }
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return 'update';
        }

        return 'default';
    }
}
