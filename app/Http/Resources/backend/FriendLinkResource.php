<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendLinkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'link_name' => $this->link_name,
            'link_url' => $this->link_url,
            'link_logo' => $this->link_logo,
            'link_desc' => $this->link_desc,
            'link_sort' => (int) $this->link_sort,
            'link_status' => $this->link_status?->value,
            'link_status_label' => $this->link_status?->label(),
            'created_at' => optional($this->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($this->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }
}
