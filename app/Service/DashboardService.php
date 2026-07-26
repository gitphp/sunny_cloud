<?php

namespace App\Service;

use App\Enums\AdAuditStatus;
use App\Enums\AdStatus;
use App\Enums\ArticleStatus;
use App\Enums\BossJobStatus;
use App\Enums\FeedbackStatus;
use App\Enums\OperationAction;
use App\Enums\OperationBizType;
use App\Enums\ProductStatus;
use App\Enums\UserStatus;
use App\Enums\WfApplyStatus;
use App\Models\AdPosition;
use App\Models\AdSlot;
use App\Models\Article;
use App\Models\BookMark;
use App\Models\BossJob;
use App\Models\Feedback;
use App\Models\FriendLink;
use App\Models\HrDepartment;
use App\Models\HrPost;
use App\Models\OperationLog;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\UserAccount;
use App\Models\WfFlowApply;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class DashboardService
{
    public function overview(): array
    {
        return [
            'summary' => $this->summary(),
            'todos' => $this->todos(),
            'trends' => $this->trends(7),
            'recent_logs' => $this->recentLogs(8),
            'recent_feedbacks' => $this->recentFeedbacks(5),
            'recent_articles' => $this->recentArticles(5),
            'ad_metrics' => $this->adMetrics(),
            'quick_links' => $this->quickLinks(),
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    private function summary(): array
    {
        $usersTotal = UserAccount::query()->count();
        $usersNormal = UserAccount::query()->where('user_status', UserStatus::Normal)->count();

        $productsTotal = Product::query()->count();
        $productsOn = Product::query()->where('product_status', ProductStatus::OnShelf)->count();

        $articlesTotal = Article::query()->count();
        $articlesPublished = Article::query()->where('art_status', ArticleStatus::Published)->count();

        $feedbacksTotal = Feedback::query()->count();
        $feedbacksPending = Feedback::query()->where('fb_status', FeedbackStatus::Pending)->count();

        $jobsPublished = BossJob::query()->where('job_status', BossJobStatus::Published)->count();
        $jobsHot = BossJob::query()->where('is_hot', 1)->where('job_status', BossJobStatus::Published)->count();

        $adsRunning = AdPosition::query()->where('status', AdStatus::Running)->count();
        $adsPending = AdPosition::query()->where('audit_status', AdAuditStatus::Pending)->count();

        $wfPending = WfFlowApply::query()->where('apply_status', WfApplyStatus::Pending)->count();

        $todayStart = Carbon::today();
        $logsToday = OperationLog::query()->where('created_at', '>=', $todayStart)->count();

        return [
            [
                'key' => 'users',
                'label' => '用户',
                'value' => $usersTotal,
                'sub_label' => '正常',
                'sub_value' => $usersNormal,
                'path' => '/backend/users',
                'tone' => 'teal',
            ],
            [
                'key' => 'products',
                'label' => '商品',
                'value' => $productsTotal,
                'sub_label' => '上架',
                'sub_value' => $productsOn,
                'path' => '/backend/product/products',
                'tone' => 'blue',
            ],
            [
                'key' => 'articles',
                'label' => '文章',
                'value' => $articlesTotal,
                'sub_label' => '已发布',
                'sub_value' => $articlesPublished,
                'path' => '/backend/news/articles',
                'tone' => 'green',
            ],
            [
                'key' => 'feedbacks',
                'label' => '留言',
                'value' => $feedbacksTotal,
                'sub_label' => '待处理',
                'sub_value' => $feedbacksPending,
                'path' => '/backend/feedbacks',
                'tone' => 'orange',
            ],
            [
                'key' => 'jobs',
                'label' => '招聘职位',
                'value' => $jobsPublished,
                'sub_label' => '急聘',
                'sub_value' => $jobsHot,
                'path' => '/backend/boss-jobs',
                'tone' => 'cyan',
            ],
            [
                'key' => 'ads',
                'label' => '投放中广告',
                'value' => $adsRunning,
                'sub_label' => '待审核',
                'sub_value' => $adsPending,
                'path' => '/backend/ad-positions',
                'tone' => 'indigo',
            ],
            [
                'key' => 'workflow',
                'label' => '审批中',
                'value' => $wfPending,
                'sub_label' => '申请单',
                'sub_value' => WfFlowApply::query()->count(),
                'path' => '/backend/wf/todo',
                'tone' => 'amber',
            ],
            [
                'key' => 'logs',
                'label' => '今日操作',
                'value' => $logsToday,
                'sub_label' => '日志总量',
                'sub_value' => OperationLog::query()->count(),
                'path' => '/backend/system/operation-logs',
                'tone' => 'slate',
            ],
        ];
    }

    private function todos(): array
    {
        $items = [];

        $pendingFeedback = Feedback::query()->where('fb_status', FeedbackStatus::Pending)->count();
        if ($pendingFeedback > 0) {
            $items[] = [
                'key' => 'feedback_pending',
                'title' => '待处理用户留言',
                'count' => $pendingFeedback,
                'path' => '/backend/feedbacks',
                'level' => 'warning',
            ];
        }

        $pendingArticle = Article::query()->where('art_status', ArticleStatus::Pending)->count();
        if ($pendingArticle > 0) {
            $items[] = [
                'key' => 'article_pending',
                'title' => '待审核文章',
                'count' => $pendingArticle,
                'path' => '/backend/news/articles',
                'level' => 'warning',
            ];
        }

        $pendingAd = AdPosition::query()->where('audit_status', AdAuditStatus::Pending)->count();
        if ($pendingAd > 0) {
            $items[] = [
                'key' => 'ad_pending',
                'title' => '待审核广告',
                'count' => $pendingAd,
                'path' => '/backend/ad-positions',
                'level' => 'warning',
            ];
        }

        $pendingWf = WfFlowApply::query()->where('apply_status', WfApplyStatus::Pending)->count();
        if ($pendingWf > 0) {
            $items[] = [
                'key' => 'wf_pending',
                'title' => '待办审批',
                'count' => $pendingWf,
                'path' => '/backend/wf/todo',
                'level' => 'danger',
            ];
        }

        $expiringJobs = BossJob::query()
            ->where('job_status', BossJobStatus::Published)
            ->whereNotNull('expire_at')
            ->whereBetween('expire_at', [now(), now()->addDays(7)])
            ->count();
        if ($expiringJobs > 0) {
            $items[] = [
                'key' => 'job_expiring',
                'title' => '7 日内到期职位',
                'count' => $expiringJobs,
                'path' => '/backend/boss-jobs',
                'level' => 'info',
            ];
        }

        $endingAds = AdPosition::query()
            ->where('status', AdStatus::Running)
            ->whereBetween('end_time', [now(), now()->addDays(3)])
            ->count();
        if ($endingAds > 0) {
            $items[] = [
                'key' => 'ad_ending',
                'title' => '3 日内结束投放',
                'count' => $endingAds,
                'path' => '/backend/ad-positions',
                'level' => 'info',
            ];
        }

        return $items;
    }

    private function trends(int $days): array
    {
        $dates = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $dates[] = Carbon::today()->subDays($i)->format('Y-m-d');
        }

        $series = [
            'users' => $this->dailyCounts(UserAccount::query(), 'created_at', $dates),
            'feedbacks' => $this->dailyCounts(Feedback::query(), 'created_at', $dates),
            'articles' => $this->dailyCounts(Article::query(), 'created_at', $dates),
            'logs' => $this->dailyCounts(OperationLog::query(), 'created_at', $dates),
        ];

        return [
            'dates' => $dates,
            'series' => [
                ['key' => 'users', 'label' => '新用户', 'data' => $series['users']],
                ['key' => 'feedbacks', 'label' => '留言', 'data' => $series['feedbacks']],
                ['key' => 'articles', 'label' => '文章', 'data' => $series['articles']],
                ['key' => 'logs', 'label' => '操作', 'data' => $series['logs']],
            ],
        ];
    }

    private function dailyCounts($query, string $column, array $dates): array
    {
        $start = Carbon::parse($dates[0])->startOfDay();
        $end = Carbon::parse($dates[array_key_last($dates)])->endOfDay();

        $rows = (clone $query)
            ->whereBetween($column, [$start, $end])
            ->selectRaw("DATE({$column}) as d, COUNT(*) as c")
            ->groupBy('d')
            ->pluck('c', 'd')
            ->all();

        return array_map(fn (string $date) => (int) ($rows[$date] ?? 0), $dates);
    }

    private function recentLogs(int $limit): array
    {
        return OperationLog::query()
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(function (OperationLog $log) {
                $action = $log->action instanceof OperationAction
                    ? $log->action->label()
                    : (string) $log->action;
                $biz = OperationBizType::tryFrom((string) $log->biz_type)?->label()
                    ?? (string) $log->biz_type;

                return [
                    'id' => (string) $log->id,
                    'operator_name' => $log->operator_name ?: '-',
                    'biz_type' => $biz,
                    'action' => $action,
                    'biz_label' => $log->biz_label ?: '-',
                    'created_at' => optional($log->created_at)?->format('Y-m-d H:i:s'),
                ];
            })
            ->values()
            ->all();
    }

    private function recentFeedbacks(int $limit): array
    {
        return Feedback::query()
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn (Feedback $item) => [
                'id' => (string) $item->id,
                'fb_name' => $item->fb_name,
                'fb_title' => $item->fb_title,
                'fb_status' => $item->fb_status?->value,
                'fb_status_label' => $item->fb_status?->label(),
                'created_at' => optional($item->created_at)?->format('Y-m-d H:i:s'),
            ])
            ->values()
            ->all();
    }

    private function recentArticles(int $limit): array
    {
        return Article::query()
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn (Article $item) => [
                'id' => (string) $item->id,
                'title' => $item->title,
                'author_name' => $item->author_name ?: '-',
                'art_status' => $item->art_status?->value,
                'art_status_label' => $item->art_status?->label(),
                'view_count' => (int) $item->view_count,
                'published_at' => optional($item->published_at)?->format('Y-m-d H:i:s'),
                'created_at' => optional($item->created_at)?->format('Y-m-d H:i:s'),
            ])
            ->values()
            ->all();
    }

    private function adMetrics(): array
    {
        $running = AdPosition::query()->where('status', AdStatus::Running);
        $impressions = (clone $running)->sum('impression_count');
        $clicks = (clone $running)->sum('click_count');
        $ctr = $impressions > 0 ? round($clicks / $impressions, 4) : 0;

        return [
            'slots_enabled' => AdSlot::query()->where('slot_status', 1)->count(),
            'slots_total' => AdSlot::query()->count(),
            'ads_running' => (clone $running)->count(),
            'ads_total' => AdPosition::query()->count(),
            'impression_count' => (int) $impressions,
            'click_count' => (int) $clicks,
            'click_rate' => number_format($ctr, 4, '.', ''),
        ];
    }

    private function quickLinks(): array
    {
        $links = [
            ['title' => '用户管理', 'path' => '/backend/users', 'desc' => '账号与状态'],
            ['title' => '商品管理', 'path' => '/backend/product/products', 'desc' => '商品上下架'],
            ['title' => '文章管理', 'path' => '/backend/news/articles', 'desc' => '内容发布'],
            ['title' => '用户留言', 'path' => '/backend/feedbacks', 'desc' => '待办回复'],
            ['title' => '广告管理', 'path' => '/backend/ad-positions', 'desc' => '投放与审核'],
            ['title' => '招聘职位', 'path' => '/backend/boss-jobs', 'desc' => '职位发布'],
            ['title' => '待办审批', 'path' => '/backend/wf/todo', 'desc' => '流程处理'],
            ['title' => '操作日志', 'path' => '/backend/system/operation-logs', 'desc' => '审计追溯'],
        ];

        $extra = [
            'brands' => ProductBrand::query()->count(),
            'departments' => HrDepartment::query()->count(),
            'posts' => HrPost::query()->count(),
            'friend_links' => FriendLink::query()->count(),
            'bookmarks' => Schema::hasTable('book_mark') ? BookMark::query()->count() : 0,
        ];

        return [
            'items' => $links,
            'assets' => [
                ['label' => '品牌', 'value' => $extra['brands'], 'path' => '/backend/product/brands'],
                ['label' => '部门', 'value' => $extra['departments'], 'path' => '/backend/hr/departments'],
                ['label' => '岗位', 'value' => $extra['posts'], 'path' => '/backend/hr/posts'],
                ['label' => '友链', 'value' => $extra['friend_links'], 'path' => '/backend/friend-links'],
                ['label' => '书签', 'value' => $extra['bookmarks'], 'path' => '/backend/bookmarks'],
            ],
        ];
    }
}
