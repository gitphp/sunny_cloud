<?php

namespace App\Models;

use App\Enums\DataScope;
use App\Enums\RoleStatus;
use App\Enums\RoleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(AuthMenu::class, 'auth_role_menus', 'role_id', 'menu_id')
            ->withPivot('created_at');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(AuthPermission::class, 'auth_role_permissions', 'role_id', 'permission_id')
            ->withPivot('created_at');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(UserAccount::class, 'auth_user_role', 'role_id', 'user_id')
            ->withPivot('created_at');
    }
}
