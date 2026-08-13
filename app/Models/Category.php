<?php

namespace App\Models;

use App\Enums\CategoryLevel;
use App\Enums\CategoryShowType;
use App\Enums\CategoryStatus;
use App\Enums\CategoryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $table = 'category';

    protected $fillable = [
        'category_name',
        'parent_id',
        'category_type',
        'show_type',
        'cat_status',
        'level',
        'sort_order',
        'description',
        'cat_remark',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'category_type' => CategoryType::class,
            'show_type' => CategoryShowType::class,
            'cat_status' => CategoryStatus::class,
            'level' => CategoryLevel::class,
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
