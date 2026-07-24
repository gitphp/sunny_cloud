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
            'id' => (int) $this->id,
            'name' => $this->name,
            'parent_id' => (int) $this->parent_id,
            'sort' => (int) $this->sort,
            'children' => CategoryResource::collection($this->whenLoaded('childrenRecursive')),
        ];
    }
}
