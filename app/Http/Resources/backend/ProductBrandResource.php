<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductBrandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'brand_code' => $this->brand_code,
            'brand_name' => $this->brand_name,
            'alias' => $this->alias,
            'is_system' => $this->is_system?->value,
            'is_system_label' => $this->is_system?->label(),
            'is_show' => $this->is_show?->value,
            'is_show_label' => $this->is_show?->label(),
            'sort_order' => (int) $this->sort_order,
            'brand_remark' => $this->brand_remark,
        ];
    }
}
