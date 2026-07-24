<?php

namespace Database\Seeders;

use App\Enums\MenuStatus;
use App\Enums\PermissionStatus;
use App\Enums\PermissionType;
use App\Models\AuthMenu;
use App\Models\AuthPermission;
use Illuminate\Database\Seeder;

class AuthPermissionSeeder extends Seeder
{
    public function run(): void
    {
        if (! AuthPermission::query()->exists()) {
            $this->seedPermissions();
        }

        $this->ensurePermissionMenu();
    }

    private function seedPermissions(): void
    {
        $modules = [
            [
                'per_name' => '用户管理',
                'per_code' => 'user',
                'per_type' => PermissionType::Menu,
                'per_path' => '/backend/users',
                'per_icon' => 'User',
                'per_sort' => 100,
                'children' => [
                    ['per_name' => '用户查看', 'per_code' => 'user:view', 'per_type' => PermissionType::Api, 'per_path' => '/backend/api/users', 'per_method' => 'GET', 'per_sort' => 40],
                    ['per_name' => '用户新增', 'per_code' => 'user:create', 'per_type' => PermissionType::Button, 'per_path' => '', 'per_method' => '', 'per_sort' => 30],
                    ['per_name' => '用户编辑', 'per_code' => 'user:update', 'per_type' => PermissionType::Button, 'per_path' => '', 'per_method' => '', 'per_sort' => 20],
                    ['per_name' => '用户删除', 'per_code' => 'user:delete', 'per_type' => PermissionType::Button, 'per_path' => '', 'per_method' => '', 'per_sort' => 10],
                ],
            ],
            [
                'per_name' => '角色管理',
                'per_code' => 'role',
                'per_type' => PermissionType::Menu,
                'per_path' => '/backend/roles',
                'per_icon' => 'Avatar',
                'per_sort' => 90,
                'children' => [
                    ['per_name' => '角色查看', 'per_code' => 'role:view', 'per_type' => PermissionType::Api, 'per_path' => '/backend/api/roles', 'per_method' => 'GET', 'per_sort' => 40],
                    ['per_name' => '角色新增', 'per_code' => 'role:create', 'per_type' => PermissionType::Button, 'per_path' => '', 'per_method' => '', 'per_sort' => 30],
                    ['per_name' => '角色编辑', 'per_code' => 'role:update', 'per_type' => PermissionType::Button, 'per_path' => '', 'per_method' => '', 'per_sort' => 20],
                    ['per_name' => '角色删除', 'per_code' => 'role:delete', 'per_type' => PermissionType::Button, 'per_path' => '', 'per_method' => '', 'per_sort' => 10],
                ],
            ],
            [
                'per_name' => '菜单管理',
                'per_code' => 'menu',
                'per_type' => PermissionType::Menu,
                'per_path' => '/backend/menus',
                'per_icon' => 'Menu',
                'per_sort' => 80,
                'children' => [
                    ['per_name' => '菜单查看', 'per_code' => 'menu:view', 'per_type' => PermissionType::Api, 'per_path' => '/backend/api/menus', 'per_method' => 'GET', 'per_sort' => 40],
                    ['per_name' => '菜单新增', 'per_code' => 'menu:create', 'per_type' => PermissionType::Button, 'per_path' => '', 'per_method' => '', 'per_sort' => 30],
                    ['per_name' => '菜单编辑', 'per_code' => 'menu:update', 'per_type' => PermissionType::Button, 'per_path' => '', 'per_method' => '', 'per_sort' => 20],
                    ['per_name' => '菜单删除', 'per_code' => 'menu:delete', 'per_type' => PermissionType::Button, 'per_path' => '', 'per_method' => '', 'per_sort' => 10],
                ],
            ],
            [
                'per_name' => '权限管理',
                'per_code' => 'permission',
                'per_type' => PermissionType::Menu,
                'per_path' => '/backend/permissions',
                'per_icon' => 'Lock',
                'per_sort' => 70,
                'children' => [
                    ['per_name' => '权限查看', 'per_code' => 'permission:view', 'per_type' => PermissionType::Api, 'per_path' => '/backend/api/permissions', 'per_method' => 'GET', 'per_sort' => 40],
                    ['per_name' => '权限新增', 'per_code' => 'permission:create', 'per_type' => PermissionType::Button, 'per_path' => '', 'per_method' => '', 'per_sort' => 30],
                    ['per_name' => '权限编辑', 'per_code' => 'permission:update', 'per_type' => PermissionType::Button, 'per_path' => '', 'per_method' => '', 'per_sort' => 20],
                    ['per_name' => '权限删除', 'per_code' => 'permission:delete', 'per_type' => PermissionType::Button, 'per_path' => '', 'per_method' => '', 'per_sort' => 10],
                ],
            ],
        ];

        foreach ($modules as $module) {
            $this->createPermission($module, 0);
        }
    }

    private function createPermission(array $item, int|string $parentId): AuthPermission
    {
        $permission = AuthPermission::query()->create([
            'parent_id' => $parentId,
            'per_name' => $item['per_name'],
            'per_code' => $item['per_code'],
            'per_type' => $item['per_type'],
            'per_path' => $item['per_path'] ?? '',
            'per_method' => $item['per_method'] ?? '',
            'per_icon' => $item['per_icon'] ?? '',
            'per_sort' => $item['per_sort'] ?? 0,
            'per_status' => PermissionStatus::Enabled,
        ]);

        foreach ($item['children'] ?? [] as $child) {
            $this->createPermission($child, $permission->id);
        }

        return $permission;
    }

    private function ensurePermissionMenu(): void
    {
        // 菜单路径已存在则跳过（占位路由 /backend/permissions）
        $exists = AuthMenu::query()->where('menu_path', '/backend/permissions')->exists();
        if ($exists) {
            // 若仍是 Placeholder，更新为真实组件
            AuthMenu::query()
                ->where('menu_path', '/backend/permissions')
                ->where(function ($q) {
                    $q->where('component', 'Placeholder')->orWhere('component', '');
                })
                ->update([
                    'menu_name' => '权限管理',
                    'component' => 'permissions/Index',
                    'permission_code' => 'permission.view',
                ]);

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
            'menu_name' => '权限管理',
            'menu_icon' => '',
            'menu_path' => '/backend/permissions',
            'component' => 'permissions/Index',
            'permission_code' => 'permission.view',
            'menu_sort' => 25,
            'menu_status' => MenuStatus::Enabled,
        ]);
    }
}
