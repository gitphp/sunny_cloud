<?php

namespace Database\Seeders;

use App\Enums\MenuStatus;
use App\Models\AuthMenu;
use Illuminate\Database\Seeder;

class AuthMenuSeeder extends Seeder
{
    public function run(): void
    {
        if (AuthMenu::query()->exists()) {
            return;
        }

        $menus = [
            [
                'menu_name' => '控制台',
                'menu_icon' => 'Odometer',
                'menu_path' => '/backend/dashboard',
                'component' => 'Dashboard',
                'permission_code' => 'dashboard.view',
                'menu_sort' => 100,
                'children' => [],
            ],
            [
                'menu_name' => '权限菜单分类管理',
                'menu_icon' => 'Lock',
                'menu_path' => '',
                'component' => '',
                'permission_code' => 'permission',
                'menu_sort' => 90,
                'children' => [
                    [
                        'menu_name' => '权限',
                        'menu_icon' => '',
                        'menu_path' => '/backend/permissions',
                        'component' => 'Placeholder',
                        'permission_code' => 'permission.view',
                        'menu_sort' => 20,
                    ],
                    [
                        'menu_name' => '角色管理',
                        'menu_icon' => '',
                        'menu_path' => '/backend/roles',
                        'component' => 'roles/Index',
                        'permission_code' => 'role.view',
                        'menu_sort' => 15,
                    ],
                    [
                        'menu_name' => '菜单管理',
                        'menu_icon' => '',
                        'menu_path' => '/backend/menus',
                        'component' => 'menus/Index',
                        'permission_code' => 'menu.view',
                        'menu_sort' => 10,
                    ],
                ],
            ],
            [
                'menu_name' => '系统设置',
                'menu_icon' => 'Setting',
                'menu_path' => '',
                'component' => '',
                'permission_code' => 'system',
                'menu_sort' => 80,
                'children' => [
                    [
                        'menu_name' => '网站设置',
                        'menu_icon' => '',
                        'menu_path' => '/backend/system/settings',
                        'component' => 'Placeholder',
                        'permission_code' => 'system.settings',
                        'menu_sort' => 10,
                    ],
                ],
            ],
            [
                'menu_name' => '用户管理',
                'menu_icon' => 'User',
                'menu_path' => '',
                'component' => '',
                'permission_code' => 'user',
                'menu_sort' => 70,
                'children' => [
                    [
                        'menu_name' => '用户管理',
                        'menu_icon' => '',
                        'menu_path' => '/backend/users',
                        'component' => 'users/Index',
                        'permission_code' => 'user.view',
                        'menu_sort' => 10,
                    ],
                ],
            ],
            [
                'menu_name' => '新闻',
                'menu_icon' => 'Document',
                'menu_path' => '',
                'component' => '',
                'permission_code' => 'news',
                'menu_sort' => 60,
                'children' => [
                    [
                        'menu_name' => '文章管理',
                        'menu_icon' => '',
                        'menu_path' => '/backend/news/articles',
                        'component' => 'Placeholder',
                        'permission_code' => 'news.article',
                        'menu_sort' => 20,
                    ],
                    [
                        'menu_name' => '分类管理',
                        'menu_icon' => '',
                        'menu_path' => '/backend/news/categories',
                        'component' => 'news/Category',
                        'permission_code' => 'news.category',
                        'menu_sort' => 10,
                    ],
                ],
            ],
            [
                'menu_name' => '案例',
                'menu_icon' => 'Collection',
                'menu_path' => '',
                'component' => '',
                'permission_code' => 'cases',
                'menu_sort' => 50,
                'children' => [
                    [
                        'menu_name' => '案例管理',
                        'menu_icon' => '',
                        'menu_path' => '/backend/cases',
                        'component' => 'Placeholder',
                        'permission_code' => 'cases.view',
                        'menu_sort' => 10,
                    ],
                ],
            ],
            [
                'menu_name' => '产品',
                'menu_icon' => 'Goods',
                'menu_path' => '',
                'component' => '',
                'permission_code' => 'products',
                'menu_sort' => 40,
                'children' => [
                    [
                        'menu_name' => '产品管理',
                        'menu_icon' => '',
                        'menu_path' => '/backend/products',
                        'component' => 'Placeholder',
                        'permission_code' => 'products.view',
                        'menu_sort' => 10,
                    ],
                ],
            ],
            [
                'menu_name' => '关于',
                'menu_icon' => 'InfoFilled',
                'menu_path' => '',
                'component' => '',
                'permission_code' => 'about',
                'menu_sort' => 30,
                'children' => [
                    [
                        'menu_name' => '关于我们',
                        'menu_icon' => '',
                        'menu_path' => '/backend/about',
                        'component' => 'Placeholder',
                        'permission_code' => 'about.view',
                        'menu_sort' => 10,
                    ],
                ],
            ],
            [
                'menu_name' => '服务',
                'menu_icon' => 'Headset',
                'menu_path' => '',
                'component' => '',
                'permission_code' => 'services',
                'menu_sort' => 20,
                'children' => [
                    [
                        'menu_name' => '服务管理',
                        'menu_icon' => '',
                        'menu_path' => '/backend/services',
                        'component' => 'Placeholder',
                        'permission_code' => 'services.view',
                        'menu_sort' => 10,
                    ],
                ],
            ],
            [
                'menu_name' => '其它功能',
                'menu_icon' => 'More',
                'menu_path' => '',
                'component' => '',
                'permission_code' => 'others',
                'menu_sort' => 10,
                'children' => [
                    [
                        'menu_name' => '其它',
                        'menu_icon' => '',
                        'menu_path' => '/backend/others',
                        'component' => 'Placeholder',
                        'permission_code' => 'others.view',
                        'menu_sort' => 10,
                    ],
                ],
            ],
            [
                'menu_name' => '文件管理',
                'menu_icon' => 'Folder',
                'menu_path' => '/backend/files',
                'component' => 'Placeholder',
                'permission_code' => 'files.view',
                'menu_sort' => 5,
                'children' => [],
            ],
        ];

        foreach ($menus as $item) {
            $this->createMenu($item, 0);
        }
    }

    private function createMenu(array $item, int $parentId): AuthMenu
    {
        $menu = AuthMenu::query()->create([
            'parent_id' => $parentId,
            'menu_name' => $item['menu_name'],
            'menu_icon' => $item['menu_icon'] ?? '',
            'menu_path' => $item['menu_path'] ?? '',
            'component' => $item['component'] ?? '',
            'permission_code' => $item['permission_code'] ?? '',
            'menu_sort' => $item['menu_sort'] ?? 0,
            'menu_status' => MenuStatus::Enabled,
        ]);

        foreach ($item['children'] ?? [] as $child) {
            $this->createMenu($child, (int) $menu->id);
        }

        return $menu;
    }
}
