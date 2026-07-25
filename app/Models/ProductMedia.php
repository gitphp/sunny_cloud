<?php

namespace App\Models;

use App\Enums\ProductMediaType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductMedia extends Model
{
    use SoftDeletes;

    protected $table = 'product_media';

    protected $fillable = [
        'product_id',
        'media_type',
        'file_url',
        'file_name',
        'file_key',
        'storage_provider',
        'extension',
        'file_size',
        'file_type',
        'sort_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'media_type' => ProductMediaType::class,
            'file_size' => 'integer',
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
}
