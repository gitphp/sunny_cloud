<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookMarkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'category_id' => (string) $this->category_id,
            'category_name' => $this->category?->category_name ?? ($this->category_id == 0 ? '未分类' : ''),
            'short_title' => $this->short_title,
            'book_title' => $this->book_title,
            'book_url' => $this->book_url,
            'book_favicon' => $this->book_favicon,
            'book_desc' => $this->book_desc,
            'sort_order' => (int) $this->sort_order,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'is_bold' => $this->is_bold?->value,
            'is_bold_label' => $this->is_bold?->label(),
            'created_by' => (string) $this->created_by,
            'created_at' => optional($this->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($this->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }
}
