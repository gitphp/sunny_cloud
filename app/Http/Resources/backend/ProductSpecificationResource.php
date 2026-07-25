<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSpecificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'spec_code' => $this->spec_code,
            'spec_name' => $this->spec_name,
            'spec_remark' => $this->spec_remark,
            'spec_status' => $this->spec_status?->value,
            'spec_status_label' => $this->spec_status?->label(),
            'sort_order' => (int) $this->sort_order,
            'values' => ProductSpecificationValueResource::collection($this->whenLoaded('values')),
        ];
    }
}
