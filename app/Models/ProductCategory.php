<?php

namespace App\Models;

use App\Enums\CategoryLevel;
use App\Enums\ProductShowStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use SoftDeletes;

    protected $table = 'product_category';

    protected $fillable = [
        'category_code',
        'category_name',
        'parent_id',
        'level',
        'product_count',
        'unit',
        'cat_status',
        'sort_order',
        'cat_remark',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'level' => CategoryLevel::class,
            'product_count' => 'integer',
            'cat_status' => ProductShowStatus::class,
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
            ->orderByDesc('sort_order')
            ->orderBy('id');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }
}
