<?php

namespace App\Models;

use App\Enums\DataScope;
use App\Enums\RoleStatus;
use App\Enums\RoleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuthRole extends Model
{
    use SoftDeletes;

    protected $table = 'auth_role';

    protected $fillable = [
        'role_name',
        'role_code',
        'role_type',
        'role_sort',
        'data_scope',
        'scope_departments',
        'role_status',
        'role_remark',
    ];

    protected function casts(): array
    {
        return [
            'role_type' => RoleType::class,
            'role_sort' => 'integer',
            'data_scope' => DataScope::class,
            'scope_departments' => 'array',
            'role_status' => RoleStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function isSystem(): bool
    {
        return $this->role_type === RoleType::System;
    }
}
