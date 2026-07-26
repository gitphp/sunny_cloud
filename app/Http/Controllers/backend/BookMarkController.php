<?php

namespace App\Http\Controllers\backend;

use App\Enums\BookMarkBold;
use App\Enums\BookMarkStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\BookMarkRequest;
use App\Http\Resources\backend\BookMarkResource;
use App\Models\BookMark;
use App\Service\BookMarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class BookMarkController extends AbstractController
{
    public function __construct(
        private readonly BookMarkService $bookMarkService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->bookMarkService->paginate(
            $request->only(['keyword', 'category_id', 'status']),
            (int) $request->query('per_page', 15)
        );

        return $this->success([
            'list' => collect($paginator->items())
                ->map(fn (BookMark $item) => $this->bookMarkService->toArray($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'status' => BookMarkStatus::labels(),
                'is_bold' => BookMarkBold::labels(),
                'categories' => $this->bookMarkService->categoryOptions(),
            ],
        ]);
    }

    public function store(BookMarkRequest $request): JsonResponse
    {
        try {
            $bookmark = $this->bookMarkService->create($request->validated());

            return $this->success(
                (new BookMarkResource($bookmark))->resolve(),
                '添加成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(940099, '添加失败');
        }
    }

    public function update(BookMarkRequest $request, BookMark $bookMark): JsonResponse
    {
        try {
            $bookmark = $this->bookMarkService->update($bookMark, $request->validated());

            return $this->success(
                (new BookMarkResource($bookmark))->resolve(),
                '修改成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(940099, '修改失败');
        }
    }

    public function updateSort(BookMarkRequest $request, BookMark $bookMark): JsonResponse
    {
        $bookmark = $this->bookMarkService->updateSort(
            $bookMark,
            (int) $request->validated('sort_order')
        );

        return $this->success(
            (new BookMarkResource($bookmark))->resolve(),
            '排序更新成功'
        );
    }

    public function updateStatus(BookMarkRequest $request, BookMark $bookMark): JsonResponse
    {
        $bookmark = $this->bookMarkService->updateStatus(
            $bookMark,
            (int) $request->validated('status')
        );

        return $this->success(
            (new BookMarkResource($bookmark))->resolve(),
            '状态更新成功'
        );
    }

    public function destroy(BookMark $bookMark): JsonResponse
    {
        try {
            $this->bookMarkService->delete($bookMark);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(940099, '删除失败');
        }
    }
}
