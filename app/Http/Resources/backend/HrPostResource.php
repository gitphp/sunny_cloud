<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HrPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'parent_id' => (string) $this->parent_id,
            'post_name' => $this->post_name,
            'post_code' => $this->post_code,
            'post_sort' => (int) $this->post_sort,
            'post_status' => $this->post_status?->value,
            'post_status_label' => $this->post_status?->label(),
            'remark' => $this->remark,
            'children' => HrPostResource::collection($this->whenLoaded('childrenRecursive')),
        ];
    }
}
