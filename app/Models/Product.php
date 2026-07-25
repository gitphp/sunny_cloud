<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'product';

    protected $fillable = [
        'auto_code',
        'product_name',
        'product_model',
        'category_id',
        'brand_id',
        'material_quality',
        'filling',
        'short_desc',
        'main_image_url',
        'product_status',
        'sort_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'brand_id' => 'integer',
            'product_status' => ProductStatus::class,
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class, 'brand_id', 'id');
    }

    public function skus(): HasMany
    {
        return $this->hasMany(ProductSku::class, 'product_id', 'id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class, 'product_id', 'id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
