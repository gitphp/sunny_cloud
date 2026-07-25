<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HrUserDeptPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'user_id' => (string) $this->user_id,
            'dept_id' => (string) $this->dept_id,
            'post_id' => (string) $this->post_id,
            'is_main' => $this->is_main?->value,
            'is_main_label' => $this->is_main?->label(),
            'remark' => $this->remark,
            'start_at' => optional($this->start_at)?->format('Y-m-d H:i:s'),
            'end_at' => optional($this->end_at)?->format('Y-m-d H:i:s'),
            'user_name' => $this->whenLoaded('user', fn () => $this->user?->user_name),
            'nick_name' => $this->whenLoaded('user', fn () => $this->user?->nick_name),
            'dept_name' => $this->whenLoaded('department', fn () => $this->department?->dept_name),
            'post_name' => $this->whenLoaded('post', fn () => $this->post?->post_name),
        ];
    }
}
