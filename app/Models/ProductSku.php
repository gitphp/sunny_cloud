<?php

namespace App\Models;

use App\Enums\ProductSkuSaleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSku extends Model
{
    use SoftDeletes;

    protected $table = 'product_sku';

    protected $fillable = [
        'product_id',
        'sku_code',
        'price',
        'market_price',
        'cost_price',
        'stock_num',
        'weight',
        'volume',
        'sale_status',
        'sort_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'price' => 'decimal:2',
            'market_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'stock_num' => 'integer',
            'weight' => 'decimal:2',
            'volume' => 'decimal:4',
            'sale_status' => ProductSkuSaleStatus::class,
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function specValues(): HasMany
    {
        return $this->hasMany(ProductSkuSpecValue::class, 'sku_id', 'id');
    }
}
