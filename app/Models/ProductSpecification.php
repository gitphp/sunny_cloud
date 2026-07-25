<?php

namespace App\Models;

use App\Enums\ProductShowStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSpecification extends Model
{
    use SoftDeletes;

    protected $table = 'product_specification';

    protected $fillable = [
        'spec_code',
        'spec_name',
        'spec_remark',
        'spec_status',
        'sort_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'spec_status' => ProductShowStatus::class,
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function values(): HasMany
    {
        return $this->hasMany(ProductSpecificationValue::class, 'spec_id', 'id')
            ->orderByDesc('sort_order')
            ->orderBy('id');
    }
}
