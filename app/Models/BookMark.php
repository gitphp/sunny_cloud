<?php

namespace App\Models;

use App\Enums\BookMarkBold;
use App\Enums\BookMarkStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookMark extends Model
{
    protected $table = 'book_mark';

    protected $fillable = [
        'category_id',
        'short_title',
        'book_title',
        'book_url',
        'book_favicon',
        'book_desc',
        'sort_order',
        'status',
        'is_bold',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'sort_order' => 'integer',
            'created_by' => 'integer',
            'status' => BookMarkStatus::class,
            'is_bold' => BookMarkBold::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
