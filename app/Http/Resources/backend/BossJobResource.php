<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BossJobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'job_title' => $this->job_title,
            'department' => $this->department,
            'workplace' => $this->workplace,
            'experience' => $this->experience,
            'education' => $this->education,
            'salary_range' => $this->salary_range,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'benefits' => $this->benefits,
            'is_hot' => $this->is_hot?->value,
            'is_hot_label' => $this->is_hot?->label(),
            'job_status' => $this->job_status?->value,
            'job_status_label' => $this->job_status?->label(),
            'expire_at' => optional($this->expire_at)?->format('Y-m-d H:i:s'),
            'view_count' => (int) $this->view_count,
            'job_sort' => (int) $this->job_sort,
            'created_at' => optional($this->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($this->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }
}
