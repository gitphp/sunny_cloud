<?php

namespace Database\Seeders;

use App\Enums\MenuStatus;
use App\Enums\WfStatus;
use App\Models\AuthMenu;
use App\Models\AuthRole;
use App\Models\WfFlowType;
use Illuminate\Database\Seeder;

class WfSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedFlowTypes();
        $this->seedMenus();
    }

    private function seedFlowTypes(): void
    {
        $types = [
            ['type_name' => '请假审批', 'type_code' => 'leave', 'icon' => 'Calendar', 'sort' => 100],
            ['type_name' => '费用报销', 'type_code' => 'reimburse', 'icon' => 'Wallet', 'sort' => 90],
            ['type_name' => '采购申请', 'type_code' => 'purchase', 'icon' => 'ShoppingCart', 'sort' => 80],
            ['type_name' => '商品上架审批', 'type_code' => 'product_online', 'icon' => 'Goods', 'sort' => 70],
            ['type_name' => '客户入驻审批', 'type_code' => 'customer_audit', 'icon' => 'UserFilled', 'sort' => 60],
        ];

        foreach ($types as $type) {
            if (WfFlowType::query()->where('type_code', $type['type_code'])->exists()) {
                continue;
            }
            WfFlowType::query()->create([
                ...$type,
                'status' => WfStatus::Enabled,
            ]);
        }
    }

    private function seedMenus(): void
    {
        $parent = AuthMenu::query()->where('permission_code', 'wf')->first();
        if (! $parent) {
            $parent = AuthMenu::query()->create([
                'parent_id' => 0,
                'menu_name' => '流程管理',
                'menu_icon' => 'Share',
                'menu_path' => '',
                'component' => '',
                'permission_code' => 'wf',
                'menu_sort' => 35,
                'menu_status' => MenuStatus::Enabled,
            ]);
        }

        $children = [
            [
                'menu_name' => '待我审批',
                'menu_path' => '/backend/wf/todo',
                'component' => 'wf/TodoIndex',
                'permission_code' => 'wf.todo',
                'menu_sort' => 50,
            ],
            [
                'menu_name' => '我的申请',
                'menu_path' => '/backend/wf/applies',
                'component' => 'wf/ApplyIndex',
                'permission_code' => 'wf.apply',
                'menu_sort' => 40,
            ],
            [
                'menu_name' => '抄送我的',
                'menu_path' => '/backend/wf/cc',
                'component' => 'wf/CcIndex',
                'permission_code' => 'wf.cc',
                'menu_sort' => 30,
            ],
            [
                'menu_name' => '流程类型',
                'menu_path' => '/backend/wf/flow-types',
                'component' => 'wf/FlowType',
                'permission_code' => 'wf.flow_type',
                'menu_sort' => 20,
            ],
            [
                'menu_name' => '流程模板',
                'menu_path' => '/backend/wf/flow-definitions',
                'component' => 'wf/FlowDefinitionIndex',
                'permission_code' => 'wf.flow_definition',
                'menu_sort' => 10,
            ],
        ];

        $menuIds = [(string) $parent->id];
        foreach ($children as $child) {
            $exists = AuthMenu::query()->where('permission_code', $child['permission_code'])->first();
            if ($exists) {
                $menuIds[] = (string) $exists->id;
                continue;
            }
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
            $menuIds[] = (string) $menu->id;
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
