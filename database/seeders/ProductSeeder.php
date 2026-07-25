<?php

namespace Database\Seeders;

use App\Enums\CategoryLevel;
use App\Enums\MenuStatus;
use App\Enums\ProductIsSystem;
use App\Enums\ProductShowStatus;
use App\Models\AuthMenu;
use App\Models\AuthRole;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductSpecification;
use App\Models\ProductSpecificationValue;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBrands();
        $this->seedCategories();
        $this->seedSpecifications();
        $this->seedMenus();
    }

    private function seedBrands(): void
    {
        if (ProductBrand::query()->exists()) {
            return;
        }

        ProductBrand::query()->create([
            'brand_code' => 'BR000001',
            'brand_name' => '自有品牌',
            'alias' => 'Own',
            'is_system' => ProductIsSystem::System,
            'is_show' => ProductShowStatus::Visible,
            'sort_order' => 100,
            'brand_remark' => '系统预设品牌',
        ]);

        ProductBrand::query()->create([
            'brand_code' => 'BR000002',
            'brand_name' => '示例品牌',
            'alias' => 'Demo',
            'is_system' => ProductIsSystem::Custom,
            'is_show' => ProductShowStatus::Visible,
            'sort_order' => 90,
        ]);
    }

    private function seedCategories(): void
    {
        if (ProductCategory::query()->exists()) {
            return;
        }

        $root = ProductCategory::query()->create([
            'category_code' => 'FL000001',
            'category_name' => '家具',
            'parent_id' => 0,
            'level' => CategoryLevel::Level1,
            'unit' => '件',
            'cat_status' => ProductShowStatus::Visible,
            'sort_order' => 100,
        ]);

        ProductCategory::query()->create([
            'category_code' => 'FL000002',
            'category_name' => '沙发',
            'parent_id' => $root->id,
            'level' => CategoryLevel::Level2,
            'unit' => '件',
            'cat_status' => ProductShowStatus::Visible,
            'sort_order' => 20,
        ]);

        ProductCategory::query()->create([
            'category_code' => 'FL000003',
            'category_name' => '桌椅',
            'parent_id' => $root->id,
            'level' => CategoryLevel::Level2,
            'unit' => '套',
            'cat_status' => ProductShowStatus::Visible,
            'sort_order' => 10,
        ]);
    }

    private function seedSpecifications(): void
    {
        if (ProductSpecification::query()->exists()) {
            return;
        }

        $color = ProductSpecification::query()->create([
            'spec_code' => 'GL000001',
            'spec_name' => '颜色',
            'spec_status' => ProductShowStatus::Visible,
            'sort_order' => 100,
        ]);

        foreach ([['红色', 'GV000001'], ['黑色', 'GV000002'], ['白色', 'GV000003']] as $i => [$name, $code]) {
            ProductSpecificationValue::query()->create([
                'spec_id' => $color->id,
                'value_code' => $code,
                'value' => $name,
                'sort_order' => 30 - $i * 10,
                'value_status' => ProductShowStatus::Visible,
            ]);
        }

        $material = ProductSpecification::query()->create([
            'spec_code' => 'GL000002',
            'spec_name' => '材质',
            'spec_status' => ProductShowStatus::Visible,
            'sort_order' => 90,
        ]);

        ProductSpecificationValue::query()->create([
            'spec_id' => $material->id,
            'value_code' => 'GV000004',
            'value' => '实木',
            'sort_order' => 20,
            'value_status' => ProductShowStatus::Visible,
        ]);
        ProductSpecificationValue::query()->create([
            'spec_id' => $material->id,
            'value_code' => 'GV000005',
            'value' => '布艺',
            'sort_order' => 10,
            'value_status' => ProductShowStatus::Visible,
        ]);
    }

    private function seedMenus(): void
    {
        $parent = AuthMenu::query()->where('permission_code', 'products')->first();
        if (! $parent) {
            $parent = AuthMenu::query()->create([
                'parent_id' => 0,
                'menu_name' => '产品',
                'menu_icon' => 'Goods',
                'menu_path' => '',
                'component' => '',
                'permission_code' => 'products',
                'menu_sort' => 40,
                'menu_status' => MenuStatus::Enabled,
            ]);
        }

        $children = [
            [
                'menu_name' => '商品管理',
                'menu_path' => '/backend/product/products',
                'component' => 'product/Index',
                'permission_code' => 'product.product',
                'menu_sort' => 50,
            ],
            [
                'menu_name' => '品牌管理',
                'menu_path' => '/backend/product/brands',
                'component' => 'product/Brand',
                'permission_code' => 'product.brand',
                'menu_sort' => 40,
            ],
            [
                'menu_name' => '商品分类',
                'menu_path' => '/backend/product/categories',
                'component' => 'product/Category',
                'permission_code' => 'product.category',
                'menu_sort' => 30,
            ],
            [
                'menu_name' => '规格管理',
                'menu_path' => '/backend/product/specifications',
                'component' => 'product/Specification',
                'permission_code' => 'product.spec',
                'menu_sort' => 20,
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
