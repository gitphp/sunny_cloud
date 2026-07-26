<?php

namespace App\Models;

use App\Enums\ArticleCategoryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleCategory extends Model
{
    use SoftDeletes;

    protected $table = 'article_category';

    protected $fillable = [
        'parent_id',
        'cat_name',
        'cat_url',
        'description',
        'cat_sort',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'cat_sort' => 'integer',
            'status' => ArticleCategoryStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
            ->orderByDesc('cat_sort')
            ->orderBy('id');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'category_id', 'id');
    }
}
