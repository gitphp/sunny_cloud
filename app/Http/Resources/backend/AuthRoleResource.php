<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthRoleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'role_name' => $this->role_name,
            'role_code' => $this->role_code,
            'role_type' => $this->role_type?->value,
            'role_type_label' => $this->role_type?->label(),
            'role_sort' => (int) $this->role_sort,
            'data_scope' => $this->data_scope?->value,
            'data_scope_label' => $this->data_scope?->label(),
            'scope_departments' => $this->scope_departments ?? [],
            'role_status' => $this->role_status?->value,
            'role_status_label' => $this->role_status?->label(),
            'role_remark' => $this->role_remark,
            'menu_ids' => $this->whenLoaded('menus', fn () => $this->menus->pluck('id')->map(fn ($id) => (string) $id)->values()->all()),
            'permission_ids' => $this->whenLoaded('permissions', fn () => $this->permissions->pluck('id')->map(fn ($id) => (string) $id)->values()->all()),
            'created_at' => optional($this->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($this->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }
}
