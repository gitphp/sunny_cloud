<?php

namespace App\Http\Resources\backend;

use App\Enums\SiteConfigGroup;
use App\Enums\SiteConfigInputType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteConfigResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'conf_group' => $this->conf_group,
            'conf_group_label' => SiteConfigGroup::tryFrom((string) $this->conf_group)?->label() ?? $this->conf_group,
            'conf_key' => $this->conf_key,
            'conf_value' => $this->conf_value,
            'conf_desc' => $this->conf_desc,
            'input_type' => $this->input_type,
            'input_type_label' => SiteConfigInputType::tryFrom((string) $this->input_type)?->label() ?? $this->input_type,
            'conf_sort' => (int) $this->conf_sort,
            'created_at' => optional($this->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($this->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }
}
