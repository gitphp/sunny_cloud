<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'category_code' => $this->category_code,
            'category_name' => $this->category_name,
            'parent_id' => (string) $this->parent_id,
            'level' => $this->level?->value,
            'level_label' => $this->level?->label(),
            'product_count' => (int) $this->product_count,
            'unit' => $this->unit,
            'cat_status' => $this->cat_status?->value,
            'cat_status_label' => $this->cat_status?->label(),
            'sort_order' => (int) $this->sort_order,
            'cat_remark' => $this->cat_remark,
            'children' => ProductCategoryResource::collection($this->whenLoaded('childrenRecursive')),
        ];
    }
}
