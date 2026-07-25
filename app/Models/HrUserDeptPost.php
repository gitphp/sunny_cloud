<?php

namespace App\Models;

use App\Enums\HrIsMain;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrUserDeptPost extends Model
{
    protected $table = 'hr_user_dept_post';

    protected $fillable = [
        'user_id',
        'dept_id',
        'post_id',
        'is_main',
        'remark',
        'start_at',
        'end_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'dept_id' => 'integer',
            'post_id' => 'integer',
            'is_main' => HrIsMain::class,
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'user_id', 'id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(HrDepartment::class, 'dept_id', 'id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(HrPost::class, 'post_id', 'id');
    }
}
