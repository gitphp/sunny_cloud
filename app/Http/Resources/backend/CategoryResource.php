<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'category_name' => $this->category_name,
            'parent_id' => (string) $this->parent_id,
            'show_type' => $this->show_type?->value,
            'show_type_label' => $this->show_type?->label(),
            'cat_status' => $this->cat_status?->value,
            'cat_status_label' => $this->cat_status?->label(),
            'level' => $this->level?->value,
            'level_label' => $this->level?->label(),
            'sort_order' => (int) $this->sort_order,
            'description' => $this->description,
            'cat_remark' => $this->cat_remark,
            'children' => CategoryResource::collection($this->whenLoaded('childrenRecursive')),
        ];
    }
}
