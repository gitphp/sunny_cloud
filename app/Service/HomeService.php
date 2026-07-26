<?php

namespace App\Service;

use App\Enums\AdStatus;
use App\Enums\ArticleStatus;
use App\Enums\BossJobStatus;
use App\Enums\FriendLinkStatus;
use App\Enums\ProductShowStatus;
use App\Enums\ProductStatus;
use App\Models\AdPosition;
use App\Models\Article;
use App\Models\BossJob;
use App\Models\FriendLink;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SiteConfig;
use Illuminate\Support\Carbon;

class HomeService
{
    public function index(): array
    {
        $site = $this->siteConfig();

        return [
            'site' => $site,
            'banners' => $this->banners(),
            'products' => $this->products(6),
            'categories' => $this->categories(8),
            'articles' => $this->articles(6),
            'jobs' => $this->jobs(6),
            'friend_links' => $this->friendLinks(12),
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
            'site_name' => '名扬科技',
            'site_title' => '深圳市名扬科技 — 企业数字化与智能云服务',
            'site_keywords' => '名扬科技,深圳软件,数字化转型,企业云,AI平台',
            'site_description' => '深圳市名扬科技专注企业数字化建设，提供云平台、智能应用与行业解决方案，助力企业稳健增长。',
            'phone' => '13026661119',
            'email' => 'githup@163.com',
            'address' => '深圳市龙岗区科技园南区科苑路33号9908',
            'wechat' => 'mingyang_tech',
            'weibo' => '',
            'company_full_name' => '深圳市名扬科技有限公司',
        ];

        return array_merge($defaults, array_filter($map, fn ($v) => $v !== null && $v !== ''));
    }

    private function banners(): array
    {
        $now = Carbon::now();

        return AdPosition::query()
            ->where('position_code', 'home_banner_top')
            ->where('status', AdStatus::Running)
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->orderByDesc('sort')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(fn (AdPosition $ad) => [
                'id' => (string) $ad->id,
                'ad_title' => $ad->ad_title,
                'subtitle' => $ad->subtitle,
                'cover_url' => $ad->cover_url,
                'cover_mobile' => $ad->cover_mobile ?: $ad->cover_url,
                'link_type' => $ad->link_type?->value,
                'link_url' => $ad->link_url,
                'sort' => (int) $ad->sort,
            ])
            ->values()
            ->all();
    }

    private function products(int $limit): array
    {
        return Product::query()
            ->with(['brand:id,brand_name', 'category:id,category_name'])
            ->where('product_status', ProductStatus::OnShelf)
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn (Product $item) => [
                'id' => (string) $item->id,
                'product_name' => $item->product_name,
                'short_desc' => $item->short_desc,
                'main_image_url' => $item->main_image_url,
                'brand_name' => $item->brand?->brand_name ?? '',
                'category_name' => $item->category?->category_name ?? '',
            ])
            ->values()
            ->all();
    }

    private function categories(int $limit): array
    {
        return ProductCategory::query()
            ->where('cat_status', ProductShowStatus::Visible)
            ->where('parent_id', 0)
            ->orderByDesc('sort_order')
            ->orderBy('id')
            ->limit($limit)
            ->get(['id', 'category_name', 'product_count'])
            ->map(fn (ProductCategory $item) => [
                'id' => (string) $item->id,
                'category_name' => $item->category_name,
                'product_count' => (int) $item->product_count,
            ])
            ->values()
            ->all();
    }

    private function articles(int $limit): array
    {
        return Article::query()
            ->where('art_status', ArticleStatus::Published)
            ->orderByDesc('is_top')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn (Article $item) => [
                'id' => (string) $item->id,
                'title' => $item->title,
                'summary' => $item->summary,
                'art_cover' => $item->art_cover,
                'is_top' => (int) ($item->is_top?->value ?? $item->is_top ?? 0),
                'view_count' => (int) $item->view_count,
                'published_at' => optional($item->published_at)?->format('Y-m-d'),
            ])
            ->values()
            ->all();
    }

    private function jobs(int $limit): array
    {
        return BossJob::query()
            ->where('job_status', BossJobStatus::Published)
            ->orderByDesc('is_hot')
            ->orderByDesc('job_sort')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn (BossJob $item) => [
                'id' => (string) $item->id,
                'job_title' => $item->job_title,
                'department' => $item->department,
                'workplace' => $item->workplace,
                'salary_range' => $item->salary_range,
                'experience' => $item->experience,
                'education' => $item->education,
                'is_hot' => (int) ($item->is_hot?->value ?? $item->is_hot ?? 0),
            ])
            ->values()
            ->all();
    }

    private function friendLinks(int $limit): array
    {
        return FriendLink::query()
            ->where('link_status', FriendLinkStatus::Enabled)
            ->orderBy('link_sort')
            ->orderBy('id')
            ->limit($limit)
            ->get()
            ->map(fn (FriendLink $item) => [
                'id' => (string) $item->id,
                'link_name' => $item->link_name,
                'link_url' => $item->link_url,
                'link_logo' => $item->link_logo,
            ])
            ->values()
            ->all();
    }
}
