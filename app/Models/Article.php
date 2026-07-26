<?php

namespace App\Models;

use App\Enums\ArticleContentType;
use App\Enums\ArticleFlag;
use App\Enums\ArticleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    protected $table = 'articles';

    protected $fillable = [
        'title',
        'subtitle',
        'art_cover',
        'art_content',
        'content_type',
        'summary',
        'category_id',
        'tag_ids',
        'author_id',
        'author_name',
        'source',
        'source_url',
        'art_status',
        'is_top',
        'is_original',
        'is_commentable',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'extra_fields',
        'view_count',
        'like_count',
        'collect_count',
        'share_count',
        'comment_count',
        'published_at',
        'reviewer_id',
        'reviewed_at',
        'reject_reason',
    ];

    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'author_id' => 'integer',
            'reviewer_id' => 'integer',
            'content_type' => ArticleContentType::class,
            'art_status' => ArticleStatus::class,
            'is_top' => ArticleFlag::class,
            'is_original' => ArticleFlag::class,
            'is_commentable' => ArticleFlag::class,
            'tag_ids' => 'array',
            'extra_fields' => 'array',
            'published_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id', 'id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'author_id', 'id');
    }
}
