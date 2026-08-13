<?php

namespace App\Service;

use App\Enums\BookMarkStatus;
use App\Enums\CategoryStatus;
use App\Enums\CategoryType;
use App\Enums\FriendLinkStatus;
use App\Models\BookMark;
use App\Models\Category;
use App\Models\FriendLink;
use App\Models\SiteConfig;

class PortalService
{
    public function index(?string $channelId = null): array
    {
        $site = $this->siteConfig();
        $nav = $this->navChannels();
        $sections = $this->sections($channelId);

        return [
            'site' => $site,
            'logo' => '/uploads/logo/budff_logo.png',
            'nav' => $nav,
            'sections' => $sections,
            'friend_links' => $this->friendLinks(),
            'channel_id' => $channelId ? (string) $channelId : '',
        ];
    }

    private function siteConfig(): array
    {
        $map = SiteConfig::query()
            ->orderBy('conf_sort')
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn (SiteConfig $item) => [$item->conf_key => $item->conf_value])
            ->all();

        $defaults = [
            'site_name' => '帮扶导航',
            'site_title' => '帮扶导航 — 优质资源导航站',
            'site_keywords' => '导航,云盘,工具,资源',
            'site_description' => '精选优质网站与云盘资源导航',
            'email' => 'githup@163.com',
            'icp' => '',
            'copyright' => 'Copyright © 2022 - 2026 帮扶导航 All Rights Reserved 粤ICP备2026110578号',
        ];

        return array_merge($defaults, array_filter($map, fn ($v) => $v !== null && $v !== ''));
    }

    /**
     * 顶级导航频道（category_type=2, parent_id=0）
     */
    private function navChannels(): array
    {
        return Category::query()
            ->where('category_type', CategoryType::Portal)
            ->where('parent_id', 0)
            ->where('cat_status', CategoryStatus::Visible)
            ->orderByDesc('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Category $c) => [
                'id' => (string) $c->id,
                'category_name' => $c->category_name,
                'description' => $c->description,
                'children' => Category::query()
                    ->where('parent_id', $c->id)
                    ->where('category_type', CategoryType::Portal)
                    ->where('cat_status', CategoryStatus::Visible)
                    ->orderByDesc('sort_order')
                    ->orderBy('id')
                    ->get()
                    ->map(fn (Category $child) => [
                        'id' => (string) $child->id,
                        'category_name' => $child->category_name,
                    ])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * 内容区块：有书签的导航分类（可按频道过滤）
     */
    private function sections(?string $channelId): array
    {
        $categoryQuery = Category::query()
            ->where('category_type', CategoryType::Portal)
            ->where('cat_status', CategoryStatus::Visible)
            ->orderByDesc('sort_order')
            ->orderBy('id');

        if ($channelId) {
            $channel = Category::query()
                ->where('id', $channelId)
                ->where('category_type', CategoryType::Portal)
                ->first();
            if (! $channel) {
                return [];
            }
            $ids = $this->collectSelfAndDescendantIds($channel);
            $categoryQuery->whereIn('id', $ids);
        }

        $categories = $categoryQuery->get();
        $categoryIds = $categories->pluck('id')->all();
        if ($categoryIds === []) {
            return [];
        }

        $bookmarks = BookMark::query()
            ->whereIn('category_id', $categoryIds)
            ->where('status', BookMarkStatus::Normal)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->groupBy(fn (BookMark $b) => (string) $b->category_id);

        $sections = [];
        foreach ($categories as $category) {
            $items = $bookmarks->get((string) $category->id, collect());
            if ($items->isEmpty()) {
                continue;
            }
            $sections[] = [
                'id' => (string) $category->id,
                'category_name' => $category->category_name,
                'description' => $category->description,
                'layout' => $this->resolveLayout($category->category_name),
                'bookmarks' => $items->map(fn (BookMark $b) => $this->bookmarkToArray($b))->values()->all(),
            ];
        }

        return $sections;
    }

    private function resolveLayout(string $name): string
    {
        if (str_contains($name, '友链') || str_contains($name, '友情链接')) {
            return 'text';
        }

        return 'card';
    }

    private function bookmarkToArray(BookMark $b): array
    {
        return [
            'id' => (string) $b->id,
            'short_title' => $b->short_title,
            'book_title' => $b->book_title,
            'book_url' => $b->book_url,
            'book_favicon' => $b->book_favicon,
            'book_desc' => $b->book_desc,
            'is_bold' => $b->is_bold?->value,
        ];
    }

    private function friendLinks(): array
    {
        return FriendLink::query()
            ->where('link_status', FriendLinkStatus::Enabled)
            ->orderBy('link_sort')
            ->orderBy('id')
            ->limit(30)
            ->get()
            ->map(fn (FriendLink $link) => [
                'id' => (string) $link->id,
                'link_name' => $link->link_name,
                'link_url' => $link->link_url,
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<int|string>
     */
    private function collectSelfAndDescendantIds(Category $category): array
    {
        $ids = [$category->id];
        $children = Category::query()->where('parent_id', $category->id)->get();
        foreach ($children as $child) {
            $ids = array_merge($ids, $this->collectSelfAndDescendantIds($child));
        }

        return $ids;
    }
}
