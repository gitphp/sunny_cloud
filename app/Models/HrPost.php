<?php

namespace App\Models;

use App\Enums\HrPostStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrPost extends Model
{
    use SoftDeletes;

    protected $table = 'hr_post';

    protected $fillable = [
        'parent_id',
        'post_name',
        'post_code',
        'post_sort',
        'post_status',
        'remark',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'post_sort' => 'integer',
            'post_status' => HrPostStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
            ->orderByDesc('post_sort')
            ->orderBy('id');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function userDeptPosts(): HasMany
    {
        return $this->hasMany(HrUserDeptPost::class, 'post_id', 'id');
    }
}
