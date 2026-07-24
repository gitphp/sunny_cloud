<?php

namespace Database\Seeders;

use App\Enums\DataScope;
use App\Enums\MenuStatus;
use App\Enums\RoleStatus;
use App\Enums\RoleType;
use App\Models\AuthMenu;
use App\Models\AuthRole;
use Illuminate\Database\Seeder;

class AuthRoleSeeder extends Seeder
{
    public function run(): void
    {
        AuthRole::query()->updateOrCreate(
            ['role_code' => 'super_admin'],
            [
                'role_name' => '超级管理员',
                'role_type' => RoleType::System,
                'role_sort' => 100,
                'data_scope' => DataScope::All,
                'scope_departments' => null,
                'role_status' => RoleStatus::Enabled,
                'role_remark' => '系统内置超级管理员，拥有全部数据权限',
            ]
        );

        AuthRole::query()->updateOrCreate(
            ['role_code' => 'admin'],
            [
                'role_name' => '管理员',
                'role_type' => RoleType::System,
                'role_sort' => 90,
                'data_scope' => DataScope::All,
                'scope_departments' => null,
                'role_status' => RoleStatus::Enabled,
                'role_remark' => '系统内置管理员',
            ]
        );

        $this->ensureRoleMenu();
    }

    private function ensureRoleMenu(): void
    {
        if (AuthMenu::query()->where('menu_path', '/backend/roles')->exists()) {
            return;
        }

        $parent = AuthMenu::query()
            ->where('menu_name', '权限菜单分类管理')
            ->where('parent_id', 0)
            ->first();

        if (! $parent) {
            $parent = AuthMenu::query()->create([
                'parent_id' => 0,
                'menu_name' => '权限菜单分类管理',
                'menu_icon' => 'Lock',
                'menu_path' => '',
                'component' => '',
                'permission_code' => 'permission',
                'menu_sort' => 90,
                'menu_status' => MenuStatus::Enabled,
            ]);
        }

        AuthMenu::query()->create([
            'parent_id' => $parent->id,
            'menu_name' => '角色管理',
            'menu_icon' => '',
            'menu_path' => '/backend/roles',
            'component' => 'roles/Index',
            'permission_code' => 'role.view',
            'menu_sort' => 15,
            'menu_status' => MenuStatus::Enabled,
        ]);
    }
}
