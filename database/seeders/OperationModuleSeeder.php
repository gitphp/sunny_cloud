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

        $feedbackMenu = AuthMenu::query()->updateOrCreate(
            ['permission_code' => 'feedbacksview'],
            [
                'parent_id' => $parent->id,
                'menu_name' => '用户留言',
                'menu_icon' => '',
                'menu_path' => '/backend/feedbacks',
                'component' => 'operation/FeedbackIndex',
                'menu_sort' => 20,
                'menu_status' => MenuStatus::Enabled,
            ]
        );

        $jobMenu = AuthMenu::query()->updateOrCreate(
            ['permission_code' => 'bossjobview'],
            [
                'parent_id' => $parent->id,
                'menu_name' => '招聘职位',
                'menu_icon' => '',
                'menu_path' => '/backend/boss-jobs',
                'component' => 'operation/BossJobIndex',
                'menu_sort' => 10,
                'menu_status' => MenuStatus::Enabled,
            ]
        );

        $roles = AuthRole::query()->whereIn('role_code', ['super_admin', 'admin'])->get();
        $now = now();
        foreach ($roles as $role) {
            $role->menus()->syncWithoutDetaching([
                $parent->id => ['created_at' => $now],
                $feedbackMenu->id => ['created_at' => $now],
                $jobMenu->id => ['created_at' => $now],
            ]);
        }
    }
}
