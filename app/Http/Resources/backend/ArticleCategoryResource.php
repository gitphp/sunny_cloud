<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'parent_id' => (string) $this->parent_id,
            'cat_name' => $this->cat_name,
            'cat_url' => $this->cat_url,
            'description' => $this->description,
            'cat_sort' => (int) $this->cat_sort,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'created_at' => optional($this->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($this->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }
}
