<?php

namespace App\Models;

use App\Enums\ProductIsSystem;
use App\Enums\ProductShowStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductBrand extends Model
{
    use SoftDeletes;

    protected $table = 'product_brand';

    protected $fillable = [
        'brand_code',
        'brand_name',
        'alias',
        'is_system',
        'is_show',
        'sort_order',
        'brand_remark',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => ProductIsSystem::class,
            'is_show' => ProductShowStatus::class,
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function isSystemBrand(): bool
    {
        return $this->is_system === ProductIsSystem::System;
    }
}
