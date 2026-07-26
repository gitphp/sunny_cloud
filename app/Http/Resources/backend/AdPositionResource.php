<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdPositionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return app(\App\Service\AdPositionService::class)->toArray($this->resource);
    }
}
