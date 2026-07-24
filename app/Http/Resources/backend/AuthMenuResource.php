<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthMenuResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'parent_id' => (string) $this->parent_id,
            'menu_name' => $this->menu_name,
            'menu_icon' => $this->menu_icon,
            'menu_path' => $this->menu_path,
            'component' => $this->component,
            'permission_code' => $this->permission_code,
            'menu_sort' => (int) $this->menu_sort,
            'menu_status' => $this->menu_status?->value,
            'menu_status_label' => $this->menu_status?->label(),
            'children' => AuthMenuResource::collection($this->whenLoaded('childrenRecursive')),
        ];
    }
}
