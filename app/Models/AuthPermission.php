<?php

namespace App\Models;

use App\Enums\PermissionStatus;
use App\Enums\PermissionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuthPermission extends Model
{
    use SoftDeletes;

    protected $table = 'auth_permissions';

    protected $fillable = [
        'parent_id',
        'per_name',
        'per_code',
        'per_type',
        'per_path',
        'per_method',
        'per_icon',
        'per_sort',
        'per_status',
    ];

    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'per_type' => PermissionType::class,
            'per_sort' => 'integer',
            'per_status' => PermissionStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
            ->orderByDesc('per_sort')
            ->orderBy('id');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(AuthRole::class, 'auth_role_permissions', 'permission_id', 'role_id')
            ->withPivot('created_at');
    }
}
