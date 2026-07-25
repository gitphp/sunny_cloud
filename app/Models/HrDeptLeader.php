<?php

namespace App\Models;

use App\Enums\HrLeaderRoleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrDeptLeader extends Model
{
    use SoftDeletes;

    protected $table = 'hr_dept_leaders';

    protected $fillable = [
        'dept_id',
        'user_id',
        'role_type',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'dept_id' => 'integer',
            'user_id' => 'integer',
            'role_type' => HrLeaderRoleType::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(HrDepartment::class, 'dept_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'user_id', 'id');
    }
}
