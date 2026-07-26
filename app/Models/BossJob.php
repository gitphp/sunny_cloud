<?php

namespace App\Models;

use App\Enums\BossJobHot;
use App\Enums\BossJobStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BossJob extends Model
{
    use SoftDeletes;

    protected $table = 'boss_job';

    protected $fillable = [
        'job_title',
        'department',
        'workplace',
        'experience',
        'education',
        'salary_range',
        'description',
        'requirements',
        'benefits',
        'is_hot',
        'job_status',
        'expire_at',
        'view_count',
        'job_sort',
    ];

    protected function casts(): array
    {
        return [
            'is_hot' => BossJobHot::class,
            'job_status' => BossJobStatus::class,
            'view_count' => 'integer',
            'job_sort' => 'integer',
            'expire_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
