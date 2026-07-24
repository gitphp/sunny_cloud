<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAccountResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'user_name' => $this->user_name,
            'nick_name' => $this->nick_name,
            'user_mobile' => $this->user_mobile,
            'user_email' => $this->user_email,
            'user_status' => $this->user_status?->value,
            'user_status_label' => $this->user_status?->label(),
            'lock_reason' => $this->lock_reason,
            'lock_expire_time' => optional($this->lock_expire_time)?->format('Y-m-d H:i:s'),
            'last_login_ip' => $this->last_login_ip,
            'last_login_region' => $this->last_login_region,
            'last_login_at' => optional($this->last_login_at)?->format('Y-m-d H:i:s'),
            'register_ip' => $this->register_ip,
            'register_device' => $this->register_device,
            'register_channel' => $this->register_channel,
            'real_auth_status' => $this->real_auth_status?->value,
            'real_auth_status_label' => $this->real_auth_status?->label(),
            'role_ids' => $this->whenLoaded('roles', fn () => $this->roles->pluck('id')->map(fn ($id) => (string) $id)->values()->all()),
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->map(fn ($role) => [
                'id' => (string) $role->id,
                'role_name' => $role->role_name,
                'role_code' => $role->role_code,
            ])->values()->all()),
            'created_at' => optional($this->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($this->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }
}
