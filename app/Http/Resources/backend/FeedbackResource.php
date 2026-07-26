<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'fb_name' => $this->fb_name,
            'fb_phone' => $this->fb_phone,
            'fb_email' => $this->fb_email,
            'fb_company' => $this->fb_company,
            'fb_title' => $this->fb_title,
            'fb_content' => $this->fb_content,
            'fb_status' => $this->fb_status?->value,
            'fb_status_label' => $this->fb_status?->label(),
            'reply_content' => $this->reply_content,
            'replied_at' => optional($this->replied_at)?->format('Y-m-d H:i:s'),
            'ip' => $this->ip,
            'created_at' => optional($this->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($this->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }
}
