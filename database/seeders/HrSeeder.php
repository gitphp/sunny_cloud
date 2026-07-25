<?php

namespace Database\Seeders;

use App\Enums\HrDeptStatus;
use App\Enums\HrPostStatus;
use App\Models\AuthMenu;
use App\Models\AuthRole;
use App\Models\HrDepartment;
use App\Models\HrPost;
use App\Enums\MenuStatus;
use Illuminate\Database\Seeder;

class HrSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedDepartments();
        $this->seedPosts();
        $this->seedMenus();
    }

    private function seedDepartments(): void
    {
        if (HrDepartment::query()->exists()) {
            return;
        }

        $root = HrDepartment::query()->create([
            'parent_id' => 0,
            'dept_name' => '总公司',
            'dept_code' => 'ROOT',
            'ancestors' => '0',
            'dept_level' => 1,
            'leader_user_id' => 0,
            'dept_phone' => '',
            'dept_sort' => 100,
            'dept_status' => HrDeptStatus::Enabled,
            'created_by' => 0,
        ]);

        $tech = HrDepartment::query()->create([
            'parent_id' => $root->id,
            'dept_name' => '技术部',
            'dept_code' => 'TECH',
            'ancestors' => '0,'.$root->id,
            'dept_level' => 2,
            'leader_user_id' => 0,
            'dept_sort' => 90,
            'dept_status' => HrDeptStatus::Enabled,
            'created_by' => 0,
        ]);

        HrDepartment::query()->create([
            'parent_id' => $tech->id,
            'dept_name' => '前端组',
            'dept_code' => 'TECH_FE',
            'ancestors' => '0,'.$root->id.','.$tech->id,
            'dept_level' => 3,
            'leader_user_id' => 0,
            'dept_sort' => 20,
            'dept_status' => HrDeptStatus::Enabled,
            'created_by' => 0,
        ]);

        HrDepartment::query()->create([
            'parent_id' => $root->id,
            'dept_name' => '财务部',
            'dept_code' => 'FINANCE',
            'ancestors' => '0,'.$root->id,
            'dept_level' => 2,
            'leader_user_id' => 0,
            'dept_sort' => 80,
            'dept_status' => HrDeptStatus::Enabled,
            'created_by' => 0,
        ]);
    }

    private function seedPosts(): void
    {
        if (HrPost::query()->exists()) {
            return;
        }

        $gm = HrPost::query()->create([
            'parent_id' => 0,
            'post_name' => '总经理',
            'post_code' => 'GM',
            'post_sort' => 100,
            'post_status' => HrPostStatus::Enabled,
            'remark' => '公司最高管理岗位',
            'created_by' => 0,
        ]);

        $mgr = HrPost::query()->create([
            'parent_id' => $gm->id,
            'post_name' => '部门经理',
            'post_code' => 'DEPT_MGR',
            'post_sort' => 90,
            'post_status' => HrPostStatus::Enabled,
            'remark' => '',
            'created_by' => 0,
        ]);

        HrPost::query()->create([
            'parent_id' => $mgr->id,
            'post_name' => '前端开发',
            'post_code' => 'FE_DEV',
            'post_sort' => 20,
            'post_status' => HrPostStatus::Enabled,
            'remark' => '',
            'created_by' => 0,
        ]);

        HrPost::query()->create([
            'parent_id' => $mgr->id,
            'post_name' => '财务专员',
            'post_code' => 'FIN_SPEC',
            'post_sort' => 10,
            'post_status' => HrPostStatus::Enabled,
            'remark' => '',
            'created_by' => 0,
        ]);
    }

    private function seedMenus(): void
    {
        if (AuthMenu::query()->where('permission_code', 'hr')->exists()) {
            return;
        }

        $parent = AuthMenu::query()->create([
            'parent_id' => 0,
            'menu_name' => '人事管理',
            'menu_icon' => 'Avatar',
            'menu_path' => '',
            'component' => '',
            'permission_code' => 'hr',
            'menu_sort' => 65,
            'menu_status' => MenuStatus::Enabled,
        ]);

        $children = [
            [
                'menu_name' => '部门管理',
                'menu_path' => '/backend/hr/departments',
                'component' => 'hr/Department',
                'permission_code' => 'hr.department',
                'menu_sort' => 30,
            ],
            [
                'menu_name' => '岗位管理',
                'menu_path' => '/backend/hr/posts',
                'component' => 'hr/Post',
                'permission_code' => 'hr.post',
                'menu_sort' => 20,
            ],
            [
                'menu_name' => '任职管理',
                'menu_path' => '/backend/hr/user-dept-posts',
                'component' => 'hr/UserDeptPost',
                'permission_code' => 'hr.user_dept_post',
                'menu_sort' => 10,
            ],
        ];

        $menuIds = [$parent->id];
        foreach ($children as $child) {
            $menu = AuthMenu::query()->create([
                'parent_id' => $parent->id,
                'menu_name' => $child['menu_name'],
                'menu_icon' => '',
                'menu_path' => $child['menu_path'],
                'component' => $child['component'],
                'permission_code' => $child['permission_code'],
                'menu_sort' => $child['menu_sort'],
                'menu_status' => MenuStatus::Enabled,
            ]);
            $menuIds[] = $menu->id;
        }

        $roles = AuthRole::query()->whereIn('role_code', ['super_admin', 'admin'])->get();
        $now = now();
        foreach ($roles as $role) {
            $payload = [];
            foreach ($menuIds as $id) {
                $payload[$id] = ['created_at' => $now];
            }
            $role->menus()->syncWithoutDetaching($payload);
        }
    }
}
