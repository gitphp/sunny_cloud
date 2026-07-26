<?php

namespace Database\Seeders;

use App\Enums\MenuStatus;
use App\Models\AuthMenu;
use App\Models\AuthRole;
use Illuminate\Database\Seeder;

class BookMarkSeeder extends Seeder
{
    public function run(): void
    {
        $parent = AuthMenu::query()->updateOrCreate(
            ['permission_code' => 'bookmarkview'],
            [
                'parent_id' => 0,
                'menu_name' => '书签管理',
                'menu_icon' => 'Star',
                'menu_path' => '',
                'component' => '',
                'menu_sort' => 55,
                'menu_status' => MenuStatus::Enabled,
            ]
        );

        $listMenu = AuthMenu::query()->updateOrCreate(
            ['permission_code' => 'bookmarklist'],
            [
                'parent_id' => $parent->id,
                'menu_name' => '书签列表',
                'menu_icon' => '',
                'menu_path' => '/backend/bookmarks',
                'component' => 'bookmark/Index',
                'menu_sort' => 10,
                'menu_status' => MenuStatus::Enabled,
            ]
        );

        // 旧路径兼容：禁用「我的书签」占位菜单
        AuthMenu::query()
            ->where('permission_code', 'bookmarkmy')
            ->update(['menu_status' => MenuStatus::Disabled]);

        $roles = AuthRole::query()->whereIn('role_code', ['super_admin', 'admin'])->get();
        $now = now();
        foreach ($roles as $role) {
            $role->menus()->syncWithoutDetaching([
                $parent->id => ['created_at' => $now],
                $listMenu->id => ['created_at' => $now],
            ]);
        }
    }
}
