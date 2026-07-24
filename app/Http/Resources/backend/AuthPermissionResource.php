<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthPermissionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'parent_id' => (string) $this->parent_id,
            'per_name' => $this->per_name,
            'per_code' => $this->per_code,
            'per_type' => $this->per_type?->value,
            'per_type_label' => $this->per_type?->label(),
            'per_path' => $this->per_path,
            'per_method' => $this->per_method,
            'per_icon' => $this->per_icon,
            'per_sort' => (int) $this->per_sort,
            'per_status' => $this->per_status?->value,
            'per_status_label' => $this->per_status?->label(),
            'children' => AuthPermissionResource::collection($this->whenLoaded('childrenRecursive')),
        ];
    }
}
