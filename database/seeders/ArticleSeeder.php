<?php

namespace Database\Seeders;

use App\Enums\ArticleCategoryStatus;
use App\Enums\ArticleContentType;
use App\Enums\ArticleFlag;
use App\Enums\ArticleStatus;
use App\Enums\MenuStatus;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\AuthMenu;
use App\Models\AuthRole;
use App\Models\UserAccount;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedMenus();
        $this->seedCategoriesAndArticles();
    }

    private function seedMenus(): void
    {
        $parent = AuthMenu::query()->firstOrCreate(
            ['permission_code' => 'news'],
            [
                'parent_id' => 0,
                'menu_name' => '新闻',
                'menu_icon' => 'Document',
                'menu_path' => '',
                'component' => '',
                'menu_sort' => 60,
                'menu_status' => MenuStatus::Enabled,
            ]
        );

        $articleMenu = AuthMenu::query()->updateOrCreate(
            ['permission_code' => 'news.article'],
            [
                'parent_id' => $parent->id,
                'menu_name' => '文章管理',
                'menu_icon' => '',
                'menu_path' => '/backend/news/articles',
                'component' => 'news/ArticleIndex',
                'menu_sort' => 20,
                'menu_status' => MenuStatus::Enabled,
            ]
        );

        $categoryMenu = AuthMenu::query()->updateOrCreate(
            ['permission_code' => 'news.category'],
            [
                'parent_id' => $parent->id,
                'menu_name' => '分类管理',
                'menu_icon' => '',
                'menu_path' => '/backend/news/categories',
                'component' => 'news/Category',
                'menu_sort' => 10,
                'menu_status' => MenuStatus::Enabled,
            ]
        );

        $roles = AuthRole::query()->whereIn('role_code', ['super_admin', 'admin'])->get();
        $now = now();
        foreach ($roles as $role) {
            $role->menus()->syncWithoutDetaching([
                $parent->id => ['created_at' => $now],
                $articleMenu->id => ['created_at' => $now],
                $categoryMenu->id => ['created_at' => $now],
            ]);
        }
    }

    private function seedCategoriesAndArticles(): void
    {
        if (ArticleCategory::query()->exists()) {
            return;
        }

        $news = ArticleCategory::query()->create([
            'parent_id' => 0,
            'cat_name' => '新闻资讯',
            'cat_url' => 'news',
            'description' => '公司新闻与行业资讯',
            'cat_sort' => 100,
            'status' => ArticleCategoryStatus::Enabled,
        ]);

        $company = ArticleCategory::query()->create([
            'parent_id' => $news->id,
            'cat_name' => '公司动态',
            'cat_url' => 'company-news',
            'description' => '公司内部动态',
            'cat_sort' => 90,
            'status' => ArticleCategoryStatus::Enabled,
        ]);

        ArticleCategory::query()->create([
            'parent_id' => $news->id,
            'cat_name' => '行业观察',
            'cat_url' => 'industry',
            'description' => '行业分析与观察',
            'cat_sort' => 80,
            'status' => ArticleCategoryStatus::Enabled,
        ]);

        $admin = UserAccount::query()->where('user_name', 'admin')->first();

        Article::query()->create([
            'title' => '欢迎使用文章模块',
            'subtitle' => '系统演示文章',
            'art_cover' => '',
            'art_content' => '<p>这是一篇演示文章，可在后台新闻模块中编辑与发布。</p>',
            'content_type' => ArticleContentType::RichText,
            'summary' => '这是一篇演示文章，可在后台新闻模块中编辑与发布。',
            'category_id' => $company->id,
            'tag_ids' => [],
            'author_id' => $admin?->id ?? 0,
            'author_name' => $admin?->nick_name ?: ($admin?->user_name ?: '管理员'),
            'source' => '原创',
            'source_url' => '',
            'art_status' => ArticleStatus::Published,
            'is_top' => ArticleFlag::Yes,
            'is_original' => ArticleFlag::Yes,
            'is_commentable' => ArticleFlag::Yes,
            'seo_title' => '欢迎使用文章模块',
            'seo_keywords' => '文章,演示',
            'seo_description' => '系统演示文章',
            'published_at' => now(),
        ]);
    }
}
