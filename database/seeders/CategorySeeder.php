<?php

namespace Database\Seeders;

use App\Enums\CategoryLevel;
use App\Enums\CategoryShowType;
use App\Enums\CategoryStatus;
use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        if (Category::query()->exists()) {
            return;
        }

        $parent = Category::query()->create([
            'category_name' => '新闻资讯',
            'parent_id' => 0,
            'category_type' => CategoryType::Content,
            'show_type' => CategoryShowType::All,
            'cat_status' => CategoryStatus::Visible,
            'level' => CategoryLevel::Level1,
            'sort_order' => 4,
            'description' => '',
            'cat_remark' => '',
        ]);

        Category::query()->create([
            'category_name' => '公司动态',
            'parent_id' => $parent->id,
            'category_type' => CategoryType::Content,
            'show_type' => CategoryShowType::All,
            'cat_status' => CategoryStatus::Visible,
            'level' => CategoryLevel::Level2,
            'sort_order' => 12,
        ]);
        Category::query()->create([
            'category_name' => '项目动态',
            'parent_id' => $parent->id,
            'category_type' => CategoryType::Content,
            'show_type' => CategoryShowType::All,
            'cat_status' => CategoryStatus::Visible,
            'level' => CategoryLevel::Level2,
            'sort_order' => 1,
        ]);
        Category::query()->create([
            'category_name' => '行业新闻',
            'parent_id' => $parent->id,
            'category_type' => CategoryType::Content,
            'show_type' => CategoryShowType::All,
            'cat_status' => CategoryStatus::Visible,
            'level' => CategoryLevel::Level2,
            'sort_order' => 0,
        ]);
    }
}
