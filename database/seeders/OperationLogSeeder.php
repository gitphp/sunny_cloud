<?php

namespace Database\Seeders;

use App\Enums\MenuStatus;
use App\Models\AuthMenu;
use App\Models\AuthRole;
use Illuminate\Database\Seeder;

class OperationLogSeeder extends Seeder
{
    public function run(): void
    {
        $parent = AuthMenu::query()->where('permission_code', 'systemview')->first();

        if (! $parent) {
            $parent = AuthMenu::query()->updateOrCreate(
                ['permission_code' => 'system'],
                [
                    'parent_id' => 0,
                    'menu_name' => '系统设置',
                    'menu_icon' => 'Setting',
                    'menu_path' => '',
                    'component' => '',
                    'menu_sort' => 40,
                    'menu_status' => MenuStatus::Enabled,
                ]
            );
        }

        $menu = AuthMenu::query()->updateOrCreate(
            ['permission_code' => 'logview'],
            [
                'parent_id' => $parent->id,
                'menu_name' => '操作日志',
                'menu_icon' => 'Document',
                'menu_path' => '/backend/system/operation-logs',
                'component' => 'system/OperationLog',
                'menu_sort' => 5,
                'menu_status' => MenuStatus::Enabled,
            ]
        );

        // 兼容旧 permission_code
        AuthMenu::query()
            ->where('permission_code', 'system.operation_log')
            ->where('id', '!=', $menu->id)
            ->delete();

        $roles = AuthRole::query()->whereIn('role_code', ['super_admin', 'admin'])->get();
        $now = now();
        foreach ($roles as $role) {
            $role->menus()->syncWithoutDetaching([
                $parent->id => ['created_at' => $now],
                $menu->id => ['created_at' => $now],
            ]);
        }
    }
}
