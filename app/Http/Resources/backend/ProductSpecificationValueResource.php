<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSpecificationValueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'spec_id' => (string) $this->spec_id,
            'value_code' => $this->value_code,
            'value' => $this->value,
            'sort_order' => (int) $this->sort_order,
            'value_status' => $this->value_status?->value,
            'value_status_label' => $this->value_status?->label(),
        ];
    }
}
