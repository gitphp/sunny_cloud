<?php

namespace App\Models;

use App\Enums\MenuStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuthMenu extends Model
{
    use SoftDeletes;

    protected $table = 'auth_menus';

    protected $fillable = [
        'parent_id',
        'menu_name',
        'menu_icon',
        'menu_path',
        'component',
        'permission_code',
        'menu_sort',
        'menu_status',
    ];

    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'menu_sort' => 'integer',
            'menu_status' => MenuStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
            ->orderByDesc('menu_sort')
            ->orderBy('id');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(AuthRole::class, 'auth_role_menus', 'menu_id', 'role_id')
            ->withPivot('created_at');
    }
}
