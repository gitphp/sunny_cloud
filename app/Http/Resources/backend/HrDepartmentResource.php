<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HrDepartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'parent_id' => (string) $this->parent_id,
            'dept_name' => $this->dept_name,
            'dept_code' => $this->dept_code,
            'ancestors' => $this->ancestors,
            'dept_level' => (int) $this->dept_level,
            'leader_user_id' => (string) $this->leader_user_id,
            'dept_phone' => $this->dept_phone,
            'dept_sort' => (int) $this->dept_sort,
            'dept_status' => $this->dept_status?->value,
            'dept_status_label' => $this->dept_status?->label(),
            'children' => HrDepartmentResource::collection($this->whenLoaded('childrenRecursive')),
        ];
    }
}
