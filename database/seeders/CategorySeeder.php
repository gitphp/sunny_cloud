<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $parent = Category::query()->create([
            'name' => '新闻资讯',
            'parent_id' => 0,
            'sort' => 4,
        ]);

        // 对齐截图示例数据的业务语义（ID 由数据库自增）
        Category::query()->create([
            'name' => '公司动态',
            'parent_id' => $parent->id,
            'sort' => 12,
        ]);
        Category::query()->create([
            'name' => '项目动态',
            'parent_id' => $parent->id,
            'sort' => 1,
        ]);
        Category::query()->create([
            'name' => '行业新闻',
            'parent_id' => $parent->id,
            'sort' => 0,
        ]);
    }
}
