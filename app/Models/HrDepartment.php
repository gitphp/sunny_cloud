<?php

namespace App\Models;

use App\Enums\HrDeptStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrDepartment extends Model
{
    use SoftDeletes;

    protected $table = 'hr_department';

    protected $fillable = [
        'parent_id',
        'dept_name',
        'dept_code',
        'ancestors',
        'dept_level',
        'leader_user_id',
        'dept_phone',
        'dept_sort',
        'dept_status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'dept_level' => 'integer',
            'leader_user_id' => 'integer',
            'dept_sort' => 'integer',
            'dept_status' => HrDeptStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
            ->orderByDesc('dept_sort')
            ->orderBy('id');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function leaders(): HasMany
    {
        return $this->hasMany(HrDeptLeader::class, 'dept_id', 'id');
    }

    public function leaderUser(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'leader_user_id', 'id');
    }

    public function userDeptPosts(): HasMany
    {
        return $this->hasMany(HrUserDeptPost::class, 'dept_id', 'id');
    }
}
