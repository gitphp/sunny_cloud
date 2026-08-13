<?php

namespace App\Service;

use App\Constants\Code\BookMarkError;
use App\Constants\Code\CodePrefix;
use App\Enums\BookMarkBold;
use App\Enums\BookMarkStatus;
use App\Enums\CategoryType;
use App\Enums\OperationAction;
use App\Enums\OperationBizType;
use App\Exceptions\BusinessException;
use App\Models\BookMark;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class BookMarkService
{
    public function __construct(
        private readonly OperationLogService $operationLogService,
        private readonly CategoryService $categoryService
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = BookMark::query()
            ->with('category:id,category_name')
            ->orderBy('sort_order')
            ->orderByDesc('id');

        if (! empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('short_title', 'like', '%'.$kw.'%')
                    ->orWhere('book_title', 'like', '%'.$kw.'%')
                    ->orWhere('book_url', 'like', '%'.$kw.'%')
                    ->orWhere('book_desc', 'like', '%'.$kw.'%');
            });
        }

        if (isset($filters['category_id']) && $filters['category_id'] !== '' && $filters['category_id'] !== null) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== null) {
            $query->where('status', (int) $filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): BookMark
    {
        $categoryId = $this->normalizeCategoryId($data['category_id'] ?? 0);
        $this->assertCategory($categoryId);
        $this->assertUrl((string) ($data['book_url'] ?? ''));

        $bookmark = BookMark::query()->create([
            'category_id' => $categoryId,
            'short_title' => (string) ($data['short_title'] ?? ''),
            'book_title' => (string) $data['book_title'],
            'book_url' => (string) $data['book_url'],
            'book_favicon' => (string) ($data['book_favicon'] ?? ''),
            'book_desc' => (string) ($data['book_desc'] ?? ''),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'status' => BookMarkStatus::from((int) ($data['status'] ?? BookMarkStatus::Normal->value)),
            'is_bold' => BookMarkBold::from((int) ($data['is_bold'] ?? BookMarkBold::Bold->value)),
            'created_by' => Auth::guard('backend')->id() ?? 0,
        ]);

        $this->operationLogService->logCrud(
            OperationAction::Insert,
            OperationBizType::BookMark,
            'book_mark_created',
            $bookmark->id,
            $bookmark->book_title ?: $bookmark->short_title,
            null,
            $this->toArray($bookmark),
            'BookMarkService@create'
        );

        return $bookmark->load('category:id,category_name');
    }

    public function update(BookMark $bookmark, array $data): BookMark
    {
        $categoryId = $this->normalizeCategoryId($data['category_id'] ?? $bookmark->category_id);
        $this->assertCategory($categoryId);

        $url = (string) ($data['book_url'] ?? $bookmark->book_url);
        $this->assertUrl($url);

        $old = $this->toArray($bookmark);

        $bookmark->fill([
            'category_id' => $categoryId,
            'short_title' => (string) ($data['short_title'] ?? $bookmark->short_title),
            'book_title' => (string) ($data['book_title'] ?? $bookmark->book_title),
            'book_url' => $url,
            'book_favicon' => (string) ($data['book_favicon'] ?? $bookmark->book_favicon),
            'book_desc' => (string) ($data['book_desc'] ?? $bookmark->book_desc),
            'sort_order' => (int) ($data['sort_order'] ?? $bookmark->sort_order),
            'status' => isset($data['status'])
                ? BookMarkStatus::from((int) $data['status'])
                : $bookmark->status,
            'is_bold' => isset($data['is_bold'])
                ? BookMarkBold::from((int) $data['is_bold'])
                : $bookmark->is_bold,
        ]);
        $bookmark->save();
        $bookmark = $bookmark->fresh()->load('category:id,category_name');

        $this->operationLogService->logCrud(
            OperationAction::Update,
            OperationBizType::BookMark,
            'book_mark_updated',
            $bookmark->id,
            $bookmark->book_title ?: $bookmark->short_title,
            $old,
            $this->toArray($bookmark),
            'BookMarkService@update'
        );

        return $bookmark;
    }

    public function updateSort(BookMark $bookmark, int $sort): BookMark
    {
        $bookmark->sort_order = $sort;
        $bookmark->save();

        return $bookmark->fresh()->load('category:id,category_name');
    }

    public function updateStatus(BookMark $bookmark, int $status): BookMark
    {
        $bookmark->status = BookMarkStatus::from($status);
        $bookmark->save();

        return $bookmark->fresh()->load('category:id,category_name');
    }

    public function delete(BookMark $bookmark): void
    {
        $old = $this->toArray($bookmark);
        $bookmark->delete();

        $this->operationLogService->logCrud(
            OperationAction::Delete,
            OperationBizType::BookMark,
            'book_mark_deleted',
            $old['id'],
            $old['book_title'] ?: $old['short_title'],
            $old,
            null,
            'BookMarkService@delete'
        );
    }

    public function toArray(BookMark $bookmark): array
    {
        return [
            'id' => (string) $bookmark->id,
            'category_id' => (string) $bookmark->category_id,
            'category_name' => $bookmark->category?->category_name ?? ($bookmark->category_id == 0 ? '未分类' : ''),
            'short_title' => $bookmark->short_title,
            'book_title' => $bookmark->book_title,
            'book_url' => $bookmark->book_url,
            'book_favicon' => $bookmark->book_favicon,
            'book_desc' => $bookmark->book_desc,
            'sort_order' => (int) $bookmark->sort_order,
            'status' => $bookmark->status?->value,
            'status_label' => $bookmark->status?->label(),
            'is_bold' => $bookmark->is_bold?->value,
            'is_bold_label' => $bookmark->is_bold?->label(),
            'created_by' => (string) $bookmark->created_by,
            'created_at' => optional($bookmark->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($bookmark->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }

    public function categoryOptions(): array
    {
        return $this->categoryService->getTree(null, CategoryType::Portal->value);
    }

    private function normalizeCategoryId(mixed $categoryId): int
    {
        if ($categoryId === null || $categoryId === '' || $categoryId === '0') {
            return 0;
        }

        return (int) $categoryId;
    }

    private function assertCategory(int $categoryId): void
    {
        if ($categoryId === 0) {
            return;
        }

        if (! Category::query()->whereKey($categoryId)->exists()) {
            throw new BusinessException(
                $this->code(BookMarkError::CATEGORY_INVALID),
                '所属分类不存在'
            );
        }
    }

    private function assertUrl(string $url): void
    {
        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new BusinessException(
                $this->code(BookMarkError::URL_INVALID),
                '请填写有效的链接地址'
            );
        }
    }

    private function code(int $error): int
    {
        return CodePrefix::BOOK_MARK * 1000 + $error;
    }
}
