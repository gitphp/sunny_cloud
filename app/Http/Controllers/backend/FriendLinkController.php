<?php

namespace App\Http\Controllers\backend;

use App\Enums\FriendLinkStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\FriendLinkRequest;
use App\Http\Resources\backend\FriendLinkResource;
use App\Models\FriendLink;
use App\Service\FriendLinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class FriendLinkController extends AbstractController
{
    public function __construct(
        private readonly FriendLinkService $friendLinkService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->friendLinkService->paginate(
            $request->only(['keyword', 'link_status']),
            (int) $request->query('per_page', 15)
        );

        return $this->success([
            'list' => collect($paginator->items())
                ->map(fn (FriendLink $item) => $this->friendLinkService->toArray($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'link_status' => FriendLinkStatus::labels(),
            ],
        ]);
    }

    public function store(FriendLinkRequest $request): JsonResponse
    {
        try {
            $link = $this->friendLinkService->create($request->validated());

            return $this->success(
                (new FriendLinkResource($link))->resolve(),
                '添加成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(970099, '添加失败');
        }
    }

    public function update(FriendLinkRequest $request, FriendLink $friendLink): JsonResponse
    {
        try {
            $link = $this->friendLinkService->update($friendLink, $request->validated());

            return $this->success(
                (new FriendLinkResource($link))->resolve(),
                '修改成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(970099, '修改失败');
        }
    }

    public function updateSort(FriendLinkRequest $request, FriendLink $friendLink): JsonResponse
    {
        $link = $this->friendLinkService->updateSort($friendLink, (int) $request->validated('link_sort'));

        return $this->success(
            (new FriendLinkResource($link))->resolve(),
            '排序更新成功'
        );
    }

    public function updateStatus(FriendLinkRequest $request, FriendLink $friendLink): JsonResponse
    {
        $link = $this->friendLinkService->updateStatus($friendLink, (int) $request->validated('link_status'));

        return $this->success(
            (new FriendLinkResource($link))->resolve(),
            '状态更新成功'
        );
    }

    public function destroy(FriendLink $friendLink): JsonResponse
    {
        try {
            $this->friendLinkService->delete($friendLink);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(970099, '删除失败');
        }
    }
}
