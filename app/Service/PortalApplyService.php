<?php

namespace App\Service;

use App\Constants\Code\BookMarkError;
use App\Constants\Code\CodePrefix;
use App\Enums\BookMarkBold;
use App\Enums\BookMarkStatus;
use App\Enums\CategoryStatus;
use App\Enums\CategoryType;
use App\Exceptions\BusinessException;
use App\Models\BookMark;
use App\Models\Category;
use App\Models\SiteConfig;
use Illuminate\Support\Facades\Http;

class PortalApplyService
{
    public function formMeta(): array
    {
        $site = $this->siteConfig();

        return [
            'site' => $site,
            'logo' => '/uploads/logo/budff_logo.png',
            'categories' => $this->portalCategoryOptions(),
            'requirements_html' => $this->requirementsHtml($site),
            'view_count' => (int) BookMark::query()->sum('click_count') + 17233,
            'published_at' => '2026-08-22',
        ];
    }

    public function create(array $data): BookMark
    {
        $url = trim((string) ($data['site_url'] ?? ''));
        $this->assertUrl($url);

        $categoryId = (int) ($data['category_id'] ?? 0);
        $this->assertPortalCategory($categoryId);

        $exists = BookMark::query()
            ->where('book_url', $url)
            ->whereIn('status', [BookMarkStatus::Hidden, BookMarkStatus::Normal])
            ->exists();
        if ($exists) {
            throw new BusinessException($this->code(BookMarkError::URL_INVALID), '该网址已提交过，请勿重复申请');
        }

        $shortTitle = mb_substr((string) ($data['site_tag'] ?? ''), 0, 32);
        $bookTitle = (string) ($data['site_subtitle'] ?? '');
        if ($bookTitle === '') {
            $bookTitle = $shortTitle;
        }

        return BookMark::query()->create([
            'category_id' => $categoryId,
            'short_title' => $shortTitle,
            'book_title' => mb_substr($bookTitle, 0, 128),
            'book_url' => $url,
            'book_favicon' => (string) ($data['site_favicon'] ?? ''),
            'book_desc' => mb_substr((string) ($data['site_intro'] ?? ''), 0, 1024),
            'click_count' => 0,
            'sort_order' => 0,
            // 前台申请默认隐藏，后台审核改为正常后展示
            'status' => BookMarkStatus::Hidden,
            'is_bold' => BookMarkBold::Bold,
            'created_by' => 0,
        ]);
    }

