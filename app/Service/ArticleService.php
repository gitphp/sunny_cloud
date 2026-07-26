<?php

namespace App\Service;

use App\Constants\Code\ArticleError;
use App\Constants\Code\CodePrefix;
use App\Enums\ArticleContentType;
use App\Enums\ArticleFlag;
use App\Enums\ArticleStatus;
use App\Enums\OperationAction;
use App\Enums\OperationBizType;
use App\Exceptions\BusinessException;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\UserAccount;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ArticleService
{
    public function __construct(
        private readonly OperationLogService $operationLogService
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Article::query()
            ->with('category:id,cat_name')
            ->orderByDesc('is_top')
            ->orderByDesc('id');

        if (! empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('title', 'like', '%'.$kw.'%')
                    ->orWhere('subtitle', 'like', '%'.$kw.'%')
                    ->orWhere('author_name', 'like', '%'.$kw.'%')
                    ->orWhere('summary', 'like', '%'.$kw.'%');
            });
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if (isset($filters['art_status']) && $filters['art_status'] !== '' && $filters['art_status'] !== null) {
            $query->where('art_status', (int) $filters['art_status']);
        }

        if (isset($filters['is_top']) && $filters['is_top'] !== '' && $filters['is_top'] !== null) {
            $query->where('is_top', (int) $filters['is_top']);
        }

        return $query->paginate($perPage);
    }

    public function findOrFail(int|string $id): Article
    {
        $article = Article::query()->with('category:id,cat_name')->find($id);
        if (! $article) {
            throw new BusinessException($this->code(ArticleError::NOT_FOUND), '文章不存在');
        }

        return $article;
    }

    public function create(array $data): Article
    {
        $categoryId = (int) ($data['category_id'] ?? 0);
        $this->assertCategory($categoryId);

        [$authorId, $authorName] = $this->resolveAuthor($data);

        $summary = (string) ($data['summary'] ?? '');
        if ($summary === '' && ! empty($data['art_content'])) {
            $summary = Str::limit(strip_tags((string) $data['art_content']), 200, '');
        }

        $status = ArticleStatus::from((int) ($data['art_status'] ?? ArticleStatus::Draft->value));

        $article = Article::query()->create([
            'title' => (string) $data['title'],
            'subtitle' => (string) ($data['subtitle'] ?? ''),
            'art_cover' => (string) ($data['art_cover'] ?? ''),
            'art_content' => (string) ($data['art_content'] ?? ''),
            'content_type' => ArticleContentType::from((int) ($data['content_type'] ?? ArticleContentType::RichText->value)),
            'summary' => $summary,
            'category_id' => $categoryId,
            'tag_ids' => $data['tag_ids'] ?? null,
            'author_id' => $authorId,
            'author_name' => $authorName,
            'source' => (string) ($data['source'] ?? ''),
            'source_url' => (string) ($data['source_url'] ?? ''),
            'art_status' => $status,
            'is_top' => ArticleFlag::from((int) ($data['is_top'] ?? ArticleFlag::No->value)),
            'is_original' => ArticleFlag::from((int) ($data['is_original'] ?? ArticleFlag::Yes->value)),
            'is_commentable' => ArticleFlag::from((int) ($data['is_commentable'] ?? ArticleFlag::Yes->value)),
            'seo_title' => (string) ($data['seo_title'] ?? ''),
            'seo_keywords' => (string) ($data['seo_keywords'] ?? ''),
            'seo_description' => (string) ($data['seo_description'] ?? ''),
            'extra_fields' => $data['extra_fields'] ?? null,
            'published_at' => $status === ArticleStatus::Published ? now() : null,
            'reject_reason' => null,
        ]);

        $this->operationLogService->logCrud(
            OperationAction::Insert,
            OperationBizType::Article,
            'article_created',
            $article->id,
            $article->title,
            null,
            $this->toArray($article),
            'ArticleService@create'
        );

        return $article->load('category:id,cat_name');
    }

    public function update(Article $article, array $data): Article
    {
        $categoryId = (int) ($data['category_id'] ?? $article->category_id);
        $this->assertCategory($categoryId);

        $old = $this->toArray($article);

        [$authorId, $authorName] = $this->resolveAuthor($data, $article);

        $summary = (string) ($data['summary'] ?? $article->summary);
        if ($summary === '' && array_key_exists('art_content', $data)) {
            $summary = Str::limit(strip_tags((string) $data['art_content']), 200, '');
        }

        $status = isset($data['art_status'])
            ? ArticleStatus::from((int) $data['art_status'])
            : $article->art_status;

        $publishedAt = $article->published_at;
        if ($status === ArticleStatus::Published && ! $publishedAt) {
            $publishedAt = now();
        }

        $article->fill([
            'title' => (string) ($data['title'] ?? $article->title),
            'subtitle' => (string) ($data['subtitle'] ?? $article->subtitle),
            'art_cover' => (string) ($data['art_cover'] ?? $article->art_cover),
            'art_content' => (string) ($data['art_content'] ?? $article->art_content),
            'content_type' => isset($data['content_type'])
                ? ArticleContentType::from((int) $data['content_type'])
                : $article->content_type,
            'summary' => $summary,
            'category_id' => $categoryId,
            'tag_ids' => array_key_exists('tag_ids', $data) ? $data['tag_ids'] : $article->tag_ids,
            'author_id' => $authorId,
            'author_name' => $authorName,
            'source' => (string) ($data['source'] ?? $article->source),
            'source_url' => (string) ($data['source_url'] ?? $article->source_url),
            'art_status' => $status,
            'is_top' => isset($data['is_top'])
                ? ArticleFlag::from((int) $data['is_top'])
                : $article->is_top,
            'is_original' => isset($data['is_original'])
                ? ArticleFlag::from((int) $data['is_original'])
                : $article->is_original,
            'is_commentable' => isset($data['is_commentable'])
                ? ArticleFlag::from((int) $data['is_commentable'])
                : $article->is_commentable,
            'seo_title' => (string) ($data['seo_title'] ?? $article->seo_title),
            'seo_keywords' => (string) ($data['seo_keywords'] ?? $article->seo_keywords),
            'seo_description' => (string) ($data['seo_description'] ?? $article->seo_description),
            'extra_fields' => array_key_exists('extra_fields', $data) ? $data['extra_fields'] : $article->extra_fields,
            'published_at' => $publishedAt,
        ]);
        $article->save();
        $article = $article->fresh()->load('category:id,cat_name');

        $this->operationLogService->logCrud(
            OperationAction::Update,
            OperationBizType::Article,
            'article_updated',
            $article->id,
            $article->title,
            $old,
            $this->toArray($article),
            'ArticleService@update'
        );

        return $article;
    }

    public function updateStatus(Article $article, int $status, string $rejectReason = ''): Article
    {
        $newStatus = ArticleStatus::from($status);
        $old = $this->toArray($article);

        if ($newStatus === ArticleStatus::Rejected && trim($rejectReason) === '') {
            throw new BusinessException(
                $this->code(ArticleError::REJECT_REASON_REQUIRED),
                '驳回时请填写驳回原因'
            );
        }

        $article->art_status = $newStatus;

        if ($newStatus === ArticleStatus::Published && ! $article->published_at) {
            $article->published_at = now();
        }

        if (in_array($newStatus, [ArticleStatus::Approved, ArticleStatus::Rejected, ArticleStatus::Published], true)) {
            $article->reviewer_id = Auth::guard('backend')->id() ?? 0;
            $article->reviewed_at = now();
        }

        $article->reject_reason = $newStatus === ArticleStatus::Rejected ? $rejectReason : null;
        $article->save();
        $article = $article->fresh()->load('category:id,cat_name');

        $this->operationLogService->logCrud(
            OperationAction::Update,
            OperationBizType::Article,
            'article_status_updated',
            $article->id,
            $article->title,
            $old,
            $this->toArray($article),
            'ArticleService@updateStatus'
        );

        return $article;
    }

    public function updateTop(Article $article, int $isTop): Article
    {
        $article->is_top = ArticleFlag::from($isTop);
        $article->save();

        return $article->fresh()->load('category:id,cat_name');
    }

    public function delete(Article $article): void
    {
        $old = $this->toArray($article);
        $article->delete();

        $this->operationLogService->logCrud(
            OperationAction::Delete,
            OperationBizType::Article,
            'article_deleted',
            $old['id'],
            $old['title'],
            $old,
            null,
            'ArticleService@delete'
        );
    }

    public function toArray(Article $article): array
    {
        return [
            'id' => (string) $article->id,
            'title' => $article->title,
            'subtitle' => $article->subtitle,
            'art_cover' => $article->art_cover,
            'art_content' => $article->art_content,
            'content_type' => $article->content_type?->value,
            'content_type_label' => $article->content_type?->label(),
            'summary' => $article->summary,
            'category_id' => (string) $article->category_id,
            'category_name' => $article->category?->cat_name ?? '',
            'tag_ids' => $article->tag_ids ?? [],
            'author_id' => (string) $article->author_id,
            'author_name' => $article->author_name,
            'source' => $article->source,
            'source_url' => $article->source_url,
            'art_status' => $article->art_status?->value,
            'art_status_label' => $article->art_status?->label(),
            'is_top' => $article->is_top?->value,
            'is_top_label' => $article->is_top?->label(),
            'is_original' => $article->is_original?->value,
            'is_original_label' => $article->is_original?->label(),
            'is_commentable' => $article->is_commentable?->value,
            'is_commentable_label' => $article->is_commentable?->label(),
            'seo_title' => $article->seo_title,
            'seo_keywords' => $article->seo_keywords,
            'seo_description' => $article->seo_description,
            'extra_fields' => $article->extra_fields,
            'view_count' => (int) $article->view_count,
            'like_count' => (int) $article->like_count,
            'collect_count' => (int) $article->collect_count,
            'share_count' => (int) $article->share_count,
            'comment_count' => (int) $article->comment_count,
            'published_at' => optional($article->published_at)?->format('Y-m-d H:i:s'),
            'reviewer_id' => (string) $article->reviewer_id,
            'reviewed_at' => optional($article->reviewed_at)?->format('Y-m-d H:i:s'),
            'reject_reason' => $article->reject_reason,
            'created_at' => optional($article->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($article->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }

    public function toListArray(Article $article): array
    {
        $data = $this->toArray($article);
        unset($data['art_content'], $data['extra_fields']);

        return $data;
    }

    private function assertCategory(int $categoryId): void
    {
        if ($categoryId <= 0 || ! ArticleCategory::query()->whereKey($categoryId)->exists()) {
            throw new BusinessException($this->code(ArticleError::CATEGORY_INVALID), '请选择有效的文章分类');
        }
    }

    /**
     * @return array{0:int,1:string}
     */
    private function resolveAuthor(array $data, ?Article $article = null): array
    {
        /** @var UserAccount|null $user */
        $user = Auth::guard('backend')->user();

        $authorId = (int) ($data['author_id'] ?? ($article?->author_id ?: ($user?->id ?? 0)));
        $authorName = (string) ($data['author_name'] ?? '');

        if ($authorName === '' && $authorId > 0) {
            if ($user && (int) $user->id === $authorId) {
                $authorName = $user->nick_name !== '' ? $user->nick_name : $user->user_name;
            } else {
                $author = UserAccount::query()->find($authorId);
                $authorName = $author
                    ? ($author->nick_name !== '' ? $author->nick_name : $author->user_name)
                    : ($article?->author_name ?? '');
            }
        }

        if ($authorName === '' && $article) {
            $authorName = $article->author_name;
        }

        return [$authorId, mb_substr($authorName, 0, 16)];
    }

    private function code(int $error): int
    {
        return CodePrefix::ARTICLE * 1000 + $error;
    }
}
