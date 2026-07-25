<?php

namespace App\Models;

use App\Enums\ProductShowStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSpecificationValue extends Model
{
    use SoftDeletes;

    protected $table = 'product_specification_value';

    protected $fillable = [
        'spec_id',
        'value_code',
        'value',
        'sort_order',
        'value_status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'spec_id' => 'integer',
            'sort_order' => 'integer',
            'value_status' => ProductShowStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function specification(): BelongsTo
    {
        return $this->belongsTo(ProductSpecification::class, 'spec_id', 'id');
    }
}
