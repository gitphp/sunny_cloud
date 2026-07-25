<?php

namespace App\Http\Controllers\backend;

use App\Enums\HrPostStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\HrPostRequest;
use App\Http\Resources\backend\HrPostResource;
use App\Models\HrPost;
use App\Service\HrPostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class HrPostController extends AbstractController
{
    public function __construct(
        private readonly HrPostService $hrPostService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $tree = $this->hrPostService->getTree($request->query('keyword'));

        return $this->success([
            'list' => $tree,
            'options' => [
                'post_status' => HrPostStatus::labels(),
            ],
        ]);
    }

    public function store(HrPostRequest $request): JsonResponse
    {
        try {
            $post = $this->hrPostService->create($request->validated());

            return $this->success(
                (new HrPostResource($post))->resolve(),
                '添加成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(840099, '添加失败');
        }
    }

    public function update(HrPostRequest $request, HrPost $hrPost): JsonResponse
    {
        try {
            $post = $this->hrPostService->update($hrPost, $request->validated());

            return $this->success(
                (new HrPostResource($post))->resolve(),
                '修改成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(840099, '修改失败');
        }
    }

    public function updateSort(HrPostRequest $request, HrPost $hrPost): JsonResponse
    {
        $post = $this->hrPostService->updateSort(
            $hrPost,
            (int) $request->validated('post_sort')
        );

        return $this->success(
            (new HrPostResource($post))->resolve(),
            '排序更新成功'
        );
    }

    public function updateStatus(HrPostRequest $request, HrPost $hrPost): JsonResponse
    {
        $post = $this->hrPostService->updateStatus(
            $hrPost,
            (int) $request->validated('post_status')
        );

        return $this->success(
            (new HrPostResource($post))->resolve(),
            '状态更新成功'
        );
    }

    public function destroy(HrPost $hrPost): JsonResponse
    {
        try {
            $this->hrPostService->delete($hrPost);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(840099, '删除失败');
        }
    }
}