    public function fetchTkd(string $url): array
    {
        $this->assertUrl($url);

        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; BudffBot/1.0)',
                    'Accept' => 'text/html,application/xhtml+xml',
                ])
                ->get($url);

            if (! $response->successful()) {
                throw new BusinessException($this->code(BookMarkError::URL_INVALID), '无法访问该网址，请检查地址');
            }

            $html = $response->body();
            $title = $this->matchMeta($html, 'title') ?: $this->matchTitleTag($html);
            $keywords = $this->matchMeta($html, 'keywords');
            $description = $this->matchMeta($html, 'description');
            $favicon = $this->matchFavicon($html, $url);
            $host = parse_url($url, PHP_URL_HOST) ?: '';
            $tag = $title !== '' ? $title : $host;

            return [
                'site_tag' => mb_substr($tag, 0, 32),
                'site_subtitle' => mb_substr($description !== '' ? $description : $title, 0, 128),
                'site_favicon' => mb_substr($favicon, 0, 512),
                'site_intro' => mb_substr($description, 0, 1024),
                'meta_title' => mb_substr($title, 0, 256),
                'meta_keywords' => mb_substr($keywords, 0, 512),
                'meta_description' => mb_substr($description, 0, 1024),
            ];
        } catch (BusinessException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            throw new BusinessException($this->code(BookMarkError::URL_INVALID), '获取 TKD 失败，请手动填写');
        }
    }

    private function portalCategoryOptions(): array
    {
        return Category::query()
            ->where('category_type', CategoryType::Portal)
            ->where('cat_status', CategoryStatus::Visible)
            ->orderByDesc('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Category $c) => [
                'id' => (string) $c->id,
                'category_name' => $c->category_name,
                'parent_id' => (string) $c->parent_id,
                'level' => $c->level?->value,
            ])
            ->values()
            ->all();
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
            'site_url' => 'http://www.budff.com',
            'email' => 'githup@163.com',
        ];

        return array_merge($defaults, array_filter($map, fn ($v) => $v !== null && $v !== ''));
    }

    private function requirementsHtml(array $site): string
    {
        $name = e($site['site_name'] ?? '帮扶导航');
        $url = e($site['site_url'] ?? 'http://www.budff.com');
        $email = e($site['email'] ?? 'githup@163.com');

        return <<<HTML
<p><strong>收录要求：</strong></p>
<ul>
  <li>网站内容合法合规，不含赌博、色情、诈骗等违法违规信息。</li>
  <li>站点需稳定可访问，具备基本备案与安全措施。</li>
  <li>申请前请先添加本站友链，审核通过后将优先展示。</li>
  <li>本站名称：<strong>{$name}</strong>；网址：<a href="{$url}" target="_blank" rel="noopener">{$url}</a></li>
  <li>联系邮箱：{$email}</li>
  <li>同类站点按综合质量与点击排序，请勿重复提交。</li>
</ul>
HTML;
    }

    private function assertUrl(string $url): void
    {
        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL) || ! preg_match('#^https?://#i', $url)) {
            throw new BusinessException($this->code(BookMarkError::URL_INVALID), '请填写带 http:// 或 https:// 的有效网址');
        }
    }

    private function assertPortalCategory(int $categoryId): void
    {
        $ok = Category::query()
            ->where('id', $categoryId)
            ->where('category_type', CategoryType::Portal)
            ->where('cat_status', CategoryStatus::Visible)
            ->exists();
        if (! $ok) {
            throw new BusinessException($this->code(BookMarkError::CATEGORY_INVALID), '请选择有效的所属分类');
        }
    }

    private function matchTitleTag(string $html): string
    {
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $m)) {
            return html_entity_decode(trim(strip_tags($m[1])), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return '';
    }

    private function matchMeta(string $html, string $name): string
    {
        $pattern = '/<meta[^>]+(?:name|property)=["\'](?:og:)?' . preg_quote($name, '/') . '["\'][^>]+content=["\'](.*?)["\']/is';
        if (preg_match($pattern, $html, $m)) {
            return html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        $pattern2 = '/<meta[^>]+content=["\'](.*?)["\'][^>]+(?:name|property)=["\'](?:og:)?' . preg_quote($name, '/') . '["\']/is';
        if (preg_match($pattern2, $html, $m)) {
            return html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return '';
    }

    private function matchFavicon(string $html, string $baseUrl): string
    {
        if (preg_match('/<link[^>]+rel=["\'][^"\']*icon[^"\']*["\'][^>]+href=["\'](.*?)["\']/is', $html, $m)
            || preg_match('/<link[^>]+href=["\'](.*?)["\'][^>]+rel=["\'][^"\']*icon[^"\']*["\']/is', $html, $m)) {
            $href = trim($m[1]);
            if (str_starts_with($href, '//')) {
                return 'https:'.$href;
            }
            if (preg_match('#^https?://#i', $href)) {
                return $href;
            }
            $parts = parse_url($baseUrl);
            $origin = ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? '');
            if (str_starts_with($href, '/')) {
                return $origin.$href;
            }

            return rtrim($origin, '/').'/'.$href;
        }

        $parts = parse_url($baseUrl);

        return (($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? '')).'/favicon.ico';
    }

    private function code(int $error): int
    {
        return CodePrefix::BOOK_MARK * 1000 + $error;
    }
}
