<?php

namespace Database\Seeders;

use App\Enums\MenuStatus;
use App\Models\AuthMenu;
use App\Models\AuthRole;
use Illuminate\Database\Seeder;

class OperationModuleSeeder extends Seeder
{
    public function run(): void
    {
        $parent = AuthMenu::query()->updateOrCreate(
            ['permission_code' => 'operationview'],
            [
                'parent_id' => 0,
                'menu_name' => '运营管理',
                'menu_icon' => 'Promotion',
                'menu_path' => '',
                'component' => '',
                'menu_sort' => 50,
                'menu_status' => MenuStatus::Enabled,
            ]
        );

        $menus = [
            [
                'permission_code' => 'adslotsview',
                'menu_name' => '广告位管理',
                'menu_path' => '/backend/ad-slots',
                'component' => 'operation/AdSlotIndex',
                'menu_sort' => 50,
            ],
            [
                'permission_code' => 'adpositionsview',
                'menu_name' => '广告管理',
                'menu_path' => '/backend/ad-positions',
                'component' => 'operation/AdPositionIndex',
                'menu_sort' => 40,
            ],
            [
                'permission_code' => 'friendlinksview',
                'menu_name' => '友情链接',
                'menu_path' => '/backend/friend-links',
                'component' => 'operation/FriendLinkIndex',
                'menu_sort' => 30,
            ],
            [
                'permission_code' => 'feedbacksview',
                'menu_name' => '用户留言',
                'menu_path' => '/backend/feedbacks',
                'component' => 'operation/FeedbackIndex',
                'menu_sort' => 20,
            ],
            [
                'permission_code' => 'bossjobview',
                'menu_name' => '招聘职位',
                'menu_path' => '/backend/boss-jobs',
                'component' => 'operation/BossJobIndex',
                'menu_sort' => 10,
            ],
        ];

        $menuIds = [$parent->id];
        foreach ($menus as $item) {
            $menu = AuthMenu::query()->updateOrCreate(
                ['permission_code' => $item['permission_code']],
                [
                    'parent_id' => $parent->id,
                    'menu_name' => $item['menu_name'],
                    'menu_icon' => '',
                    'menu_path' => $item['menu_path'],
                    'component' => $item['component'],
                    'menu_sort' => $item['menu_sort'],
                    'menu_status' => MenuStatus::Enabled,
                ]
            );
            $menuIds[] = $menu->id;
        }

        $systemParent = AuthMenu::query()->updateOrCreate(
            ['permission_code' => 'systemview'],
            [
                'parent_id' => 0,
                'menu_name' => '系统管理',
                'menu_icon' => 'Setting',
                'menu_path' => '',
                'component' => '',
                'menu_sort' => 40,
                'menu_status' => MenuStatus::Enabled,
            ]
        );

        $configMenu = AuthMenu::query()->updateOrCreate(
            ['permission_code' => 'configview'],
            [
                'parent_id' => $systemParent->id,
                'menu_name' => '网站设置',
                'menu_icon' => '',
                'menu_path' => '/backend/system/settings',
                'component' => 'system/SiteConfig',
                'menu_sort' => 20,
                'menu_status' => MenuStatus::Enabled,
            ]
        );

        $roles = AuthRole::query()->whereIn('role_code', ['super_admin', 'admin'])->get();
        $now = now();
        $allIds = array_merge($menuIds, [$systemParent->id, $configMenu->id]);
        foreach ($roles as $role) {
            $payload = [];
            foreach ($allIds as $id) {
                $payload[$id] = ['created_at' => $now];
            }
            $role->menus()->syncWithoutDetaching($payload);
        }
    }
}
