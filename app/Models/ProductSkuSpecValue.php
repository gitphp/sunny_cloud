<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSkuSpecValue extends Model
{
    use SoftDeletes;

    protected $table = 'product_sku_spec_value';

    protected $fillable = [
        'sku_id',
        'spec_id',
        'spec_value_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'sku_id' => 'integer',
            'spec_id' => 'integer',
            'spec_value_id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function sku(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class, 'sku_id', 'id');
    }

    public function specification(): BelongsTo
    {
        return $this->belongsTo(ProductSpecification::class, 'spec_id', 'id');
    }

    public function value(): BelongsTo
    {
        return $this->belongsTo(ProductSpecificationValue::class, 'spec_value_id', 'id');
    }
}
